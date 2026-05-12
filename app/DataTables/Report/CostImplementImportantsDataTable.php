<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginMandate;
use App\Models\CostImplementImportant;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CostImplementImportantsDataTable extends DataTable
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

            // ->addColumn('action', function ($module) {
            //     return view('report::report.cost_implement.agency.action', ['module' => $module]);
            // })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginMandate $model): QueryBuilder
    {
        $query = $model->newQuery();

        /**
         * FILTER YEAR
         */
        if ($this->request()->has('yearFilter') && !empty($this->request()->yearFilter)) {

            $query->whereYear('created_at', $this->request()->yearFilter);

            /**
             * OR if you have direct year column:
             *
             * $query->where('year', $this->request()->yearFilter);
             */
        }

        /**
         * FILTER ministry_id
         */
        if ($this->request()->has('ministry_id') && !empty($this->request()->ministry_id)) {

            $query->where('ministry_id', $this->request()->ministry_id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->parameters([
                'language' => [
                    "url" => asset("assets/lang/language.json")
                ]
            ])
            ->setTableId('costimplementimportants-table')
            ->ajax([
                'data' => 'function(d) {

                    d.yearFilter = $("#yearFilter").val();
                    d.ministry_id = $("#ministryFilter").val();

    }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#costimplementimportants-table").DataTable().ajax.reload();
                });
            }')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('txtDescription')->title(__('tables.th.description')),
            Column::make('program_id')->title(__('tables.th.program')),
            Column::make('agency_id')->title(__('tables.th.agency')),
            Column::make('agency_id')->title(__('tables.th.agency.execute')),
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
        return 'CostImplementImportants_' . date('YmdHis');
    }
}
