<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginMandate;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Program;
use App\Models\CostImplementProgram;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CostImplementProgramDataTable extends DataTable
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
            ->editColumn('new_credit_status', function ($row) {
                return number_format($row->new_credit_status ?? 0);
            })
            ->editColumn('deadline_balance', function ($row) {
                return number_format($row->deadline_balance ?? 0);
            })
            ->editColumn('law_average', function ($row) {
                return number_format($row->law_average ?? 0);
            })
            ->editColumn('law_correction', function ($row) {
                return number_format($row->law_correction ?? 0);
            })
            // ->editColumn('soft_delete', function ($soft_delete) {
            //     $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            //     return $active;
            // })
            // ->editColumn('txtDescription', function ($row) {
            //     return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            // })
            // ->rawColumns(['txtDescription'])

            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BeginVoucher $model): QueryBuilder
    {
        $query = $model->newQuery();

        $query->join('programs', 'begin_vouchers.program_id', '=', 'programs.id');

        $query->select([

            'programs.id',
            'programs.no as no',
            'programs.title as title',

            DB::raw('SUM(begin_vouchers.fin_law) as fin_law'),
            DB::raw('SUM(begin_vouchers.new_credit_status) as new_credit_status'),
            DB::raw('SUM(begin_vouchers.deadline_balance) as deadline_balance'),
            DB::raw('SUM(begin_vouchers.law_average) as law_average'),
            DB::raw('SUM(begin_vouchers.law_correction) as law_correction'),

            DB::raw('COUNT(begin_vouchers.id) as total_records'),
        ])->groupBy(
                'programs.id',
                'programs.no',
                'programs.title'
            );

        /**
         * FILTER YEAR
         */
        if (request()->filled('yearFilter')) {
            $query->whereYear('begin_vouchers.created_at', request('yearFilter'));
        }

        /**
         * FILTER MINISTRY
         */
        if (request()->filled('ministry_id')) {
            $query->where('begin_vouchers.ministry_id', request('ministry_id'));
        }

        $model->orderBy('programs.no', 'ASC');

        return $query;
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementprogram-table') // FIXED
            ->ajax([
                'data' => 'function(d) {

                    d.yearFilter = $("#yearFilter").val();
                    d.ministry_id = $("#ministryFilter").val();

    }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#costimplementprogram-table").DataTable().ajax.reload();
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
                ->width(30)
                ->addClass('text-center align-middle')
                ->orderable(false),

            Column::make('no')
                ->title(__('tables.th.program')),

            Column::make('title')
                ->title(__('tables.th.description')),

            Column::make('fin_law')
                ->title(__('tables.th.financeLaw')),

            Column::make('new_credit_status')
                ->title(__('tables.th.new_credit_status')),

            Column::make('deadline_balance')
                ->title(__('tables.th.deadline_balance')),

            Column::make('law_average')
                ->title(__('tables.th.law_average')),

            Column::make('law_correction')
                ->title(__('tables.th.law_correction')),

        ];
    }
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CostImplementProgram_' . date('YmdHis');
    }
}
