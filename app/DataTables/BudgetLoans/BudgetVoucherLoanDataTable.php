<?php

namespace App\DataTables\BudgetLoans;

use App\Models\Loans\BudgetVoucherLoan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
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
            ->addColumn('agency', function ($row) {
                return '<strong>' . $row->agency_no  . '</strong><br/><hr/>' . $row->agency_name;
            })
            ->addColumn('internal_increase', function ($row) {
                return number_format($row->internal_increase ?? 0);
            })
            ->addColumn('unexpected_increase', function ($row) {
                return number_format($row->unexpected_increase ?? 0);
            })
            ->addColumn('additional_increase', function ($row) {
                return number_format($row->additional_increase ?? 0);
            })
            ->addColumn('decrease', function ($row) {
                return number_format($row->decrease ?? 0);
            })
            ->addColumn('editorial', function ($row) {
                return number_format($row->editorial ?? 0);
            })
            ->addColumn('soft_delete', function ($module) {
                return is_null($module->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($module) {
                return view('loanbudget::voucher.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'agency']);
    }

    /**
     * Get the query source of the dataTable.
     */
    public function query(BudgetVoucherLoan $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();

        $model->leftJoin('agencies', 'budget_voucher_loans.agency_id', '=', 'agencies.id');

        // ===== FILTERS =====

        if ($request->cboAgency) {
            $model->where('budget_voucher_loans.agency_id', $request->cboAgency);
        }

        if ($request->subAccountNumber) {
            $model->where('budget_voucher_loans.account_sub_id', $request->subAccountNumber);
        }

        if ($request->start_date) {
            $model->whereDate('budget_voucher_loans.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $model->whereDate('budget_voucher_loans.created_at', '<=', $request->end_date);
        }
        // ===== FIXED CONDITION =====
        $model->where('budget_voucher_loans.ministry_id', $id);

        // ===== SELECT =====
        $model->select([
            'budget_voucher_loans.id',
            'budget_voucher_loans.ministry_id',
            'agencies.no as agency_no',
            'agencies.name as agency_name',
            'budget_voucher_loans.account_sub_id',
            'budget_voucher_loans.no',
            'budget_voucher_loans.internal_increase',
            'budget_voucher_loans.unexpected_increase',
            'budget_voucher_loans.additional_increase',
            'budget_voucher_loans.decrease',
            'budget_voucher_loans.editorial',
            'budget_voucher_loans.created_at'
        ]);

        $model->orderBy('budget_voucher_loans.created_at', 'DESC');

        return $model;
    }

    /**
     * Use the HTML Builder to define DataTable.
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
                    d.cboAgency = $("#cboAgency").val();
                    d.subAccountNumber = $("#subAccountNumber").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#budgetvoucherloan-table").DataTable().ajax.reload();
                });
                var tr = document.createElement("tr");
                var columns = this.api().init().columns;
                this.api().columns().every(function (index) {
                    var column = this;
                    var td = document.createElement("td");
                    if (columns[index] && columns[index].searchable) {
                        var input = document.createElement("input");
                        input.className = "form-control form-control-sm";
                        $(input).on("change", function () {
                            column.search($(this).val(), false, false, true).draw();
                        }).appendTo(td);
                    }
                    $(td).appendTo(tr);
                });
                $(".table-responsive table thead").append(tr);
            }')
            ->setTableId('budgetvoucherloan-table')
            ->columns($this->getColumns());
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
        return 'BudgetVoucherLoan_' . now()->format('YmdHis');
    }
}
