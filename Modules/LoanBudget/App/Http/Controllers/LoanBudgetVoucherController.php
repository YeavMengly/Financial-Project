<?php

namespace Modules\LoanBudget\App\Http\Controllers;

use App\DataTables\BudgetLoans\BudgetVoucherLoanDataTable;
use App\DataTables\BudgetLoans\InitialVoucherLoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Content;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\InitialBudget;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Loans\BudgetVoucherLoan;
use App\Models\Loans\LoanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LoanBudgetVoucherController extends Controller
{
    public function getIndex(InitialVoucherLoanDataTable $dataTable)
    {
        $ministry = Ministry::all();

        return $dataTable->render(
            'loanbudget::voucherLoan.index',
            ['ministry' => $ministry]
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BudgetVoucherLoanDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();

        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $voucherLoan = BudgetVoucherLoan::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('loanbudget::voucher.index', [
            'params' => $params,
            'agency' => $agency,
            'ministry' => $ministry,
            'accountSub' => $accountSub,
            'voucherLoan' => $voucherLoan,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        return view('loanbudget::voucher.create')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('accountSub', $accountSub)
            ->with('agency', $agency);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validatedData = $request->validate([
            'cboAgency'           => 'required|integer',
            'cboSubAccount'       => 'required|integer',
            'no'                  => 'required|string',
            'internal_increase'   => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease'            => 'nullable|numeric|min:0',
            'editorial'           => 'nullable|numeric|min:0',
            'txtDescription'      => 'required',
        ]);

        foreach (['internal_increase', 'unexpected_increase', 'additional_increase', 'decrease', 'editorial'] as $k) {
            $validatedData[$k] = $validatedData[$k] ?? 0;
        }

        DB::beginTransaction();
        try {
            $id = decode_params($params);
            $ministry = Ministry::where('id', $id)->first();

            $beginVoucher = BeginVoucher::where('no', $validatedData['no'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginVoucher) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }
            $total_increase = $validatedData['internal_increase']
                + $validatedData['unexpected_increase']
                + $validatedData['additional_increase'];

            BudgetVoucherLoan::create([
                'ministry_id'        => $ministry->id,
                'agency_id'          => $validatedData['cboAgency'],
                'account_sub_id'     => $validatedData['cboSubAccount'],
                'no'                 => $beginVoucher->no,
                'internal_increase'  => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease'           => $validatedData['decrease'],
                'editorial'          => $validatedData['editorial'],
                'total_increase'     => $total_increase,
                'txtDescription'     => strip_tags($validatedData['txtDescription']),
            ]);

            $agg = BudgetVoucherLoan::query()
                ->where('ministry_id', $ministry->id)
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('no', $validatedData['no'])
                ->selectRaw('
                COALESCE(SUM(internal_increase),0)   AS internal_increase_sum,
                COALESCE(SUM(unexpected_increase),0) AS unexpected_increase_sum,
                COALESCE(SUM(additional_increase),0) AS additional_increase_sum,
                COALESCE(SUM(decrease),0)            AS decrease_sum,
                COALESCE(SUM(editorial),0)           AS editorial_sum
            ')
                ->first();

            $totalIncreaseSum = (float)$agg->internal_increase_sum
                + (float)$agg->unexpected_increase_sum
                + (float)$agg->additional_increase_sum;

            $currentApplyTotal = BudgetVoucher::where('no', $validatedData['no'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->sum('budget');

            $early_balance    = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance = $early_balance + $currentApplyTotal;

            $new_credit_status = $beginVoucher->current_loan
                + $totalIncreaseSum
                - ((float)$agg->decrease_sum + (float)$agg->editorial_sum);

            $credit        = $new_credit_status - $deadline_balance;
            $law_average   = $beginVoucher->fin_law ? ($deadline_balance / $beginVoucher->fin_law) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

            $beginVoucher->update([
                'current_loan'     => $beginVoucher->current_loan,
                'new_credit_status' => $new_credit_status,
                'apply'            => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit'           => $credit,
                'law_average'      => $law_average,
                'law_correction'   => $law_correction,
            ]);

            DB::commit();
            flash()->translate('en')->option('timeout', 2000)
                ->success('success_msg', 'successful')->flash();

            return redirect()->route('voucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('voucher.index', $params);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('loanbudget::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $module = BudgetVoucherLoan::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('loanbudget::voucher.edit')
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('module', $module)
            ->with('agency', $agency)
            ->with('accountSub', $accountSub);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $params, $id)
    {
        $validatedData = $request->validate([
            'cboAgency' => 'required',
            'cboSubAccount' => 'required',
            'no' => 'required',
            'internal_increase' => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease' => 'nullable|numeric|min:0',
            'editorial' => 'nullable|numeric|min:0',
            'txtDescription' => 'required',
        ]);

        $id = decode_params($id);
        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $voucherLoan = BudgetVoucherLoan::where('no', $validatedData['no'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $beginVoucher = BeginVoucher::where('no', $validatedData['no'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $fin_law = $beginVoucher->fin_law;
            $current_loan = $beginVoucher->current_loan;

            $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $current_loan + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $currentApplyTotal = BudgetVoucher::where('no', $validatedData['no'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('ministry_id', $ministry->id)
                ->sum('budget');

            $deadline_balance = $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $fin_law ? ($deadline_balance / $fin_law) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

            $voucherLoan->update([
                'agency_id' => $validatedData['cboAgency'],
                'account_sub_id' => $validatedData['cboSubAccount'],
                'no' => $beginVoucher->no,
                'internal_increase' => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease' => $validatedData['decrease'],
                'editorial' => $validatedData['editorial'],
                'total_increase' => $total_increase,
                'txtDescription' => strip_tags($validatedData['txtDescription']),
            ]);

            $beginVoucher->update([
                'current_loan' => $beginVoucher->current_loan,
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('voucher.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('voucher.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        DB::beginTransaction();
        try {
            $id   = decode_params($id);
            $ministry = Ministry::where('id', decode_params($params))->first();
            $voucherLoan = BudgetVoucherLoan::where('id', $id)->first();

            $beginVoucher = BeginVoucher::query()
                ->where('no', $voucherLoan->no)
                ->where('account_sub_id', $voucherLoan->account_sub_id)
                ->where('agency_id',     $voucherLoan->agency_id)
                ->where('ministry_id',   $ministry->id)
                ->first();

            $voucherLoan->delete();

            if ($beginVoucher) {
                $bvl = BudgetVoucherLoan::query()
                    ->where('ministry_id',   $ministry->id)
                    ->where('agency_id',     $beginVoucher->agency_id)
                    ->where('account_sub_id', $beginVoucher->account_sub_id)
                    ->where('no',            $beginVoucher->no)
                    ->selectRaw('
                    COALESCE(SUM(internal_increase),0)   AS internal_increase_sum,
                    COALESCE(SUM(unexpected_increase),0) AS unexpected_increase_sum,
                    COALESCE(SUM(additional_increase),0) AS additional_increase_sum,
                    COALESCE(SUM(decrease),0)            AS decrease_sum,
                    COALESCE(SUM(editorial),0)           AS editorial_sum
                ')->first();

                $totalIncreaseSum = (float)$bvl->internal_increase_sum
                    + (float)$bvl->unexpected_increase_sum
                    + (float)$bvl->additional_increase_sum;

                $currentApplyTotal = BudgetVoucher::query()
                    ->where('no',            $beginVoucher->no)
                    ->where('account_sub_id', $beginVoucher->account_sub_id)
                    ->where('agency_id',     $beginVoucher->agency_id)
                    ->where('ministry_id',   $ministry->id)
                    ->sum('budget');

                $early_balance    = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
                $deadline_balance = $early_balance + $currentApplyTotal;

                $new_credit_status = $beginVoucher->current_loan
                    + $totalIncreaseSum
                    - ((float)$bvl->decrease_sum + (float)$bvl->editorial_sum);

                $credit         = $new_credit_status - $deadline_balance;
                $law_average    = $beginVoucher->fin_law ? ($deadline_balance / $beginVoucher->fin_law) * 100 : 0;
                $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

                $beginVoucher->update([
                    'new_credit_status' => $new_credit_status,
                    'apply'             => $currentApplyTotal,
                    'deadline_balance'  => $deadline_balance,
                    'credit'            => $credit,
                    'law_average'       => $law_average,
                    'law_correction'    => $law_correction,
                ]);
            }

            DB::commit();
            flash()->translate('en')->option('timeout', 2000)
                ->success('success_msg', 'successful')->flash();

            return redirect()->route('voucher.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Voucher delete error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('voucher.index', $params);
        }
    }
}
