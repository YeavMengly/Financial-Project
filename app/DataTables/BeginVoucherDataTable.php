<?php

namespace App\DataTables;

use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\SubAccount;
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
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['txtDescription'])
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

        $query = $model->newQuery()
            ->leftJoin('account_subs', 'begin_vouchers.account_sub_id', '=', 'account_subs.id')
            ->leftJoin('agencies', 'begin_vouchers.agency_id', '=', 'agencies.id')
            ->select([
                'begin_vouchers.id',
                'begin_vouchers.agency_id',
                'begin_vouchers.account_sub_id',
                'begin_vouchers.no as program_no',
                'begin_vouchers.txtDescription',
                'begin_vouchers.fin_law',
                'begin_vouchers.current_loan',
                'begin_vouchers.ministry_id',
                'agencies.name as agency_name',
                'account_subs.no as account_sub_no',
            ])
            ->where('begin_vouchers.ministry_id', $id)
            ->when(
                $request->filled('agency'),
                fn($q) =>
                $q->where('begin_vouchers.agency_id', $request->agency)
            )
            ->when(
                $request->filled('accountSub'),
                fn($q) =>
                $q->where('begin_vouchers.account_sub_id', $request->accountSub)
            )
            ->when(
                $request->filled('no'),
                fn($q) =>
                $q->where('begin_vouchers.no', 'like', "%{$request->no}%")
            )
            ->when(
                $request->filled('txtDescription'),
                fn($q) =>
                $q->where('begin_vouchers.txtDescription', 'like', "%{$request->txtDescription}%")
            );

        return $query->orderBy('begin_vouchers.created_at', 'DESC');
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
            Column::make('program_no')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('fin_law')->title(__('tables.th.financeLaw'))->width(120)->addClass('align-middle'),
            Column::make('current_loan')->title(__('tables.th.currentCredit'))->width(120)->addClass('align-middle'),

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
