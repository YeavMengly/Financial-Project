<?php

namespace App\DataTables\Material;

use App\Models\Material\MaterialRelease;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
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
            ->editColumn('total', function ($row) {
                return number_format($row->total ?? 0) . ' ៛';
            })
            ->editColumn('quantity_request', function ($row) {
                return number_format($row->quantity_request ?? 0) . ' ៛';
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
        $query->where('material_releases.ministry_id', $id)
            ->leftJoin('agencies', 'material_releases.agency_id', '=', 'agencies.id')
            ->leftJoin('projects as parent_project', 'material_releases.project_id', '=', 'parent_project.id')
            ->leftJoin('projects as sub_project_rel', 'material_releases.project_sub_id', '=', 'sub_project_rel.id')
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
                'material_releases.total',
                'material_releases.source',
                'material_releases.refer',
                'material_releases.date_release',
                'material_releases.file',
                'material_releases.created_at',
                'material_releases.updated_at',
            ])
            ->where('material_releases.ministry_id', $id)
            ->orderBy('material_releases.id', 'DESC');

        // Date Filters (Applied directly to $query)
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('material_releases.date_release', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('material_releases.date_release', '<=', $request->end_date);
        });

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
               
                d.start_date = $("#start_date").val();
                d.end_date = $("#end_date").val();
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
            Column::make('quantity_total')->title(__('tables.th.quantity.req'))->width(80)->addClass('align-middle'),
            Column::make('unit')->title(__('tables.th.unit'))->width(80)->addClass('align-middle'),
            Column::make('quantity_request')->title(__('tables.th.price.unit'))->width(80)->addClass('align-middle'),
            Column::make('total')->title(__('tables.th.total.price'))->width(80)->addClass('align-middle'),
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
