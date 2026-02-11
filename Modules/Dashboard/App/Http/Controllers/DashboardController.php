<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content\Chapter;
use App\Models\Content\Cluster;
use App\Models\Content\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $ministries = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name')
            ->orderBy('year', 'desc')
            ->get();

        // ✅ default year = request('year') else latest ministry year else current year
        $defaultYear = $ministries->first()->year ?? date('Y');
        $year = $request->filled('year') ? $request->input('year') : $defaultYear;

        // BEGIN VOUCHERS (by year)
        $beginReport = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->select('begin_vouchers.*')
            ->where('ministries.year', $year)
            ->get();

        $total_fin_law       = $beginReport->sum('fin_law');
        $chartDataFinLaw     = $beginReport->pluck('fin_law')->toArray();
        $totalBeginVoucher   = $beginReport->count();

        $total_deadline_balance = $beginReport->sum('deadline_balance');
        $chartDataDeadLine      = $beginReport->pluck('deadline_balance')->toArray();

        $total_credit       = $beginReport->sum('credit');
        $chartDataCredit     = $beginReport->pluck('credit')->toArray();
        // $totalBeginVoucher   = $beginReport->count();

        $percent_credit = $total_fin_law > 0 ? ($total_credit / $total_fin_law) * 100 : 0;
        $percent_deadline_balance = $total_fin_law > 0 ? ($total_deadline_balance / $total_fin_law) * 100 : 0;
        $percent_fin_law = $total_fin_law > 0 ? 100 : 0;

        // remaining percent so donut works correctly
        // $percent_remaining = max(0, 100 - ($percent_credit + $percent_deadline_balance));

        // dd($percent_fin_law);
        $law_average_sum     = $beginReport->sum('law_average');
        $law_correction_sum  = $beginReport->sum('law_correction');
        $law_average_percent    = $law_average_sum / 100;
        $law_correction_percent = $law_correction_sum / 100;

        $chartAvg        = $beginReport->pluck('law_average')->toArray();
        $chartAvgCorrect = $beginReport->pluck('law_correction')->toArray();

        // LOANS
        $loanReport = DB::table('budget_voucher_loans')
            ->join('ministries', 'budget_voucher_loans.ministry_id', '=', 'ministries.id')
            ->select('budget_voucher_loans.*', 'ministries.year')
            ->where('ministries.year', $year)
            ->get();

        $total_total_increase = $loanReport->sum('total_increase');
        $chartTotalIncrease   = $loanReport->pluck('total_increase')->toArray();
        $loanCount            = $loanReport->count();

        // DUEL Entry
        $duelEntries = DB::table('duel_entries')
            ->join('ministries', 'duel_entries.ministry_id', '=', 'ministries.id')
            ->select('duel_entries.*', 'ministries.year')
            ->where('ministries.year', $year)
            ->get();

        $totalsByItem = $duelEntries->groupBy('item_name')->map(fn($rows) => $rows->sum('quantity'));

        $qtyFuel   = $totalsByItem['ប្រេងសាំង'] ?? 0;
        $qtyDiesel = $totalsByItem['ប្រេងម៉ាស៊ូត'] ?? 0;
        $qtyOil    = $totalsByItem['ប្រេងម៉ាស៊ីន'] ?? 0;

        $chartDataFuel   = $duelEntries->where('item_name', 'ប្រេងសាំង')->pluck('quantity')->toArray();
        $chartDataDiesel = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ូត')->pluck('quantity')->toArray();
        $chartDataOil    = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ីន')->pluck('quantity')->toArray();

        $totalFuel   = $duelEntries->where('item_name', 'ប្រេងសាំង')->count();
        $totalDiesel = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ូត')->count();
        $totalOil    = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ីន')->count();

        // Duel Release
        $duelReleases = DB::table('duel_releases')
            ->join('ministries', 'duel_releases.ministry_id', '=', 'ministries.id')
            ->select('duel_releases.*', 'ministries.year')
            ->where('ministries.year', $year)
            ->get();

        $totalsReleaseByItem = $duelReleases
            ->groupBy('item_name')
            ->map(fn($rows) => $rows->sum('quantity_request'));

        $qtyFuelRelease   = $totalsReleaseByItem['ប្រេងសាំង'] ?? 0;
        $qtyDieselRelease = $totalsReleaseByItem['ប្រេងម៉ាស៊ូត'] ?? 0;
        $qtyOilRelease    = $totalsReleaseByItem['ប្រេងម៉ាស៊ីន'] ?? 0;

        $chartReleaseFuel   = $duelReleases->where('item_name', 'ប្រេងសាំង')->pluck('quantity_request')->toArray();
        $chartReleaseDiesel = $duelReleases->where('item_name', 'ប្រេងម៉ាស៊ូត')->pluck('quantity_request')->toArray();
        $chartReleaseOil    = $duelReleases->where('item_name', 'ប្រេងម៉ាស៊ីន')->pluck('quantity_request')->toArray();

        $totalFuelRelease   = $duelReleases->where('item_name', 'ប្រេងសាំង')->count();
        $totalDieselRelease = $duelReleases->where('item_name', 'ប្រេងម៉ាស៊ូត')->count();
        $totalOilRelease    = $duelReleases->where('item_name', 'ប្រេងម៉ាស៊ីន')->count();


        $totalEntry   = $duelEntries->count();
        $itemOptions = ['ប្រេងសាំង', 'ប្រេងម៉ាស៊ូត', 'ប្រេងម៉ាស៊ីន'];
        // MATERIAL
        $materialQuery = DB::table('material_entries')
            ->join('ministries', 'material_entries.ministry_id', '=', 'ministries.id')
            ->select('material_entries.*', 'ministries.year')
            ->where('ministries.year', $year);

        $materialEntries = $materialQuery->get();
        $total_quantity = $materialEntries->sum('quantity');
        $chartTotalquantity = $materialEntries->pluck('quantity')->toArray();
        $materialCount = $materialEntries->count();

        $programTotals = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->groupBy('begin_vouchers.program_id')
            ->selectRaw('
        begin_vouchers.program_id,
        SUM(begin_vouchers.fin_law) AS fin_law,
        SUM(begin_vouchers.apply) AS apply,
        SUM(begin_vouchers.deadline_balance) AS remain,
        SUM(begin_vouchers.credit) AS credit,
        COUNT(*) AS total_records
    ')
            ->get()
            ->keyBy('program_id');

        $data = DB::table('programs')
            ->join('ministries', 'programs.ministry_id', '=', 'ministries.id')
            ->where('ministries.year', $year)
            ->select('programs.*', 'ministries.year')
            ->orderBy('no')
            ->get();

        $programs = $data->map(function ($data) use ($programTotals) {
            $total = $programTotals[$data->id] ?? null;
            $data->fin_law        = $total->fin_law        ?? 0;
            $data->apply          = $total->apply          ?? 0;
            $data->remain         = $total->remain         ?? 0;
            $data->credit         = $total->credit         ?? 0;
            $data->total_records  = $total->total_records  ?? 0;
            $data->percent        = $data->fin_law > 0 ? ($data->apply / $data->fin_law) * 100 : 0;
            return $data;
        });



        $chapters = Chapter::where('ministries.year', $year)
            ->join('ministries', 'chapters.ministry_id', '=', 'ministries.id')
            ->select('chapters.*')
            ->orderBy('no')
            ->get();
        // dd($chapter[0]);

        $chapterTotals = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->where('ministries.year', $year)
            ->groupBy('begin_vouchers.chapter_id')
            ->selectRaw('
        begin_vouchers.chapter_id,
        SUM(begin_vouchers.fin_law) AS fin_law,
        SUM(begin_vouchers.apply) AS apply,
        SUM(begin_vouchers.deadline_balance) AS remain,
        SUM(begin_vouchers.credit) AS credit,
        COUNT(*) AS total_records
    ')
            ->get()
            ->keyBy('chapter_id');


        $chapters = $chapters->map(function ($chapter) use ($chapterTotals) {

            $total = $chapterTotals[$chapter->id] ?? null;

            $chapter->fin_law       = $total->fin_law ?? 0;
            $chapter->apply         = $total->apply ?? 0;
            $chapter->remain        = $total->remain ?? 0;
            $chapter->credit        = $total->credit ?? 0;
            $chapter->total_records = $total->total_records ?? 0;

            // example percentage
            $chapter->percent_apply = $chapter->fin_law > 0
                ? round(($chapter->apply / $chapter->fin_law) * 100, 2)
                : 0;

            return $chapter;
        });

        $chapterLabels = $chapters->pluck('no')->map(fn($n) => "ជំពូក $n");
        $finLawData    = $chapters->pluck('fin_law');



        // dd($chapters);

        return view('dashboard::index', [
            'ministries' => $ministries,
            'selectedYear' => $year,

            'total_fin_law' => $total_fin_law,
            'chartDataFinLaw' => $chartDataFinLaw,
            'totalBeginVoucher' => $totalBeginVoucher,

            'total_deadline_balance' => $total_deadline_balance,
            'chartDataDeadLine' => $chartDataDeadLine,

            'total_credit' => $total_credit,
            'chartDataCredit' => $chartDataCredit,

            'law_average_percent' => $law_average_percent,
            'law_correction_percent' => $law_correction_percent,
            'chartAvg' => $chartAvg,
            'chartAvgCorrect' => $chartAvgCorrect,

            'total_total_increase' => $total_total_increase,
            'chartTotalIncrease' => $chartTotalIncrease,
            'loanCount' => $loanCount,

            'qtyFuel' => $qtyFuel,
            'qtyDiesel' => $qtyDiesel,
            'qtyOil' => $qtyOil,

            'chartDataFuel' => $chartDataFuel,
            'chartDataDiesel' => $chartDataDiesel,
            'chartDataOil' => $chartDataOil,

            'totalFuel' => $totalFuel,
            'totalDiesel' => $totalDiesel,
            'totalOil' => $totalOil,

            'qtyFuelRelease' => $qtyFuelRelease,
            'qtyDieselRelease' => $qtyDieselRelease,
            'qtyOilRelease' => $qtyOilRelease,
            'itemOptions' => $itemOptions,

            'total_quantity' => $total_quantity,
            'chartTotalquantity' => $chartTotalquantity,
            'materialCount' => $materialCount,
            'programs' => $programs,
            'programTotals' => $programTotals,

            'percent_credit'           => round($percent_credit, 2),
            'percent_deadline_balance' => round($percent_deadline_balance, 2),
            'percent_fin_law'           => round($percent_fin_law, 2),
            // 'percent_remaining'        => round($percent_remaining, 2),

            'chapters' => $chapters,
            'chapterLabels' => $chapterLabels,
            'finLawData' => $finLawData,
        ]);
    }

    // public function getProgramSubs($programId)
    // {
    //     // Get all programSubs for this program
    //     $programSubs = ProgramSub::where('program_id', $programId)
    //         ->select('id', 'no', 'decription') // adjust fields you want to show
    //         ->get();

    //     $programTotals = DB::table('begin_vouchers')
    //         ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
    //         ->groupBy('begin_vouchers.program_id')
    //         ->selectRaw('
    //     begin_vouchers.program_id,
    //     SUM(begin_vouchers.fin_law) AS fin_law,
    //     SUM(begin_vouchers.apply) AS apply,
    //     SUM(begin_vouchers.deadline_balance) AS remain,
    //     SUM(begin_vouchers.credit) AS credit,
    //     COUNT(*) AS total_records
    //     ')
    //         ->get()
    //         ->keyBy('program_id');

    //     $programSubs = $programSubs->map(function ($data) use ($programTotals) {
    //         $total = $programTotals[$data->id] ?? null;
    //         $data->fin_law        = $total->fin_law        ?? 0;
    //         $data->apply          = $total->apply          ?? 0;
    //         $data->remain         = $total->remain         ?? 0;
    //         $data->credit         = $total->credit         ?? 0;
    //         $data->total_records  = $total->total_records  ?? 0;
    //         $data->percent        = $data->fin_law > 0 ? ($data->apply / $data->fin_law) * 100 : 0;
    //         return $data;
    //     });

    //     return response()->json($programSubs);
    // }
    public function getProgramSubs($programId)
    {
        // 1️⃣ Get program subs
        $programSubs = ProgramSub::where('program_id', $programId)
            ->select('id', 'no', 'decription')
            ->get();

        // 2️⃣ Get totals grouped by program_sub_id
        $programSubTotals = DB::table('begin_vouchers')
            ->where('program_id', $programId) // 🔥 IMPORTANT
            ->groupBy('program_sub_id')
            ->selectRaw('
            program_sub_id,
            SUM(fin_law) AS fin_law,
            SUM(apply) AS apply,
            SUM(deadline_balance) AS remain,
            SUM(credit) AS credit,
            COUNT(*) AS total_records
        ')
            ->get()
            ->keyBy('program_sub_id');

        // 3️⃣ Merge totals into program subs
        $programSubs = $programSubs->map(function ($sub) use ($programSubTotals) {
            $total = $programSubTotals->get($sub->id);

            $sub->fin_law       = $total->fin_law ?? 0;
            $sub->apply         = $total->apply ?? 0;
            $sub->remain        = $total->remain ?? 0;
            $sub->credit        = $total->credit ?? 0;
            $sub->total_records = $total->total_records ?? 0;
            $sub->percent       = $sub->fin_law > 0
                ? ($sub->apply / $sub->fin_law) * 100
                : 0;

            return $sub;
        });

        return response()->json($programSubs);
    }

    public function getClusters($programSubId)
    {
        $clusters = Cluster::where('program_sub_id', $programSubId)
            ->select(
                'id',
                'no',
                'decription as description'
            )
            ->get();

        // 2️⃣ Get totals grouped by program_sub_id
        $clusterTotal = DB::table('begin_vouchers')
            ->where('program_sub_id', $programSubId) // 🔥 IMPORTANT
            ->groupBy('cluster_id')
            ->selectRaw('
            cluster_id,
            SUM(fin_law) AS fin_law,
            SUM(apply) AS apply,
            SUM(deadline_balance) AS remain,
            SUM(credit) AS credit,
            COUNT(*) AS total_records
        ')
            ->get()
            ->keyBy('cluster_id');

        // 3️⃣ Merge totals into program subs
        $clusters = $clusters->map(function ($cluster) use ($clusterTotal) {
            $total = $clusterTotal->get($cluster->id);
            $cluster->fin_law       = $total->fin_law ?? 0;
            $cluster->apply         = $total->apply ?? 0;
            $cluster->remain        = $total->remain ?? 0;
            $cluster->credit        = $total->credit ?? 0;
            $cluster->total_records = $total->total_records ?? 0;
            $cluster->percent       = $cluster->fin_law > 0
                ? ($cluster->apply / $cluster->fin_law) * 100
                : 0;
            return $cluster;
        });

        return response()->json($clusters);
    }
}
