<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgramSub;
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

        $programs = DB::table('programs')
            ->join('ministries', 'programs.ministry_id', '=', 'ministries.id')
            ->where('ministries.id', $ministries->first()->id ?? 0)
            ->select('programs.*', 'ministries.year')
            ->orderBy('no')
            ->get();

            // dd('programs', $programs);


        // ✅ default year = request('year') else latest ministry year else current year
        $defaultYear = $ministries->first()->year ?? date('Y');
        $year = $request->filled('year') ? $request->input('year') : $defaultYear;

        // BEGIN VOUCHERS (by year)
        $beginReport = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->select('begin_vouchers.*', 'ministries.year')
            ->where('ministries.year', $year)
            ->get();

        $total_fin_law       = $beginReport->sum('fin_law');
        $chartDataFinLaw     = $beginReport->pluck('fin_law')->toArray();
        $totalBeginVoucher   = $beginReport->count();

        $total_deadline_balance = $beginReport->sum('deadline_balance');
        $chartDataDeadLine      = $beginReport->pluck('deadline_balance')->toArray();

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

        // DUEL
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
            ->where('ministries.year', $year)
            ->groupBy('begin_vouchers.program_id')
            ->selectRaw('
            begin_vouchers.program_id,
            SUM(begin_vouchers.fin_law) AS fin_law,
            SUM(begin_vouchers.apply) AS apply,
            SUM(begin_vouchers.deadline_balance) AS remain,
            COUNT(*) AS total_records
        ')
            ->get()
            ->keyBy('program_id');

        return view('dashboard::index', [
            'ministries' => $ministries,
            'selectedYear' => $year,

            'total_fin_law' => $total_fin_law,
            'chartDataFinLaw' => $chartDataFinLaw,
            'totalBeginVoucher' => $totalBeginVoucher,

            'total_deadline_balance' => $total_deadline_balance,
            'chartDataDeadLine' => $chartDataDeadLine,

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

            'total_quantity' => $total_quantity,
            'chartTotalquantity' => $chartTotalquantity,
            'materialCount' => $materialCount,
            'programs' => $programs,
            'programTotals' => $programTotals,
        ]);
    }

    // DashboardController.php
    public function subs($programId)
    {

        dd('hit subs', $programId);
        $subs = ProgramSub::where('program_id', $programId)
            ->select('id', 'no', 'name_kh', 'name_en')
            ->orderBy('no')
            ->get();

        dd('subs', $subs);

        return response()->json([
            'data' => $subs
        ]);
    }
}
