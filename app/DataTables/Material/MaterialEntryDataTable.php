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
                return number_format($row->price ?? 0);
            })
            ->editColumn('total_price', function ($row) {
                return number_format($row->total_price ?? 0);
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('material::materialEntry.action', ['module' => $module]);
            })
            ->editColumn('note', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->note) . '</div>';
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
    public function query(MaterialEntry $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery()
            ->select([
                'material_entries.id',
                'material_entries.ministry_id',
                'material_entries.company_name',
                'material_entries.stock_number',
                'material_entries.stock_name',
                'material_entries.user_entry',
                'material_entries.p_code',
                'material_entries.p_name',
                'material_entries.p_year',
                'material_entries.title',
                'material_entries.unit',
                'material_entries.quantity',
                'material_entries.price',
                'material_entries.total_price',
                'material_entries.source',
                'material_entries.note',
                'material_entries.refer',
                'material_entries.date_entry',
                'material_entries.file',
                'material_entries.created_at',
                'material_entries.updated_at',
            ])
            ->where('material_entries.ministry_id', $id);

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

            Column::make('company_name')->title(__('tables.th.company.name'))->width(90)->addClass('align-middle'),
            Column::make('stock_number')->title(__('tables.th.stock.number'))->width(30)->addClass('align-middle'),
            Column::make('stock_name')->title(__('tables.th.stock.name'))->width(30)->addClass('align-middle'),
            Column::make('user_entry')->title(__('tables.th.user.entry'))->width(60)->addClass('align-middle'),
            Column::make('p_code')->title(__('tables.th.pro.code'))->width(60)->addClass('align-middle'),
            Column::make('p_name')->title(__('tables.th.pro.name'))->width(80)->addClass('align-middle'),
            Column::make('p_year')->title(__('tables.th.pro.year'))->width(80)->addClass('align-middle'),
            Column::make('title')->title(__('tables.th.title'))->width(80)->addClass('align-middle'),
            Column::make('unit')->title(__('tables.th.unit'))->width(80)->addClass('align-middle'),
            Column::make('quantity')->title(__('tables.th.quantity'))->width(80)->addClass('align-middle'),
            Column::make('price')->title(__('tables.th.price'))->width(80)->addClass('align-middle'),
            Column::make('total_price')->title(__('tables.th.total.price'))->width(80)->addClass('align-middle'),
            Column::make('source')->title(__('tables.th.source'))->width(80)->addClass('align-middle'),
            Column::make('note')->title(__('tables.th.note'))->addClass('align-middle'),
            Column::make('refer')->title(__('tables.th.refer'))->addClass('align-middle'),
            Column::make('date_entry')->title(__('tables.th.date.entry'))->width(200)->addClass('align-middle'),
            Column::make('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),

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
