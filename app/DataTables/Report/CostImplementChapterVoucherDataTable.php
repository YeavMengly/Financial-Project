<?php

namespace App\DataTables\Report;

use App\Models\Content\Chapter;
use App\Models\BeginCredit\BeginVoucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CostImplementChapterVoucherDataTable extends DataTable
{
    private function getMinistryId()
    {
        $ministryId = request('ministry_id');
        $year       = request('yearFilter');

        if ($ministryId) {
            return $ministryId;
        }

        if ($year) {
            return DB::table('ministries')
                ->where('year', $year)
                ->value('id');
        }

        return DB::table('ministries')
            ->where('year', date('Y'));
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $ministryId = $this->getMinistryId();

        // 1. Calculate Summary Row Totals
        $totalQuery = BeginVoucher::query()
            ->when($ministryId, function ($q) use ($ministryId) {
                $q->where('ministry_id', $ministryId);
            });

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

        $rawFinLaw  = (float) ($total->fin_law ?? 0);
        $rawApply   = (float) ($total->apply ?? 0);
        $rawCredit  = (float) ($total->credit ?? 0);
        $rawBalance = (float) ($total->deadline_balance ?? 0);

        $totals = [
            'fin_law'           => $rawFinLaw,
            'new_credit_status' => (float) ($total->new_credit_status ?? 0),
            'early_balance'     => (float) ($total->early_balance ?? 0),
            'apply'             => $rawApply,
            'apply_percent'     => $rawFinLaw > 0 ? ($rawApply / $rawFinLaw) * 100 : 0,
            'credit'            => $rawCredit,
            'credit_percent'    => $rawFinLaw > 0 ? ($rawCredit / $rawFinLaw) * 100 : 0,
            'deadline_balance'  => $rawBalance,
            'remaining_percent' => $rawFinLaw > 0 ? ($rawBalance / $rawFinLaw) * 100 : 0,
        ];

        return (new EloquentDataTable($query))
            ->addColumn('chapter', function ($row) {
                return 'ជំពូកទី ' . $row->no;
            })
            ->editColumn('fin_law', function ($row) {
                return number_format($row->fin_law ?? 0) . ' ៛';
            })
            ->editColumn('new_credit_status', function ($row) {
                return number_format($row->new_credit_status ?? 0) . ' ៛';
            })
            ->editColumn('early_balance', function ($row) {
                return number_format($row->early_balance ?? 0) . ' ៛';
            })
            ->editColumn('apply', function ($row) {
                return number_format($row->apply ?? 0) . ' ៛';
            })
            ->addColumn('apply_percent', function ($row) {
                $finLaw = (float) $row->fin_law;
                $apply  = (float) $row->apply;
                $val    = $finLaw > 0 ? ($apply / $finLaw) * 100 : 0;
                return number_format($val, 2, '.', ',') . '%';
            })
            ->editColumn('credit', function ($row) {
                return number_format($row->credit ?? 0) . ' ៛';
            })
            ->addColumn('credit_percent', function ($row) {
                $finLaw = (float) $row->fin_law;
                $credit = (float) $row->credit;
                $val    = $finLaw > 0 ? ($credit / $finLaw) * 100 : 0;
                return number_format($val, 2, '.', ',') . '%';
            })
            ->editColumn('deadline_balance', function ($row) {
                return number_format($row->deadline_balance ?? 0) . ' ៛';
            })
            ->addColumn('remaining_percent', function ($row) {
                $finLaw  = (float) $row->fin_law;
                $balance = (float) $row->deadline_balance;
                $val     = $finLaw > 0 ? ($balance / $finLaw) * 100 : 0;
                return number_format($val, 2, '.', ',') . '%';
            })
            ->with('totals', $totals)
            ->rawColumns(['chapter'])
            ->setRowId('no');
    }

    public function query(Chapter $model): QueryBuilder
    {
        $ministryId = $this->getMinistryId();

        return $model->newQuery()
            ->join('begin_vouchers', function ($join) {
                $join->on('chapters.no', '=', 'begin_vouchers.chapter_id')
                     ->on('chapters.ministry_id', '=', 'begin_vouchers.ministry_id');
            })
            ->when($ministryId, function ($q) use ($ministryId) {
                $q->where('chapters.ministry_id', $ministryId);
            })
            ->select([
                'chapters.no as no',
                DB::raw('COALESCE(SUM(begin_vouchers.fin_law), 0) as fin_law'),
                DB::raw('COALESCE(SUM(begin_vouchers.new_credit_status), 0) as new_credit_status'),
                DB::raw('COALESCE(SUM(begin_vouchers.early_balance), 0) as early_balance'),
                DB::raw('COALESCE(SUM(begin_vouchers.apply), 0) as apply'),
                DB::raw('COALESCE(SUM(begin_vouchers.credit), 0) as credit'),
                DB::raw('COALESCE(SUM(begin_vouchers.deadline_balance), 0) as deadline_balance'),
            ])
            ->groupBy('chapters.no')
            ->orderBy('chapters.no', 'DESC');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costimplementchaptervoucher-table')
            ->ajax([
                'url'  => route('cost.implement.chapter.index'),
                'type' => 'GET',
                'data' => 'function(d) {
                    d.yearFilter = $("#yearFilter").val();
                    d.ministry_id = $("#yearFilter option:selected").data("ministry-id") || $("#ministryFilter").val() || $("select[name=\"ministry_id\"]").val();
                }',
            ])
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'autoWidth'  => false,
                'ordering'   => false,
                'responsive' => false,
                'scrollX'    => false,
                'language'   => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->columns($this->getColumns());
    }

    public function getColumns(): array
    {
        return [
            Column::computed('chapter')->data('chapter')->name('chapter')->title('')->addClass('text-center'),
            Column::make('fin_law')->data('fin_law')->name('fin_law')->title('')->addClass('text-end'),
            Column::make('new_credit_status')->data('new_credit_status')->name('new_credit_status')->title('')->addClass('text-end'),
            Column::make('early_balance')->data('early_balance')->name('early_balance')->title('')->addClass('text-end'),
            Column::make('apply')->data('apply')->name('apply')->title('')->addClass('text-end'),
            Column::computed('apply_percent')->data('apply_percent')->name('apply_percent')->title('')->addClass('text-end'),
            Column::make('credit')->data('credit')->name('credit')->title('')->addClass('text-end'),
            Column::computed('credit_percent')->data('credit_percent')->name('credit_percent')->title('')->addClass('text-end'),
            Column::make('deadline_balance')->data('deadline_balance')->name('deadline_balance')->title('')->addClass('text-end'),
            Column::computed('remaining_percent')->data('remaining_percent')->name('remaining_percent')->title('')->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'CostImplementChapterVoucher_' . date('YmdHis');
    }
}