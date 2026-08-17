<?php

namespace App\DataTables\Budget;

use App\Models\BudgetPlan\BudgetVoucher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BudgetVoucherDataTable extends DataTable
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
            ->editColumn('budget', function ($row) {
                return number_format($row->budget ?? 0) . ' ៛';
            })
            ->editColumn('transaction_date', function ($row) {
                $active =  Carbon::parse($row->transaction_date)->format('Y-m-d');

                return $active;
            })
            ->editColumn('request_date', function ($row) {
                $active =  Carbon::parse($row->request_date)->format('Y-m-d');

                return $active;
            })
            ->editColumn('legal_date', function ($row) {
                $active =  Carbon::parse($row->legal_date)->format('Y-m-d');

                return $active;
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                $active = $active . '<br />' . Carbon::parse($soft_delete->created_at)->format('Y-m-d  h:i:s A');

                return $active;
            })
            ->editColumn('name_kh', function ($row) {
                return $row->name_kh ?? '-';
            })
            ->addColumn('action', function ($module) {
                return view('budgetplan::budgetVoucher.action', ['module' => $module]);
            })
            ->editColumn('is_archived', function ($module) {
                $notes = ($module->is_archived == 2) ? '<button class="btn btn-sm btn-outline-success">បានបញ្ចប់</button>' : '<button class="btn btn-sm btn-outline-primary">កំពុងធ្វើ</button>';

                return $notes;
            })
            ->editColumn('attachments', function ($row) {
                if (!$row->attachments) {
                    return '<span class="text-muted">-</span>';
                }
                $url = asset('storage/' . $row->attachments);
                $filename = basename($row->attachments);

                return "<a href='{$url}' target='_blank' class='text-primary'>
                <i class='fas fa-file-alt me-1'></i>Preview
            </a>";
            })
            ->rawColumns(['soft_delete', 'description', 'attachments', 'agency', 'is_archived']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetVoucher $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();
        $model->withTrashed();

        if ($request->has('cboStatus')) {
            if ($request->cboStatus == '2') {
                // Only non-deleted
                $model->whereNull('budget_vouchers.deleted_at');
            } elseif ($request->cboStatus == '3') {
                // Only deleted
                $model->onlyTrashed();
            } else {
                // All records
                $model->withTrashed();
            }
        } else {
            // Default: non-deleted
            $model->whereNull('budget_vouchers.deleted_at');
        }

        if ($request->cboTodo) {
            if ($request->cboTodo == 2) {
                $model->where('budget_vouchers.is_archived', 1);
            } elseif ($request->cboTodo == 3) {
                $model->where('budget_vouchers.is_archived', 2);
            }
        } else {
            $model->where('budget_vouchers.is_archived', 2);
        }

        if ($request->filled('cboProgram')) {
            $model->where(
                'budget_vouchers.program_id',
                $request->cboProgram
            );
        }

        if ($request->cboAccountSub) {
            $model->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
        }

        if ($request->cboAgency) {
            $model->where('budget_vouchers.agency_id', $request->cboAgency);
        }

        if ($request->filled('cboExpenseType')) {
            $model->where(
                'budget_vouchers.expense_type_id',
                $request->cboExpenseType
            );
        }

        if ($request->filled('CboPaymentVoucherNumber')) {
            $model->where(
                'budget_vouchers.payment_voucher_number',
                $request->CboPaymentVoucherNumber
            );
        }

        // ==========================================
        // Date Filtering Logic
        // ==========================================
        // Default column for start_date
        $startDateColumn = 'budget_vouchers.request_date';

        // If payment_voucher_number == 0001, switch start_date column to legal_date
        if ($request->filled('CboPaymentVoucherNumber') && $request->CboPaymentVoucherNumber === '0001') {
            $startDateColumn = 'budget_vouchers.legal_date';
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $model->whereDate($startDateColumn, '>=', $request->start_date)
                ->whereDate('budget_vouchers.transaction_date', '<=', $request->end_date);
        } else {
            if ($request->filled('start_date')) {
                $model->whereDate($startDateColumn, '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $model->whereDate('budget_vouchers.transaction_date', '<=', $request->end_date);
            }
        }

        $model->leftJoin('account_subs', function ($join) use ($id) {
            $join->on('budget_vouchers.account_sub_id', '=', 'account_subs.no')
                ->where('account_subs.ministry_id', '=', $id);
        })->from('budget_vouchers');

        $model->leftJoin('agencies', 'budget_vouchers.agency_id', '=', 'agencies.id');
        $model->leftJoin('expense_types', 'budget_vouchers.expense_type_id', '=', 'expense_types.id');


        // ===== FIXED CONDITION =====
        $model->where('budget_vouchers.ministry_id', $id);

        // ===== SELECT =====
        $model->select([
            'budget_vouchers.id',
            'budget_vouchers.ministry_id',
            'agencies.no AS agency_no',
            'agencies.name AS agency_name',
            'budget_vouchers.program_id',
            'budget_vouchers.program_sub_id',
            'budget_vouchers.cluster_id',
            'account_subs.no as account_sub_no',
            'budget_vouchers.no',
            'budget_vouchers.budget',
            'budget_vouchers.expense_type_id',
            'budget_vouchers.legal_id',
            'budget_vouchers.payment_voucher_number AS pvn',
            'budget_vouchers.legal_number',
            'budget_vouchers.legal_name',
            'budget_vouchers.is_archived',
            'expense_types.name_kh',
            'budget_vouchers.description',
            'budget_vouchers.attachments',
            'budget_vouchers.transaction_date',
            'budget_vouchers.request_date',
            'budget_vouchers.legal_date',
            'budget_vouchers.created_at',
            'budget_vouchers.deleted_at',
        ]);

        // ==========================================
        // Sorting Logic
        // ==========================================
        if (!$request->has('order')) {
            $model->orderBy('budget_vouchers.payment_voucher_number', 'asc')
                ->orderBy('account_subs.no', 'asc')
                ->orderBy('budget_vouchers.no', 'asc');
        }

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
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                    d.cboTodo = $("#cboTodo").val();
                    d.cboStatus = $("#cboStatus").val();
                    d.cboProgram = $("#cboProgram").val();
                    d.cboAccountSub = $("#cboAccountSub").val();
                    d.cboAgency = $("#cboAgency").val();
                    d.cboExpenseType = $("#cboExpenseType").val();
                    d.CboPaymentVoucherNumber = $("#CboPaymentVoucherNumber").val();
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#budgetvoucher-table").DataTable().ajax.reload();
                });
            }')
            ->setTableId('budgetvoucher-table')
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::computed('is_archived')->title(__('Task'))->width(100)->addClass('text-center align-middle'),
            Column::make('pvn')->title(__('tables.th.pvn'))->width(90)->addClass('align-middle'),
            Column::make('account_sub_no')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(60)->addClass('align-middle'),
            Column::make('budget')->title(__('tables.th.budget'))->width(80)->addClass('align-middle'),
            Column::make('name_kh')->title(__('tables.th.expense.type'))->width(80)->addClass('align-middle'),
            Column::make('transaction_date')->title(__('tables.th.date.transaction'))->width(80)->addClass('align-middle'),
            Column::make('request_date')->title(__('tables.th.date.request'))->width(80)->addClass('align-middle'),
            Column::make('legal_date')->title(__('tables.th.date.legal'))->width(80)->addClass('align-middle'),
            Column::make('agency')->title(__('tables.th.agency'))->width(90)->addClass('align-middle'),
            Column::make('legal_id')->title(__('tables.th.legal.id'))->width(30)->addClass('align-middle'),
            Column::make('legal_number')->title(__('tables.th.legal.number'))->width(90)->addClass('align-middle'),
            Column::make('legal_name')->title(__('tables.th.legal.name'))->width(90)->addClass('align-middle'),
            Column::make('description')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('attachments')->title(__('tables.th.document.title'))->width(200)->addClass('align-middle'),
            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BudgetVoucher_' . date('YmdHis');
    }
}
