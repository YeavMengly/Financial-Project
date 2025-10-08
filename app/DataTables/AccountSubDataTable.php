<?php

namespace App\DataTables;

use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\SubAccount;
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

class AccountSubDataTable extends DataTable
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
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __("buttons.active") . '</span>' : '<span class="badge bg-danger">' . __("buttons.deleted") . '</span>';
                return $active;
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($module) {
                return view('beginningcredit::accounts.accountSub.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(AccountSub $model, Request $request): QueryBuilder
    {

        $params = $request->params;
        $id = decode_params($params);


        $model = $model->newQuery();
        $model->withTrashed();
        $query = $model->newQuery()
            ->select([
                'account_subs.id',
                'account_subs.ministry_id',
                'account_subs.account_id as CNA',
                'account_subs.no as SNA',
                'account_subs.name',
                'account_subs.deleted_at',
            ])
            ->orderBy('account_subs.created_at', 'ASC');

        $query->where('account_subs.ministry_id', $id);
        if ($request->filled('no')) {
            $query->where('account_subs.id', $request->no);
        }

        // ✅ Apply search filter for `name` (by exact name match)
        if ($request->filled('name')) {
            $query->where('account_subs.name', 'like', '%' . $request->name . '%');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('accountsub-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
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
                
            Column::make('CNA')->title(__('tables.th.account'))->addClass('align-middle'),
            Column::make('SNA')->title(__('tables.th.sub.account'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.name'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AccountSub_' . date('YmdHis');
    }
}
