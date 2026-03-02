<?php

namespace Modules\LoanBudget\App\Http\Controllers;

use App\DataTables\BudgetLoans\BudgetMandateLoanDataTable;
use App\DataTables\BudgetLoans\InitialMandateLoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginMandate;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\Content\Cluster;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanBudgetMandateController extends Controller
{
    public function getIndex(InitialMandateLoanDataTable $dataTable)
    {
        $ministry = Ministry::all();
        return $dataTable->render('loanbudget::mandateLoan.index', ['mandateLoan' => $ministry]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BudgetMandateLoanDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();

        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $mandateLoan = BudgetMandateLoan::where('ministry_id', $ministry->id)->get();;

        return $dataTable->render('loanbudget::mandate.index', [
            'params' => $params,
            'agency' => $agency,
            'ministry' => $ministry,
            'accountSub' => $accountSub,
            'mandateLoan' => $mandateLoan,
        ]);
    }

    /**
     * AJAX: Fetch program sub-options by program ID request.
     */
    public function getByProgramId(Request $request)
    {
        if ($request->program_id) {
            $data = ProgramSub::select('id', 'program_id', 'no', 'decription')
                ->where('program_id', $request->program_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByProgramId(Request $request)
    {
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = ProgramSub::select('id', 'no', 'decription')
            ->where('program_id', $request->program_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    public function getByAgency(Request $request)
    {
        if ($request->program_id) {
            $data = Agency::select('id', 'program_id', 'no', 'name')
                ->where('program_id', $request->program_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->name}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByAgency(Request $request)
    {
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = Agency::select('id', 'no', 'name')
            ->where('program_id', $request->program_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->name}</option>";
        }

        return response($html);
    }

    public function getByProgramSubId(Request $request)
    {
        if ($request->program_sub_id) {

            $data = Cluster::select('id', 'program_sub_id', 'no', 'decription')
                ->where('program_sub_id', $request->program_sub_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = ((string)$selectedId === (string)$d->id) ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByProgramSubId(Request $request)
    {
        if (!$request->program_sub_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = Cluster::select('id', 'no', 'decription')
            ->where('program_sub_id', $request->program_sub_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program   = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        return view('loanbudget::mandate.create')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('program', $program);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validatedData = $request->validate([
            'cboProgram'     => 'required',
            'cboProgramSub'  => 'required',
            'cboCluster'     => 'required',
            'cboAgency'           => 'required|integer',
            'cboSubAccount'       => 'required|integer',
            'internal_increase'   => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease'            => 'nullable|numeric|min:0',
            'editorial'           => 'nullable|numeric|min:0',
            'txtDescription'      => 'nullable',
        ]);

        foreach (['internal_increase', 'unexpected_increase', 'additional_increase', 'decrease', 'editorial'] as $k) {
            $validatedData[$k] = $validatedData[$k] ?? 0;
        }

        DB::beginTransaction();
        try {
            $id = decode_params($params);
            $ministry = Ministry::where('id', $id)->first();
            $program    = Program::where('id', $validatedData['cboProgram'])->first();
            $programSub = ProgramSub::where('program_id', $program->id)
                ->where('id', $validatedData['cboProgramSub'])
                ->first();
            $cluster    = Cluster::where('id', $validatedData['cboCluster'])
                ->where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])->first();

            $valueNo = $ministry->no . $program->no .  $programSub->no . $cluster->no;

            $beginMandate = BeginMandate::where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])
                ->where('cluster_id', $validatedData['cboCluster'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginMandate) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }
            $total_increase = $validatedData['internal_increase']
                + $validatedData['unexpected_increase']
                + $validatedData['additional_increase'];

            BudgetMandateLoan::create([
                'ministry_id'        => $ministry->id,
                'agency_id'          => $validatedData['cboAgency'],
                'program_id'          => $validatedData['cboProgram'],
                'program_sub_id'      => $validatedData['cboProgramSub'],
                'cluster_id'          => $validatedData['cboCluster'],
                'account_sub_id'     => $validatedData['cboSubAccount'],
                'no'                  => $valueNo,
                'internal_increase'  => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease'           => $validatedData['decrease'],
                'editorial'          => $validatedData['editorial'],
                'total_increase'     => $total_increase,
                'txtDescription'     => strip_tags($validatedData['txtDescription']),
            ]);

            $agg = BudgetMandateLoan::query()
                ->where('ministry_id', $ministry->id)
                ->where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])
                ->where('cluster_id', $validatedData['cboCluster'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
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

            $currentApplyTotal = BudgetMandate::where('no', $valueNo)
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->sum('budget');

            $early_balance    = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance = $early_balance + $currentApplyTotal;

            $new_credit_status = $beginMandate->current_loan
                + $totalIncreaseSum
                - ((float)$agg->decrease_sum + (float)$agg->editorial_sum);

            $credit        = $new_credit_status - $deadline_balance;
            $law_average   = $beginMandate->fin_law ? ($deadline_balance / $beginMandate->fin_law) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

            $beginMandate->update([
                'current_loan'     => $beginMandate->current_loan,
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

            return redirect()->route('mandate.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('mandate.index', $params);
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

        $module = BudgetMandateLoan::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $program     = Program::where('ministry_id', $ministry->id)->get();
        $programId   = Program::findOrFail($module->program_id);
        $programSub  = ProgramSub::where('ministry_id', $ministry->id)
            ->where('program_id', $module->program_id)->get();

        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();


        return view('loanbudget::mandate.edit')
            ->with('params', $params)
            ->with('agency', $agency)
            ->with('program', $program)
            ->with('programId', $programId)
            ->with('programSub', $programSub)
            ->with('accountSub', $accountSub)
            ->with('module', $module);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validatedData = $request->validate([
            'cboProgram'     => 'required',
            'cboProgramSub'  => 'required',
            'cboCluster'     => 'required',
            'cboAgency' => 'required',
            'cboSubAccount' => 'required',
            'internal_increase' => 'nullable|numeric|min:0',
            'unexpected_increase' => 'nullable|numeric|min:0',
            'additional_increase' => 'nullable|numeric|min:0',
            'decrease' => 'nullable|numeric|min:0',
            'editorial' => 'nullable|numeric|min:0',
            'txtDescription' => 'nullable',
        ]);

        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', decode_params($params))->first();

            $program    = Program::where('id', $validatedData['cboProgram'])
                ->where('ministry_id', $ministry->id)->first();

            $programSub = ProgramSub::where('program_id', $program->id)
                ->where('id', $validatedData['cboProgramSub'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $cluster    = Cluster::where('id', $validatedData['cboCluster'])
                ->where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $mandateLoan = BudgetMandateLoan::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $beginMandate = BeginMandate::where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])
                ->where('cluster_id', $validatedData['cboCluster'])
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginMandate) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យត្រឹមត្រូវ', 'បញ្ហា')->flash();
                return back()->withInput();
            }

            $fin_law = $beginMandate->fin_law;
            $current_loan = $beginMandate->current_loan;

            $total_increase = $validatedData['internal_increase'] + $validatedData['unexpected_increase'] + $validatedData['additional_increase'];
            $new_credit_status = $current_loan + $total_increase - $validatedData['decrease'] - $validatedData['editorial'];

            $valueNo = $ministry->no . $program->no .  $programSub->no . $cluster->no;

            $currentApplyTotal = BudgetMandate::where('no', $valueNo)
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('ministry_id', $ministry->id)
                ->sum('budget');

            $deadline_balance = $currentApplyTotal;
            $credit = $new_credit_status - $deadline_balance;

            $law_average = $fin_law ? ($deadline_balance / $fin_law) * 100 : 0;
            $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

            $mandateLoan->update([
                'agency_id' => $validatedData['cboAgency'],
                'program_id'          => $validatedData['cboProgram'],
                'program_sub_id'      => $validatedData['cboProgramSub'],
                'cluster_id'          => $validatedData['cboCluster'],
                'account_sub_id' => $validatedData['cboSubAccount'],
                'no' => $beginMandate->no,
                'internal_increase' => $validatedData['internal_increase'],
                'unexpected_increase' => $validatedData['unexpected_increase'],
                'additional_increase' => $validatedData['additional_increase'],
                'decrease' => $validatedData['decrease'],
                'editorial' => $validatedData['editorial'],
                'total_increase' => $total_increase,
                'txtDescription' => strip_tags($validatedData['txtDescription']),
            ]);

            $beginMandate->update([
                'current_loan' => $beginMandate->current_loan,
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

            return redirect()->route('mandate.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('mandate.index', $params);
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
            $mandateLoan = BudgetMandateLoan::where('id', $id)->first();

            $beginMandate = BeginMandate::query()
                ->where('no', $mandateLoan->no)
                ->where('account_sub_id', $mandateLoan->account_sub_id)
                ->where('agency_id',     $mandateLoan->agency_id)
                ->where('ministry_id',   $ministry->id)
                ->first();

            $mandateLoan->delete();

            if ($beginMandate) {
                $bvl = BudgetMandateLoan::query()
                    ->where('ministry_id',   $ministry->id)
                    ->where('agency_id',     $beginMandate->agency_id)
                    ->where('account_sub_id', $beginMandate->account_sub_id)
                    ->where('no',            $beginMandate->no)
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

                $currentApplyTotal = BudgetMandate::query()
                    ->where('no',            $beginMandate->no)
                    ->where('account_sub_id', $beginMandate->account_sub_id)
                    ->where('agency_id',     $beginMandate->agency_id)
                    ->where('ministry_id',   $ministry->id)
                    ->sum('budget');

                $early_balance    = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
                $deadline_balance = $early_balance + $currentApplyTotal;

                $new_credit_status = $beginMandate->current_loan
                    + $totalIncreaseSum
                    - ((float)$bvl->decrease_sum + (float)$bvl->editorial_sum);

                $credit         = $new_credit_status - $deadline_balance;
                $law_average    = $beginMandate->fin_law ? ($deadline_balance / $beginMandate->fin_law) * 100 : 0;
                $law_correction = $new_credit_status ? ($deadline_balance / $new_credit_status) * 100 : 0;

                $beginMandate->update([
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

            return redirect()->route('mandate.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mandate delete error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('mandate.index', $params);
        }
    }
}
