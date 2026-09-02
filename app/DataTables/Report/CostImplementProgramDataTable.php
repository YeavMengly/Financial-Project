<?php

namespace App\DataTables\Report;

use App\Models\BeginCredit\BeginVoucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CostImplementProgramDataTable extends DataTable
{
    /**
     * Formats numbers to European standard (e.g. 1.234,56).
     */
    private function formatAmount($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    /**
     * Build DataTable.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // ==========================================
        // TOTAL AGGREGATION
        // ==========================================
        $totalQuery = BeginVoucher::query();

        if (request()->filled('yearFilter')) {
            $totalQuery->whereYear(
                'begin_vouchers.created_at',
                request('yearFilter')
            );
        }

        if (request()->filled('ministry_id')) {
            $totalQuery->where(
                'begin_vouchers.ministry_id',
                request('ministry_id')
            );
        }

        $total = $totalQuery
            ->selectRaw('
                COALESCE(SUM(fin_law), 0) as fin_law,
                COALESCE(SUM(new_credit_status), 0) as new_credit_status,
                COALESCE(SUM(early_balance), 0) as early_balance,
                COALESCE(SUM(apply), 0) as apply,
                COALESCE(SUM(credit), 0) as credit,
                COALESCE(SUM(deadline_balance), 0) as deadline_balance
            ')
            ->first();

        $finLaw  = (float) $total->fin_law;
        $apply   = (float) $total->apply;
        $credit  = (float) $total->credit;
        $balance = (float) $total->deadline_balance;

        $totals = [
            'fin_law'           => $finLaw,
            'new_credit_status' => (float) $total->new_credit_status,
            'early_balance'     => (float) $total->early_balance,
            'apply'             => $apply,
            'apply_percent'     => $finLaw > 0 ? ($apply / $finLaw) * 100 : 0,
            'credit'            => $credit,
            'credit_percent'    => $finLaw > 0 ? ($credit / $finLaw) * 100 : 0,
            'deadline_balance'  => $balance,
            'remaining_percent' => $finLaw > 0 ? ($balance / $finLaw) * 100 : 0,
        ];

        // ==========================================
        // DATATABLE RESPONSE FORMATTING
        // ==========================================
        return (new EloquentDataTable($query))
            // 1. កម្មវិធី
            ->addColumn('program', function ($row) {
                return e('កម្មវិធី'.$row->no);
            })

            // 2. ច្បាប់ហិរញ្ញវត្ថុ
            ->editColumn('fin_law', function ($row) {
                return $this->formatAmount($row->fin_law);
            })

            // 3. ឥណទានថ្មី
            ->editColumn('new_credit_status', function ($row) {
                return $this->formatAmount($row->new_credit_status);
            })

            // 4. ដើមគ្រា
            ->editColumn('early_balance', function ($row) {
                return $this->formatAmount($row->early_balance);
            })

            // 5. អនុវត្ត
            ->editColumn('apply', function ($row) {
                return $this->formatAmount($row->apply);
            })

            // 6. ភាគរយ (5/2)
            ->addColumn('apply_percent', function ($row) {
                $finLaw = (float) $row->fin_law;
                $apply  = (float) $row->apply;
                $val    = $finLaw > 0 ? ($apply / $finLaw) * 100 : 0;
                return number_format($val, 2, ',', '.') . '%';
            })

            // 7. បូកយោង (4+5)
            ->editColumn('credit', function ($row) {
                return $this->formatAmount($row->credit);
            })

            // 8. ភាគរយ (7/2)
            ->addColumn('credit_percent', function ($row) {
                $finLaw = (float) $row->fin_law;
                $credit = (float) $row->credit;
                $val    = $finLaw > 0 ? ($credit / $finLaw) * 100 : 0;
                return number_format($val, 2, ',', '.') . '%';
            })

            // 9. នៅសល់ (2-7)
            ->editColumn('deadline_balance', function ($row) {
                return $this->formatAmount($row->deadline_balance);
            })

            // 10. ភាគរយ (9/2)
            ->addColumn('remaining_percent', function ($row) {
                $finLaw  = (float) $row->fin_law;
                $balance = (float) $row->deadline_balance;
                $val     = $finLaw > 0 ? ($balance / $finLaw) * 100 : 0;
                return number_format($val, 2, ',', '.') . '%';
            })

            ->with('totals', $totals)
            ->rawColumns(['program'])
            ->setRowId('id');
    }

    /**
     * Query source.
     */
    public function query(BeginVoucher $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->join(
                'programs',
                'begin_vouchers.program_id',
                '=',
                'programs.id'
            )
            ->select([
                'programs.no as no',

                DB::raw('SUM(begin_vouchers.fin_law) as fin_law'),
                DB::raw('SUM(begin_vouchers.new_credit_status) as new_credit_status'),
                DB::raw('SUM(begin_vouchers.early_balance) as early_balance'),
                DB::raw('SUM(begin_vouchers.apply) as apply'),
                DB::raw('SUM(begin_vouchers.credit) as credit'),
                DB::raw('SUM(begin_vouchers.deadline_balance) as deadline_balance'),
            ])
            // Group ONLY by program number so sub-programs roll up into 1 single row per program number
            ->groupBy('programs.no');

        // Year Filter
        if (request()->filled('yearFilter')) {
            $query->whereYear(
                'begin_vouchers.created_at',
                request('yearFilter')
            );
        }

        // Ministry Filter
        if (request()->filled('ministry_id')) {
            $query->where(
                'begin_vouchers.ministry_id',
                request('ministry_id')
            );
        }

        return $query->orderBy('programs.no', 'ASC');
    }
    /**
     * HTML builder configured for horizontal scrolling.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementprogram-table')
            ->ajax([
                'url'  => route('cost.implement.program.index'),
                'type' => 'GET',
                'data' => 'function(d) {
                d.yearFilter = $("#yearFilter").val();
                d.ministry_id = $("#ministryFilter").val();
            }',
            ])
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'autoWidth'  => false,
                'ordering'   => false,
                'responsive' => false,
                'scrollX'    => false, // Disabled JS splitting; wrapper CSS handles scrolling instead
                'language'   => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->columns($this->getColumns());
    }

    /**
     * Columns setup with text alignments.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('program')->title('')->addClass('text-center'),
            Column::make('fin_law')->title('')->addClass('text-end'),
            Column::make('new_credit_status')->title('')->addClass('text-end'),
            Column::make('early_balance')->title('')->addClass('text-end'),
            Column::make('apply')->title('')->addClass('text-end'),
            Column::computed('apply_percent')->title('')->addClass('text-end'),
            Column::make('credit')->title('')->addClass('text-end'),
            Column::computed('credit_percent')->title('')->addClass('text-end'),
            Column::make('deadline_balance')->title('')->addClass('text-end'),
            Column::computed('remaining_percent')->title('')->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'CostImplementProgram_' . date('YmdHis');
    }
}
