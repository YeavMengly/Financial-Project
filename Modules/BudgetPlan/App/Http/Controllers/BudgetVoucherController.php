<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetVoucherDataTable;
use App\DataTables\Budget\InitialVoucherDataTable;
use App\Exports\BeginExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginMandate;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Loans\BudgetVoucherLoan;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BudgetVoucherController extends Controller
{

    public function getIndex(InitialVoucherDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialVoucher.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(BudgetVoucherDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)
            ->orWhere('id', 2)
            ->get();
        $agency = Agency::all();
        $budgetVoucher = BudgetVoucher::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetVoucher.index', [
            'data' => $data,
            'params' => $params,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetVoucher' => $budgetVoucher
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

    public function getByExpenseId(Request $request)
    {
        if ($request->expense_type_id) {

            $data = BudgetMandate::select('id', 'legal_number')
                ->where('expense_type_id', $request->expense_type_id)
                ->where('is_archived', 1)
                ->where('status', 'todo')
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';

            foreach ($data as $d) {

                $selected = $selectedId == $d->legal_number ? 'selected' : '';

                $html .= "<option value='{$d->legal_number}' {$selected}>{$d->legal_number}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByExpenseId(Request $request)
    {
        if (!$request->expense_type_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = BudgetMandate::select('id', 'legal_number')
            ->where('expense_type_id', $request->expense_type_id)
            ->where('is_archived', 2)
            ->where('status', 'done')
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->legal_number === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->legal_number}' {$selected}>{$d->legal_number}</option>";
        }

        return response($html);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)
            ->orWhere('id', 2)
            ->get();

        $beginVoucher = BeginVoucher::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_vouchers.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id); // avoid cross-ministry dupes
            })
            ->where('begin_vouchers.ministry_id', $ministry->id)
            ->select(
                'begin_vouchers.account_sub_id',
                'begin_vouchers.no as voucher_no',
                'account_subs.name as sub_name'
            )
            ->groupBy(
                'begin_vouchers.account_sub_id',
                'begin_vouchers.no',
                'account_subs.name'
            )
            ->orderBy('begin_vouchers.account_sub_id')
            ->get();

        $budgetMandate = BudgetMandate::where("is_archived", "!=", 2)
            ->orderBy('legal_number', 'asc')->get();

        return view('budgetplan::budgetVoucher.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('budgetMandate', $budgetMandate)
            ->with('program', $program);
    }

    public function getEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $request->validate([
            'account_sub_id' => 'required',
            'program_id'     => 'required',
            'program_sub_id' => 'required',
            'cluster_id'     => 'required',
        ]);

        $beginVoucher = BeginVoucher::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginVoucher) {
            return response()->json([
                'fin_law'            => 0,
                'credit_movement'    => 0,
                'new_credit_status'  => 0,
                'credit'             => 0,
                'deadline_balance'   => 0,
                'exists'             => false,
                'message'           => 'No voucher data found for this selection.'
            ]);
        }

        $loan = $beginVoucher->loans;
        $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

        return response()->json([
            'fin_law'            => (float) ($beginVoucher->fin_law ?? 0),
            'credit_movement'    => (float) $credit_movement,
            'new_credit_status'  => (float) ($beginVoucher->new_credit_status ?? 0),
            'credit'             => (float) ($beginVoucher->credit ?? 0),
            'deadline_balance'   => (float) ($beginVoucher->deadline_balance ?? 0),
            'exists'             => true,
        ]);
    }

    public function editEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $request->validate([
            'account_sub_id' => 'required',
            'program_id'     => 'required',
            'program_sub_id' => 'required',
            'cluster_id'     => 'required',
        ]);

        $beginVoucher = BeginVoucher::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginVoucher) {
            return response()->json([
                'fin_law'           => 0,
                'credit_movement'   => 0,
                'new_credit_status' => 0,
                'credit'            => 0,
                'deadline_balance'  => 0,
                'exists'            => false,
                'message'           => 'No mandate data found for this selection.'
            ]);
        }

        $loan = $beginVoucher->loans;

        $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

        return response()->json([
            'fin_law'           => (float) ($beginVoucher->fin_law ?? 0),
            'credit_movement'   => (float) $credit_movement,
            'new_credit_status' => (float) ($beginVoucher->new_credit_status ?? 0),
            'credit'            => (float) ($beginVoucher->credit ?? 0),
            'deadline_balance'  => (float) ($beginVoucher->deadline_balance ?? 0),
            'exists'            => true,
        ]);
    }

    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>   'required',
            'legalName' =>  'required',
            'cboProgram'       => 'required',
            'cboProgramSub'       => 'required',
            'cboCluster'       => 'required',
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'budget'          => 'required|numeric|min:0',
            'cboExpenseType'       => 'required',
            'txtDescription'  => 'required',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|mimes:pdf,doc,docx|max:2048',
            'transactionDate'            => 'required|date',
            'requestDate'            => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministryId = decode_params($params);
            $ministry   = Ministry::where('id', $ministryId)->first();

            $beginVoucher = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $beginMandate = BeginMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginVoucher) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            if (!$beginMandate) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            $budgetMandate = BudgetMandate::where('legal_number', $validated['cboLegalNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('expense_type_id', $validated['cboExpenseType'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$budgetMandate) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យធានាចំណាយ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            $applyValue      = (float) $validated['budget'];
            $currentCredit   = (float) ($beginVoucher->credit ?? 0);
            $remainingCredit = $currentCredit - $applyValue;

            if ($remainingCredit < 0) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
                    ->flash();

                return back();
            }

            $stored = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $stored[] = $file->store('certificateDatas', 'public');
                    }
                }
            }

            BudgetVoucher::create([
                'ministry_id'    => $ministry->id,
                'agency_id'      => $validated['cboAgency'],
                'program_id'      => $validated['cboProgram'],
                'program_sub_id'      => $validated['cboProgramSub'],
                'cluster_id'      => $validated['cboCluster'],
                'account_sub_id' => $validated['cboSubAccount'],
                'no'             => $beginVoucher->no,
                'budget'         => $applyValue,
                'expense_type_id'      => $validated['cboExpenseType'],
                'legal_number'      => $validated['cboLegalNumber'],
                'legal_name'      => $validated['legalName'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginVoucher);

            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('legal_number', $validated['cboLegalNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->expense_type_id = $lastVoucher?->expense_type_id ?? 0;
            $beginVoucher->save();

            $budgetMandate->update([
                'status' => 'done',
                'is_archived' => 2,
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetVoucher.index', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetVoucher store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('budgetvoucher::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        $agency   = Agency::where('ministry_id', $ministry->id)->get();

        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        $module = BudgetVoucher::where('id', $id)
            ->where('is_archived', 2)
            ->where('status', 'done')
            ->where('ministry_id', $ministry->id)
            ->first();

        $expenseType = ExpenseType::where('id', $module->expense_type_id)
            ->get();

        $program     = Program::where('ministry_id', $ministry->id)->get();
        $programId   = Program::findOrFail($module->program_id);
        $programSub  = ProgramSub::where('ministry_id', $ministry->id)
            ->where('program_id', $module->program_id)->get();

        $beginVoucher = BeginVoucher::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_vouchers.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id); // avoid cross-ministry dupes
            })
            ->where('begin_vouchers.ministry_id', $ministry->id)
            ->select(
                'begin_vouchers.account_sub_id',
                'begin_vouchers.no as voucher_no',
                'account_subs.name as sub_name'
            )
            ->groupBy(
                'begin_vouchers.account_sub_id',
                'begin_vouchers.no',
                'account_subs.name'
            )
            ->orderBy('begin_vouchers.account_sub_id')
            ->get();

        return view('budgetplan::budgetVoucher.edit')
            ->with('expenseType', $expenseType)
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('program', $program)
            ->with('programId', $programId)
            ->with('programSub', $programSub)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('module', $module);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>   'required',
            'legalName' =>  'required',
            'cboProgram'       => 'required',
            'cboProgramSub'       => 'required',
            'cboCluster'       => 'required',
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'budget'          => 'required|numeric|min:0',
            'cboExpenseType'       => 'required',
            'txtDescription'  => 'required',
            'transactionDate'            => 'required|date',
            'requestDate'            => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $voucher = BudgetVoucher::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->where('expense_type_id', $validated['cboExpenseType'])
                ->where('is_archived', 1)
                ->where('status', 'todo')
                ->first();

            $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('agency_id', $validated['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            // dd($beginCredit);

            if (!$beginCredit) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }

            $applyValue = $validated['budget'];
            $remainingCredit = $beginCredit->credit - $applyValue;

            // if ($remainingCredit < 0) {
            //     flash()
            //         ->translate('en')
            //         ->option('timeout', 2000)
            //         ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
            //         ->flash();

            //     return back();
            // }

            $storedFilePaths = json_decode($voucher->attachments ?? '[]', true);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $storedFilePaths[] = $file->store('certificateDatas', 'public');
                    }
                }
            }

            $voucher->update([
                'ministry_id'    => $ministry->id,
                'agency_id'      => $validated['cboAgency'],
                'program_id'      => $validated['cboProgram'],
                'program_sub_id'      => $validated['cboProgramSub'],
                'cluster_id'      => $validated['cboCluster'],
                'account_sub_id' => $validated['cboSubAccount'],
                'no' => $beginCredit->no,
                'budget' => $applyValue,
                'expense_type_id' => $validated['cboExpenseType'],
                'legal_number'    => $validated['cboLegalNumber'],
                'legal_name'    => $validated['legalName'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                // 'attachments' => json_encode($storedFilePaths),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginCredit);

            $beginCredit->refresh();
            $lastVoucher = BudgetVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)->latest()->first();
            $beginCredit->apply = $lastVoucher?->budget ?? 0;
            $beginCredit->save();

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('budgetVoucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetVoucher.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $mandate = BudgetMandate::where('legal_number', $voucher->legal_number)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {
            $attachments = json_decode($voucher->attachments, true);
            foreach ($attachments as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                } else {
                    Log::warning("Attachment not found for deletion: " . $filePath);
                }
            }
        }
        $mandate->update([
            'is_archived' => 1,
            'status' => 'todo'
        ]);

        $voucher->delete();
        $beginCredit = BeginVoucher::where('no', $voucher->no)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('ministry_id', $voucher->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budgetVoucher.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();

        $voucher = BudgetVoucher::withTrashed()->whereKey($pid)->first();

        $mandate = BudgetMandate::where('legal_number', $voucher->legal_number)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {

            $attachments = json_decode($voucher->attachments, true);
            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $voucher->attachments = json_encode($restoredFiles);
        }

        $mandate->update([

            'status' => 'done',
            'is_archived' => 2,
        ]);

        $voucher->restore();
        $beginCredit = BeginVoucher::where('account_sub_id', $voucher->account_sub_id)
            ->where('no', $voucher->no)
            ->where('ministry_id', $voucher->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }


        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetVoucher.index', $params);
    }

    private function recalculateAndSaveReport(BeginVoucher $beginVoucher)
    {
        $newApplyTotal = BudgetVoucher::where('account_sub_id', $beginVoucher->account_sub_id)
            ->where('program_id', $beginVoucher->program_id)
            ->where('program_sub_id', $beginVoucher->program_sub_id)
            ->where('cluster_id', $beginVoucher->cluster_id)
            ->where('ministry_id', $beginVoucher->ministry_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $beginVoucher->early_balance = $this->calculateEarlyBalance($beginVoucher);

        $beginVoucher->apply = $newApplyTotal;
        $credit = $beginVoucher->new_credit_status - $beginVoucher->deadline_balance;
        $beginVoucher->credit = $credit;
        $beginVoucher->deadline_balance = $beginVoucher->early_balance + $beginVoucher->apply;
        $beginVoucher->credit = $beginVoucher->new_credit_status - $beginVoucher->deadline_balance;
        $beginVoucher->law_average = $beginVoucher->deadline_balance > 0 ? ($beginVoucher->deadline_balance / $beginVoucher->fin_law * 100)  : 0;
        $beginVoucher->law_correction =  $beginVoucher->deadline_balance > 0 ? ($beginVoucher->deadline_balance /  $beginVoucher->new_credit_status * 100)  : 0;
        $beginVoucher->save();
    }

    private function calculateEarlyBalance($beginCredit)
    {
        $budgetVoucher = BudgetVoucher::where('account_sub_id', $beginCredit->account_sub_id)
            ->where('program_id', $beginCredit->program_id)
            ->where('program_sub_id', $beginCredit->program_sub_id)
            ->where('cluster_id', $beginCredit->cluster_id)
            ->where('ministry_id', $beginCredit->ministry_id)
            ->get();


        if ($budgetVoucher->count() === 1) {
            return 0;
        }
        $totalEarlyBalance = $budgetVoucher->slice(0, -1)
            ->filter(function ($item) {
                return !is_null($item->budget) && $item->budget !== '';
            })
            ->sum('budget');

        return $totalEarlyBalance ?: 0;
    }

    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);

            $query = BudgetVoucher::query();

            $query->leftJoin('begin_vouchers', function ($join) use ($ministryId) {
                $join->on('budget_vouchers.account_sub_id', '=', 'begin_vouchers.account_sub_id')
                    ->on('budget_vouchers.no', '=', 'begin_vouchers.no')
                    ->where('begin_vouchers.ministry_id', $ministryId);
            });

            $query->select(
                'budget_vouchers.program_id',
                'budget_vouchers.account_sub_id',
                'begin_vouchers.account_id',
                'begin_vouchers.chapter_id',
                'budget_vouchers.no',
                'begin_vouchers.txtDescription',
                'begin_vouchers.fin_law',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.deadline_balance',
                'begin_vouchers.current_loan',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.early_balance',
                'begin_vouchers.credit',
                'begin_vouchers.law_average',
                'begin_vouchers.law_correction',
                DB::raw('SUM(budget_vouchers.budget) as apply')
            );
            $query->groupBy(
                'budget_vouchers.program_id',
                'budget_vouchers.account_sub_id',
                'begin_vouchers.account_id',
                'begin_vouchers.chapter_id',
                'budget_vouchers.no',
                'begin_vouchers.txtDescription',
                'begin_vouchers.fin_law',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.deadline_balance',
                'begin_vouchers.current_loan',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.early_balance',
                'begin_vouchers.credit',
                'begin_vouchers.law_average',
                'begin_vouchers.law_correction',
            );
            
            // Expense Type filter
            if ($request->filled('cboExpenseType')) {
                $expenseType = intval($request->cboExpenseType); // ensure integer

                if ($expenseType === 2) {
                    $query->where('budget_vouchers.expense_type_id', 1);
                } elseif ($expenseType === 3) {
                    $query->where('budget_vouchers.expense_type_id', 2);
                } elseif ($expenseType === 1) {
                    $query->where('budget_vouchers.expense_type_id', $expenseType);
                }
            }
            // Sub Account filter
            if ($request->filled('cboAccountSub')) {
                $query->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_vouchers.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_vouchers.deleted_at','!=',null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_vouchers.deleted_at', null);
            }
            //To do
            if ($request->cboTodo) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_vouchers.is_archived', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_vouchers.is_archived', 2);
                }
            } else {
                $query->where('budget_vouchers.is_archived', 2);
            }
            //Date
            // if ($request->filled('start_date') && $request->filled('end_date')) {
            //     $query->whereDate('budget_vouchers.legal_date', '>=', $request->start_date)
            //         ->whereDate('budget_vouchers.request_date', '<=', $request->end_date);
            // } else {
            //     if ($request->filled('start_date')) {
            //         $query->whereDate('budget_vouchers.legal_date', '>=', $request->start_date);
            //     }
            //     if ($request->filled('end_date')) {
            //         $query->whereDate('budget_vouchers.request_date', '<=', $request->end_date);
            //     }
            // }


            $data = $query->get();

            Log::info('Exported BeginVoucher Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginExport($data, $ministryId);

            return $export->export($request);
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចេញទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetVoucher.index', $params);
        }
    }
}
