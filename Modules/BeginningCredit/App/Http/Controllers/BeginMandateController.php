<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\BeginCreditMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\BeginCreditMandate;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\InitialBudgetMandate;
use App\Models\BeginCredit\SubAccount;
use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginMandateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BeginCreditMandateDataTable $dataTable, $id)
    {
        $params = decode_params($id);
        $initialBudgetId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialBudget = InitialBudget::findOrFail($initialBudgetId);
        $agency = Agency::all();

        foreach ($initialBudget as $item) {
            $item->id;
        }
        $year = $item->id;

        $data = BeginCreditMandate::where('year', $year)->get();

        request()->merge(['year' => $year]);

        return $dataTable->render('beginningcredit::beginCreditMandate.index', [

            // 'initialBudget' => $initialBudget
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
        $subAccount = SubAccount::all();

        return view('beginningcredit::beginCreditMandate.create')->with('subAccount', $subAccount)->with('params', $params)->with('initialBudget', $initialBudget);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(array $report)
    {
        try {

            DB::transaction(function () use ($report) {
                foreach ($report['sub_accounts'] as $subAccount => $data) {
                    Log::info('Processing Sub Account Key: ' . $subAccount);
                    Log::info('Data: ' . json_encode($data));
                    try {
                        $beginMandate = BeginCreditMandate::updateOrCreate(
                            [
                                'subAccountNumber' => $subAccount,
                                'program' => $data['program'],
                                'year' => $data['year'],
                            ],
                            [
                                'agencyNumber'      => $data['agencyNumber'],     // ✅ Add this
                                'subDepart'         => $data['subDepart'],
                                'txtDescription' => $data['txtDescription'],
                                'fin_law' => number_format($data['fin_law'], 2, '.', ''),
                                'current_loan' => number_format($data['current_loan'], 2, '.', ''),
                                'new_credit_status' => number_format($data['new_credit_status'], 2, '.', ''),
                                'apply' => number_format($data['apply'] ?? 0, 2, '.', ''), // Default to 0 if null
                                'deadline_balance' => number_format($data['deadline_balance'], 2, '.', ''),
                                'credit' => number_format($data['credit'], 2, '.', ''),
                                'law_average' => number_format($data['law_average'], 2, '.', ''),
                                'law_correction' => number_format($data['law_correction'], 2, '.', ''),
                            ]
                        );

                        $this->recalculateAndSaveReport($beginMandate);
                    } catch (\Exception $e) {
                        Log::error("Error storing data mandate for sub_account_key: {$subAccount}, Error: " . $e->getMessage());
                        throw $e; // Re-throw to ensure rollback if needed
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            throw $e; // Optionally re-throw to propagate the error
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
    public function edit($id)
    {
        return view('beginningcredit::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(array $report)
    {
        //
        try {

            DB::transaction(function () use ($report) {
                foreach ($report['sub_accounts'] as $subAccount => $data) {
                    Log::info('Processing Sub Account Key: ' . $subAccount);
                    Log::info('Data: ' . json_encode($data));
                    try {
                        $beginMandate = BeginCreditMandate::updateOrCreate(
                            [
                                'subAccountNumber' => $subAccount,
                                'program' => $data['program'],
                                // 'year' => $data['year'],
                            ],
                            [
                                'agencyNumber'      => $data['agencyNumber'],     // ✅ Add this
                                'subDepart'         => $data['subDepart'],
                                'txtDescription' => $data['txtDescription'],
                                'fin_law' => number_format($data['fin_law'], 2, '.', ''),
                                'current_loan' => number_format($data['current_loan'], 2, '.', ''),
                                'new_credit_status' => number_format($data['new_credit_status'], 2, '.', ''),
                                'apply' => number_format($data['apply'] ?? 0, 2, '.', ''), // Default to 0 if null
                                'deadline_balance' => number_format($data['deadline_balance'], 2, '.', ''),
                                'credit' => number_format($data['credit'], 2, '.', ''),
                                'law_average' => number_format($data['law_average'], 2, '.', ''),
                                'law_correction' => number_format($data['law_correction'], 2, '.', ''),
                            ]
                        );

                        $this->recalculateAndSaveReport($beginMandate);
                    } catch (\Exception $e) {
                        Log::error("Error storing data mandate for sub_account_key: {$subAccount}, Error: " . $e->getMessage());
                        throw $e; // Re-throw to ensure rollback if needed
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            throw $e; // Optionally re-throw to propagate the error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $beginCredit = BeginCreditMandate::where('id', $id)->first();
        if ($beginCredit) {
            $beginCredit->delete();
        }
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('beginCreditMandate.index', ['params' => $params]);
    }

    private function recalculateAndSaveReport(BeginCreditMandate $beginCreditMandate)
    {
        // $newApplyTotal = CertificateData::where('report_key', $report->id)->sum('value_certificate');
        $newApplyTotal = BudgetMandate::where('program', $beginCreditMandate->program)
            ->latest('created_at') // Order by latest created record
            ->value('budget') ?? 0; // Get only the value_certificate column
        $beginCreditMandate->apply = $newApplyTotal;
        $credit = $beginCreditMandate->new_credit_status - $beginCreditMandate->deadline_balance;
        $beginCreditMandate->credit = $credit;
        $beginCreditMandate->deadline_balance = $beginCreditMandate->early_balance + $beginCreditMandate->apply;
        $beginCreditMandate->credit = $beginCreditMandate->new_credit_status - $beginCreditMandate->deadline_balance;
        $beginCreditMandate->law_average = $beginCreditMandate->deadline_balance > 0 ? ($beginCreditMandate->deadline_balance / $beginCreditMandate->fin_law) * 100 : 0;
        $beginCreditMandate->law_correction =  $beginCreditMandate->deadline_balance > 0 ? ($beginCreditMandate->deadline_balance /  $beginCreditMandate->new_credit_status) * 100 : 0;
        $beginCreditMandate->save();
    }
}
