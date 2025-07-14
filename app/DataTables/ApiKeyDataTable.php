<?php

namespace App\DataTables;

use App\Models\ApiKey;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ApiKeyDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';

                return $active;
            })
            ->addColumn('dateTime', function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($module) {
                return view('setting::keys.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ApiKey $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->withTrashed();
        $model->select([
            'id',
            'title',
            'key',
            'created_at',
            'deleted_at',
        ]);

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('keys-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))->width(30)->addClass('text-center'),
            Column::make('title')->title(__('tables.th.name')),
            Column::make('key')->title(__('tables.th.key')),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),
            Column::computed(
                'action',
                __('tables.th.action')
            )->exportable(false)->printable(false)->width(100)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ApiKey_' . date('YmdHis');
    }
}
