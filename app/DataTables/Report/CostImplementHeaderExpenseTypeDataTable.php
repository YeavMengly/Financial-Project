<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CostImplementHeaderExpenseTypeDataTable extends DataTable
{
    /**
     * Build the DataTable.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('fin_law', function ($row) {
                return number_format($row->fin_law ?? 0) . ' ៛';
            })
            ->editColumn('direct_payment', function ($row) {
                return number_format($row->direct_payment ?? 0) . ' ៛';
            })
            ->editColumn('public_procurement', function ($row) {
                return number_format($row->public_procurement ?? 0) . ' ៛';
            })
            ->editColumn('advance_payment', function ($row) {
                return number_format($row->advance_payment ?? 0) . ' ៛';
            })
            ->editColumn('advance_cash', function ($row) {
                return number_format($row->advance_cash ?? 0) . ' ៛';
            });
    }

    /**
     * Get the query source.
     */
    public function query(
        BeginVoucher $model,
        Request $request
    ): QueryBuilder {
        // Capture the selected year filter from the view dropdown
        $yearFilter = $request->input('ministryFilter');

        $subQuery = DB::table('budget_vouchers')
            ->leftJoin('header_expenses_type', 'budget_vouchers.header_expense_type_id', '=', 'header_expenses_type.id')
            ->leftJoin('ministries', 'budget_vouchers.ministry_id', '=', 'ministries.id');

        // Apply filter by year if selected from the dropdown
        if (!empty($yearFilter)) {
            $subQuery->where('ministries.year', $yearFilter);
        }

        $subQuery->selectRaw('
            COALESCE(SUM(budget_vouchers.fin_law), 0) AS fin_law,

            COALESCE(SUM(CASE WHEN budget_vouchers.header_expense_type_id = 1 THEN budget_vouchers.budget ELSE 0 END), 0) AS direct_payment,
            COALESCE(SUM(CASE WHEN budget_vouchers.header_expense_type_id = 2 THEN budget_vouchers.budget ELSE 0 END), 0) AS public_procurement,
            COALESCE(SUM(CASE WHEN budget_vouchers.header_expense_type_id = 3 THEN budget_vouchers.budget ELSE 0 END), 0) AS advance_payment,
            COALESCE(SUM(CASE WHEN budget_vouchers.header_expense_type_id = 4 THEN budget_vouchers.budget ELSE 0 END), 0) AS advance_cash
        ');

        return $model->newQuery()
            ->fromSub($subQuery, 'budget_summary')
            ->select([
                'budget_summary.fin_law',
                'budget_summary.direct_payment',
                'budget_summary.public_procurement',
                'budget_summary.advance_payment',
                'budget_summary.advance_cash',
            ]);
    }

    /**
     * HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementheaderexpensetype-table')
            ->ajax([
                'url' => request()->url(),
                'data' => "function(d) {
                    d.ministryFilter = $('#ministryFilter').val();
                }"
            ])
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
                'searching' => false,
                'paging' => false,
                'info' => false,
                'ordering' => false,
            ])
            ->columns($this->getColumns());
    }

    /**
     * DataTable columns.
     */
    public function getColumns(): array
    {
        return [
            Column::make('fin_law')
                ->title('ច្បាប់ហិរញ្ញវត្ថុ'),

            Column::make('direct_payment')
                ->title('ទូទាត់ត្រង់'),

            Column::make('public_procurement')
                ->title('លទ្ធកម្មសាធារណៈ'),

            Column::make('advance_payment')
                ->title('រជ្ជទេយ្យបុរេប្រទាន'),

            Column::make('advance_cash')
                ->title('បើកផ្តល់សាច់ប្រាក់មុន'),
        ];
    }

    /**
     * Export filename.
     */
    protected function filename(): string
    {
        return 'CostImplementHeaderExpenseType_' . date('YmdHis');
    }
}