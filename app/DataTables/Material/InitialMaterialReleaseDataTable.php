<?php


namespace App\DataTables\Material;

use App\Models\BeginCredit\Ministry;
use App\Models\InitialMaterialRelease;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InitialMaterialReleaseDataTable extends DataTable
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
            ->editColumn('soft_delete', function ($module) {
                return is_null($module->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->addColumn('action', function ($model) {
                return view('material::materialRelease.initialMaterialRelease.action', ['module' => $model]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ministry $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('initialmaterialrelease-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
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
            Column::computed(
                'DT_RowIndex',
                __('tables.th.no')
            )->width(30)->addClass('text-center align-middle')->orderable(false),

            Column::make('year')->title(__('tables.th.year'))->width(80)->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.description'))->addClass('align-middle'),

            Column::computed(
                'action',
                __('tables.th.action')
            )->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InitialMaterialRelease_' . date('YmdHis');
    }
}
