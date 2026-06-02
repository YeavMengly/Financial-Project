<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginMandate;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use Illuminate\Http\Request;
use App\Models\CostImplementAgency;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CostImplementAgencyDataTable extends DataTable
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
            ->addColumn('program', function ($row) {
                return '<strong>' . $row->program_no  . '</strong><br/><hr/>' . $row->program_title;
            })
            ->addColumn('agency', function ($row) {
                return '<strong>' . $row->agency_no  . '</strong><br/><hr/>' . $row->agency_name;
            })
            ->editColumn('fin_law', function ($row) {
                return number_format($row->fin_law ?? 0) . ' ៛';
            })
            ->editColumn('new_credit_status', function ($row) {
                return number_format($row->new_credit_status ?? 0) . ' ៛';
            })
            ->editColumn('deadline_balance', function ($row) {
                return number_format($row->deadline_balance ?? 0) . ' ៛';
            })
            ->editColumn('law_average', function ($row) {

                $value = $row->law_average ?? 0;

                return number_format($value / 100, 2)  . '%';
            })
            ->editColumn('law_correction', function ($row) {
                $value = $row->law_correction ?? 0;

                return number_format($value / 100, 2) . '%';
            })
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['id', 'program', 'agency', 'txtDescription']);
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
            'agencies',
            'begin_vouchers.agency_id',
            '=',
            'agencies.id'
        );

        $model->join(
            'ministries',
            'begin_vouchers.ministry_id',
            '=',
            'ministries.id'
        );

        $model->join(
            'programs',
            'begin_vouchers.program_id',
            '=',
            'programs.id'
        );

        /**
         * IMPORTANT:
         * USE executive_unit_id IF EXISTS
         */
        $model->leftJoin(
            'executive_units',
            'executive_units.agency_id',
            '=',
            'agencies.id'
        );

        /**
         * SELECT
         */
        $model->select([
            'begin_vouchers.id',
            'begin_vouchers.ministry_id',
            'begin_vouchers.program_id',
            'begin_vouchers.agency_id',
            'begin_vouchers.txtDescription',
            'begin_vouchers.fin_law',
            'begin_vouchers.new_credit_status',
            'begin_vouchers.deadline_balance',
            'begin_vouchers.law_average',
            'begin_vouchers.law_correction',
            'begin_vouchers.created_at',

            'ministries.name as ministry_name',

            'programs.no as program_no',
            'programs.title as program_title',

            'agencies.no as agency_no',
            'agencies.name as agency_name',

            'executive_units.title as executive_unit_title',
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
        if ($request->filled('customSearch')) {

            $search = $request->customSearch;

            $model->where(function ($query) use ($search) {

                $query->where('programs.no', 'LIKE', "%{$search}%")

                    ->orWhere('programs.title', 'LIKE', "%{$search}%")

                    ->orWhere('agencies.no', 'LIKE', "%{$search}%")

                    ->orWhere('agencies.name', 'LIKE', "%{$search}%")

                    ->orWhere('executive_units.title', 'LIKE', "%{$search}%")

                    ->orWhere('begin_vouchers.fin_law', 'LIKE', "%{$search}%")

                    ->orWhere('begin_vouchers.new_credit_status', 'LIKE', "%{$search}%")

                    ->orWhere('begin_vouchers.deadline_balance', 'LIKE', "%{$search}%")

                    ->orWhere('begin_vouchers.law_average', 'LIKE', "%{$search}%")

                    ->orWhere('begin_vouchers.law_correction', 'LIKE', "%{$search}%");
            });
        }

        // fixed condition 
        $model->where(
            'begin_vouchers.ministry_id',
            $this->request()->ministry_id
        );

        $model->orderBy('programs.no', 'ASC');

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementagency-table')
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
                    $("#costimplementagency-table").DataTable().ajax.reload();
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
            Column::computed('txtDescription')->title(__('tables.th.description')),
            Column::make('program')->title(__('tables.th.program')),
            Column::make('agency')->title(__('tables.th.agency')),
            Column::computed('executive_unit_title')->title(__('tables.th.agency.executive.unit')),
            Column::computed('fin_law')->title(__('tables.th.financeLaw')),
            Column::computed('new_credit_status')->title(__('tables.th.new_credit_status')),
            Column::computed('deadline_balance')->title(__('tables.th.deadline_balance')),
            Column::computed('law_average')->title(__('tables.th.law_average')),
            Column::computed('law_correction')->title(__('tables.th.law_correction')),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CostImplementAgency_' . date('YmdHis');
    }
}
