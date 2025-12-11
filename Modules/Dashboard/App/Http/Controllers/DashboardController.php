<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Dropdown: ministries
        $ministries = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name')
            ->orderBy('year', 'desc')
            ->get();

        // Year filter: from request, or default to current year
        $year = $request->input('year') ?: date('Y');

        /**
         * =========================
         * BEGIN VOUCHERS (by year)
         * =========================
         */
        $beginQuery = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->select('begin_vouchers.*', 'ministries.year')
            ->where('ministries.year', $year);

        $beginReport = $beginQuery->get();

        // finance law card
        $total_fin_law       = $beginReport->sum('fin_law');
        $chartDataFinLaw     = $beginReport->pluck('fin_law')->toArray();
        $totalBeginVoucher   = $beginReport->count();

        // deadline balance card
        $total_deadline_balance = $beginReport->sum('deadline_balance');
        $chartDataDeadLine      = $beginReport->pluck('deadline_balance')->toArray();

        // law average & correction card
        $law_average_sum     = $beginReport->sum('law_average');
        $law_correction_sum  = $beginReport->sum('law_correction');

        // convert to percent (if your logic is /100)
        $law_average_percent    = $law_average_sum / 100;
        $law_correction_percent = $law_correction_sum / 100;

        $chartAvg        = $beginReport->pluck('law_average')->toArray();
        $chartAvgCorrect = $beginReport->pluck('law_correction')->toArray();

        /**
         * ==============================
         * LOAN TABLE (budget_voucher_loans)
         * ==============================
         * assuming budget_voucher_loans has ministry_id too
         */
        $loanQuery = DB::table('budget_voucher_loans')
            ->join('ministries', 'budget_voucher_loans.ministry_id', '=', 'ministries.id')
            ->select('budget_voucher_loans.*', 'ministries.year')
            ->where('ministries.year', $year);

        $loanReport = $loanQuery->get();

        $total_total_increase = $loanReport->sum('total_increase');
        $chartTotalIncrease   = $loanReport->pluck('total_increase')->toArray();
        $loanCount            = $loanReport->count();

        $duelQuery = DB::table('duel_entries')
            ->join('ministries', 'duel_entries.ministry_id', '=', 'ministries.id')
            ->select('duel_entries.*', 'ministries.year')
            ->where('ministries.year', $year);

        // ✅ All duel entries after filter (year + ministry)
        $duelEntries = $duelQuery->get();

        // ✅ Group totals by item_name
        $totalsByItem = $duelEntries
            ->groupBy('item_name')
            ->map(function ($rows) {
                return $rows->sum('quantity');
            });

        // ✅ Now extract each fuel type safely
        $qtyFuel   = $totalsByItem['ប្រេងសាំង']   ?? 0;
        $qtyDiesel = $totalsByItem['ប្រេងម៉ាស៊ូត'] ?? 0;
        $qtyOil    = $totalsByItem['ប្រេងម៉ាស៊ីន'] ?? 0;

        // ✅ If you still need a global total (optional)
        $total_qty_fuel = $duelEntries->sum('quantity');

        // ✅ Chart data PER item (if needed for mini chart)
        $chartDataFuel   = $duelEntries->where('item_name', 'ប្រេងសាំង')->pluck('quantity')->toArray();
        $chartDataDiesel = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ូត')->pluck('quantity')->toArray();
        $chartDataOil    = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ីន')->pluck('quantity')->toArray();

        // ✅ Total record count
        $totalFuel   = $duelEntries->where('item_name', 'ប្រេងសាំង')->count();
        $totalDiesel = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ូត')->count();
        $totalOil    = $duelEntries->where('item_name', 'ប្រេងម៉ាស៊ីន')->count();


        $materialQuery = DB::table('material_entries')
            ->join('ministries', 'material_entries.ministry_id', '=', 'ministries.id')
            ->select('material_entries.*', 'ministries.year')
            ->where('ministries.year', $year);
        $materialEntries = $materialQuery->get();

        $total_quantity = $materialQuery->sum('quantity');
        $chartTotalquantity   = $materialQuery->pluck('quantity')->toArray();
        $materialCount            = $materialQuery->count();

        return view('dashboard::index', [
            'ministries'              => $ministries,
            'selectedYear'            => $year,

            'total_fin_law'           => $total_fin_law,
            'chartDataFinLaw'         => $chartDataFinLaw,
            'totalBeginVoucher'       => $totalBeginVoucher,

            'total_deadline_balance'  => $total_deadline_balance,
            'chartDataDeadLine'       => $chartDataDeadLine,

            'law_average_percent'     => $law_average_percent,
            'law_correction_percent'  => $law_correction_percent,
            'chartAvg'                => $chartAvg,
            'chartAvgCorrect'         => $chartAvgCorrect,

            'total_total_increase'    => $total_total_increase,
            'chartTotalIncrease'      => $chartTotalIncrease,
            'loanCount'               => $loanCount,

            'qtyFuel'                 => $qtyFuel,
            'qtyDiesel'               => $qtyDiesel,
            'qtyOil'                  => $qtyOil,

            'chartDataFuel'            => $chartDataFuel,
            'chartDataDiesel'         => $chartDataDiesel,
            'chartDataOil'            => $chartDataOil,

            'totalFuel'               => $totalFuel,
            'totalDiesel'              => $totalDiesel,
            'totalOil'                => $totalOil,

            'total_quantity'          => $total_quantity,
            'chartTotalquantity'      => $chartTotalquantity,
            'materialCount'           => $materialCount,
        ]);
    }
}
