<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginVoucher;

use App\Models\Content\Ministry;
use Illuminate\Http\Request;
use App\Models\CostImplementheaderexpensetype;
use App\Models\HeaderExpenseType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CostImplementHeaderExpenseTypeDataTable extends DataTable
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
                return number_format($row->fin_law ?? 0) . ' ៛';
            })
            ->editColumn('new_credit_status', function ($row) {
                return number_format($row->new_credit_status ?? 0) . ' ៛';
            })
            ->editColumn('deadline_balance', function ($row) {
                return number_format($row->deadline_balance ?? 0) . ' ៛';
            })
              ->rawColumns(['id' ]);
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginVoucher $model, Request $request): QueryBuilder
    {

        $model = $model->newQuery();

        /**
         * JOIN TABLES
         */
        $model->join(
            'header_expenses_type',
            'begin_vouchers.header_expense_type_id',
            '=',
            'header_expenses_type.id'
        );

        $model->join(
            'ministries',
            'begin_vouchers.ministry_id',
            '=',
            'ministries.id'
        );



        /**
         * SELECT
         */
        $model->select([
            'begin_vouchers.id',
            'begin_vouchers.ministry_id',
            'begin_vouchers.fin_law',
            'begin_vouchers.new_credit_status',
            'begin_vouchers.header_expense_type_id',
            'ministries.name as ministry_name',            
        ]);
        /**
         * FILTER YEAR
         */
        if ($this->request()->has('yearFilter') && !empty($this->request()->yearFilter)) {

            $model->whereYear('created_at', $this->request()->yearFilter);

            /**
             * OR if you have direct year column:
             *
             * $model->where('year', $this->request()->yearFilter);
             */
        }

        /**
         * FILTER ministry_id
         */
        if ($this->request()->has('ministry_id') && !empty($this->request()->ministry_id)) {

            $model->where('ministry_id', $this->request()->ministry_id);
        }

        /**
         * CUSTOM SEARCH
         */
        // if ($request->filled('customSearch')) {

        //     $search = $request->customSearch;

        //     $model->where(function ($query) use ($search) {

        //         $query->where('programs.no', 'LIKE', "%{$search}%")

        //             ->orWhere('programs.title', 'LIKE', "%{$search}%")

        //             ->orWhere('header_expenses_type.no', 'LIKE', "%{$search}%")

        //             ->orWhere('header_expenses_type.name', 'LIKE', "%{$search}%")

        //             ->orWhere('executive_units.title', 'LIKE', "%{$search}%")

        //             ->orWhere('begin_vouchers.fin_law', 'LIKE', "%{$search}%")

        //             ->orWhere('begin_vouchers.new_credit_status', 'LIKE', "%{$search}%")

        //             ->orWhere('begin_vouchers.deadline_balance', 'LIKE', "%{$search}%")

        //             ->orWhere('begin_vouchers.law_average', 'LIKE', "%{$search}%")

        //             ->orWhere('begin_vouchers.law_correction', 'LIKE', "%{$search}%");
        //     });
        // }

        // fixed condition 
        $model->where(
            'begin_vouchers.ministry_id',
            $this->request()->ministry_id
        );

        // $model->orderBy('programs.no', 'ASC');

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementheaderexpensetype-table')
            ->ajax([
                'data' => 'function(d) {
                    d.yearFilter = $("#yearFilter").val();
                    d.ministry_id = $("#ministryFilter").val();
                    d.customSearch = $("#customSearch-agency").val();

            }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#costimplementheaderexpensetype-table").DataTable().ajax.reload();
                });
            }')
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
            Column::computed('fin_law')->title(__('tables.th.financeLaw')),
            Column::computed('new_credit_status')->title(__('tables.th.new_credit_status')),
            Column::computed('new_credit_status')->title(__('tables.th.direct.payment')),
            Column::computed('new_credit_status')->title(__('tables.th.pre')),
            Column::computed('new_credit_status')->title(__('tables.th.advance.payment.royalty')),
            Column::computed('new_credit_status')->title(__('tables.th.pre.financing')),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CostImplementHeaderExpenseType_' . date('YmdHis');
    }
}
