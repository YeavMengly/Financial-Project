<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\BeginMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCreditMandate;
use App\Models\BeginCredit\BeginMandate;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginMandateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BeginMandateDataTable $dataTable, $params)
    {
        return $dataTable->render('beginningcredit::beginMandate.index', [
            'params' => $params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::findOrFail($id);
        $subAccount = AccountSub::all();

        return view('beginningcredit::beginMandate.create')
            ->with('subAccount', $subAccount)
            ->with('params', $params)
            ->with('ministry', $ministry);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(array $report)
    {
        try {

            DB::transaction(function () use ($report) {
                foreach ($report['account_subs'] as $subAccount => $data) {
                    Log::info('Processing Sub Account Key: ' . $subAccount);
                    Log::info('Data: ' . json_encode($data));
                    try {
                        $beginMandate = BeginMandate::updateOrCreate(
                            [
                                'account_sub_id' => $subAccount,
                                'no' => $data['no'],
                                'ministry_id' => $data['ministry_id'],
                            ],
                            [
                                'agency_id'      => $data['agency_id'],     // ✅ Add this
                                'program_sub_id'         => $data['program_sub_id'],
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
                foreach ($report['account_subs'] as $subAccount => $data) {
                    Log::info('Processing Sub Account Key: ' . $subAccount);
                    Log::info('Data: ' . json_encode($data));
                    try {
                        $beginMandate = BeginMandate::updateOrCreate(
                            [
                                'account_sub_id' => $subAccount,
                                'no' => $data['no'],
                                'ministry_id' => $data['ministry_id'],
                            ],
                            [
                                'agency_id'      => $data['agency_id'],     // ✅ Add this
                                'program_sub_id'         => $data['program_sub_id'],
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
        $beginCredit = BeginMandate::where('id', $id)->first();
        $beginCredit->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('beginMandate.index', ['params' => $params]);
    }

    private function recalculateAndSaveReport(BeginMandate $data)
    {
        $newApplyTotal = BudgetMandate::where('no', $data->no)
            ->latest('created_at')
            ->value('budget') ?? 0;
        $data->apply = $newApplyTotal;
        $credit = $data->new_credit_status - $data->deadline_balance;
        $data->credit = $credit;
        $data->deadline_balance = $data->early_balance + $data->apply;
        $data->credit = $data->new_credit_status - $data->deadline_balance;
        $data->law_average = $data->deadline_balance > 0 ? ($data->deadline_balance / $data->fin_law) * 100 : 0;
        $data->law_correction =  $data->deadline_balance > 0 ? ($data->deadline_balance /  $data->new_credit_status) * 100 : 0;
        $data->save();
    }
}
