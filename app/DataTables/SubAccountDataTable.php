<?php

namespace App\DataTables;

use App\Models\BeginCredit\SubAccount;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubAccountDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // return (new EloquentDataTable($query))
        //     ->addColumn('action', 'subaccount.action')
        //     ->setRowId('id');
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('beginningcredit::accounts.subAccount.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SubAccount $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'accountNumber as CNA',
                'subAccountNumber as SNA',
                'txtSubAccount',
                'deleted_at',
            ])
            ->orderBy('created_at', 'DESC');

        if (request()->filled('subAccountNumber')) {
            $query->where('subAccountNumber', request('subAccountNumber'));
        }

        if (request()->filled('txtSubAccount')) {
            $query->where('txtSubAccount', 'like', '%' . request('txtSubAccount') . '%');
        }

        return $query;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subaccount-table')
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
            Column::make('txtSubAccount')->title(__('tables.th.txtSubAccount'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SubAccount_' . date('YmdHis');
    }
}
