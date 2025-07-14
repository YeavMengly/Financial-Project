<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\BeginCreditDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\SubAccount;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\SubDepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginningCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BeginCreditDataTable $dataTable, $id)
    {
        $params = decode_params($id);
        $data = BeginCredit::where('year', $params)->get();
        request()->merge(['year' => $params]);
        $agency = Agency::findOrFail($params);

        return $dataTable->render('beginningcredit::beginCredit.index', [
            'params' => $params,
            'data' => $data,
            'agency' => $agency
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $params = decode_params($id);
        $initialBudget = InitialBudget::findOrFail($params);
        $agency = Agency::all();
        $subDepart = SubDepart::all();
        $subAccount = SubAccount::all();

        return view('beginningcredit::beginCredit.create')->with('subAccount', $subAccount)->with('params', $params)->with('initialBudget', $initialBudget)->with('agency', $agency)->with('subDepart', $subDepart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $params = decode_params($id);

        $initialBudget = InitialBudget::findOrFail($params);

        foreach ($initialBudget as $item) {
            $item->id;
        }

        $year =  $item->id;


        $validatedData = $request->validate([
            'cboAgency' => 'required|exists:agencies,agencyNumber',
            'cboSubDepart' => 'required|exists:sub_departs,subDepart',
            'cboSubAccountNumber' => 'required',
            'program' => 'required',
            'fin_law' => 'required|numeric|min:0',
            'current_loan' => 'required|numeric|min:0',
            'txtDescription' => 'required',
        ]);

        DB::beginTransaction();

        try {

            if ($validatedData['current_loan'] < 0) {
                return redirect()->back()->withErrors([
                    'current_loan' => 'ចំនួនទុនបងវិញមិនអាចមានតម្លៃអវិជ្ជមាន។',
                ])->withInput();
            }

            if ($validatedData['fin_law'] < $validatedData['current_loan']) {
                return redirect()->back()->withErrors([
                    'fin_law' => 'ច្បាប់ហិរញ្ញវត្ថុត្រូវតែធំជាងឬស្មើចំនួនទុនបងវិញ។',
                ])->withInput();
            }


            // Optional fields default
            $validatedData['internal_increase'] = $validatedData['internal_increase'] ?? 0;
            $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
            $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
            $validatedData['decrease'] = $validatedData['decrease'] ?? 0;
            $validatedData['editorial'] = $validatedData['editorial'] ?? 0;

            $total_increase = 0;
            // $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $validatedData['current_loan'] + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            // Check for duplicate
            // $exists = BeginCredit::where('subAccountNumber', $validatedData['cboSubAccountNumber'])
            //     ->where('program', $validatedData['program'])
            //     ->exists();

            // if ($exists) {
            //     return redirect()->back()->withErrors([
            //         'program' => 'Sub-account and program already exist.',
            //     ])->withInput();
            // }

            $currentApplyTotal = BudgetVoucher::where('program', $validatedData['program'])->sum('budget');
            $early_balance = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance = $early_balance + $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $validatedData['fin_law'] ? max(-100, min(100, ($deadline_balance / $validatedData['fin_law']) * 100)) : 0;
            $law_correction = $new_credit_status ? max(-100, min(100, ($deadline_balance / $new_credit_status) * 100)) : 0;

            $beginCredit = BeginCredit::create([
                'agencyNumber' => $validatedData['cboAgency'],
                'subDepart' => $validatedData['cboSubDepart'],
                'subAccountNumber' => $validatedData['cboSubAccountNumber'],
                'program' => $validatedData['program'],
                'txtDescription' => strip_tags($validatedData['txtDescription']),
                'fin_law' => $validatedData['fin_law'],
                'current_loan' => $validatedData['current_loan'],
                'year' => $year,
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
            ]);

            $this->recalculateAndSaveBeginCredit($beginCredit);

            // Prepare data for BeginMandateController
            $totals = [
                'sub_accounts' => [
                    $validatedData['cboSubAccountNumber'] => [
                        'agencyNumber'      => $validatedData['cboAgency'],          // ✅ Added
                        'subDepart'         => $validatedData['cboSubDepart'],
                        'program' => $validatedData['program'],
                        'year' =>  $year,
                        'txtDescription' => strip_tags($validatedData['txtDescription']),
                        'fin_law' => $validatedData['fin_law'],
                        'current_loan' => $validatedData['current_loan'],
                        'new_credit_status' => $new_credit_status,
                        'apply' => $currentApplyTotal ?? 0,
                        'deadline_balance' => $deadline_balance,
                        'credit' => $credit,
                        'law_average' => $law_average,
                        'law_correction' => $law_correction,
                    ],
                ],
            ];

            // Save to BeginCreditMandate
            try {
                app(\Modules\BeginningCredit\App\Http\Controllers\BeginMandateController::class)->store($totals);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                    ->flash();

                return back();
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('beginCredit.index', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('beginCredit.index', $id);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('beginningcredit::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id = decode_params($params);

        $beginCredit = BeginCredit::where('id', $id)->first();

        $agency = Agency::all();
        $subDepart = SubDepart::all();
        $subAccount = SubAccount::all();

        return view('beginningcredit::beginCredit.edit', compact(
            'agency',
            'subDepart',
            'subAccount',
            'beginCredit',
            'params',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {

        // dd($params, $paramsId);


        $id = decode_params($params); // year (probably encoded)


        // $year = $initialBudget->year;

        $validatedData = $request->validate([
            'cboAgency' => 'required|exists:agencies,agencyNumber',
            'cboSubDepart' => 'required|exists:sub_departs,subDepart',
            'cboSubAccountNumber' => 'required',
            'program' => 'required',
            'fin_law' => 'required|numeric|min:0',
            'current_loan' => 'required|numeric|min:0',
            'txtDescription' => 'required',
        ]);


        DB::beginTransaction();

        try {

            $beginCredit = BeginCredit::where('id', $id)->first(); // this returns Model, not Collection

            // dd($beginCredit);

            if ($validatedData['current_loan'] < 0) {
                return redirect()->back()->withErrors([
                    'current_loan' => 'ចំនួនទុនបងវិញមិនអាចមានតម្លៃអវិជ្ជមាន។',
                ])->withInput();
            }

            if ($validatedData['fin_law'] < $validatedData['current_loan']) {
                return redirect()->back()->withErrors([
                    'fin_law' => 'ច្បាប់ហិរញ្ញវត្ថុត្រូវតែធំជាងឬស្មើចំនួនទុនបងវិញ។',
                ])->withInput();
            }

            // Optional fields
            $validatedData['internal_increase'] = $validatedData['internal_increase'] ?? 0;
            $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
            $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
            $validatedData['decrease'] = $validatedData['decrease'] ?? 0;
            $validatedData['editorial'] = $validatedData['editorial'] ?? 0;

            $total_increase = 0;
            $new_credit_status = $validatedData['current_loan'] + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $currentApplyTotal = BudgetVoucher::where('program', $validatedData['program'])->sum('budget');
            $early_balance = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance = $early_balance + $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $validatedData['fin_law'] ? max(-100, min(100, ($deadline_balance / $validatedData['fin_law']) * 100)) : 0;
            $law_correction = $new_credit_status ? max(-100, min(100, ($deadline_balance / $new_credit_status) * 100)) : 0;

            // Update the BeginCredit
            $beginCredit->update([
                'agencyNumber' => $validatedData['cboAgency'],
                'subDepart' => $validatedData['cboSubDepart'],
                'subAccountNumber' => $validatedData['cboSubAccountNumber'],
                'program' => $validatedData['program'],
                'txtDescription' => strip_tags($validatedData['txtDescription']),
                'fin_law' => $validatedData['fin_law'],
                'current_loan' => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
            ]);

            $this->recalculateAndSaveBeginCredit($beginCredit);

            // Prepare and store BeginMandate
            $totals = [
                'sub_accounts' => [
                    $validatedData['cboSubAccountNumber'] => [
                        'agencyNumber'      => $validatedData['cboAgency'],
                        'subDepart'         => $validatedData['cboSubDepart'],
                        'program'           => $validatedData['program'],
                        'txtDescription'    => strip_tags($validatedData['txtDescription']),
                        'fin_law'           => $validatedData['fin_law'],
                        'current_loan'      => $validatedData['current_loan'],
                        'new_credit_status' => $new_credit_status,
                        'apply'             => $currentApplyTotal ?? 0,
                        'deadline_balance'  => $deadline_balance,
                        'credit'            => $credit,
                        'law_average'       => $law_average,
                        'law_correction'    => $law_correction,
                    ],
                ],
            ];

            app(\Modules\BeginningCredit\App\Http\Controllers\BeginMandateController::class)->update($totals);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('beginCredit.index', encode_params($beginCredit->year));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាព: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $beginCredit = BeginCredit::where('id', $id)->first();

        if ($beginCredit) {
            $beginCredit->delete();
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        // Redirect with required `params` value
        return back();
    }

    // public function agencyCredits($params)
    // {


    //     // Show credits or forward to a filtered index view
    //     return view('beginningcredit::beginCredit.index', compact('agency'));
    // }

    private function recalculateAndSaveBeginCredit(BeginCredit $beginCredit)
    {

        $newApplyTotal = BudgetVoucher::where('program', $beginCredit->program)
            ->latest('created_at') // Order by latest created record
            ->value('budget') ?? 0; // Get only the budget column
        $beginCredit->apply = $newApplyTotal;
        $credit = $beginCredit->new_credit_status - $beginCredit->deadline_balance;
        $beginCredit->credit = $credit;
        $beginCredit->deadline_balance = $beginCredit->early_balance + $beginCredit->apply;
        $beginCredit->credit = $beginCredit->new_credit_status - $beginCredit->deadline_balance;
        $beginCredit->law_average = $beginCredit->deadline_balance > 0 ? ($beginCredit->deadline_balance / $beginCredit->fin_law) * 100 : 0;
        $beginCredit->law_correction =  $beginCredit->deadline_balance > 0 ? ($beginCredit->deadline_balance /  $beginCredit->new_credit_status) * 100 : 0;

        $beginCredit->save();
    }
}
