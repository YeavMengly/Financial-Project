<?php

namespace App\DataTables\Report;

use App\Models\StatesAssetsVehicle;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StatesAssetsVehiclesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // ->addColumn('action', 'statesassetsvehicles.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StatesAssetsVehicle $model): QueryBuilder
    {
        // StatesAssetsVehicle
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('statesassetsvehicles-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
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
            Column::make('txtDescription')->title(__('tables.th.description')),
            // Column::make('unit')->title(__('tables.th.unit')),
            // Column::make('motor')->title(__('tables.th.motor')),
            // Column::make('car')->title('ឡាន (ក្រោម១២កៅអី)'),
            // Column::make('car')->title(__('ឡាន (១២កៅអីឡើង)')),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'StatesAssetsVehicles_' . date('YmdHis');
    }
}
