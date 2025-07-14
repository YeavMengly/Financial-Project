<?php

namespace App\DataTables;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class RoleDataTable extends DataTable
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
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">'.__("buttons.active").'</span>' : '<span class="badge bg-danger">'.__("buttons.deleted").'</span>';
                return $active;
            })
            ->addColumn("dateTime", function($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function($module) {
                if($module->id != '1') {
                    return view('setting::role.action', ['module' => $module]);
                } else {
                    return '';
                }

            })
            ->rawColumns(['soft_delete']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->withTrashed();
        $model->orderBy('id', "ASC");
        $model->select(['roles.id', 'roles.name', 'roles.created_at', 'roles.deleted_at']);
        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->parameters([
                        'language' => [
                            "url" => asset("assets/lang/language.json")
                        ]
                    ])
                    ->setTableId('role-table')
                    ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))->width(30)->addClass('text-center'),
            Column::make('name')->title(__('tables.th.roles')),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),
            Column::computed('action', __('tables.th.action'))->exportable(false)->printable(false)->width(100)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Role_' . date('YmdHis');
    }
}
