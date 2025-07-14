<?php

namespace App\DataTables\BudgetLoans;

use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BudgetMandateLoanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // return (new EloquentDataTable($query))
        //     ->addColumn('action', 'budgetmandateloan.action')
        //     ->setRowId('id');
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('loanbudget::mandate.action', ['module' => $module]);
            })
            ->editColumn('program', function ($row) {
                return $row->beginCredit->program ?? $row->program;
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetMandateLoan $model): QueryBuilder
    {
        return $model->newQuery()->with('budgetMandate');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('budgetmandateloan-table')
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

            Column::make('subAccountNumber')->title(__('tables.th.sub.account'))->addClass('align-middle'),
            Column::make('program')->title(__('tables.th.program'))->addClass('align-middle'),
            Column::make('internal_increase')->title(__('tables.th.internal'))->addClass('align-middle'),
            Column::make('unexpected_increase')->title(__('tables.th.unexpected'))->addClass('align-middle'),
            Column::make('additional_increase')->title(__('tables.th.additional'))->addClass('align-middle'),
            Column::make('decrease')->title(__('tables.th.decrease'))->addClass('align-middle'),
            Column::make('editorial')->title(__('tables.th.editorial'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BudgetMandateLoan_' . date('YmdHis');
    }
}
