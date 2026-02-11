<?php

namespace App\DataTables;

use App\Models\BeginCredit\BeginMandate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BeginMandateDataTable extends DataTable
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
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['txtDescription'])

            ->addColumn('action', function ($module) {
                return view('beginningcredit::beginMandate.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginMandate $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery()
            ->leftJoin('account_subs', 'begin_mandates.account_sub_id', '=', 'account_subs.id')
            ->leftJoin('agencies', 'begin_mandates.agency_id', '=', 'agencies.id')
            ->select([
                'begin_mandates.id',
                'begin_mandates.agency_id',
                'begin_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.no as program_no',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.current_loan',
                'begin_mandates.ministry_id',
                'agencies.name as agency_name',
                'account_subs.no as account_sub_no',
            ])
            ->where('begin_mandates.ministry_id', $id)
            ->when(
                $request->filled('agency'),
                fn($q) =>
                $q->where('begin_mandates.agency_id', $request->agency)
            )
            ->when(
                $request->filled('account'),
                fn($q) =>
                $q->where('begin_mandates.account_id', $request->account)
            )
            ->when(
                $request->filled('accountSub'),
                fn($q) =>
                $q->where('begin_mandates.account_sub_id', $request->accountSub)
            )
            ->when(
                $request->filled('no'),
                fn($q) =>
                $q->where('begin_mandates.no', 'like', "%{$request->no}%")
            )
            ->when(
                $request->filled('txtDescription'),
                fn($q) =>
                $q->where('begin_mandates.txtDescription', 'like', "%{$request->txtDescription}%")
            );

        return $query->orderBy('begin_mandates.created_at', 'DESC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('beginmandate-table')
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
        return 'BeginMandate_' . date('YmdHis');
    }
}
