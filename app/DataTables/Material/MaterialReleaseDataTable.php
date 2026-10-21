<?php

namespace App\DataTables\Material;

use App\Models\Material\MaterialRelease;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MaterialReleaseDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                return number_format($row->price ?? 0) . ' ៛';
            })
            ->editColumn('total_price', function ($row) {
                return number_format($row->total_price ?? 0) . ' ៛';
            })
            ->editColumn('quantity_request', function ($row) {
                return number_format($row->quantity_request ?? 0);
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('material::materialRelease.action', ['module' => $module]);
            })
            ->editColumn('refer', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->refer) . '</div>';
            })
            ->editColumn('file', function ($row) {
                if (!$row->attachments) {
                    return '<span class="text-muted">-</span>';
                }
                $files = json_decode($row->file, true);
                if (is_array($files)) {
                    $html = '<ul class="list-unstyled m-0">';
                    foreach ($files as $file) {
                        $url = asset('storage/uploads/' . $file);
                        $html .= "<li><a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>$file</a></li>";
                    }
                    $html .= '</ul>';
                    return $html;
                } else {
                    $url = asset('storage/uploads/' . $row->file);
                    return "<a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>Preview</a>";
                }
            })
            ->rawColumns(['note', 'refer', 'file']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MaterialRelease $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery();

        $previousReleasesSubQuery = DB::table('material_releases as dr_prev')
            ->selectRaw('COALESCE(SUM(dr_prev.quantity_request), 0)')
            ->whereColumn('dr_prev.ministry_id', 'material_releases.ministry_id')
            ->whereColumn('dr_prev.project_id', 'material_releases.project_id')
            ->whereColumn('dr_prev.p_name', 'material_releases.p_name')
            ->where(function ($q) {
                $q->whereColumn('dr_prev.date_release', '<', 'material_releases.date_release')
                    ->orWhere(function ($q2) {
                        $q2->whereColumn('dr_prev.date_release', '=', 'material_releases.date_release');
                        // ->whereColumn('dr_prev.receipt_number', '<', 'material_releases.receipt_number');
                    })
                    ->orWhere(function ($q3) {
                        $q3->whereColumn('dr_prev.date_release', '=', 'material_releases.date_release')
                            // ->whereColumn('dr_prev.receipt_number', '=', 'material_releases.receipt_number')
                            ->whereColumn('dr_prev.id', '<', 'material_releases.id');
                    });
            });

        $query->where('material_releases.ministry_id', $id)
            ->leftJoin('agencies', 'material_releases.agency_id', '=', 'agencies.id')
            ->leftJoin('projects as parent_project', 'material_releases.project_id', '=', 'parent_project.id')
            ->leftJoin('projects as sub_project_rel', 'material_releases.project_sub_id', '=', 'sub_project_rel.id')

            // 3. Join Duel Entries table cleanly via project_id and item_name
            ->leftJoin('material_entries', function ($join) use ($id) {
                $join->on('material_entries.project_id', '=', 'material_releases.project_id')
                    ->on('material_entries.p_name', '=', 'material_releases.p_name')
                    ->where('material_entries.ministry_id', '=', $id);
            })

            ->select([
                'material_releases.id',
                'material_releases.ministry_id',
                'agencies.name',
                'parent_project.stock_name',
                'sub_project_rel.sub_project as sub_project',
                'material_releases.p_name',
                'material_releases.p_year',
                'material_releases.title',
                'material_releases.unit',
                'material_releases.quantity_total',
                'material_releases.quantity_request',
                'material_releases.price',
                'material_releases.total_price',
                'material_releases.source',
                'material_releases.refer',
                'material_releases.date_release',
                'material_releases.file',
                'material_releases.created_at',
                'material_releases.updated_at',
                // DB::raw("GREATEST(0, COALESCE(material_entries.qty, 0) - ({$previousReleasesSubQuery->toSql()})) as running_total")

            ])->mergeBindings($previousReleasesSubQuery)
            ->where('material_releases.ministry_id', $id)
            ->orderBy('material_releases.project_id', 'ASC');

        if ($request->filled('project')) {
            $query->where('material_releases.project_id', $request->input('project'));
        }
        if ($request->filled('companyName')) {
            $query->where('parent_project.company_name', $request->input('companyName'));
        }
        if ($request->filled('userEntry')) {
            $query->where('parent_project.user_entry', $request->input('userEntry'));
        }
        if ($request->filled('source')) {
            $query->where('parent_project.source', 'LIKE', '%' . $request->input('source') . '%');
        }
        if ($request->filled('Pname')) {
            $query->where('parent_project.p_name', 'LIKE', '%' . $request->input('Pname') . '%');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('materialrelease-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.project = $("#project").val();
                d.companyName = $("#companyName").val();
                d.userEntry = $("#userEntry").val();
                d.source = $("#source").val();
                d.Pname = $("#Pname").val();
            }',
            ])
            ->orderBy(2, 'ASC');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('p_name')->title(__('tables.th.item.name'))->width(80)->addClass('align-middle'),
            Column::make('unit')->title(__('tables.th.unit'))->width(80)->addClass('align-middle'),

            // Column::make('quantity_total')->title(__('tables.th.quantity'))->width(80)->addClass('align-middle'),
            Column::make('quantity_request')->title(__('tables.th.quantity.req'))->width(80)->addClass('align-middle'),
            Column::make('price')->title(__('tables.th.price'))->width(80)->addClass('align-middle'),
            Column::make('total_price')->title(__('tables.th.total.price'))->width(80)->addClass('align-middle'),

            Column::make('name')->title(__('tables.th.agency'))->width(80)->addClass('align-middle'),
            Column::make('stock_name')->title(__('tables.th.project'))->width(80)->addClass('align-middle'),
            Column::make('sub_project')->title(__('tables.th.project.sub'))->width(80)->addClass('align-middle'),
            Column::make('title')->title(__('tables.th.title'))->width(80)->addClass('align-middle'),
            Column::make('p_year')->title(__('tables.th.pro.year'))->width(80)->addClass('align-middle'),
            Column::make('source')->title(__('tables.th.source'))->width(80)->addClass('align-middle'),
            Column::make('refer')->title(__('tables.th.refer'))->addClass('align-middle'),
            Column::make('date_release')->title(__('tables.th.date.release'))->width(200)->addClass('align-middle'),
            // Column::make('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'MaterialRelease_' . date('YmdHis');
    }
}
