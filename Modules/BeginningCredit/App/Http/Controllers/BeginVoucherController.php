<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\BeginCreditDataTable;
use App\DataTables\BeginVoucherDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Program;
use App\Models\ProgramSub;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BeginVoucherDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        $module = BeginVoucher::query()
            ->join('account_subs', 'begin_vouchers.account_sub_id', '=', 'account_subs.id')
            ->join('agencies', 'begin_vouchers.agency_id', '=', 'agencies.id')
            ->where('begin_vouchers.ministry_id', $id)
            ->select('begin_vouchers.*', 'account_subs.no as account_sub_no', 'agencies.name as agency_name')
            ->get();

        return $dataTable->render('beginningcredit::beginVoucher.index', [
            'data' => $data,
            'params' => $params,
            'module' => $module,
        ]);
    }

    public function getByProgramId(Request $request)
    {
        echo '<option value="">ជ្រើសរើស អនុកម្មវិធី</option>';
        if ($request->program_id) {
            $data = ProgramSub::select('id', 'no', 'decription')->where('program_id', $request->program_id)->get();
            foreach ($data as $d) {
                echo '<option value="' . $d->id . '">' . $d->no . '-' . $d->decription . '</option>';
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::findOrFail($id);
        $agency = Agency::orderBy('no', 'ASC')->get();
        $accountSub = AccountSub::all();

        return view('beginningcredit::beginVoucher.create')
            ->with('accountSub', $accountSub)
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('agency', $agency);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validatedData = $request->validate([
            'cboAgency' => 'required',
            'cboProgramSub' => 'required',
            'cboSubAccount' => 'required',
            'no' => 'required',
            'fin_law' => 'required|numeric|min:0',
            'current_loan' => 'required|numeric|min:0',
            'txtDescription' => 'required',
        ]);
        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', $id)->first();

            $validatedData['internal_increase'] = $validatedData['internal_increase'] ?? 0;
            $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
            $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
            $validatedData['decrease'] = $validatedData['decrease'] ?? 0;
            $validatedData['editorial'] = $validatedData['editorial'] ?? 0;

            $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $validatedData['current_loan'] + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $currentApplyTotal = BudgetVoucher::where('no', $validatedData['no'])->sum('budget');
            $early_balance = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance = $early_balance + $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $validatedData['fin_law'] ? max(-100, min(100, ($deadline_balance / $validatedData['fin_law']) * 100)) : 0;
            $law_correction = $new_credit_status ? max(-100, min(100, ($deadline_balance / $new_credit_status) * 100)) : 0;

            $beginCredit = BeginVoucher::create([
                'ministry_id' => $ministry->id,
                'agency_id' => $validatedData['cboAgency'],
                'program_sub_id' => $validatedData['cboProgramSub'],
                'account_sub_id' => $validatedData['cboSubAccount'],
                'no' => $validatedData['no'],
                'txtDescription' => strip_tags($validatedData['txtDescription']),
                'fin_law' => $validatedData['fin_law'],
                'current_loan' => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply' => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'early_balance' =>  $early_balance,
                'credit' => $credit,
                'law_average' => $law_average,
                'law_correction' => $law_correction,
            ]);

            $this->recalculateAndSaveBeginCredit($beginCredit);

            $totals = [
                'account_subs' => [
                    $validatedData['cboSubAccount'] => [
                        'agency_id'      => $validatedData['cboAgency'],          // ✅ Added
                        'program_sub_id'         => $validatedData['cboProgramSub'],
                        'no' => $validatedData['no'],
                        'ministry_id' => $ministry->id,
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

            return redirect()->route('beginVoucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('beginVoucher.index',  $params);
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

        $beginCredit = BeginVoucher::where('id', $id)->first();

        $agency = Agency::all();
        $subDepart = ProgramSub::all();
        $subAccount = AccountSub::all();

        return view('beginningcredit::beginVoucher.edit', compact(
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
        $id = decode_params($params);
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

            $beginCredit = BeginVoucher::where('id', $id)->first(); // this returns Model, not Collection

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

            $law_average = $validatedData['fin_law'] ? ($deadline_balance / $validatedData['fin_law']) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

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

            return redirect()->route('beginVoucher.index', encode_params($beginCredit->year));
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
        $beginCredit = BeginVoucher::where('id', $id)->first();

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


    private function recalculateAndSaveBeginCredit(BeginVoucher $data)
    {

        $newApplyTotal = BudgetVoucher::where('no', $data->no)
            ->latest('created_at') // Order by latest created record
            ->value('budget') ?? 0; // Get only the budget column
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
