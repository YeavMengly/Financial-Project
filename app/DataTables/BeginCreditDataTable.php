<?php

namespace App\DataTables;

use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\SubAccount;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BeginCreditDataTable extends DataTable
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
                return view('beginningcredit::begincredit.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */


    public function query(BeginCredit $model): QueryBuilder
    {
        $year = request()->get('year');
        

        // /**
        //  * ================       Step 1:  Find year from InitialBudget using the ID        ================
        //  */

        // if ($initialBudgetId) {
        //     $initialBudget = InitialBudget::where('id', $initialBudgetId);
        //     if ($initialBudget) {
        //         $year = $initialBudget->id;
        //     }
        // }

        /**
         * ================       Step 2:  Build query        ================
         */

        $query = $model->newQuery()
            ->leftJoin('sub_accounts', 'begin_credits.subAccountNumber', '=', 'sub_accounts.subAccountNumber')
            // ->leftJoin('initial_budgets', 'begin_credits.year', '=', 'initial_budgets.year')
            ->select([
                'begin_credits.id',
                'begin_credits.agencyNumber',
                'begin_credits.subAccountNumber as CNA',
                'begin_credits.program',
                'begin_credits.txtDescription',
                'begin_credits.fin_law',
                'begin_credits.current_loan',
                'begin_credits.deleted_at',
                'begin_credits.year',
            ]);

        /**
         * ================       Step 3:  Build query        ================
         */

        if ($year) {
            $query->where('begin_credits.year', $year);
        }

        if (request()->filled('agencyNumber')) {
            $query->where('begin_credits.agencyNumber', request('agencyNumber'));
        }

        if (request()->filled('subAccountNumber')) {
            $query->where('begin_credits.subAccountNumber', request('subAccountNumber'));
        }

        if (request()->filled('program')) {
            $query->where('begin_credits.program', request('program'));
        }

        if (request()->filled('txtDescription')) {
            $query->where('begin_credits.txtDescription', 'like', '%' . request('txtDescription') . '%');
        }

        return $query->orderBy('begin_credits.created_at', 'DESC');
        // return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('begincredit-table')
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
            Column::make('CNA')->title(__('tables.th.sub.account'))->addClass('align-middle'),
            Column::make('program')->title(__('tables.th.program'))->addClass('align-middle'),
            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('fin_law')->title(__('tables.th.financeLaw'))->addClass('align-middle'),
            Column::make('current_loan')->title(__('tables.th.currentCredit'))->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BeginCredit_' . date('YmdHis');
    }
}
