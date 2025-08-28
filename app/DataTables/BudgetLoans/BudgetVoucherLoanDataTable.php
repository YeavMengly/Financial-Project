<?php

namespace App\DataTables\BudgetLoans;

use App\Models\Loans\BudgetVoucherLoan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BudgetVoucherLoanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('soft_delete', function ($model) {
                return is_null($model->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->editColumn('program', function ($model) {
                return optional($model->beginCredit)->program ?? $model->program;
            })
            ->addColumn('action', function ($model) {
                return view('loanbudget::voucher.action', ['module' => $model]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of the dataTable.
     */
    public function query(BudgetVoucherLoan $model): QueryBuilder
    {

        return $model->newQuery()->with(['budgetVoucher', 'beginCredit']);
    }

    /**
     * Use the HTML Builder to define DataTable.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('budgetvoucherloan_-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'asc')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ]);
    }

    /**
     * Define DataTable columns.
     */
    protected function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)
                ->addClass('text-center align-middle')
                ->orderable(false),
            Column::make('agencyNumber')->title(__('tables.th.number'))->width(30)->addClass('align-middle'),
            Column::make('subAccountNumber')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('program')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('internal_increase')->title(__('tables.th.internal'))->width(80)->addClass('align-middle'),
            Column::make('unexpected_increase')->title(__('tables.th.unexpected'))->width(80)->addClass('align-middle'),
            Column::make('additional_increase')->title(__('tables.th.additional'))->width(80)->addClass('align-middle'),
            Column::make('decrease')->title(__('tables.th.decrease'))->width(80)->addClass('align-middle'),
            Column::make('editorial')->title(__('tables.th.editorial'))->width(80)->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BudgetVoucherLoan_' . now()->format('YmdHis');
    }
}
