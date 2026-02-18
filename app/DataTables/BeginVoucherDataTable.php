<?php

namespace App\DataTables;

use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\SubAccount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class BeginVoucherDataTable extends DataTable
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
            ->editColumn('fin_law', function ($row) {
                return number_format($row->fin_law ?? 0);
            })
            ->editColumn('current_loan', function ($row) {
                return number_format($row->current_loan ?? 0);
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
            ->rawColumns(['txtDescription', 'soft_delete'])
            ->addColumn('action', function ($module) {
                return view('beginningcredit::beginVoucher.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginVoucher $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();

        $model->leftJoin('account_subs', 'begin_vouchers.account_sub_id', '=', 'account_subs.id');
        $model->leftJoin('agencies', 'begin_vouchers.agency_id', '=', 'agencies.id');
        $model->leftJoin('clusters', 'begin_vouchers.cluster_id', '=', 'clusters.id');

        // ===== FILTERS =====

        if ($request->agency) {
            $model->where('begin_vouchers.agency_id', $request->agency);
        }

        if ($request->chapter) {
            $model->where('begin_vouchers.chapter_id', $request->chapter);
        }

        if ($request->account) {
            $model->where('begin_vouchers.account_id', $request->account);
        }

        if ($request->accountSub) {
            $model->where('begin_vouchers.account_sub_id', $request->accountSub);
        }

        if ($request->txtDescription) {
            $model->where('begin_vouchers.txtDescription', 'like', '%' . $request->txtDescription . '%');
        }

        // ===== FIXED CONDITION =====
        $model->where('begin_vouchers.ministry_id', $id);

        // ===== SELECT =====
        $model->select([
            'begin_vouchers.id',
            'begin_vouchers.agency_id',
            'begin_vouchers.account_sub_id',
            'begin_vouchers.account_id',
            'begin_vouchers.no',
            'begin_vouchers.txtDescription',
            'begin_vouchers.fin_law',
            'begin_vouchers.current_loan',
            'begin_vouchers.ministry_id',
            'agencies.name as agency_name',
            'account_subs.no as account_sub_no',
            'clusters.decription',
            'begin_vouchers.created_at',
        ]);

        $model->orderBy('begin_vouchers.created_at', 'DESC');

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('beginvoucher-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.agency     = $("#agency").val();
                d.chapter    = $("#chapter").val();
                d.account    = $("#account").val();
                d.accountSub = $("#accountSub").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#beginvoucher-table").DataTable().ajax.reload();
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

            Column::make('agency_name')->title(__('tables.th.agency'))->width(30)->addClass('align-middle'),
            Column::make('account_sub_id')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('fin_law')->title(__('tables.th.financeLaw'))->width(120)->addClass('align-middle'),
            Column::make('current_loan')->title(__('tables.th.currentCredit'))->width(120)->addClass('align-middle'),
            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BeginVoucher_' . date('YmdHis');
    }
}
