<?php

namespace App\DataTables\Material;

use App\Models\Material\MaterialEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MaterialEntryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Calculate totals dynamically using the computed columns
        $totalQtyBefore = (clone $query)->sum('material_entries.qty');
        $totalQtyAfter  = (clone $query)->sum(DB::raw('CAST(material_entries.qty AS SIGNED) - COALESCE(material_releases_sum.total_released, 0)'));

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('qty_before_release', function ($row) {
                return number_format($row->qty_before_release ?? 0);
            })
            // Target the correctly calculated column
            ->editColumn('qty_after_release', function ($row) {
                return number_format($row->qty_after_release ?? 0);
            })
            ->editColumn('price', function ($row) {
                return number_format($row->price ?? 0) . ' ៛';
            })
            ->editColumn('total_price', function ($row) {
                return number_format($row->total_price ?? 0) . ' ៛';
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                return is_null($soft_delete->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->addColumn('action', function ($module) {
                return view('material::materialEntry.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action'])
            ->with([
                'total_qty_before' => number_format($totalQtyBefore),
                'total_qty_after'  => number_format($totalQtyAfter),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MaterialEntry $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        // Subquery: Sum releases normalized by material name and ministry ID
        $releasedSubquery = DB::table('material_releases')
            ->select(
                DB::raw('LOWER(TRIM(p_name)) as clean_pname'),
                'ministry_id',
                DB::raw('SUM(quantity_total) as total_released')
            )
            ->groupBy(DB::raw('LOWER(TRIM(p_name))'), 'ministry_id');

        $query = $model->newQuery();
        $query->where('material_entries.ministry_id', $id)
            ->leftJoin('projects', 'material_entries.project_sub_id', '=', 'projects.id')
            ->leftJoinSub($releasedSubquery, 'material_releases_sum', function ($join) {
                $join->on(DB::raw('LOWER(TRIM(material_entries.p_name))'), '=', 'material_releases_sum.clean_pname')
                    ->on('material_entries.ministry_id', '=', 'material_releases_sum.ministry_id');
            })
            ->select([
                'material_entries.id',
                'material_entries.ministry_id',
                'material_entries.project_id',
                'material_entries.project_sub_id',
                'projects.sub_project',
                'material_entries.p_name',
                'material_entries.p_year',
                'material_entries.unit',
                // Raw base stock quantity (Original Entry)
                'material_entries.qty AS qty_before_release',
                // Dynamic stock calculation (Entry Stock - Released Stock)
                DB::raw('GREATEST(0, CAST(material_entries.qty AS SIGNED) - COALESCE(material_releases_sum.total_released, 0)) AS qty_after_release'),
                'material_entries.price',
                'material_entries.total_price',
                'material_entries.source',
                'material_entries.created_at',
                'material_entries.updated_at',
            ])
            ->orderBy('material_entries.id', 'DESC');

        // Search & Date Filters
        if ($request->filled('project')) {
            $query->where('material_entries.project_id', $request->input('project'));
        }
        if ($request->filled('source')) {
            $query->where('material_entries.source', 'LIKE', '%' . $request->input('source') . '%');
        }
        if ($request->filled('Pname')) {
            $query->where('material_entries.p_name', 'LIKE', '%' . $request->input('Pname') . '%');
        }

        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('material_entries.updated_at', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('material_entries.updated_at', '<=', $request->end_date);
        });

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('materialentry-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
                'drawCallback' => 'function(settings) {
                var json = this.api().ajax.json();
                if (json) {
                    if (json.total_qty_before !== undefined) {
                        $("#total-qty-before").html(json.total_qty_before);
                    }
                    if (json.total_qty_after !== undefined) {
                        $("#total-qty-after").html(json.total_qty_after);
                    }
                }
            }',
            ])
            ->ajax([
                'data' => 'function(d) {
                d.project = $("#project").val();
                d.source = $("#source").val();
                d.Pname = $("#Pname").val();
                d.start_date = $("#start_date").val();
                d.end_date = $("#end_date").val();
            }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('sub_project')->title(__('tables.th.sub.pro'))->width(80)->addClass('align-middle'),
            Column::make('p_name')->title(__('tables.th.item.name'))->width(80)->addClass('align-middle'),
            Column::make('unit')->title(__('tables.th.unit'))->width(80)->addClass('align-middle'),
            // Total Stock Before Release
            Column::make('qty_before_release')->title(__('បរិមាណសរុប'))->width(90)->addClass('text-center align-middle'),
            // Remaining Stock After Release (Updated to match sql alias)
            Column::make('qty_after_release')->title(__('បរិមាណនៅសល់'))->width(90)->addClass('text-center align-middle'),
            Column::make('price')->title(__('tables.th.price'))->width(80)->addClass('align-middle'),
            Column::make('total_price')->title(__('tables.th.total.price'))->width(80)->addClass('align-middle'),
            Column::make('source')->title(__('tables.th.source'))->width(80)->addClass('align-middle'),
            Column::make('p_year')->title(__('tables.th.pro.year'))->width(80)->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'MaterialEntry_' . date('YmdHis');
    }
}