<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\Content\Chapter;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Content\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $ministries = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name')
            ->where('is_archived', 1)
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
        $total_new_credit_status     = $beginReport->sum('new_credit_status');
        $chartDataCreditStatus     = $beginReport->pluck('new_credit_status')->toArray();

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
        // total count pro
        $totalProgaramVoucher = DB::table('budget_vouchers')
            ->join('ministries', 'budget_vouchers.ministry_id', '=', 'ministries.id')
            ->groupBy('budget_vouchers.program_id')
            ->selectRaw('
        budget_vouchers.program_id,
        COUNT(*) AS total_record_voucher
    ')
            ->get()
            ->keyBy('program_id');

        // dd($totalProgaramVoucher);
        $totalProgaramMandate = DB::table('budget_mandates')
            ->join('ministries', 'budget_mandates.ministry_id', '=', 'ministries.id')
            ->groupBy('budget_mandates.program_id')
            ->selectRaw('
        budget_mandates.program_id,
        COUNT(*) AS total_record_mandate
    ')
            ->get()
            ->keyBy('program_id');
        //  dd($totalProgaramMandate,$totalProgaramVoucher);
        $programs = $data->map(function ($data) use ($programTotals, $totalProgaramVoucher, $totalProgaramMandate) {
            $total = $programTotals[$data->id] ?? null;
            $totalVoucher = $totalProgaramVoucher[$data->id] ?? null;
            $totalMandate = $totalProgaramMandate[$data->id] ?? null;

            $data->fin_law        = $total->fin_law        ?? 0;
            $data->apply          = $total->apply          ?? 0;
            $data->remain         = $total->remain         ?? 0;
            $data->credit         = $total->credit         ?? 0;
            $data->total_record_voucher  = $totalVoucher->total_record_voucher  ?? 0;
            $data->total_record_mandate  = $totalMandate->total_record_mandate  ?? 0;
            $data->total_records  = $total->total_records  ?? 0;
            $data->percent        = $data->fin_law > 0 ? ($data->apply / $data->fin_law) * 100 : 0;
            return $data;
        });

        // dd($programs);

        $chapters = Chapter::where('ministries.year', $year)
            ->join('ministries', 'chapters.ministry_id', '=', 'ministries.id')
            ->select('chapters.*')
            ->orderBy('no')
            ->get();
        // dd($chapters);
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

            $total = $chapterTotals[$chapter->no] ?? null;


            $chapter->fin_law       = $total->fin_law ?? 0;
            $chapter->apply         = $total->apply ?? 0;
            $chapter->remain        = $total->remain ?? 0;
            $chapter->credit        = $total->credit ?? 0;
            $chapter->deadline_balance        = $total->deadline_balance ?? 0;
            $chapter->total_records = $total->total_records ?? 0;

            // example percentage
            $chapter->percent_apply = $chapter->fin_law > 0
                ? round(($chapter->apply / $chapter->fin_law) * 100, 2)
                : 0;

            return $chapter;
        });

        $chapterLabels = $chapters->pluck('no')->map(fn($n) => " $n");
        //  dd($chapters);
        $finLawData    = $chapters->pluck('fin_law');
        $remainData    = $chapters->pluck('remain');
        $deadlineData    = $chapters->pluck('credit');

        $account = Account::where('ministries.year', $year)
            ->join('ministries', 'accounts.ministry_id', '=', 'ministries.id')
            ->leftJoin('chapters', 'accounts.chapter_id', '=', 'chapters.id')
            ->select('accounts.*')
            ->orderBy('no')
            ->get();

        $accountTotal = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->where('ministries.year', $year)
            ->groupBy('begin_vouchers.account_id')
            ->selectRaw('
        begin_vouchers.account_id,
        SUM(begin_vouchers.fin_law) AS fin_law,
        SUM(begin_vouchers.deadline_balance) as deadline_balance,
        SUM(begin_vouchers.credit) as credit,
        COUNT(*) AS total_records
    ')
            ->get()
            ->keyBy('account_id');
        $accounts = $account->map(function ($account) use ($accountTotal) {

            $total = $accountTotal[$account->no] ?? null;


            $account->fin_law       = $total->fin_law ?? 0;

            $account->deadline_balance        = $total->deadline_balance ?? 0;
            $account->credit        = $total->credit ?? 0;


            // example percentage
            // $account->percent_apply = $account->fin_law > 0
            //     ? round(($account->apply / $account->fin_law) * 100, 2)
            //     : 0;

            return $account;
        });

        // expense Type donut chart
        //exp_directPayment
        $budgetVouchers = DB::table('budget_vouchers')
            ->join('ministries', 'budget_vouchers.ministry_id', '=', 'ministries.id')
            ->select('budget_vouchers.*')
            ->where('ministries.year', $year)
            ->get();
       //exp_guarantee
        $budgetMandate = DB::table('budget_mandates')
            ->join('ministries', 'budget_mandates.ministry_id', '=', 'ministries.id')
            ->select('budget_mandates.*')
            ->where('ministries.year', $year)
            ->get();
        $expenditure_Guarantee = $budgetMandate->where('expense_type_id', '1')->pluck('budget');
        // $advance_Payment = $budgetVouchers->where('expense_type_id', '2')->pluck('budget');
        $direct_Payment = $budgetVouchers->where('expense_type_id', '3')->pluck('budget');
        // $procurement = $budgetVouchers->where('expense_type_id', '4')->pluck('budget');
        // $pre_Financing = $budgetVouchers->where('expense_type_id', '5')->pluck('budget');
        //$taskType = $budgetVouchers->where('expense_type_id', '2')->pluck('budget')->toArray();
        $expenditure_Guarantee = round($budgetMandate->where('expense_type_id', '1')->sum('budget'), 2);
        // $advance_Payment = round($budgetVouchers->where('expense_type_id', '2')->sum('budget'), 2);
        $direct_Payment = round($budgetVouchers->where('expense_type_id', '3')->sum('budget'), 2);
        // $procurement = round($budgetVouchers->where('expense_type_id', '4')->sum('budget'), 2);
        // $pre_Financing = round($budgetVouchers->where('expense_type_id', '5')->sum('budget'), 2);

        $totalCountArch = $budgetMandate->where('is_archived', '1')->count();
        $totalCountDir = $budgetVouchers->where('is_archived', '2')->count();

        $budgetReport = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->select('begin_vouchers.*')
            ->where('ministries.year', $year)
            ->get();

        $total_fin_law       = $budgetReport->sum('fin_law');
        $percent_expenditure_Guarantee = $total_fin_law > 0 ? ($expenditure_Guarantee / $total_fin_law) * 100 : 0;
        // $percent_advance_Payment = $total_fin_law > 0 ? ($advance_Payment / $total_fin_law) * 100 : 0;
        $percent_direct_Payment = $total_fin_law > 0 ? ($direct_Payment / $total_fin_law) * 100 : 0;
       // $percent_procurement = $total_fin_law > 0 ? ($procurement / $total_fin_law) * 100 : 0;
       // $percent_pre_Financing = $total_fin_law > 0 ? ($pre_Financing / $total_fin_law) * 100 : 0;
        $expenseType = ExpenseType::all();
            
       
      // dd($taskType);
        // $percent_procurement = $total_fin_law > 0 ? ($procurement / $total_fin_law) * 100 : 0;
        // $percent_pre_Financing = $total_fin_law > 0 ? ($pre_Financing / $total_fin_law) * 100 : 0;
        $totalExpend = $total_fin_law > 0 ? $total_fin_law - $expenditure_Guarantee : 0;
        $totalDir = $expenditure_Guarantee > 0 ? $expenditure_Guarantee - $direct_Payment : 0;

        //   dd($totalExpend );
        return view('dashboard::index', [
            'ministries' => $ministries,
            'selectedYear' => $year,

            'total_fin_law' => $total_fin_law,
            'chartDataFinLaw' => $chartDataFinLaw,
            'totalBeginVoucher' => $totalBeginVoucher,
            'total_new_credit_status' => $total_new_credit_status,
            'chartDataCreditStatus' => $chartDataCreditStatus,

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
            'totalProgaramMandate' => $totalProgaramMandate,

            'percent_credit'           => round($percent_credit, 2),
            'percent_deadline_balance' => round($percent_deadline_balance, 2),
            'percent_fin_law'           => round($percent_fin_law, 2),
            // 'percent_remaining'        => round($percent_remaining, 2),

            'chapters' => $chapters,
            'chapterLabels' => $chapterLabels,
            'finLawData' => $finLawData,
            'remainData' => $remainData,
            'deadlineData' => $deadlineData,
            'accounts' => $accounts,
            'expenditure_Guarantee' => $expenditure_Guarantee,
            //'advance_Payment' => $advance_Payment,
            'direct_Payment' => $direct_Payment,
            // 'procurement' => $procurement,
            // 'pre_Financing' => $pre_Financing,
            'percent_expenditure_Guarantee' => $percent_expenditure_Guarantee,
            // 'percent_advance_Payment' => $percent_advance_Payment,
            'percent_direct_Payment' => $percent_direct_Payment,
           // 'percent_procurement' => $percent_procurement,
           // 'percent_pre_Financing' => $percent_pre_Financing,
            // 'taskType' => $taskType,
            'expenseType' => $expenseType,
 
            // 'percent_procurement' => $percent_procurement,
            // 'percent_pre_Financing' => $percent_pre_Financing,
            // 'taskType' => $taskType,
            'totalCountArch' => $totalCountArch,
            'totalCountDir' => $totalCountDir,
            'totalExpend' => $totalExpend,
            'totalDir' => $totalDir
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
            // dd($programSubTotals);
        // total count pro
        $totalProSubVoucher = DB::table('budget_vouchers')
            ->join('ministries', 'budget_vouchers.ministry_id', '=', 'ministries.id')
            ->groupBy('budget_vouchers.program_sub_id')
            ->selectRaw('
        budget_vouchers.program_sub_id,
        COUNT(*) AS total_record_sub_voucher
    ')
            ->get()
            ->keyBy('program_sub_id');

        //  dd($programSubTotals);
        $totalProSubMandate = DB::table('budget_mandates')
            ->join('ministries', 'budget_mandates.ministry_id', '=', 'ministries.id')
            ->groupBy('budget_mandates.program_sub_id')
            ->selectRaw('
        budget_mandates.program_sub_id,
        COUNT(*) AS total_record_sub_mandate
    ')
            ->get()
            ->keyBy('program_sub_id');

        // 3️⃣ Merge totals into program subs
        $programSubs = $programSubs->map(function ($sub) use ($programSubTotals, $totalProSubVoucher, $totalProSubMandate) {
            $total = $programSubTotals->get($sub->id);
            $totalSubVoucher = $totalProSubVoucher[$sub->id] ?? null;
            $totalSubMandate = $totalProSubMandate[$sub->id] ?? null;

            $sub->fin_law       = $total->fin_law ?? 0;
            $sub->apply         = $total->apply ?? 0;
            $sub->remain        = $total->remain ?? 0;
            $sub->credit        = $total->credit ?? 0;
            $sub->total_records = $total->total_records ?? 0;
            $sub->total_record_sub_voucher  = $totalSubVoucher->total_record_sub_voucher  ?? 0;
            $sub->total_record_sub_mandate  = $totalSubMandate->total_record_sub_mandate  ?? 0;
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

    
    public function getAccountSubs($accountId)
    {
        // 1️⃣ Get program subs
        $accountSubs = AccountSub::where('account_id', $accountId)
            ->select('id', 'no', 'name')
            ->get();

        // 2️⃣ Get totals grouped by account_sub_id
        $accountSubTotals = DB::table('begin_vouchers')
            ->where('account_id', $accountId) // 🔥 IMPORTANT
            ->groupBy('account_sub_id')
            ->selectRaw('
            account_sub_id,
            SUM(fin_law) AS fin_law,
            SUM(apply) AS apply,
            SUM(deadline_balance) AS remain,
            SUM(credit) AS credit,
            COUNT(*) AS total_records
        ')
            ->get()
            ->keyBy('account_sub_id');

        // 3️⃣ Merge totals into program sub
        $accountSubs = $accountSubs->map(function ($subs) use ($accountSubTotals) {
            $total = $accountSubTotals->get($subs->id);

            $subs->fin_law       = $total->fin_law ?? 0;
            $subs->apply         = $total->apply ?? 0;
            $subs->remain        = $total->remain ?? 0;
            $subs->credit        = $total->credit ?? 0;
            // $subs->total_records = $total->total_records ?? 0;
            // $subs->percent       = $subs->fin_law > 0
            //     ? ($subs->apply / $subs->fin_law) * 100
            //     : 0;

            return $subs;
        });

        return response()->json($accountSubs);
    }
}
