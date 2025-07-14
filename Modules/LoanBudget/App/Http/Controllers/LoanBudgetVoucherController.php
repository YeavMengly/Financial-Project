<?php

namespace Modules\LoanBudget\App\Http\Controllers;

use App\DataTables\BudgetLoans\BudgetVoucherLoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Loans\BudgetVoucherLoan;
use App\Models\Loans\LoanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LoanBudgetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BudgetVoucherLoanDataTable $dataTable, $id)
    {
        $params = decode_params($id);
        $initialVoucherId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialBudget = InitialBudget::findOrFail($initialVoucherId);

        foreach ($initialBudget as $item) {
            $item->id;
        }

        $year = $item->id;

        request()->merge(['year' => $year]);
        return $dataTable->render('loanbudget::voucher.index', [
            'params' => $params,
            'initialBudget' => $initialBudget,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $params = decode_params($id);
        $initialVoucher = InitialBudget::findOrFail($params);
        $beginCredit = BeginCredit::where('year', $params)->with('agency')->get();


        return view('loanbudget::voucher.create')->with('beginCredit', $beginCredit)->with('params', $params)->with('initialVoucher', $initialVoucher);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $id)
    {

        $params = decode_params($id);
        $initialVoucherId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialVoucher = InitialBudget::findOrFail($initialVoucherId);

        foreach ($initialVoucher as $item) {
            $item->id;
        }

        $year = $item->id;

        $validatedData = $request->validate([
            'cboAgency'  => 'required',
            'cboSubDepart'  => 'required',
            'subAccountNumber' => 'required|numeric',
            'program' => 'required|exists:begin_credits,program',
            'internal_increase' => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease' => 'nullable|numeric|min:0',
            'editorial' => 'nullable|numeric|min:0',
            'txtDescription' => 'required',
        ]);

        $validatedData['internal_increase'] = $validatedData['internal_increase'] ?? 0;
        $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
        $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
        $validatedData['decrease'] = $validatedData['decrease'] ?? 0;
        $validatedData['editorial'] = $validatedData['editorial'] ?? 0;

        DB::beginTransaction();

        try {
            $beginCredit = BeginCredit::where('program', $validatedData['program'])
                ->where('subAccountNumber', $validatedData['subAccountNumber'])
                ->first(); // ✅ returns a single model instance

            if (!$beginCredit) {
                return redirect()->back()->withErrors([
                    'program' => 'The selected report does not exist.'
                ])->withInput();
            }

            $current_loan = $beginCredit->current_loan;
            $fin_law = $beginCredit->fin_law;
            $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $current_loan + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $currentApplyTotal = BudgetVoucher::where('program', $validatedData['program'])
                ->where('subAccountNumber', $validatedData['subAccountNumber'])
                ->sum('budget');  // changed 'report_key' to 'program' if appropriate
            $deadline_balance = $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $fin_law ? max(-100, min(100, ($deadline_balance / $fin_law) * 100)) : 0;
            $law_correction = $new_credit_status ? max(-100, min(100, ($deadline_balance / $new_credit_status) * 100)) : 0;

            BudgetVoucherLoan::create([
                'agencyNumber' => $validatedData['cboAgency'],
                'subDepart' => $validatedData['cboSubDepart'],
                'year' => $year,
                'subAccountNumber' => $validatedData['subAccountNumber'],
                'program' => $beginCredit->program,
                'internal_increase' => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease' => $validatedData['decrease'],
                'editorial' => $validatedData['editorial'],
                'total_increase' => $total_increase,
                'txtDescription' => strip_tags($validatedData['txtDescription']),
            ]);

            $beginCredit->update([
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
                'current_loan' => $current_loan,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('voucher.index', $id);
        } catch (\Exception $e) {

            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('voucher.index', $id);
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
    public function edit($params)
    {
        $id = decode_params($params);

        $voucher = BudgetVoucherLoan::where('id', $id)->first();
        $beginCredit = BeginCredit::all(); // For select input

        return view('loanbudget::voucher.edit')->with('beginCredit', $beginCredit)->with('voucher', $voucher)->with('params', $params);
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $params)
    {
        $id = decode_params($params);

        $validatedData = $request->validate([
            'subAccountNumber' => 'required|numeric',
            'program' => 'required|exists:begin_credits,program',
            'internal_increase' => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease' => 'nullable|numeric|min:0',
            'editorial' => 'nullable|numeric|min:0',
            'txtDescription' => 'required',
        ]);

        foreach (['internal_increase', 'unexpected_increase', 'additional_increase', 'decrease', 'editorial'] as $key) {
            $validatedData[$key] = $validatedData[$key] ?? 0;
        }

        DB::beginTransaction();

        try {
            $voucherLoan = BudgetVoucherLoan::findOrFail($id);

            $beginCredit = BeginCredit::where('program', $validatedData['program'])
                ->where('subAccountNumber', $validatedData['subAccountNumber'])
                ->firstOrFail();

            if (!$beginCredit->subAccountNumber) {
                throw new \Exception('មិនមាន អនុគណនី ឬកូដកម្មវិធី។');
            }

            $current_loan = $beginCredit->current_loan;
            $fin_law = $beginCredit->fin_law;

            $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $current_loan + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $currentApplyTotal = BudgetVoucher::where('program', $validatedData['program'])
                ->where('subAccountNumber', $validatedData['subAccountNumber'])
                ->sum('budget');

            $deadline_balance = $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $fin_law ? ($deadline_balance / $fin_law) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

            $voucherLoan->update([
                'subAccountNumber' => $validatedData['subAccountNumber'],
                'program' => $beginCredit->program,
                'internal_increase' => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease' => $validatedData['decrease'],
                'editorial' => $validatedData['editorial'],
                'total_increase' => $total_increase,
                'txtDescription' => strip_tags($validatedData['txtDescription']),
            ]);

            $beginCredit->update([
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
                'current_loan' => $current_loan,
            ]);

            DB::commit();

            flash()->translate('en')->option('timeout', 2000)->success('success_msg', 'successful')->flash();
            return redirect()->route('voucher.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            flash()->translate('en')->option('timeout', 2000)->error($e->getMessage(), 'បញ្ហា')->flash();
            return redirect()->route('voucher.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($params)
    {
        $id = decode_params($params);

        try {
            $voucherLoan = BudgetVoucherLoan::findOrFail($id);

            $beginCredit = BeginCredit::where('subAccountNumber', $voucherLoan->subAccountNumber)
                ->where('program', $voucherLoan->program)
                ->first(); // ✅ This returns a single model

            // $beginCredit->program; // ✅ This works


            if ($beginCredit) {

                $beginCredit->program;

                $total_increase = $voucherLoan->internal_increase + $voucherLoan->unexpected_increase + $voucherLoan->additional_increase;
                $new_credit_status = $beginCredit->new_credit_status - $total_increase + $voucherLoan->decrease + $voucherLoan->editorial;

                $currentApplyTotal = BudgetVoucher::where('program', $beginCredit->program)
                    ->where('subAccountNumber', $beginCredit->subAccountNumber)
                    ->sum('budget');

                $deadline_balance = $currentApplyTotal;
                $credit = $new_credit_status - $deadline_balance;

                $law_average = $beginCredit->fin_law ? ($deadline_balance / $beginCredit->fin_law) * 100 : 0;
                $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

                $beginCredit->update([
                    'new_credit_status' => $new_credit_status,
                    'apply' => $currentApplyTotal,
                    'deadline_balance' => $deadline_balance,
                    'credit' => $credit,
                    'law_average' => $law_average,
                    'law_correction' => $law_correction,
                ]);
            }

            $voucherLoan->delete();

            flash()->translate('en')->option('timeout', 2000)->success('success_msg', 'successful')->flash();
            return redirect()->route('voucher.index');
        } catch (\Exception $e) {
            Log::error('Voucher delete error: ' . $e->getMessage());
            flash()->translate('en')->option('timeout', 2000)->error($e->getMessage(), 'បញ្ហា')->flash();
            return redirect()->route('voucher.index');
        }
    }
}
