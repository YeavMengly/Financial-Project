<?php

namespace App\DataTables\Material;

use App\Models\Material\MaterialEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MaterialEntryDataTable extends DataTable
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
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('material::materialEntry.action', ['module' => $module]);
            })

            ->rawColumns(['id']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MaterialEntry $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        // Initialize query builder
        $query = $model->newQuery();

        // Base condition & Joins
        $query->where('material_entries.ministry_id', $id)
            ->leftJoin('projects', 'material_entries.project_sub_id', '=', 'projects.id')
            ->select([
                'material_entries.id',
                'material_entries.ministry_id',
                'material_entries.project_id',
                'material_entries.project_sub_id',
                'projects.sub_project',
                'material_entries.p_name',
                'material_entries.p_year',
                'material_entries.unit',
                'material_entries.qty',
                'material_entries.price',
                'material_entries.total_price',
                'material_entries.source',
                'material_entries.created_at',
                'material_entries.updated_at',
            ])
            ->orderBy('material_entries.id', 'DESC');

        // Project Filter (Exact Match)
        if ($request->filled('project')) {
            $query->where('material_entries.project_id', $request->input('project'));
        }

        // Source Filter (Exact or Partial Match)
        if ($request->filled('source')) {
            $query->where('material_entries.source', 'LIKE', '%' . $request->input('source') . '%');
        }

        // Product Name Filter (Partial Search)
        if ($request->filled('Pname')) {
            $query->where('material_entries.p_name', 'LIKE', '%' . $request->input('Pname') . '%');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('materialentry-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                    d.project = $("#project").val();
                    d.source = $("#source").val();
                    d.Pname = $("#Pname").val();
                    d.stockNum = $("#stockNum").val();
                    d.companyName = $("#companyName").val();
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
            Column::make('sub_project')->title(__('tables.th.sub.pro'))->width(80)->addClass('align-middle'),
            Column::make('p_name')->title(__('tables.th.item.name'))->width(80)->addClass('align-middle'),
            Column::make('unit')->title(__('tables.th.unit'))->width(80)->addClass('align-middle'),
            Column::make('qty')->title(__('tables.th.quantity'))->width(80)->addClass('align-middle'),
            Column::make('price')->title(__('tables.th.price'))->width(80)->addClass('align-middle'),
            Column::make('total_price')->title(__('tables.th.total.price'))->width(80)->addClass('align-middle'),
            Column::make('source')->title(__('tables.th.source'))->width(80)->addClass('align-middle'),
            Column::make('p_year')->title(__('tables.th.pro.year'))->width(80)->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'MaterialEntry_' . date('YmdHis');
    }
}
