<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetAdvancePaymentDataTable;
use App\DataTables\Budget\BudgetMandateDataTable;
use App\DataTables\Budget\BudgetDirectPaymentDataTable;
use App\DataTables\Budget\InitialAdvancePaymentDataTable;
use App\DataTables\Budget\InitialDirectPaymentDataTable;
use App\DataTables\Budget\InitialMandateDataTable;
use App\Exports\BeginMandateExport;
use App\Exports\BeginguaranteeExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginMandate;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BudgetMandateController extends Controller
{
    public function getIndex(InitialMandateDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialMandate.index');
    }

    public function getIndexAdvancePay(InitialAdvancePaymentDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialAdvancePayment.index');
    }
    public function getIndexExpenseRecord(InitialDirectPaymentDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialDirectPayment.expenseRecord.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BudgetMandateDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)->get();
        $program = Program::where('ministry_id', $data->id)->get();
        $accountSub = AccountSub::where('ministry_id', $data->id)->orderBy('no', 'asc')->get();
        $agency = Agency::all();
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

    public function getIndexAdvancePayment(BudgetAdvancePaymentDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)->get();
        $program = Program::where('ministry_id', $data->id)->get();

        $accountSub = AccountSub::where('ministry_id', $data->id)->get();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetAdvancePayment.index', [
            'data' => $data,
            'params' => $params,
            'program' => $program,
            'accountSub' => $accountSub,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
    }

    public function getIndexExpenseRecordBook(BudgetDirectPaymentDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)->get();
        $program = Program::where('ministry_id', $data->id)->get();

        $accountSub = AccountSub::where('ministry_id', $data->id)->get();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetDirectPayment.expenseRecord.index', [
            'data' => $data,
            'params' => $params,
            'program' => $program,
            'accountSub' => $accountSub,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
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
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)
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

        return view('budgetplan::budgetMandate.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
            ->with('program', $program);
    }

    public function createAdvancePayment($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)
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

        return view('budgetplan::budgetAdvancePayment.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
            ->with('program', $program);
    }

    public function createExpenseRecord($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)
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

        return view('budgetplan::budgetDirectPayment.expenseRecord.create')
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
                'message'           => 'No mandate data found for this selection.'
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

        $validated = $request->validate([
            'account_sub_id' => 'required',
            'program_id'     => 'required',
            'program_sub_id' => 'required',
            'cluster_id'     => 'required',
        ]);

        if (!$validated) {
            return response('<option value="">ស្វែងរក...</option>');
        }

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
                'exists' => false
            ]);
        }

        $loan = $beginMandate->loans;

        $creditMovement = ($loan->total_increase ?? 0) - ($loan->decrease ?? 0);

        return response()->json([
            'fin_law'           => (float) ($beginMandate->fin_law ?? 0),
            'credit_movement'   => (float) $creditMovement,
            'new_credit_status' => (float) ($beginMandate->new_credit_status ?? 0),
            'credit'            => (float) ($beginMandate->credit ?? 0),
            'deadline_balance'  => (float) ($beginMandate->deadline_balance ?? 0),
            'exists'            => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $params)
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
                'expense_type_id'  => 1,
                'legal_id'         => $validated['legalID'],
                'payment_voucher_number'         => $validated['paymentVoucher'],
                'legal_number'     => $validated['legalNumber'],
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

            return redirect()->route('budgetMandate.index', $params);
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

    public function storeAdvancePayment(Request $request, $params)
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
                'fin_law'               => $beginMandate->fin_law,
                'no'               => $beginMandate->no,
                'budget'           => $applyValue,
                'expense_type_id'  => 2,
                'legal_id'         => $validated['legalID'],
                'payment_voucher_number'         => $validated['paymentVoucher'],
                'legal_number'     => $validated['legalNumber'],
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

            return redirect()->route('budgetAdvancePayment.index', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetAdvancePayment store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }

    public function storeExpenseRecord(Request $request, $params)
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
                'fin_law'               => $beginMandate->fin_law,
                'no'               => $beginMandate->no,
                'budget'           => $applyValue,
                'expense_type_id'  => 2,
                'legal_id'         => $validated['legalID'],
                'payment_voucher_number'         => $validated['paymentVoucher'],
                'legal_number'     => $validated['legalNumber'],
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

            return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetDirectPayment store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
        $expenseType = ExpenseType::where('id', 1)->get();
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

        return view('budgetplan::budgetMandate.edit')
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

    public function editAdvancePayment($params, $id)
    {
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        $agency   = Agency::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)->get();
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

        return view('budgetplan::budgetAdvancePayment.edit')
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

    public function editExpenseRecord($params, $id)
    {
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        $agency   = Agency::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 1)->get();
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

        return view('budgetplan::budgetDirectPayment.expenseRecord.edit')
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
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
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


            return redirect()->route('budgetMandate.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetMandate.index', $params);
        }
    }


    public function updateAdvancePayment(Request $request, $params, $id)
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


            return redirect()->route('budgetAdvancePayment.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetAdvancePayment.index', $params);
        }
    }

    public function updateExpenseRecord(Request $request, $params, $id)
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


            return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
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

    public function destroyAdvancePayment($params, $id)
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

        return redirect()->route('budgetAdvancePayment.index', $params);
    }

    public function destroyExpenseRecord($params, $id)
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

        return redirect()->route('budgetDirectPayment.expenseRecord.index', $params);
    }

    public function restore($params, $id)
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

    private function recalculateAndSaveReport(BeginMandate $beginMandate)
    {
        $newApplyTotal = BudgetMandate::where('account_sub_id', $beginMandate->account_sub_id)
            ->where('program_id', $beginMandate->program_id)
            ->where('program_sub_id', $beginMandate->program_sub_id)
            ->where('cluster_id', $beginMandate->cluster_id)
            ->where('ministry_id', $beginMandate->ministry_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $beginMandate->early_balance = $this->calculateEaarlyBalance($beginMandate);

        $beginMandate->apply = $newApplyTotal;
        $credit = $beginMandate->new_credit_status - $beginMandate->deadline_balance;
        $beginMandate->credit = $credit;
        $beginMandate->deadline_balance = $beginMandate->early_balance + $beginMandate->apply;
        $beginMandate->credit = $beginMandate->new_credit_status - $beginMandate->deadline_balance;
        $beginMandate->law_average = $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance / $beginMandate->fin_law) * 100 : 0;
        $beginMandate->law_correction =  $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance /  $beginMandate->new_credit_status) * 100 : 0;
        $beginMandate->save();
    }

    private function calculateEaarlyBalance($data)
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

            $query->where('budget_mandates.expense_type_id', 1);
            $query->where('budget_mandates.status', 'todo');
            $query->where('budget_mandates.is_archived', 1);

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
            if ($request->cboTodo) {
                if ($request->cboTodo == 2) {
                    $query->where('budget_mandates.is_archived', 1);
                    $query->where('budget_mandates.expense_type_id', 1);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                    $query->where('budget_mandates.expense_type_id', 1);
                }
            } else {
                $query->where('budget_mandates.is_archived', 1);
                $query->where('budget_mandates.expense_type_id', 1);
            }
            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginguaranteeExport($data, $ministryId);

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

            $query->where('budget_mandates.expense_type_id', 2);
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
                    $query->where('budget_mandates.expense_type_id', 2);
                } elseif ($request->cboTodo == 3) {
                    $query->where('budget_mandates.is_archived', 2);
                    $query->where('budget_mandates.expense_type_id', 2);
                }
            } else {
                $query->where('budget_mandates.is_archived', 1);
                $query->where('budget_mandates.expense_type_id', 2);
            }

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginMandateExport($data, $ministryId);

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

            return redirect()->route('budgetAdvancePayment.index', $params);
        }
    }
}
