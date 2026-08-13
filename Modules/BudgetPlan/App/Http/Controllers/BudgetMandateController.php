<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetAdvancePaymentDataTable;
use App\DataTables\Budget\BudgetMandateDataTable;
use App\DataTables\Budget\BudgetDirectPaymentDataTable;
use App\DataTables\Budget\BudgetProcurementDataTable;
use App\DataTables\Budget\BudgetTrainingDataTable;
use App\DataTables\Budget\InitialAdvancePaymentDataTable;
use App\DataTables\Budget\InitialDirectPaymentDataTable;
use App\DataTables\Budget\InitialMandateDataTable;
use App\DataTables\Budget\InitialProcurementDataTable;
use App\DataTables\Budget\InitialRoyaltyMandateDataTable;
use App\DataTables\Budget\InitialTrainingDataTable;
use App\Exports\BeginMandateExport;
use App\Exports\BeginguaranteeExport;
use App\Exports\ExpenseRecordExport;
use App\Exports\ExpenseRecordTrainingExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginMandate;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Loans\BudgetMandateLoan;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BudgetMandateController extends Controller
{
    public function getIndex(InitialMandateDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialMandate.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BudgetMandateDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)->get();
        $program = Program::where('ministry_id', $data->id)->orderBy('no', 'asc')->get();
        $accountSub = AccountSub::where('ministry_id', $data->id)->orderBy('no', 'asc')->get();
        $agency = Agency::where('ministry_id', $id)->get();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetMandate.index', [
            'data' => $data,
            'params' => $params,
            'program' => $program,
            'accountSub' => $accountSub,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
    }
    public function getByExpenseId(Request $request)
    {
        if (!$request->filled('expense_type_id')) {
            return response()->json([]);
        }

        $data = BudgetVoucher::select('id', 'payment_voucher_number', 'legal_name', 'description')
            ->where('expense_type_id', $request->expense_type_id)
            ->where('is_archived', 1)
            ->where('status', 'todo')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->payment_voucher_number,
                    'label' => $item->payment_voucher_number,
                    'customProperties' => [
                        'legal_name' => $item->legal_name,
                        'description' => $item->description,
                    ]
                ];
            });

        return response()->json($data);
    }
    public function editByExpenseId(Request $request)
    {
        if (!$request->expense_type_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = BudgetMandate::select('id', 'payment_voucher_number')
            ->where('expense_type_id', $request->expense_type_id)
            ->where('is_archived', 2)
            ->where('status', 'done')
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->payment_voucher_number === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->payment_voucher_number}' {$selected}>{$d->payment_voucher_number}</option>";
        }

        return response($html);
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
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::all();

        $beginMandate = BeginMandate::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_mandates.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id);
            })
            ->where('begin_mandates.ministry_id', $ministry->id)
            ->select(
                'begin_mandates.account_sub_id',
                'begin_mandates.no as mandate_no',
                'account_subs.name as sub_name'
            )
            ->groupBy(
                'begin_mandates.account_sub_id',
                'begin_mandates.no',
                'account_subs.name'
            )
            ->orderBy('begin_mandates.account_sub_id')
            ->get();

        return view('budgetplan::budgetMandate.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
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

        $beginMandate = BeginMandate::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginMandate) {
            return response()->json([
                'fin_law'           => 0,
                'credit_movement'   => 0,
                'new_credit_status' => 0,
                'credit'            => 0,
                'deadline_balance'  => 0,
                'exists'            => false,
                'message'           => 'មិនមានទិន្នន័យបង្ហាញ.'
            ]);
        }

        $loan = $beginMandate->loans;
        $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

        return response()->json([
            'fin_law'           => (float) ($beginMandate->fin_law ?? 0),
            'credit_movement'   => (float) $credit_movement,
            'new_credit_status' => (float) ($beginMandate->new_credit_status ?? 0),
            'credit'            => (float) ($beginMandate->credit ?? 0),
            'deadline_balance'  => (float) ($beginMandate->deadline_balance ?? 0),
            'exists'            => true,
        ]);
    }

    public function editEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $beginMandate = BeginMandate::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginMandate) {
            return response()->json([
                'fin_law' => 0,
                'credit_movement' => 0,
                'new_credit_status' => 0,
                'credit' => 0,
                'deadline_balance' => 0,
                'exists' => false,
            ]);
        }

        $loan = $beginMandate->loans;

        return response()->json([
            'fin_law' => (float)$beginMandate->fin_law,
            'credit_movement' => (float)(($loan->total_increase ?? 0) - ($loan->decrease ?? 0)),
            'new_credit_status' => (float)$beginMandate->new_credit_status,
            'credit' => (float)$beginMandate->credit,
            'deadline_balance' => (float)$beginMandate->deadline_balance,
            'exists' => true,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboPaymentVoucherNumber' =>   'required',
            'legalName' =>  'nullable',
            'cboTemporaryId' =>  'nullable',
            'cbodayOfNumber' =>  'required',
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

            $budgetVoucher = BudgetVoucher::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('expense_type_id', $validated['cboExpenseType'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$budgetVoucher) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            $applyValue      = (float) $validated['budget'];
            $currentCredit   = (float) ($beginMandate->credit ?? 0);
            $remainingCredit = $currentCredit - $applyValue;

            if ($remainingCredit < 0) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('ឥណទានមិនគ្រប់គ្រាន់។', 'បញ្ហា')
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
            BudgetMandate::create([
                'ministry_id'    => $ministry->id,
                'agency_id'      => $validated['cboAgency'],
                'program_id'      => $validated['cboProgram'],
                'program_sub_id'      => $validated['cboProgramSub'],
                'cluster_id'      => $validated['cboCluster'],
                'account_sub_id' => $validated['cboSubAccount'],
                'no'             => $beginMandate->no,
                'budget'         => $applyValue,
                'expense_type_id'      => (int) $validated['cboExpenseType'],
                'legal_id'      => $budgetVoucher->legal_id,
                'legal_name'      => $validated['legalName'] ?? null,
                'temporary_id'      => $validated['cboTemporaryId'] ?? null,
                'payment_voucher_number'      => $validated['cboPaymentVoucherNumber'],
                'day_of_number'      => $validated['cbodayOfNumber'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginMandate);

            $beginMandate->refresh();

            $lastMandate = BudgetMandate::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $dataCheck = BudgetMandate::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->get();

            $totalBudget = $dataCheck->sum('budget');

            // Compare status update between budget-voucher and budget-mandate.
            if ($budgetVoucher->budget != $totalBudget) {
                $budgetVoucher->update([
                    'status' => 'todo',
                    'is_archived' => 1,
                ]);
            } else {
                $budgetVoucher->update([
                    'status' => 'done',
                    'is_archived' => 2,
                ]);
            }

            $beginMandate->apply = $lastMandate?->budget ?? 0;
            $beginMandate->expense_type_id = $lastMandate?->expense_type_id ?? 0;
            $beginMandate->save();

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('budgetMandate.index', $params);
            }

            return redirect()->route('budgetMandate.create', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetMandate store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
        return view('budgetmandate::show');
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
        $module = BudgetMandate::where('id', $id)
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

        return view('budgetplan::budgetMandate.edit')
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
    // public function update(Request $request, $params, $id)
    // {
    //     $validated = $request->validate([
    //         'cboPaymentVoucherNumber' =>   'required',
    //         'legalName' =>  'nullable',
    //         'cboTemporaryId' =>  'nullable',
    //         'cbodayOfNumber' =>  'required',
    //         'cboProgram'       => 'required',
    //         'cboProgramSub'       => 'required',
    //         'cboCluster'       => 'required',
    //         'cboAgency'       => 'required',
    //         'cboSubAccount'   => 'required',
    //         'budget'          => 'required|numeric|min:0',
    //         'cboExpenseType'       => 'required',
    //         'txtDescription'  => 'required',
    //         'transactionDate'            => 'required|date',
    //         'requestDate'            => 'required|date',
    //     ]);

    //     dd($validated);
    //     DB::beginTransaction();
    //     try {
    //         $ministry = Ministry::where('id', decode_params($params))->first();
    //         $voucher = BudgetVoucher::where('id', $id)
    //             ->where('ministry_id', $ministry->id)
    //             ->where('expense_type_id', $validated['cboExpenseType'])
    //             ->where('is_archived', 2)
    //             ->where('status', 'done')
    //             ->first();
    //         $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
    //             ->where('program_id', $validated['cboProgram'])
    //             ->where('program_sub_id', $validated['cboProgramSub'])
    //             ->where('cluster_id', $validated['cboCluster'])
    //             ->where('agency_id', $validated['cboAgency'])
    //             ->where('ministry_id', $ministry->id)
    //             ->first();

    //         if (!$beginCredit) {
    //             flash()->translate('en')->option('timeout', 2000)
    //                 ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
    //             return back()->withInput();
    //         }

    //         $applyValue = $validated['budget'];
    //         $remainingCredit = $beginCredit->credit - $applyValue;

    //         // if ($remainingCredit < 0) {
    //         //     flash()
    //         //         ->translate('en')
    //         //         ->option('timeout', 2000)
    //         //         ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
    //         //         ->flash();

    //         //     return back();
    //         // }

    //         $storedFilePaths = json_decode($voucher->attachments ?? '[]', true);

    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $file) {
    //                 if ($file->isValid()) {
    //                     $storedFilePaths[] = $file->store('certificateDatas', 'public');
    //                 }
    //             }
    //         }

    //         $voucher->update([
    //             'ministry_id'    => $ministry->id,
    //             'agency_id'      => $validated['cboAgency'],
    //             'program_id'      => $validated['cboProgram'],
    //             'program_sub_id'      => $validated['cboProgramSub'],
    //             'cluster_id'      => $validated['cboCluster'],
    //             'account_sub_id' => $validated['cboSubAccount'],
    //             'no' => $beginCredit->no,
    //             'budget' => $applyValue,
    //             'expense_type_id' => $validated['cboExpenseType'],
    //             'payment_voucher_number'    => $validated['cboPaymentVoucherNumber'],
    //             'legal_name'    => $validated['legalName'],
    //             // 'status' => 'done',
    //             // 'is_archived' => 2,
    //             'description' => strip_tags($validated['txtDescription']),
    //             'attachments' => json_encode($storedFilePaths),
    //             'transaction_date'           => $validated['transactionDate'],
    //             'request_date'           => $validated['requestDate'],
    //         ]);

    //         $this->recalculateAndSaveReport($beginCredit);

    //         $beginCredit->refresh();
    //         $lastVoucher = BudgetVoucher::where('account_sub_id', $validated['cboSubAccount'])
    //             ->where('program_id', $validated['cboProgram'])
    //             ->where('program_sub_id', $validated['cboProgramSub'])
    //             ->where('cluster_id', $validated['cboCluster'])
    //             ->where('ministry_id', $ministry->id)->latest()->first();
    //         $beginCredit->apply = $lastVoucher?->budget ?? 0;
    //         $beginCredit->save();

    //         DB::commit();
    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();
    //         return redirect()->route('budgetMandate.index', $params);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage());

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error($e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return redirect()->route('budgetMandate.index', $params);
    //     }
    // }
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboPaymentVoucherNumber' => 'required',
            'legalName'               => 'nullable',
            'cboTemporaryId'         => 'nullable',
            'cbodayOfNumber'         => 'nullable', // Changed to nullable because disabled fields are not sent in HTTP request
            'cboProgram'             => 'required',
            'cboProgramSub'          => 'required',
            'cboCluster'             => 'required',
            'cboAgency'              => 'required',
            'cboSubAccount'          => 'required',
            'budget'                 => 'required|numeric|min:0',
            'cboExpenseType'         => 'required',
            'txtDescription'         => 'required',
            'transactionDate'        => 'required|date',
            'requestDate'            => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $mandate = BudgetMandate::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->where('expense_type_id', $validated['cboExpenseType'])
                ->where('is_archived', 2)
                ->where('status', 'done')
                ->firstOrFail();


            $beginCredit = BeginMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('agency_id', $validated['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginCredit) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }

            $applyValue = $validated['budget'];

            $storedFilePaths = json_decode($voucher->attachments ?? '[]', true);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $storedFilePaths[] = $file->store('certificateDatas', 'public');
                    }
                }
            }

            $mandate->update([
                'ministry_id'            => $ministry->id,
                'agency_id'              => $validated['cboAgency'],
                'program_id'             => $validated['cboProgram'],
                'program_sub_id'         => $validated['cboProgramSub'],
                'cluster_id'             => $validated['cboCluster'],
                'account_sub_id'         => $validated['cboSubAccount'],
                'no'                     => $beginCredit->no,
                'budget'                 => $applyValue,
                'expense_type_id'        => $validated['cboExpenseType'],
                'payment_voucher_number' => $validated['cboPaymentVoucherNumber'],
                'day_of_number' => $validated['cbodayOfNumber'],

                'legal_name'             => $validated['legalName'],
                'description'            => strip_tags($validated['txtDescription']),
                'attachments'            => json_encode($storedFilePaths),
                'transaction_date'       => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginCredit);

            $beginCredit->refresh();
            $lastMandate = BudgetMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()
                ->first();


            $beginCredit->apply = $lastMandate?->budget ?? 0;
            $beginCredit->save();

            DB::commit();

            flash()->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetMandate.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetMandate.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $mandate = BudgetMandate::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        // ✅ Delete attached files
        if ($mandate->attachments) {
            $attachments = json_decode($mandate->attachments, true);

            foreach ($attachments as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                } else {
                    Log::warning("Attachment not found for deletion: " . $filePath);
                }
            }
        }

        $mandate->delete();

        // Recalculate related data
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budgetMandate.index', $params);
    }


    public function restore($params, $id)
    // {
    //     $pid = decode_params($id);

    //     $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

    //     if ($mandate->attachments) {

    //         $attachments = json_decode($mandate->attachments, true);
    //         $restoredFiles = [];

    //         foreach ($attachments as $filePath) {

    //             if (Storage::disk('public')->exists($filePath)) {

    //                 $originalPath = str_replace('trash/', '', $filePath);

    //                 Storage::disk('public')->move($filePath, $originalPath);

    //                 $restoredFiles[] = $originalPath;
    //             }
    //         }

    //         $mandate->attachments = json_encode($restoredFiles);
    //     }

    //     $mandate->restore();
    //     $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
    //         ->where('no', $mandate->no)
    //         ->where('ministry_id', $mandate->ministry_id)
    //         ->first();

    //     if ($beginCredit) {
    //         $this->recalculateAndSaveReport($beginCredit);
    //     }


    //     flash()
    //         ->translate('en')
    //         ->option('timeout', 2000)
    //         ->success('restore_msg', 'restore')
    //         ->flash();

    //     return redirect()->route('budgetMandate.index', $params);
    // }
    {
        $pid = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();

        $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

        $voucher = BudgetVoucher::where('legal_number', $mandate->legal_number)
            ->where('account_sub_id', $mandate->account_sub_id)
            ->where('program_id', $mandate->program_id)
            ->where('program_sub_id', $mandate->program_sub_id)
            ->where('cluster_id', $mandate->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($mandate->attachments) {

            $attachments = json_decode($mandate->attachments, true);
            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $mandate->attachments = json_encode($restoredFiles);
        }

        $mandate->update([

            'status' => 'done',
            'is_archived' => 2,
        ]);

        $mandate->restore();
        $beginCredit = BeginVoucher::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }


        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetMandate.index', $params);
    }

    public function restoreAdvancePayment($params, $id)
    {
        $pid = decode_params($id);

        $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

        if ($mandate->attachments) {

            $attachments = json_decode($mandate->attachments, true);
            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $mandate->attachments = json_encode($restoredFiles);
        }

        $mandate->restore();
        // Recalculate related data
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetAdvancePayment.index', $params);
    }

    public function restoreExpenseRecord($params, $id)
    {
        $pid = decode_params($id);

        $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

        if ($mandate->attachments) {

            $attachments = json_decode($mandate->attachments, true);
            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $mandate->attachments = json_encode($restoredFiles);
        }

        $mandate->restore();
        // Recalculate related data
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
    }

    public function restoreExpenseRecordTraining($params, $id)
    {
        $pid = decode_params($id);

        $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

        if (!empty($mandate->attachments)) {

            $attachments = json_decode($mandate->attachments, true);

            if (!is_array($attachments)) {
                $attachments = [];
            }

            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $mandate->attachments = json_encode($restoredFiles);
        }

        $mandate->restore();
        // Recalculate related data
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetTraining.expenseRecord.index', $params);
    }

    private function recalculateAndSaveReport(BeginMandate $beginMandate)
    {
        $newApplyTotal = BudgetMandate::where('account_sub_id', $beginMandate->account_sub_id)
            ->where('program_id', $beginMandate->program_id)
            ->where('program_sub_id', $beginMandate->program_sub_id)
            ->where('cluster_id', $beginMandate->cluster_id)
            ->where('ministry_id', $beginMandate->ministry_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $beginMandate->early_balance = $this->calculateEarlyBalance($beginMandate);

        $beginMandate->apply = $newApplyTotal;

        $beginMandate->deadline_balance = $beginMandate->early_balance + $beginMandate->apply;
        $beginMandate->credit = $beginMandate->new_credit_status - $beginMandate->deadline_balance;
        $beginMandate->law_average = $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance / $beginMandate->fin_law) * 100 : 0;
        $beginMandate->law_correction =  $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance /  $beginMandate->new_credit_status) * 100 : 0;
        $beginMandate->save();
    }

    private function calculateEarlyBalance($data)
    {
        $budgetMandate = BudgetMandate::where('account_sub_id', $data->account_sub_id)
            ->where('program_id', $data->program_id)
            ->where('program_sub_id', $data->program_sub_id)
            ->where('cluster_id', $data->cluster_id)
            ->where('ministry_id', $data->ministry_id)
            ->get();

        if ($budgetMandate->count() === 1) {
            return 0;
        }

        $totalEarlyBalance = $budgetMandate->slice(0, -1)
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

            $query = BudgetMandate::query();

            $query->leftJoin('begin_mandates', function ($join) use ($ministryId) {
                $join->on('begin_mandates.account_sub_id', '=', 'budget_mandates.account_sub_id')
                    ->on('begin_mandates.no', '=', 'budget_mandates.no')
                    ->on('begin_mandates.program_id', '=', 'budget_mandates.program_id')
                    ->where('begin_mandates.ministry_id', '=', $ministryId);
            });

            $query->leftJoin('budget_mandate_loans', function ($join) {
                $join->on('budget_mandate_loans.account_sub_id', '=', 'begin_mandates.account_sub_id')
                    ->on('budget_mandate_loans.no', '=', 'begin_mandates.no')
                    ->on('budget_mandate_loans.program_id', '=', 'begin_mandates.program_id');
            });
            /**
             * Current Budget
             */
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN budget_mandates.transaction_date
                                        BETWEEN '{$request->start_date}'
                                        AND '{$request->end_date}'
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = Carbon::parse($request->end_date);
            } else {

                $month = now()->month;
                $year  = now()->year;

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN MONTH(budget_mandates.transaction_date) = {$month}
                                    AND YEAR(budget_mandates.transaction_date) = {$year}
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = now();
            }
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            $lastMonthStart = $end->copy()->startOfMonth()->toDateString();

            /**
             * Early Budget (Normal)
             */
            $earlyBudget = "
                    SUM(
                        CASE
                            WHEN budget_mandates.transaction_date >= '{$start->toDateString()}'
                            AND budget_mandates.transaction_date < '{$lastMonthStart}'
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS early_budget
                    ";

            /**
             * Last Month Budget (Normal)
             */
            $lastMonthBudget = "
                    SUM(
                        CASE
                            WHEN YEAR(budget_mandates.transaction_date) = {$end->year}
                            AND MONTH(budget_mandates.transaction_date) = {$end->month}
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS last_month_budget
                    ";

            /**
             * Archived Early Budget
             */
            $archivedEarlyBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND bm2.transaction_date >= '{$start->toDateString()}'
                        AND bm2.transaction_date < '{$lastMonthStart}'
                    ),
                    0
                    ) AS archived_early_budget
                    ";

            /**
             * Archived Last Month Budget
             */
            $archivedLastMonthBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND YEAR(bm2.transaction_date) = {$end->year}
                        AND MONTH(bm2.transaction_date) = {$end->month}
                    ),
                    0
                    ) AS archived_last_month_budget
                    ";

            // G -> N caculate by date
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $loanInternal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.internal_increase
                                ELSE 0
                            END
                        ) AS loan_internal_increase
                    ";

                $loanUnexpected = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.unexpected_increase
                                ELSE 0
                            END
                        ) AS loan_unexpected_increase
                    ";

                $loanAdditional = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.additional_increase
                                ELSE 0
                            END
                        ) AS loan_additional_increase
                    ";

                $loanTotal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.total_increase
                                ELSE 0
                            END
                        ) AS loan_total_increase
                    ";

                $loanDecrease = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.decrease
                                ELSE 0
                            END
                        ) AS loan_decrease
                    ";

                $loanEditorial = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.editorial
                                ELSE 0
                            END
                        ) AS loan_editorial
                    ";
            } else {
                $loanInternal = "MAX(COALESCE(budget_mandate_loans.internal_increase,0)) AS loan_internal_increase";
                $loanUnexpected = "MAX(COALESCE(budget_mandate_loans.unexpected_increase,0)) AS loan_unexpected_increase";
                $loanAdditional = "MAX(COALESCE(budget_mandate_loans.additional_increase,0)) AS loan_additional_increase";
                $loanTotal = "MAX(COALESCE(budget_mandate_loans.total_increase,0)) AS loan_total_increase";
                $loanDecrease = "MAX(COALESCE(budget_mandate_loans.decrease,0)) AS loan_decrease";
                $loanEditorial = "MAX(COALESCE(budget_mandate_loans.editorial,0)) AS loan_editorial";
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $currentLoan = "
                    MAX(
                        begin_mandates.current_loan

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.total_increase,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        - COALESCE((
                            SELECT SUM(COALESCE(bml.decrease,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.editorial,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                    ) AS current_loan
                    ";
            } else {

                $currentLoan = "MAX(begin_mandates.current_loan) AS current_loan";
            }
            $query->select([
                'budget_mandates.no as budget_no',
                'begin_mandates.no as begin_no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
                DB::raw($currentLoan),
                DB::raw($loanInternal),
                DB::raw($loanUnexpected),
                DB::raw($loanAdditional),
                DB::raw($loanTotal),
                DB::raw($loanDecrease),
                DB::raw($loanEditorial),
                DB::raw('MAX(begin_mandates.apply) AS apply'),
                DB::raw($budgetSum),
                DB::raw('MAX(budget_mandates.transaction_date) AS transaction_date'),
                DB::raw($earlyBudget),
                DB::raw($lastMonthBudget),
                DB::raw($archivedEarlyBudget),
                DB::raw($archivedLastMonthBudget),
            ]);
            $query->groupBy(
                'budget_mandates.no',
                'begin_mandates.no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
            );
            $query->orderBy('budget_mandates.transaction_date');
            // === Filters (PREFIX table name!) ===
            // Account
            if ($request->filled('subAccountNumber')) {
                $query->where('begin_mandates.account_sub_id', $request->subAccountNumber);
            }
            // program
            if ($request->filled('cboProgram')) {
                $query->where('begin_mandates.program_id', $request->cboProgram);
            }
            // Date
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_mandates.request_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_mandates.request_date', '<=', $request->end_date);
                }
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_mandates.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_mandates.deleted_at', '!=', null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_mandates.deleted_at', null);
            }
            //To do
            if ($request->filled('cboTodo')) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                } else {
                    $query->whereIn('budget_mandates.is_archived', [1, 2]);
                }
            } else {
                // Default: include both
                $query->whereIn('budget_mandates.is_archived', [1, 2]);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginguaranteeExport(
                $data,
                $ministryId,
                $request->start_date,
                $request->end_date
            );

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

            return redirect()->route('budgetMandate.index', $params);
        }
    }


    public function exportAdvancePayment(Request $request, $params)
    {
        try {

            $ministryId = decode_params($params);

            $query = BudgetMandate::query();

            $query->leftJoin('begin_mandates', function ($join) use ($ministryId) {
                $join->on('begin_mandates.account_sub_id', '=', 'budget_mandates.account_sub_id')
                    ->on('begin_mandates.no', '=', 'budget_mandates.no')
                    ->on('begin_mandates.program_id', '=', 'budget_mandates.program_id')
                    ->where('begin_mandates.ministry_id', '=', $ministryId);
            });

            $query->leftJoin('budget_mandate_loans', function ($join) {
                $join->on('budget_mandate_loans.account_sub_id', '=', 'begin_mandates.account_sub_id')
                    ->on('budget_mandate_loans.no', '=', 'begin_mandates.no')
                    ->on('budget_mandate_loans.program_id', '=', 'begin_mandates.program_id');
            });
            /**
             * Current Budget
             */
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN budget_mandates.transaction_date
                                        BETWEEN '{$request->start_date}'
                                        AND '{$request->end_date}'
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = Carbon::parse($request->end_date);
            } else {

                $month = now()->month;
                $year  = now()->year;

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN MONTH(budget_mandates.transaction_date) = {$month}
                                    AND YEAR(budget_mandates.transaction_date) = {$year}
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = now();
            }
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            $lastMonthStart = $end->copy()->startOfMonth()->toDateString();

            /**
             * Early Budget (Normal)
             */
            $earlyBudget = "
                    SUM(
                        CASE
                            WHEN budget_mandates.transaction_date >= '{$start->toDateString()}'
                            AND budget_mandates.transaction_date < '{$lastMonthStart}'
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS early_budget
                    ";

            /**
             * Last Month Budget (Normal)
             */
            $lastMonthBudget = "
                    SUM(
                        CASE
                            WHEN YEAR(budget_mandates.transaction_date) = {$end->year}
                            AND MONTH(budget_mandates.transaction_date) = {$end->month}
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS last_month_budget
                    ";

            /**
             * Archived Early Budget
             */
            $archivedEarlyBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND bm2.transaction_date >= '{$start->toDateString()}'
                        AND bm2.transaction_date < '{$lastMonthStart}'
                    ),
                    0
                    ) AS archived_early_budget
                    ";

            /**
             * Archived Last Month Budget
             */
            $archivedLastMonthBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND YEAR(bm2.transaction_date) = {$end->year}
                        AND MONTH(bm2.transaction_date) = {$end->month}
                    ),
                    0
                    ) AS archived_last_month_budget
                    ";

            // G -> N caculate by date
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $loanInternal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.internal_increase
                                ELSE 0
                            END
                        ) AS loan_internal_increase
                    ";

                $loanUnexpected = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.unexpected_increase
                                ELSE 0
                            END
                        ) AS loan_unexpected_increase
                    ";

                $loanAdditional = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.additional_increase
                                ELSE 0
                            END
                        ) AS loan_additional_increase
                    ";

                $loanTotal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.total_increase
                                ELSE 0
                            END
                        ) AS loan_total_increase
                    ";

                $loanDecrease = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.decrease
                                ELSE 0
                            END
                        ) AS loan_decrease
                    ";

                $loanEditorial = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.editorial
                                ELSE 0
                            END
                        ) AS loan_editorial
                    ";
            } else {
                $loanInternal = "MAX(COALESCE(budget_mandate_loans.internal_increase,0)) AS loan_internal_increase";
                $loanUnexpected = "MAX(COALESCE(budget_mandate_loans.unexpected_increase,0)) AS loan_unexpected_increase";
                $loanAdditional = "MAX(COALESCE(budget_mandate_loans.additional_increase,0)) AS loan_additional_increase";
                $loanTotal = "MAX(COALESCE(budget_mandate_loans.total_increase,0)) AS loan_total_increase";
                $loanDecrease = "MAX(COALESCE(budget_mandate_loans.decrease,0)) AS loan_decrease";
                $loanEditorial = "MAX(COALESCE(budget_mandate_loans.editorial,0)) AS loan_editorial";
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $currentLoan = "
                    MAX(
                        begin_mandates.current_loan

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.total_increase,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        - COALESCE((
                            SELECT SUM(COALESCE(bml.decrease,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.editorial,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                    ) AS current_loan
                    ";
            } else {

                $currentLoan = "MAX(begin_mandates.current_loan) AS current_loan";
            }
            $query->select([
                'budget_mandates.no as budget_no',
                'begin_mandates.no as begin_no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
                DB::raw($currentLoan),
                DB::raw($loanInternal),
                DB::raw($loanUnexpected),
                DB::raw($loanAdditional),
                DB::raw($loanTotal),
                DB::raw($loanDecrease),
                DB::raw($loanEditorial),
                DB::raw('MAX(begin_mandates.apply) AS apply'),
                DB::raw($budgetSum),
                DB::raw('MAX(budget_mandates.transaction_date) AS transaction_date'),
                DB::raw($earlyBudget),
                DB::raw($lastMonthBudget),
                DB::raw($archivedEarlyBudget),
                DB::raw($archivedLastMonthBudget),
            ]);
            $query->groupBy(
                'budget_mandates.no',
                'begin_mandates.no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
            );
            $query->orderBy('budget_mandates.transaction_date');
            $query->where('budget_mandates.expense_type_id', 2);
            // === Filters (PREFIX table name!) ===
            // Account
            if ($request->filled('subAccountNumber')) {
                $query->where('begin_mandates.account_sub_id', $request->subAccountNumber);
            }
            // program
            if ($request->filled('cboProgram')) {
                $query->where('begin_mandates.program_id', $request->cboProgram);
            }
            // Date
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_mandates.request_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_mandates.request_date', '<=', $request->end_date);
                }
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_mandates.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_mandates.deleted_at', '!=', null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_mandates.deleted_at', null);
            }
            //To do
            if ($request->filled('cboTodo')) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                } else {
                    $query->whereIn('budget_mandates.is_archived', [1, 2]);
                }
            } else {
                // Default: include both
                $query->whereIn('budget_mandates.is_archived', [1, 2]);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginMandateExport(
                $data,
                $ministryId,
                $request->start_date,
                $request->end_date
            );

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

            return redirect()->route('budgetMandate.index', $params);
        }
    }
    public function exportExpenseRecordBook(Request $request, $params)
    {
        try {

            $ministryId = decode_params($params);

            $query = BudgetMandate::query();
            $query->leftJoin('begin_mandates', function ($join) use ($ministryId) {
                $join->on('begin_mandates.account_sub_id', '=', 'budget_mandates.account_sub_id')
                    ->on('begin_mandates.no', '=', 'budget_mandates.no')
                    ->on('begin_mandates.program_id', '=', 'budget_mandates.program_id')
                    ->where('begin_mandates.ministry_id', $ministryId);
            });
            $query->select(
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'budget_mandates.no',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                DB::raw('SUM(budget_mandates.budget) as apply')
            );
            $query->groupBy(
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'budget_mandates.no',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
            );

            $query->where('budget_mandates.expense_type_id', 3);
            $query->where('budget_mandates.status', 'todo');
            $query->where('budget_mandates.is_archived', 1);

            // === Filters (PREFIX table name!) ===
            if ($request->filled('subAccountNumber')) {
                $query->where('begin_mandates.account_sub_id', $request->subAccountNumber);
            }
            if ($request->filled('cboProgram')) {
                $query->where('begin_mandates.program_id', $request->cboProgram);
            }
            // Date
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_mandates.request_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_mandates.request_date', '<=', $request->end_date);
                }
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_mandates.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_mandates.deleted_at', '!=', null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_mandates.deleted_at', null);
            }
            //To do
            if ($request->cboTodo) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                    $query->where('budget_mandates.expense_type_id', 3);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                    $query->where('budget_mandates.expense_type_id', 3);
                }
            } else {
                $query->where('budget_mandates.is_archived', 1);
                $query->where('budget_mandates.expense_type_id', 3);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new ExpenseRecordExport($data, $ministryId);

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

            return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
        }
    }

    public function exportExpenseRecordBookTraining(Request $request, $params)
    {
        try {

            $ministryId = decode_params($params);

            $query = BudgetMandate::query();

            $query->leftJoin('begin_mandates', function ($join) use ($ministryId) {
                $join->on('begin_mandates.account_sub_id', '=', 'budget_mandates.account_sub_id')
                    ->on('begin_mandates.no', '=', 'budget_mandates.no')
                    ->on('begin_mandates.program_id', '=', 'budget_mandates.program_id')
                    ->where('begin_mandates.ministry_id', '=', $ministryId);
            });

            $query->leftJoin('budget_mandate_loans', function ($join) {
                $join->on('budget_mandate_loans.account_sub_id', '=', 'begin_mandates.account_sub_id')
                    ->on('budget_mandate_loans.no', '=', 'begin_mandates.no')
                    ->on('budget_mandate_loans.program_id', '=', 'begin_mandates.program_id');
            });
            /**
             * Current Budget
             */
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN budget_mandates.transaction_date
                                        BETWEEN '{$request->start_date}'
                                        AND '{$request->end_date}'
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = Carbon::parse($request->end_date);
            } else {

                $month = now()->month;
                $year  = now()->year;

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN MONTH(budget_mandates.transaction_date) = {$month}
                                    AND YEAR(budget_mandates.transaction_date) = {$year}
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = now();
            }
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            $lastMonthStart = $end->copy()->startOfMonth()->toDateString();

            /**
             * Early Budget (Normal)
             */
            $earlyBudget = "
                    SUM(
                        CASE
                            WHEN budget_mandates.transaction_date >= '{$start->toDateString()}'
                            AND budget_mandates.transaction_date < '{$lastMonthStart}'
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS early_budget
                    ";

            /**
             * Last Month Budget (Normal)
             */
            $lastMonthBudget = "
                    SUM(
                        CASE
                            WHEN YEAR(budget_mandates.transaction_date) = {$end->year}
                            AND MONTH(budget_mandates.transaction_date) = {$end->month}
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS last_month_budget
                    ";

            /**
             * Archived Early Budget
             */
            $archivedEarlyBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND bm2.transaction_date >= '{$start->toDateString()}'
                        AND bm2.transaction_date < '{$lastMonthStart}'
                    ),
                    0
                    ) AS archived_early_budget
                    ";

            /**
             * Archived Last Month Budget
             */
            $archivedLastMonthBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND YEAR(bm2.transaction_date) = {$end->year}
                        AND MONTH(bm2.transaction_date) = {$end->month}
                    ),
                    0
                    ) AS archived_last_month_budget
                    ";

            // G -> N caculate by date
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $loanInternal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.internal_increase
                                ELSE 0
                            END
                        ) AS loan_internal_increase
                    ";

                $loanUnexpected = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.unexpected_increase
                                ELSE 0
                            END
                        ) AS loan_unexpected_increase
                    ";

                $loanAdditional = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.additional_increase
                                ELSE 0
                            END
                        ) AS loan_additional_increase
                    ";

                $loanTotal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.total_increase
                                ELSE 0
                            END
                        ) AS loan_total_increase
                    ";

                $loanDecrease = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.decrease
                                ELSE 0
                            END
                        ) AS loan_decrease
                    ";

                $loanEditorial = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.editorial
                                ELSE 0
                            END
                        ) AS loan_editorial
                    ";
            } else {
                $loanInternal = "MAX(COALESCE(budget_mandate_loans.internal_increase,0)) AS loan_internal_increase";
                $loanUnexpected = "MAX(COALESCE(budget_mandate_loans.unexpected_increase,0)) AS loan_unexpected_increase";
                $loanAdditional = "MAX(COALESCE(budget_mandate_loans.additional_increase,0)) AS loan_additional_increase";
                $loanTotal = "MAX(COALESCE(budget_mandate_loans.total_increase,0)) AS loan_total_increase";
                $loanDecrease = "MAX(COALESCE(budget_mandate_loans.decrease,0)) AS loan_decrease";
                $loanEditorial = "MAX(COALESCE(budget_mandate_loans.editorial,0)) AS loan_editorial";
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $currentLoan = "
                    MAX(
                        begin_mandates.current_loan

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.total_increase,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        - COALESCE((
                            SELECT SUM(COALESCE(bml.decrease,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.editorial,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                    ) AS current_loan
                    ";
            } else {

                $currentLoan = "MAX(begin_mandates.current_loan) AS current_loan";
            }
            $query->select([
                'budget_mandates.no as budget_no',
                'begin_mandates.no as begin_no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
                DB::raw($currentLoan),
                DB::raw($loanInternal),
                DB::raw($loanUnexpected),
                DB::raw($loanAdditional),
                DB::raw($loanTotal),
                DB::raw($loanDecrease),
                DB::raw($loanEditorial),
                DB::raw('MAX(begin_mandates.apply) AS apply'),
                DB::raw($budgetSum),
                DB::raw('MAX(budget_mandates.transaction_date) AS transaction_date'),
                DB::raw($earlyBudget),
                DB::raw($lastMonthBudget),
                DB::raw($archivedEarlyBudget),
                DB::raw($archivedLastMonthBudget),
            ]);
            $query->groupBy(
                'budget_mandates.no',
                'begin_mandates.no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
            );
            $query->orderBy('budget_mandates.transaction_date');
            $query->where('budget_mandates.expense_type_id', 8);
            // === Filters (PREFIX table name!) ===
            // Account
            if ($request->filled('subAccountNumber')) {
                $query->where('begin_mandates.account_sub_id', $request->subAccountNumber);
            }
            // program
            if ($request->filled('cboProgram')) {
                $query->where('begin_mandates.program_id', $request->cboProgram);
            }
            // Date
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_mandates.request_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_mandates.request_date', '<=', $request->end_date);
                }
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_mandates.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_mandates.deleted_at', '!=', null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_mandates.deleted_at', null);
            }
            //To do
            if ($request->filled('cboTodo')) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                } else {
                    $query->whereIn('budget_mandates.is_archived', [1, 2]);
                }
            } else {
                // Default: include both
                $query->whereIn('budget_mandates.is_archived', [1, 2]);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new ExpenseRecordTrainingExport(
                $data,
                $ministryId,
                $request->start_date,
                $request->end_date
            );

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

            return redirect()->route('budgetMandate.index', $params);
        }
    }
    // Expenditure Procurement

    public function indexProcurement(BudgetProcurementDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)->get();
        $program = Program::where('ministry_id', $data->id)->get();

        $accountSub = AccountSub::where('ministry_id', $data->id)->get();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetProcurement.index', [
            'data' => $data,
            'params' => $params,
            'program' => $program,
            'accountSub' => $accountSub,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
    }

    public function createProcurement($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 4)
            ->get();

        $beginMandate = BeginMandate::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_mandates.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id); // avoid cross-ministry dupes
            })
            ->where('begin_mandates.ministry_id', $ministry->id)
            ->select(
                'begin_mandates.account_sub_id',
                'begin_mandates.no as mandate_no',
                'account_subs.name as sub_name'
            )
            ->groupBy(
                'begin_mandates.account_sub_id',
                'begin_mandates.no',
                'account_subs.name'
            )
            ->orderBy('begin_mandates.account_sub_id')
            ->get();

        return view('budgetplan::budgetProcurement.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
            ->with('program', $program);
    }

    public function storeProcurement(Request $request, $params)
    {
        $validated = $request->validate([
            'legalID' =>   'required',
            'paymentVoucher' => 'required',
            'legalNumber' => 'nullable|integer|min:1',
            'legalName' =>  'required',
            'cboProgram'       => 'required',
            'cboProgramSub'       => 'required',
            'cboCluster'       => 'required',
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'budget'          => 'required|numeric|min:0',
            'txtDescription'  => 'required',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|mimes:pdf,doc,docx|max:2048',
            'transactionDate'            => 'required|date',
            'requestDate'            => 'required|date',
            'legalDate'            => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministryId = decode_params($params);
            $ministry   = Ministry::where('id', $ministryId)->first();

            $beginMandate = BeginMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginMandate) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            $applyValue      = (float) $validated['budget'];
            $currentCredit   = (float) ($beginMandate->credit ?? 0);
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

            BudgetMandate::create([
                'ministry_id'      => $ministry->id,
                'agency_id'        => $validated['cboAgency'],
                'program_id'       => $validated['cboProgram'],
                'program_sub_id'   => $validated['cboProgramSub'],
                'cluster_id'       => $validated['cboCluster'],
                'account_sub_id'   => $validated['cboSubAccount'],
                'no'               => $beginMandate->no,
                'fin_law'          => $beginMandate->fin_law,
                'budget'           => $applyValue,
                'expense_type_id'  => 4,
                'legal_id'         => $validated['legalID'],
                'payment_voucher_number'         => $validated['paymentVoucher'],
                'legal_number'     => $validated['legalNumber'] ?? null,
                'legal_name'       => $validated['legalName'],
                'status'           => 'todo',
                'is_archived'      => 1,
                'description'      => strip_tags($validated['txtDescription']),
                'attachments'      => json_encode($stored),
                'transaction_date' => $validated['transactionDate'],
                'request_date'     => $validated['requestDate'],
                'legal_date'     => $validated['legalDate'],
            ]);

            $this->recalculateAndSaveReport($beginMandate);

            $beginMandate->refresh();
            $lastMandate = BudgetMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('agency_id', $validated['cboAgency'])
                ->latest()->first();

            $beginMandate->apply = $lastMandate?->budget ?? 0;
            $beginMandate->save();

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('budgetProcurement.index', $params);
            }
            return redirect()->route('budgetProcurement.create', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetMandate store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }

    public function editProcurement($params, $id)
    {
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        $agency   = Agency::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 4)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        $module = BudgetMandate::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->where('is_archived', 1)
            ->first();

        if (!$module) {
            flash()->translate('en')->option('timeout', 2000)
                ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
            return back()->withInput();
        }

        $program     = Program::where('ministry_id', $ministry->id)->get();
        $programId   = Program::where('ministry_id', $ministry->id)
            ->findOrFail($module->program_id);
        $programSub  = ProgramSub::where('ministry_id', $ministry->id)
            ->where('program_id', $module->program_id)->get();

        $beginMandate = BeginMandate::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_mandates.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id); // avoid cross-ministry dupes
            })
            ->where('begin_mandates.ministry_id', $ministry->id)
            ->select(
                'begin_mandates.account_sub_id',
                'begin_mandates.no as voucher_no',
                'account_subs.name as sub_name'
            )
            ->groupBy(
                'begin_mandates.account_sub_id',
                'begin_mandates.no',
                'account_subs.name'
            )
            ->orderBy('begin_mandates.account_sub_id')
            ->get();

        return view('budgetplan::budgetProcurement.edit')
            ->with('expenseType', $expenseType)
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('program', $program)
            ->with('programId', $programId)
            ->with('programSub', $programSub)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
            ->with('module', $module);
    }

    public function updateProcurement(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'legalID' =>   'required',
            'paymentVoucher' => 'required',
            'legalNumber' =>   'required',
            'legalName' =>  'required',
            'cboProgram'       => 'required',
            'cboProgramSub'       => 'required',
            'cboCluster'       => 'required',
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'budget'          => 'numeric|min:0',
            'txtDescription'  => 'required',
            'transactionDate'            => 'required|date',
            'requestDate'            => 'required|date',
            'legalDate'            => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $mandate = BudgetMandate::where('id', $id)
                ->where('ministry_id', $ministry->id)->first();

            $beginCredit = BeginMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginCredit) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }

            $applyValue = $validated['budget'];
            $remainingCredit = $beginCredit->credit - $applyValue;

            if ($remainingCredit < 0) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
                    ->flash();

                return back();
            }
            $storedFilePaths = json_decode($voucher->attachments ?? '[]', true);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $storedFilePaths[] = $file->store('certificateDatas', 'public');
                    }
                }
            }

            $mandate->update([
                'ministry_id'    => $ministry->id,
                'agency_id'      => $validated['cboAgency'],
                'program_id'      => $validated['cboProgram'],
                'program_sub_id'      => $validated['cboProgramSub'],
                'cluster_id'      => $validated['cboCluster'],
                'account_sub_id' => $validated['cboSubAccount'],
                'no'             => $beginCredit->no,
                'budget'         => $applyValue,
                'legal_id'      => $validated['legalID'],
                'legal_number'      => $validated['legalNumber'],
                'legal_name'      => $validated['legalName'],
                'status' => 'todo',
                'is_archived' => 1,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($storedFilePaths),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginCredit);

            $beginCredit->refresh();
            $lastMandater = BudgetMandate::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)->latest()->first();
            $beginCredit->apply = $lastMandater?->budget ?? 0;
            $beginCredit->save();

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();


            return redirect()->route('budgetProcurement.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetProcurement.index', $params);
        }
    }

    public function destroyProcurement($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $mandate = BudgetMandate::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        // ✅ Delete attached files
        if ($mandate->attachments) {
            $attachments = json_decode($mandate->attachments, true);

            foreach ($attachments as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                } else {
                    Log::warning("Attachment not found for deletion: " . $filePath);
                }
            }
        }

        $mandate->delete();

        // Recalculate related data
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budgetProcurement.index', $params);
    }

    public function restoreProcurement($params, $id)
    {
        $pid = decode_params($id);

        $mandate = BudgetMandate::withTrashed()->whereKey($pid)->first();

        if ($mandate->attachments) {

            $attachments = json_decode($mandate->attachments, true);
            $restoredFiles = [];

            foreach ($attachments as $filePath) {

                if (Storage::disk('public')->exists($filePath)) {

                    $originalPath = str_replace('trash/', '', $filePath);

                    Storage::disk('public')->move($filePath, $originalPath);

                    $restoredFiles[] = $originalPath;
                }
            }

            $mandate->attachments = json_encode($restoredFiles);
        }

        $mandate->restore();
        $beginCredit = BeginMandate::where('account_sub_id', $mandate->account_sub_id)
            ->where('no', $mandate->no)
            ->where('ministry_id', $mandate->ministry_id)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('budgetProcurement.index', $params);
    }

    public function exportProcurement(Request $request, $params)
    {
        try {

            $ministryId = decode_params($params);

            $query = BudgetMandate::query();

            $query->leftJoin('begin_mandates', function ($join) use ($ministryId) {
                $join->on('begin_mandates.account_sub_id', '=', 'budget_mandates.account_sub_id')
                    ->on('begin_mandates.no', '=', 'budget_mandates.no')
                    ->on('begin_mandates.program_id', '=', 'budget_mandates.program_id')
                    ->where('begin_mandates.ministry_id', '=', $ministryId);
            });

            $query->leftJoin('budget_mandate_loans', function ($join) {
                $join->on('budget_mandate_loans.account_sub_id', '=', 'begin_mandates.account_sub_id')
                    ->on('budget_mandate_loans.no', '=', 'begin_mandates.no')
                    ->on('budget_mandate_loans.program_id', '=', 'begin_mandates.program_id');
            });

            $query->where('budget_mandates.expense_type_id', 4);
            /**
             * Current Budget
             */
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN budget_mandates.transaction_date
                                        BETWEEN '{$request->start_date}'
                                        AND '{$request->end_date}'
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = Carbon::parse($request->end_date);
            } else {

                $month = now()->month;
                $year  = now()->year;

                $budgetSum = "
                            SUM(
                                CASE
                                    WHEN MONTH(budget_mandates.transaction_date) = {$month}
                                    AND YEAR(budget_mandates.transaction_date) = {$year}
                                    THEN budget_mandates.budget
                                    ELSE 0
                                END
                            ) AS budget
                        ";

                $endDate = now();
            }
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            $lastMonthStart = $end->copy()->startOfMonth()->toDateString();

            /**
             * Early Budget (Normal)
             */
            $earlyBudget = "
                    SUM(
                        CASE
                            WHEN budget_mandates.transaction_date >= '{$start->toDateString()}'
                            AND budget_mandates.transaction_date < '{$lastMonthStart}'
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS early_budget
                    ";

            /**
             * Last Month Budget (Normal)
             */
            $lastMonthBudget = "
                    SUM(
                        CASE
                            WHEN YEAR(budget_mandates.transaction_date) = {$end->year}
                            AND MONTH(budget_mandates.transaction_date) = {$end->month}
                            AND budget_mandates.is_archived = 1
                            THEN budget_mandates.budget
                            ELSE 0
                        END
                    ) AS last_month_budget
                    ";

            /**
             * Archived Early Budget
             */
            $archivedEarlyBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND bm2.transaction_date >= '{$start->toDateString()}'
                        AND bm2.transaction_date < '{$lastMonthStart}'
                    ),
                    0
                    ) AS archived_early_budget
                    ";

            /**
             * Archived Last Month Budget
             */
            $archivedLastMonthBudget = "
                    COALESCE(
                    (
                        SELECT SUM(bm2.budget)
                        FROM budget_mandates bm2
                        WHERE bm2.no = budget_mandates.no
                        AND bm2.program_id = budget_mandates.program_id
                        AND bm2.account_sub_id = budget_mandates.account_sub_id
                        AND bm2.is_archived = 2
                        AND YEAR(bm2.transaction_date) = {$end->year}
                        AND MONTH(bm2.transaction_date) = {$end->month}
                    ),
                    0
                    ) AS archived_last_month_budget
                    ";

            // G -> N caculate by date
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $loanInternal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.internal_increase
                                ELSE 0f
                            END
                        ) AS loan_internal_increase
                    ";

                $loanUnexpected = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.unexpected_increase
                                ELSE 0
                            END
                        ) AS loan_unexpected_increase
                    ";

                $loanAdditional = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.additional_increase
                                ELSE 0
                            END
                        ) AS loan_additional_increase
                    ";

                $loanTotal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.total_increase
                                ELSE 0
                            END
                        ) AS loan_total_increase
                    ";

                $loanDecrease = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.decrease
                                ELSE 0
                            END
                        ) AS loan_decrease
                    ";

                $loanEditorial = "
                        MAX(
                            CASE
                                WHEN DATE(budget_mandate_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_mandate_loans.editorial
                                ELSE 0
                            END
                        ) AS loan_editorial
                    ";
            } else {
                $loanInternal = "MAX(COALESCE(budget_mandate_loans.internal_increase,0)) AS loan_internal_increase";
                $loanUnexpected = "MAX(COALESCE(budget_mandate_loans.unexpected_increase,0)) AS loan_unexpected_increase";
                $loanAdditional = "MAX(COALESCE(budget_mandate_loans.additional_increase,0)) AS loan_additional_increase";
                $loanTotal = "MAX(COALESCE(budget_mandate_loans.total_increase,0)) AS loan_total_increase";
                $loanDecrease = "MAX(COALESCE(budget_mandate_loans.decrease,0)) AS loan_decrease";
                $loanEditorial = "MAX(COALESCE(budget_mandate_loans.editorial,0)) AS loan_editorial";
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $currentLoan = "
                    MAX(
                        begin_mandates.current_loan

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.total_increase,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        - COALESCE((
                            SELECT SUM(COALESCE(bml.decrease,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.editorial,0))
                            FROM budget_mandate_loans bml
                            WHERE bml.no = begin_mandates.no
                            AND bml.program_id = begin_mandates.program_id
                            AND bml.account_sub_id = begin_mandates.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                    ) AS current_loan
                    ";
            } else {

                $currentLoan = "MAX(begin_mandates.current_loan) AS current_loan";
            }
            $query->select([
                'budget_mandates.no as budget_no',
                'begin_mandates.no as begin_no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
                DB::raw($currentLoan),
                DB::raw($loanInternal),
                DB::raw($loanUnexpected),
                DB::raw($loanAdditional),
                DB::raw($loanTotal),
                DB::raw($loanDecrease),
                DB::raw($loanEditorial),
                DB::raw('MAX(begin_mandates.apply) AS apply'),
                DB::raw($budgetSum),
                DB::raw('MAX(budget_mandates.transaction_date) AS transaction_date'),
                DB::raw($earlyBudget),
                DB::raw($lastMonthBudget),
                DB::raw($archivedEarlyBudget),
                DB::raw($archivedLastMonthBudget),
            ]);
            $query->groupBy(
                'budget_mandates.no',
                'begin_mandates.no',
                'begin_mandates.chapter_id',
                'budget_mandates.program_id',
                'budget_mandates.account_sub_id',
                'begin_mandates.account_id',
                'begin_mandates.txtDescription',
                'begin_mandates.fin_law',
                'begin_mandates.new_credit_status',
                'begin_mandates.deadline_balance',
                'begin_mandates.early_balance',
                'begin_mandates.credit',
                'begin_mandates.law_average',
                'begin_mandates.law_correction',
            );
            $query->orderBy('budget_mandates.transaction_date');
            // === Filters (PREFIX table name!) ===
            // Account
            if ($request->filled('subAccountNumber')) {
                $query->where('begin_mandates.account_sub_id', $request->subAccountNumber);
            }
            // program
            if ($request->filled('cboProgram')) {
                $query->where('begin_mandates.program_id', $request->cboProgram);
            }
            // Date
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_mandates.request_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_mandates.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_mandates.request_date', '<=', $request->end_date);
                }
            }
            //status
            if ($request->cboStatus) {
                if ($request->cboStatus == '2') {
                    $query->where('budget_mandates.deleted_at', null);
                } elseif ($request->cboStatus == '3') {
                    $query->where('budget_mandates.deleted_at', '!=', null);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('budget_mandates.deleted_at', null);
            }
            //To do
            if ($request->filled('cboTodo')) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                } else {
                    $query->whereIn('budget_mandates.is_archived', [1, 2]);
                }
            } else {
                // Default: include both
                $query->whereIn('budget_mandates.is_archived', [1, 2]);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginguaranteeExport(
                $data,
                $ministryId,
                $request->start_date,
                $request->end_date
            );

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

            return redirect()->route('budgetProcurement.index', $params);
        }
    }
}
