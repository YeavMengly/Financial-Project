<?php

namespace App\DataTables;

use App\Models\CategorySub;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategorySubDataTable extends DataTable
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
                return view('setting::category.sub.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CategorySub $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->where("cate_id", $this->cateId);
        $model->withTrashed();
        $model->select(['category_subs.id', 'category_subs.cate_id', 'category_subs.name', 'category_subs.order', 'category_subs.created_at', 'category_subs.deleted_at']);
        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('category-sub-table')
            ->parameters([
                'language' => [
                    "url" => asset("assets/lang/language.json")
                ]
            ])
            ->columns($this->getColumns())
            ->orderBy(2, 'ASC');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))->width(30)->addClass('text-center'),
            Column::make('name')->title(__('tables.th.name')),
            Column::make('order')->title(__('tables.th.order'))->width(100),
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
        return 'CategorySub_' . date('YmdHis');
    }
}
