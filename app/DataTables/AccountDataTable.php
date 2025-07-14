<?php

namespace App\DataTables;

use App\Models\BeginCredit\Account;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AccountDataTable extends DataTable
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
            ->addColumn('action', function ($module) {
                return view('beginningcredit::accounts.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Account $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->leftJoin('chapters', 'accounts.chapterNumber', '=', 'chapters.chapterNumber')
            ->select([
                'accounts.id',
                'accounts.chapterNumber as CNA',
                'accounts.accountNumber as SNA',
                'accounts.txtAccount',
                'accounts.deleted_at',
            ])
            ->orderBy('accounts.created_at', 'DESC');

        // Filter by accountNumber if selected
        if (request()->filled('accountNumber')) {
            $query->where('accounts.accountNumber', request('accountNumber'));
        }

        // Filter by txtAccount (partial match if needed)
        if (request()->filled('txtAccount')) {
            $query->where('accounts.txtAccount', 'like', '%' . request('txtAccount') . '%');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('account-table')
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

            Column::make('CNA')->title(__('tables.th.chapter'))->addClass('align-middle'),
            Column::make('SNA')->title(__('tables.th.account'))->addClass('align-middle'),
            Column::make('txtAccount')->title(__('tables.th.txtAccount'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Account_' . date('YmdHis');
    }
}
