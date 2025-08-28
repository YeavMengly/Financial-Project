<?php

namespace App\DataTables\General;

use App\Models\BeginCredit\BeginCredit;
use App\Models\Guarantee;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class GuaranteeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'guarantee.action')
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['txtDescription'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(BeginCredit $model): QueryBuilder
    // {
    //     // return $model->newQuery();
    //     $query = $model->newQuery()
    //         ->Join('accounts', 'sub_accounts.accountNumber', '=', 'accounts.accountNumber')
    //         ->leftJoin('sub_accounts', 'begin_credits.subAccountNumber', '=', 'sub_accounts.subAccountNumber')
    //         ->leftJoin('agencies', 'begin_credits.agencyNumber', '=', 'agencies.agencyNumber')

    //         ->join('sub_accounts as sak', 'sak.id', '=', 'begin_credits.subAccountNumber')
    //         ->join('accounts as ak', 'ak.id', '=', 'sak.accountNumber')
    //         ->join('chapters', 'chapters.chapterNumber', '=', 'ak.chapterNumber')
    //         ->leftJoin('budget_vouchers as cd', 'cd.program', '=', 'begin_credits.id')
    //         // ->leftJoin('loans', 'budget_voucher_loans.program', '=', 'begin_credits.id')
    //         ->select([
    //             // 'begin_credits.id',
    //             // 'begin_credits.agencyNumber',
    //             // 'chapters.chapterNumber',
    //             // 'accounts.accountNumber',
    //             'begin_credits.subAccountNumber as CNA',
    //             'begin_credits.program',
    //             'begin_credits.txtDescription',
    //             'begin_credits.fin_law',
    //             'begin_credits.current_loan',
    //             'begin_credits.deleted_at',
    //             'begin_credits.year',
    //             // 'agencies.agencyTitle'
    //         ]);

    //     return $query->orderBy('begin_credits.created_at', 'DESC');

    //     //   return self::select([
    //     //     'keys.name',
    //     //     'keys.code',
    //     //     'ak.account_key as account_key',
    //     //     'ak.name_account_key as name_account_key',
    //     //     'sak.sub_account_key as sub_account_key',
    //     //     'sak.name_sub_account_key as name_sub_account_key',
    //     //     'reports.report_key as report_key',
    //     //     'reports.fin_law as fin_law',
    //     //     'reports.current_loan as current_loan',

    //     //     'reports.new_credit_status as new_credit_status',
    //     //     'reports.early_balance as early_balance',
    //     //     'reports.apply as apply',
    //     //     'reports.deadline_balance as deadline_balance',
    //     //     'reports.credit as credit',
    //     //     'reports.law_average as law_average',
    //     //     'reports.law_correction as law_correction',

    //     //     'loans.internal_increase',
    //     //     'loans.unexpected_increase',
    //     //     'loans.additional_increase',
    //     //     'loans.total_increase',
    //     //     'loans.decrease',
    //     //     'loans.editorial',
    //     //     'cd.value_certificate',
    //     // 'cd.amount',

    //     // 'mp.pay_mission',
    //     // 'mp.name_mission_type'

    //     // ])
    //     // ->join('sub_account_keys as sak', 'sak.id', '=', 'reports.sub_account_key')
    //     // ->join('account_keys as ak', 'ak.id', '=', 'sak.account_key')
    //     // ->join('keys', 'keys.code', '=', 'ak.code')
    //     // ->leftJoin('certificate_data as cd', 'cd.report_key', '=', 'reports.id')
    //     // ->leftJoin('loans', 'loans.report_key', '=', 'reports.id');
    // }

    public function query(BeginCredit $model): QueryBuilder
    {
        /**
         * ================       Step 2:  Build query        ================
         */

        $query = $model->newQuery()
            ->from('begin_credits') // explicitly set table
            ->leftJoin('sub_accounts', 'begin_credits.subAccountNumber', '=', 'sub_accounts.subAccountNumber')
            ->leftJoin('accounts', 'sub_accounts.accountNumber', '=', 'accounts.accountNumber')
            ->leftJoin('chapters', 'accounts.chapterNumber', '=', 'chapters.chapterNumber')
            ->leftJoin('agencies', 'begin_credits.agencyNumber', '=', 'agencies.agencyNumber')
            ->leftJoin('budget_voucher_loans', function ($join) {
                $join->on('begin_credits.program', '=', 'budget_voucher_loans.program')
                    ->on('begin_credits.subAccountNumber', '=', 'budget_voucher_loans.subAccountNumber');
            })
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
                'begin_credits.new_credit_status',
                'begin_credits.early_balance',
                'begin_credits.apply',
                'begin_credits.deadline_balance',
                'begin_credits.credit',
                'begin_credits.law_average',
                'begin_credits.law_correction',
                'agencies.agencyTitle',
                'accounts.accountNumber',
                'chapters.chapterNumber',
                'budget_voucher_loans.internal_increase', // or use bvl_sub if needed
                'budget_voucher_loans.unexpected_increase',
                'budget_voucher_loans.additional_increase',
                'budget_voucher_loans.total_increase',
                'budget_voucher_loans.decrease',
                'budget_voucher_loans.editorial'
            ]);

        return $query->orderBy('begin_credits.created_at', 'DESC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('guarantee-table')
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
            // Column::computed('DT_RowIndex', __('tables.th.no'))
            //     ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('chapterNumber')->title(__('tables.th.chapter'))->addClass('align-middle'),

            Column::make('accountNumber')->title(__('tables.th.account'))->addClass('align-middle'),


            Column::make('CNA')->title(__('tables.th.sub.account'))->addClass('align-middle'),

            Column::make('program')->title(__('tables.th.program'))->addClass('align-middle'),


            Column::make('fin_law')->title(__('tables.th.financeLaw'))->addClass('align-middle'),

            Column::make('current_loan')->title(__('tables.th.currentCredit'))->addClass('align-middle'),
            Column::make('internal_increase')
                ->title('កើនផ្ទៃក្នុង')
                ->addClass('align-middle text-center'),

            Column::make('unexpected_increase')
                ->title('មិនបានគ្រោងទុក')
                ->addClass('align-middle text-center'),

            Column::make('additional_increase')
                ->title('បំពេញបន្ថែម')
                ->addClass('align-middle text-center'),

            Column::make('total_increase')
                ->title('សរុប')
                ->addClass('align-middle text-center'),

            Column::make('decrease')
                ->title('ថយ')
                ->addClass('align-middle text-center'),

            Column::make('editorial')
                ->title('វិចារណកម្ម')
                ->addClass('align-middle text-center'),

            Column::make('new_credit_status')->title(__('tables.th.new_credit_status'))->addClass('align-middle'),
            Column::make('early_balance')->title(__('tables.th.early_balance'))->addClass('align-middle'),
            Column::make('apply')->title(__('tables.th.apply'))->addClass('align-middle'),
            Column::make('deadline_balance')->title(__('tables.th.deadline_balance'))->addClass('align-middle'),
            Column::make('credit')->title(__('ឥ.សល់'))->addClass('align-middle'),
            Column::make('law_average')->title(__('%ច្បាប់'))->addClass('align-middle'),
            Column::make('law_correction')->title(__('%ច្បាប់កែតម្រូវ'))->addClass('align-middle'),

            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Guarantee_' . date('YmdHis');
    }
}
