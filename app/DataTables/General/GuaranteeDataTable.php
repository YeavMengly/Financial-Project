<?php

namespace App\DataTables\General;

use App\Models\BeginCredit\BeginCredit;
use App\Models\Guarantee;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class GuaranteeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'guarantee.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginCredit $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('guarantee-table')
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
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('chapter')->title(__('tables.th.chapter'))->addClass('align-middle'),

            Column::make('account')->title(__('tables.th.account'))->addClass('align-middle'),


            Column::make('subAccountNumber')->title(__('tables.th.sub.account'))->addClass('align-middle'),

            Column::make('program')->title(__('tables.th.program'))->addClass('align-middle'),

            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),

            Column::make('fin_law')->title(__('tables.th.financeLaw'))->addClass('align-middle'),

            Column::make('current_loan')->title(__('tables.th.currentCredit'))->addClass('align-middle'),
            Column::make('internal_increase')
                ->title('កើនផ្ទៃក្នុង')
                ->addClass('align-middle text-center'),

            Column::make('unexpected_increase')
                ->title('មិនបានគ្រោងទុក')
                ->addClass('align-middle text-center'),

            Column::make('additional_increase')
                ->title('បំពេញបន្ថែម')
                ->addClass('align-middle text-center'),

            Column::make('total_increase')
                ->title('សរុប')
                ->addClass('align-middle text-center'),

            Column::make('decrease')
                ->title('ថយ')
                ->addClass('align-middle text-center'),

            Column::make('editorial')
                ->title('វិចារណកម្ម')
                ->addClass('align-middle text-center'),

            Column::make('new_credit_status')->title(__('tables.th.new_credit_status'))->addClass('align-middle'),
            Column::make('early_balance')->title(__('tables.th.early_balance'))->addClass('align-middle'),
            Column::make('apply')->title(__('tables.th.apply'))->addClass('align-middle'),
            Column::make('deadline_balance')->title(__('tables.th.deadline_balance'))->addClass('align-middle'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Guarantee_' . date('YmdHis');
    }
}
