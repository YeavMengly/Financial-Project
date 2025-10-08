<?php

namespace App\DataTables\BudgetLoans;

use App\Models\Loans\BudgetMandateLoan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
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
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('agency', function ($row) {
                return $row->agency_no . ' - ' . $row->agency_name;
            })
            ->editColumn('internal_increase', function ($row) {
                return number_format($row->internal_increase ?? 0);
            })
            ->editColumn('unexpected_increase', function ($row) {
                return number_format($row->unexpected_increase ?? 0);
            })
            ->editColumn('additional_increase', function ($row) {
                return number_format($row->additional_increase ?? 0);
            })
            ->editColumn('decrease', function ($row) {
                return number_format($row->decrease ?? 0);
            })
            ->editColumn('editorial', function ($row) {
                return number_format($row->editorial ?? 0);
            })
            ->editColumn('soft_delete', function ($module) {
                return is_null($module->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($module) {
                return view('loanbudget::mandate.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetMandateLoan $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery()
            ->leftJoin('agencies', 'budget_mandate_loans.agency_id', '=', 'agencies.id')
            ->select([
                'budget_mandate_loans.id',
                'budget_mandate_loans.ministry_id',
                'agencies.no as agency_no',
                'agencies.name as agency_name',
                'budget_mandate_loans.account_sub_id',
                'budget_mandate_loans.no',
                'budget_mandate_loans.internal_increase',
                'budget_mandate_loans.unexpected_increase',
                'budget_mandate_loans.additional_increase',
                'budget_mandate_loans.decrease',
                'budget_mandate_loans.editorial',
                'budget_mandate_loans.created_at'
            ])
            ->where('budget_mandate_loans.ministry_id', $id);

        // 🔍 Apply filters from the form
        if ($request->filled('cboAgency')) {
            $query->where('budget_mandate_loans.agency_id', $request->cboAgency);
        }

        if ($request->filled('subAccountNumber')) {
            $query->where('budget_mandate_loans.account_sub_id', $request->subAccountNumber);
        }

        if ($request->filled('program')) {
            $query->where('budget_mandate_loans.no', $request->program);
        }

        if ($request->filled('description')) {
            $query->where('budget_mandate_loans.txtDescription', 'LIKE', '%' . $request->description . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('budget_mandate_loans.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('budget_mandate_loans.created_at', '<=', $request->end_date);
        }

        return $query->orderBy('budget_mandate_loans.created_at', 'DESC');
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
                ->width(30)
                ->addClass('text-center align-middle')
                ->orderable(false),
                
            Column::make('agency')->title(__('tables.th.number'))->width(30)->addClass('align-middle'),
            Column::make('account_sub_id')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('internal_increase')->title(__('tables.th.internal'))->width(80)->addClass('align-middle'),
            Column::make('unexpected_increase')->title(__('tables.th.unexpected'))->width(80)->addClass('align-middle'),
            Column::make('additional_increase')->title(__('tables.th.additional'))->width(80)->addClass('align-middle'),
            Column::make('decrease')->title(__('tables.th.decrease'))->width(80)->addClass('align-middle'),
            Column::make('editorial')->title(__('tables.th.editorial'))->width(80)->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),

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
        return 'BudgetMandateLoan_' . date('YmdHis');
    }
}
