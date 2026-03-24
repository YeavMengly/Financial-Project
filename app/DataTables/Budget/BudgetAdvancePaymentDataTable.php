<?php

namespace App\DataTables\Budget;

use App\Models\BudgetAdvancePayment;
use App\Models\BudgetPlan\BudgetMandate;
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

class BudgetAdvancePaymentDataTable extends DataTable
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
                return number_format($row->budget ?? 0);
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
                return view('budgetplan::budgetAdvancePayment.action', ['module' => $module]);
            })
            ->editColumn('is_archived', function ($module) {
                $notes = ($module->is_archived == 2) ? '<button class="btn btn-sm btn-outline-success">បានបញ្ចប់</button>' : '<button class="btn btn-sm btn-outline-primary">កំពុងធ្វើ</button>';

                return $notes;
            })
            ->editColumn('attachments', function ($row) {
                if (!$row->attachments) {
                    return '<span class="text-muted">-</span>';
                }
                $files = json_decode($row->attachments, true);
                if (is_array($files)) {
                    $html = '<ul class="list-unstyled m-0">';
                    foreach ($files as $file) {
                        $url = asset('storage/uploads/' . $file);
                        $html .= "<li><a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>$file</a></li>";
                    }
                    $html .= '</ul>';
                    return $html;
                } else {
                    $url = asset('storage/uploads/' . $row->attachments);
                    return "<a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>Preview</a>";
                }
            })
            ->rawColumns(['description', 'attachments', 'agency', 'is_archived', 'soft_delete']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetMandate $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();
        $model->withTrashed();

        if ($request->cboStatus) {
            if ($request->cboStatus == '2') {
                $model->where('budget_mandates.deleted_at', null);
            } elseif ($request->cboStatus == '3') {
                $model->where('budget_mandates.deleted_at', '!=', null);
            } else {
                $model->withTrashed();
            }
        } else {
            $model->where('budget_mandates.deleted_at', null);
        }

        if ($request->cboTodo) {
            if ($request->cboTodo == 2) {
                $model->where('budget_mandates.is_archived', 1);
                $model->where('budget_mandates.expense_type_id', 2);
            } elseif ($request->cboTodo == 3) {
                $model->where('budget_mandates.is_archived', 2);
                $model->where('budget_mandates.expense_type_id', 2);
            }
        } else {
            $model->where('budget_mandates.is_archived', 1);
            $model->where('budget_mandates.expense_type_id', 2);
        }

        // ===== SEARCH FILTER =====
        if ($request->filled('subAccountNumber')) {
            $model->where('account_subs.no', $request->subAccountNumber);
        }

        if ($request->filled('agency')) {
            $model->where('agencies.no', 'like', '%' . $request->agency . '%');
        }

        if ($request->filled('legal_number')) {
            $model->where('budget_mandates.legal_number', 'like', '%' . $request->legal_number . '%');
        }

        if ($request->filled('keyword')) {
            $model->where(function ($q) use ($request) {
                $q->where('budget_mandates.no', 'like', '%' . $request->keyword . '%')
                    ->orWhere('budget_mandates.description', 'like', '%' . $request->keyword . '%')
                    ->orWhere('budget_mandates.legal_name', 'like', '%' . $request->keyword . '%');
            });
        }

        $model->from('budget_mandates')
            ->leftJoin('account_subs', function ($join) use ($id) {
                $join->on('budget_mandates.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $id);
            });

        $model->leftJoin('agencies', 'budget_mandates.agency_id', '=', 'agencies.id');
        $model->leftJoin('expense_types', 'budget_mandates.expense_type_id', '=', 'expense_types.id');

        // ===== FIXED CONDITION =====
        $model->where('budget_mandates.ministry_id', $id);
        $model->where('budget_mandates.expense_type_id', 2);

        // ===== SELECT =====
        $model->select([
            'budget_mandates.id',
            'budget_mandates.ministry_id',
            'agencies.no AS agency_no',
            'agencies.name AS agency_name',
            'account_subs.no as account_sub_no',
            'budget_mandates.no',
            'budget_mandates.budget',
            'budget_mandates.expense_type_id',
            'budget_mandates.legal_id',
            'budget_mandates.payment_voucher_number AS pvn',
            'budget_mandates.legal_number',
            'budget_mandates.legal_name',
            'budget_mandates.is_archived',
            'expense_types.name_kh',
            'budget_mandates.description',
            'budget_mandates.attachments',
            'budget_mandates.transaction_date',
            'budget_mandates.request_date',
            'budget_mandates.legal_date',
            'budget_mandates.created_at',
            'budget_mandates.deleted_at'
        ]);

        $model->orderByDesc('budget_mandates.created_at');

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
                d.agency     = $("#agency").val();
                d.no    = $("#no").val();
                d.subAccountNumber = $("#subAccountNumber").val();
                d.cboTodo = $("#cboTodo").val();
                d.cboStatus = $("#cboStatus").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#budgetadvancepayment-table").DataTable().ajax.reload();
                });
            }')
            ->setTableId('budgetadvancepayment-table')
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

            Column::make('legal_id')->title(__('tables.th.legal.id'))->width(30)->addClass('align-middle'),
            Column::make('pvn')->title(__('tables.th.pvn'))->width(90)->addClass('align-middle'),
            Column::make('legal_number')->title(__('tables.th.legal.number'))->width(90)->addClass('align-middle'),
            Column::make('legal_name')->title(__('tables.th.legal.name'))->width(90)->addClass('align-middle'),
            Column::make('agency')->title(__('tables.th.agency'))->width(90)->addClass('align-middle'),
            Column::make('account_sub_no')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(60)->addClass('align-middle'),
            // Column::make('name_kh')->title(__('tables.th.type'))->width(60)->addClass('align-middle'),
            Column::make('budget')->title(__('tables.th.budget'))->width(80)->addClass('align-middle'),
            Column::make('transaction_date')->title(__('tables.th.date.transaction'))->width(80)->addClass('align-middle'),
            Column::make('request_date')->title(__('tables.th.date.request'))->width(80)->addClass('align-middle'),
            Column::make('legal_date')->title(__('tables.th.date.legal'))->width(80)->addClass('align-middle'),

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
        return 'BudgetAdvancePayment_' . date('YmdHis');
    }
}
