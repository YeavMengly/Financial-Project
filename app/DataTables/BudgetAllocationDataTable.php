<?php

namespace App\DataTables;

use App\Models\BudgetAllocation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BudgetAllocationDataTable extends DataTable
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
                return '<strong>' . $row->agency_no  . '</strong><br/><hr/>' . $row->agency_name;
            })
            ->addColumn('no', function ($row) {
                return optional($row->beginVoucher)->no;
            })
            ->addColumn('txtDescription', function ($row) {
                return optional($row->beginVoucher)->txtDescription;
            })
            ->editColumn('amount', function ($row) {
                return number_format($row->amount ?? 0);
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['txtDescription', 'soft_delete', 'agency'])
            ->addColumn('action', function ($module) {
                return view('beginningcredit::beginVoucher.budgetAllocation.Action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */

    public function query(BudgetAllocation $model, Request $request): QueryBuilder
    {
        $id = decode_params(request()->route('params'));
        $budgetAllocationId = decode_params(request()->route('budgetAllocationId'));

        $query = $model->newQuery()
            ->join(
                'begin_vouchers',
                'begin_vouchers.id',
                '=',
                'budget_allocations.budget_begin_voucher_id'
            )
            ->leftJoin(
                'agencies',
                'agencies.id',
                '=',
                'begin_vouchers.agency_id'
            )
            ->leftJoin(
                'expense_types',
                'expense_types.id',
                '=',
                'budget_allocations.budget_expense_type_id'
            )
            ->where('begin_vouchers.ministry_id', $id)
            ->where('budget_allocations.budget_begin_voucher_id', $budgetAllocationId)
            ->select([
                'budget_allocations.*',
                'begin_vouchers.no',
                'begin_vouchers.account_sub_id',
                'begin_vouchers.fin_law',
                'begin_vouchers.current_loan',
                'begin_vouchers.txtDescription',
                'agencies.name as agency_name',
                'agencies.no as agency_no',
                'expense_types.name_kh as expense_type_name',
            ]);

        // Apply filter using matching request parameter 'expenseType'
        if ($request->filled('expenseType')) {
            $expenseType = (int) $request->expenseType;

            // Skip filter if value is 1 ("All")
            if ($expenseType > 1) {
                $query->where('budget_allocations.budget_expense_type_id', $expenseType);
            }
        }
        $query->orderBy('begin_vouchers.no', 'ASC');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('budgetallocation-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.expenseType = $("#cboExpenseType").val();
            }',
            ])
            ->initComplete('function () {
            $("#filter").submit(function(event) {
                event.preventDefault();
                $("#budgetallocation-table").DataTable().ajax.reload();
            });
        }')
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
            Column::make('account_sub_id')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('amount')->title(__('tables.th.budget'))->width(120)->addClass('align-middle'),
            Column::make('rounds')->title(__('ជុំទី'))->width(120)->addClass('align-middle'),

            Column::make('expense_type_name')
                ->title(__('tables.th.expense.type')),
            Column::make('agency')->title(__('tables.th.agency'))->width(30)->addClass('align-middle'),
            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
            Column::make('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BudgetAllocation_' . date('YmdHis');
    }
}
