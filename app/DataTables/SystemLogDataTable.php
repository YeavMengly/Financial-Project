<?php

namespace App\DataTables;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SystemLogDataTable extends DataTable
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
            ->editColumn('created_at',function($table){
                return formatDateTime($table->created_at);
            })
            ->rawColumns(['description','default_field','properties'])
            ->editColumn('description',function($table){
                $bg_color = 'primary';
                if ( $table->description == 'updated' ) {
                    $bg_color = 'warning';
                }elseif ($table->description == 'deleted') {
                    $bg_color = 'danger';
                }elseif ($table->description == 'restored') {
                    $bg_color = 'info';
                }
                return '<button class="btn btn-sm waves-effect waves-light btn-'.$bg_color.'">'.ucfirst($table->description).'</button>';
            })
            ->editColumn('default_field',function($table){
                $class ='show_properties';
                $text_color ='';
                /*if ($table->description=='deleted') {
                    $class ='';
                    $text_color ='text-default';
                }*/
                return '<a  href="javascript:void(0)" log_id="'.$table->id.'"  class="'.$class.' '.$text_color.'">'.ucfirst($table->default_field).'</a>';
            })
            ->editColumn('causer_id',function($table){
                return ucfirst($table->action_user);
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SystemLog $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->join('users', 'activity_log.causer_id', '=','users.id');
        $model->select('activity_log.*','users.fullname as action_user');
        $model->orderBy('activity_log.created_at', 'DESC');
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
            ->setTableId('system-table')
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))->width(30)->addClass('text-center')->orderable(false),
            Column::make('causer_id','users.username')->title(__('Import By'))->width(60),
            Column::make('log_name')->title(__('Log On'))->width(30),
            Column::make('default_field')->title(__('tables.th.description'))->width(120),
            Column::make('description')->title(__('tables.th.action'))->width(120),
            Column::make('ip_address')->title(__('IP Address'))->width(80),
            Column::make('browser')->title(__('Browser'))->width(80),
            Column::make('device')->title(__('Device'))->width(80),
            Column::make('platform')->title(__('Platform'))->width(60),
            Column::make('created_at')->title(__('Log Date'))->width(60)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SystemLog_' . date('YmdHis');
    }
}
