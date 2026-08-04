<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetVoucherDataTable;
use App\DataTables\Budget\budgetPaymentDeadlineDataTable;
use App\DataTables\Budget\budgetDeadlineTrainingDataTable;
use App\DataTables\Budget\InitialVoucherDataTable;
use App\DataTables\Budget\InitialPaymentDeadlineDataTable;
use App\DataTables\Budget\InitialRoyaltyVoucherDataTable;
use App\DataTables\Budget\RoyaltyVoucherDataTable;
use App\Exports\BeginExport;
use App\Exports\paymentDeadlineExport;
use App\Exports\PaymentDeadlineTrainingExport;
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
use Carbon\Carbon;

class BudgetVoucherController extends Controller
{

    public function getIndex(InitialVoucherDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialVoucher.index');
    }
    public function getPaymentDeadline(InitialPaymentDeadlineDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialDirectPayment.paymentDeadline.index');
    }

    public function getIndexRoyaltyVoucher(InitialRoyaltyVoucherDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::royalty.initialRoyaltyVoucher.index');
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

    public function indexPaymentDeadline(budgetPaymentDeadlineDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 1)
            ->orWhere('id', 2)
            ->get();
        $agency = Agency::all();
        $budgetVoucher = BudgetVoucher::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetDirectPayment.paymentDeadline.index', [
            'data' => $data,
            'params' => $params,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetVoucher' => $budgetVoucher
        ]);
    }

    public function indexPaymentDeadlineTraining(budgetDeadlineTrainingDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 8)
            // ->orWhere('id', 2)
            ->get();
        $agency = Agency::all();
        $budgetVoucher = BudgetVoucher::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetTraining.paymentDeadline.index', [
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
        if (!$request->filled('expense_type_id')) {
            return response()->json([]);
        }

        $data = BudgetMandate::select('id', 'payment_voucher_number', 'legal_name', 'description')
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

    public function getByExpenseIdPayment(Request $request)
    {
        if ($request->expense_type_id) {
            $data = BudgetMandate::select('id', 'legal_id', 'legal_name', 'description')
                ->where('expense_type_id', $request->expense_type_id)
                ->where('is_archived', 1)
                ->where('status', 'todo')
                ->get();

            $selectedId = $request->selected_id ?? null;

            $options = $data->map(function ($d) use ($selectedId) {
                return [
                    'value' => $d->legal_id,
                    'label' => $d->legal_id,
                    'selected' => $selectedId == $d->legal_id, // Handles pre-selecting for viewing/editing
                    'customProperties' => [
                        'legal_name' => $d->legal_name,
                        'description' => $d->description
                    ]
                ];
            });

            return response()->json($options);
        }

        return response()->json([]);
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

    public function editByExpenseIdPayment(Request $request)
    {
        if (!$request->expense_type_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = BudgetMandate::select('id', 'legal_id')
            ->where('expense_type_id', $request->expense_type_id)
            ->where('is_archived', 2)
            ->where('status', 'done')
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->legal_id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->legal_id}' {$selected}>{$d->legal_id}</option>";
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
            ->orWhere('id', 4)
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

    public function createPaymentDeadline($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 3)->get();

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
            ->orderBy('legal_id', 'asc')->get();

        return view('budgetplan::budgetDirectPayment.paymentDeadline.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('budgetMandate', $budgetMandate)
            ->with('program', $program);
    }

    public function createPaymentDeadlineTraining($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 8)->get();

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
            ->orderBy('legal_id', 'asc')->get();

        return view('budgetplan::budgetTraining.paymentDeadline.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('budgetMandate', $budgetMandate)
            ->with('program', $program);
    }

    // public function getEarlyBalance(Request $request, $params)
    // {
    //     $ministryId = decode_params($params);

    //     $request->validate([
    //         'account_sub_id' => 'required',
    //         'program_id'     => 'required',
    //         'program_sub_id' => 'required',
    //         'cluster_id'     => 'required',
    //     ]);

    //     $beginVoucher = BeginVoucher::with('loans')
    //         ->where('ministry_id', $ministryId)
    //         ->where('program_id', $request->program_id)
    //         ->where('program_sub_id', $request->program_sub_id)
    //         ->where('cluster_id', $request->cluster_id)
    //         ->where('account_sub_id', $request->account_sub_id)
    //         ->first();

    //     if (!$beginVoucher) {
    //         return response()->json([
    //             'fin_law'            => 0,
    //             'credit_movement'    => 0,
    //             'new_credit_status'  => 0,
    //             'credit'             => 0,
    //             'deadline_balance'   => 0,
    //             'exists'             => false,
    //             'message'           => 'No voucher data found for this selection.'
    //         ]);
    //     }

    //     $loan = $beginVoucher->loans;
    //     $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

    //     return response()->json([
    //         'fin_law'            => (float) ($beginVoucher->fin_law ?? 0),
    //         'credit_movement'    => (float) $credit_movement,
    //         'new_credit_status'  => (float) ($beginVoucher->new_credit_status ?? 0),
    //         'credit'             => (float) ($beginVoucher->credit ?? 0),
    //         'deadline_balance'   => (float) ($beginVoucher->deadline_balance ?? 0),
    //         'exists'             => true,
    //     ]);
    // }

    // public function getEarlyBalance(Request $request, $params)
    // {
    //     $ministryId = decode_params($params);

    //     $request->validate([
    //         'account_sub_id' => 'required',
    //         'program_id'     => 'required',
    //         'program_sub_id' => 'required',
    //         'cluster_id'     => 'required',
    //     ]);

    //     $beginVoucher = BeginVoucher::where('ministry_id', $ministryId)
    //         ->where('program_id', $request->program_id)
    //         ->where('program_sub_id', $request->program_sub_id)
    //         ->where('cluster_id', $request->cluster_id)
    //         ->where('account_sub_id', $request->account_sub_id)
    //         ->first();

    //     if (!$beginVoucher) {
    //         return response()->json([
    //             'fin_law'           => 0,
    //             'credit_movement'   => 0,
    //             'new_credit_status' => 0,
    //             'credit'            => 0,
    //             'deadline_balance'  => 0,
    //             'exists'            => false,
    //             'message'           => 'No voucher data found for this selection.'
    //         ]);
    //     }

    //     // FIX: Use the relationship query builder to sum across all related loans
    //     $totalIncrease = $beginVoucher->loans()->sum('total_increase');
    //     $totalDecrease = $beginVoucher->loans()->sum('decrease');
    //     $credit_movement = $totalIncrease - $totalDecrease;

    //     return response()->json([
    //         'fin_law'           => (float) ($beginVoucher->fin_law ?? 0),
    //         'credit_movement'   => (float) $credit_movement,
    //         'new_credit_status' => (float) ($beginVoucher->new_credit_status ?? 0),
    //         'credit'            => (float) ($beginVoucher->credit ?? 0),
    //         'deadline_balance'  => (float) ($beginVoucher->deadline_balance ?? 0),
    //         'exists'            => true,
    //     ]);
    // }
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
                'fin_law'           => 0,
                'credit_movement'   => 0,
                'new_credit_status' => 0,
                'credit'            => 0,
                'deadline_balance'  => 0,
                'exists'            => false,
                'message'           => 'No voucher data found for this selection.'
            ]);
        }

        // THE FIX: Safely calculate loan movement whether relationship is a Collection or a single Model
        $credit_movement = 0;
        $loans = $beginVoucher->loans;

        if ($loans) {
            if ($loans instanceof \Illuminate\Support\Collection || is_iterable($loans)) {
                $credit_movement = collect($loans)->sum('total_increase') - collect($loans)->sum('decrease');
            } elseif (is_object($loans)) {
                $credit_movement = ($loans->total_increase ?? 0) - ($loans->decrease ?? 0);
            }
        }

        return response()->json([
            'fin_law'           => (float) ($beginVoucher->fin_law ?? 0),
            'credit_movement'   => (float) $credit_movement,
            'new_credit_status' => (float) ($beginVoucher->new_credit_status ?? 0),
            'credit'            => (float) ($beginVoucher->credit ?? 0),
            'deadline_balance'  => (float) ($beginVoucher->deadline_balance ?? 0),
            'exists'            => true,
        ]);
    }
    // public function editEarlyBalance(Request $request, $params)
    // {
    //     $ministryId = decode_params($params);

    //     $request->validate([
    //         'account_sub_id' => 'required',
    //         'program_id'     => 'required',
    //         'program_sub_id' => 'required',
    //         'cluster_id'     => 'required',
    //     ]);

    //     $beginVoucher = BeginVoucher::with('loans')
    //         ->where('ministry_id', $ministryId)
    //         ->where('program_id', $request->program_id)
    //         ->where('program_sub_id', $request->program_sub_id)
    //         ->where('cluster_id', $request->cluster_id)
    //         ->where('account_sub_id', $request->account_sub_id)
    //         ->first();

    //     if (!$beginVoucher) {
    //         return response()->json([
    //             'fin_law'           => 0,
    //             'credit_movement'   => 0,
    //             'new_credit_status' => 0,
    //             'credit'            => 0,
    //             'deadline_balance'  => 0,
    //             'exists'            => false,
    //             'message'           => 'No mandate data found for this selection.'
    //         ]);
    //     }

    //     $loan = $beginVoucher->loans;

    //     $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

    //     return response()->json([
    //         'fin_law'           => (float) ($beginVoucher->fin_law ?? 0),
    //         'credit_movement'   => (float) $credit_movement,
    //         'new_credit_status' => (float) ($beginVoucher->new_credit_status ?? 0),
    //         'credit'            => (float) ($beginVoucher->credit ?? 0),
    //         'deadline_balance'  => (float) ($beginVoucher->deadline_balance ?? 0),
    //         'exists'            => true,
    //     ]);
    // }

    public function editEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $beginVoucher = BeginVoucher::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginVoucher) {
            return response()->json([
                'fin_law' => 0,
                'credit_movement' => 0,
                'new_credit_status' => 0,
                'credit' => 0,
                'deadline_balance' => 0,
                'exists' => false,
            ]);
        }

        $loan = $beginVoucher->loans;

        return response()->json([
            'fin_law' => (float)$beginVoucher->fin_law,
            'credit_movement' => (float)(($loan->total_increase ?? 0) - ($loan->decrease ?? 0)),
            'new_credit_status' => (float)$beginVoucher->new_credit_status,
            'credit' => (float)$beginVoucher->credit,
            'deadline_balance' => (float)$beginVoucher->deadline_balance,
            'exists' => true,
        ]);
    }

    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboPaymentVoucherNumber' =>   'required',
            'legalName' =>  'required',
            'cbotemporaryId' =>  'nullable',
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

            $budgetMandate = BudgetMandate::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
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
                'legal_id'      => $budgetMandate->legal_id,
                'legal_name'      => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'payment_voucher_number'      => $validated['cboPaymentVoucherNumber'],
                'day_of_number'      => $validated['cbodayOfNumber'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginVoucher);

            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $dataCheck = BudgetVoucher::where('payment_voucher_number', $validated['cboPaymentVoucherNumber'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->get();

            $totalBudget = $dataCheck->sum('budget');

            if ($budgetMandate->budget != $totalBudget) {
                $budgetMandate->update([
                    'status' => 'todo',
                    'is_archived' => 1,
                ]);
            } else {
                $budgetMandate->update([
                    'status' => 'done',
                    'is_archived' => 2,
                ]);
            }

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->expense_type_id = $lastVoucher?->expense_type_id ?? 0;
            $beginVoucher->save();

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('budgetVoucher.index', $params);
            }

            return redirect()->route('budgetVoucher.create', $params);
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

    public function storePaymentDeadline(Request $request, $params)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>  'nullable',
            'cboLegalId' => 'required',
            'legalName' =>  'required',
            'cbotemporaryId' =>  'nullable',
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

            $budgetMandate = BudgetMandate::where('legal_id', $validated['cboLegalId'])
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
                // 'legal_number'      => $validated['cboLegalNumber'],
                'legal_id' => $validated['cboLegalId'],
                'legal_name'      => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginVoucher);

            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $dataCheck = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->get();

            $totalBudget = $dataCheck->sum('budget');

            if ($budgetMandate->budget != $totalBudget) {
                $budgetMandate->update([
                    'status' => 'todo',
                    'is_archived' => 1,
                ]);
            } else {
                $budgetMandate->update([
                    'status' => 'done',
                    'is_archived' => 2,
                ]);
            }

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->expense_type_id = $lastVoucher?->expense_type_id ?? 0;
            $beginVoucher->save();

            // $budgetMandate->update([
            //     'status' => 'done',
            //     'is_archived' => 2,
            // ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
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

    public function storePaymentDeadlineTraining(Request $request, $params)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>  'nullable',
            'cboLegalId' => 'required',
            'legalName' =>  'required',
            'cbotemporaryId' =>  'nullable',
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

            $budgetMandate = BudgetMandate::where('legal_id', $validated['cboLegalId'])
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
                // 'legal_number'      => $validated['cboLegalNumber'],
                'legal_id' => $validated['cboLegalId'],
                'legal_name'      => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginVoucher);

            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $dataCheck = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->get();

            $totalBudget = $dataCheck->sum('budget');

            if ($budgetMandate->budget != $totalBudget) {
                $budgetMandate->update([
                    'status' => 'todo',
                    'is_archived' => 1,
                ]);
            } else {
                $budgetMandate->update([
                    'status' => 'done',
                    'is_archived' => 2,
                ]);
            }

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->expense_type_id = $lastVoucher?->expense_type_id ?? 0;
            $beginVoucher->save();

            // $budgetMandate->update([
            //     'status' => 'done',
            //     'is_archived' => 2,
            // ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetTraining.paymentDeadline.index', $params);
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

    public function editPaymentDeadline($params, $id)
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

        return view('budgetplan::budgetDirectPayment.paymentDeadline.edit')
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

    public function editPaymentDeadlineTraining($params, $id)
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

        return view('budgetplan::budgetTraining.paymentDeadline.edit')
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
            'cboPaymentVoucherNumber' =>   'required',
            'legalName' =>  'required',
            'cbotemporaryId' =>  'required',
            'cbodayOfNumber' =>  'required',
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
                ->where('is_archived', 2)
                ->where('status', 'done')
                ->first();
            $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
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
                'payment_voucher_number'    => $validated['cboPaymentVoucherNumber'],
                'legal_name'    => $validated['legalName'],
                // 'status' => 'done',
                // 'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments' => json_encode($storedFilePaths),
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

    public function updatePaymentDeadline(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>   'nullable',
            'cboLegalId' =>  'required',
            'cbotemporaryId' =>  'nullable',
            'cbodayOfNumber' =>  'required',
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
                // ->where('expense_type_id', $validated['cboExpenseType'])
                // ->where('is_archived', 1)
                // ->where('status', 'todo')
                ->first();

            $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                // ->where('agency_id', $validated['cboAgency'])
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
                // 'legal_number'    => $validated['cboLegalNumber'],
                'legal_id'    => $validated['cboLegalId'],
                'legal_name'    => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                // 'status' => 'done',
                // 'is_archived' => 2,
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
            return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
        }
    }

    public function updatePaymentDeadlineTraining(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>   'nullable',
            'cboLegalId' =>  'required',
            'cbotemporaryId' =>  'nullable',
            'cbodayOfNumber' =>  'required',
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
                // ->where('expense_type_id', $validated['cboExpenseType'])
                // ->where('is_archived', 1)
                // ->where('status', 'todo')
                ->first();

            $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                // ->where('agency_id', $validated['cboAgency'])
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
                // 'legal_number'    => $validated['cboLegalNumber'],
                'legal_id'    => $validated['cboLegalId'],
                'legal_name'    => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                // 'status' => 'done',
                // 'is_archived' => 2,
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
            return redirect()->route('budgetTraining.paymentDeadline.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetTraining.paymentDeadline.index', $params);
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

    public function destroyPaymentDeadline($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {
            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
    }

    public function destroyPaymentDeadlineTraining($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {
            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('budgetTraining.paymentDeadline.index', $params);
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

    public function restorePaymentDeadline($params, $id)
    {
        $pid = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();

        $voucher = BudgetVoucher::withTrashed()->whereKey($pid)->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {

            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            $restoredFiles = [];

            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
    }

    public function restorePaymentDeadlineTraining($params, $id)
    {
        $pid = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();

        $voucher = BudgetVoucher::withTrashed()->whereKey($pid)->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {

            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            $restoredFiles = [];

            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('budgetTraining.paymentDeadline.index', $params);
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
                    ->on('budget_vouchers.program_id', '=', 'begin_vouchers.program_id')
                    ->where('begin_vouchers.ministry_id', $ministryId);
            });
            $query->leftJoin('budget_voucher_loans', function ($join) {
                $join->on('budget_voucher_loans.account_sub_id', '=', 'begin_vouchers.account_sub_id')
                    ->on('budget_voucher_loans.no', '=', 'begin_vouchers.no')
                    ->on('budget_voucher_loans.program_id', '=', 'begin_vouchers.program_id');
            });

            /**
             * Current Budget
             */
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $budgetSum = "
                        SUM(
                            CASE
                                WHEN budget_vouchers.transaction_date
                                    BETWEEN '{$request->start_date}'
                                    AND '{$request->end_date}'
                                AND budget_vouchers.is_archived = 2
                                THEN budget_vouchers.budget
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
                                    WHEN MONTH(budget_vouchers.transaction_date) = {$month}
                                    AND YEAR(budget_vouchers.transaction_date) = {$year}
                                    THEN budget_vouchers.budget
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
                            WHEN budget_vouchers.transaction_date >= '{$start->toDateString()}'
                            AND budget_vouchers.transaction_date < '{$lastMonthStart}'
                            AND budget_vouchers.is_archived = 2
                            THEN budget_vouchers.budget
                            ELSE 0
                        END
                    ) AS early_budget
                ";

            $lastMonthBudget = "
                SUM(
                    CASE
                        WHEN YEAR(budget_vouchers.transaction_date) = {$end->year}
                        AND MONTH(budget_vouchers.transaction_date) = {$end->month}
                        AND budget_vouchers.is_archived = 2
                        THEN budget_vouchers.budget
                        ELSE 0
                    END
                ) AS last_month_budget
                ";

            // G -> N caculate by date
            if ($request->filled('start_date') && $request->filled('end_date')) {

                $loanInternal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.internal_increase
                                ELSE 0
                            END
                        ) AS loan_internal_increase
                    ";

                $loanUnexpected = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.unexpected_increase
                                ELSE 0
                            END
                        ) AS loan_unexpected_increase
                    ";

                $loanAdditional = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.additional_increase
                                ELSE 0
                            END
                        ) AS loan_additional_increase
                    ";

                $loanTotal = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.total_increase
                                ELSE 0
                            END
                        ) AS loan_total_increase
                    ";

                $loanDecrease = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.decrease
                                ELSE 0
                            END
                        ) AS loan_decrease
                    ";

                $loanEditorial = "
                        MAX(
                            CASE
                                WHEN DATE(budget_voucher_loans.updated_at)
                                    BETWEEN '{$request->start_date}' AND '{$request->end_date}'
                                THEN budget_voucher_loans.editorial
                                ELSE 0
                            END
                        ) AS loan_editorial
                    ";
            } else {
                $loanInternal = "MAX(COALESCE(budget_voucher_loans.internal_increase,0)) AS loan_internal_increase";
                $loanUnexpected = "MAX(COALESCE(budget_voucher_loans.unexpected_increase,0)) AS loan_unexpected_increase";
                $loanAdditional = "MAX(COALESCE(budget_voucher_loans.additional_increase,0)) AS loan_additional_increase";
                $loanTotal = "MAX(COALESCE(budget_voucher_loans.total_increase,0)) AS loan_total_increase";
                $loanDecrease = "MAX(COALESCE(budget_voucher_loans.decrease,0)) AS loan_decrease";
                $loanEditorial = "MAX(COALESCE(budget_voucher_loans.editorial,0)) AS loan_editorial";
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $currentLoan = "
                    MAX(
                        begin_vouchers.current_loan

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.total_increase,0))
                            FROM budget_voucher_loans bml
                            WHERE bml.no = begin_vouchers.no
                            AND bml.program_id = begin_vouchers.program_id
                            AND bml.account_sub_id = begin_vouchers.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        - COALESCE((
                            SELECT SUM(COALESCE(bml.decrease,0))
                            FROM budget_voucher_loans bml
                            WHERE bml.no = begin_vouchers.no
                            AND bml.program_id = begin_vouchers.program_id
                            AND bml.account_sub_id = begin_vouchers.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                        + COALESCE((
                            SELECT SUM(COALESCE(bml.editorial,0))
                            FROM budget_voucher_loans bml
                            WHERE bml.no = begin_vouchers.no
                            AND bml.program_id = begin_vouchers.program_id
                            AND bml.account_sub_id = begin_vouchers.account_sub_id
                            AND DATE(bml.updated_at) < '{$request->start_date}'
                        ),0)

                    ) AS current_loan
                    ";
            } else {

                $currentLoan = "MAX(begin_vouchers.current_loan) AS current_loan";
            }
            $query->select([
                'budget_vouchers.no as budget_no',
                'begin_vouchers.no as begin_no',
                'begin_vouchers.chapter_id',
                'budget_vouchers.program_id',
                'budget_vouchers.account_sub_id',
                'begin_vouchers.account_id',
                'begin_vouchers.txtDescription',
                'begin_vouchers.fin_law',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.deadline_balance',
                'begin_vouchers.early_balance',
                'begin_vouchers.credit',
                'begin_vouchers.law_average',
                'begin_vouchers.law_correction',
                DB::raw($currentLoan),
                DB::raw($loanInternal),
                DB::raw($loanUnexpected),
                DB::raw($loanAdditional),
                DB::raw($loanTotal),
                DB::raw($loanDecrease),
                DB::raw($loanEditorial),
                DB::raw('MAX(begin_vouchers.apply) AS apply'),
                DB::raw($budgetSum),
                DB::raw('MAX(budget_vouchers.transaction_date) AS transaction_date'),
                DB::raw($earlyBudget),
                DB::raw($lastMonthBudget),
            ]);
            $query->groupBy(
                'budget_vouchers.no',
                'begin_vouchers.no',
                'begin_vouchers.chapter_id',
                'budget_vouchers.program_id',
                'budget_vouchers.account_sub_id',
                'begin_vouchers.account_id',
                'begin_vouchers.txtDescription',
                'begin_vouchers.fin_law',
                'begin_vouchers.new_credit_status',
                'begin_vouchers.deadline_balance',
                'begin_vouchers.early_balance',
                'begin_vouchers.credit',
                'begin_vouchers.law_average',
                'begin_vouchers.law_correction',
            );
            $query->orderBy('budget_vouchers.transaction_date');

            // Sub voucher number
            if ($request->filled('CboPaymentVoucherNumber')) {
                $query->where(
                    'budget_vouchers.payment_voucher_number',
                    $request->CboPaymentVoucherNumber
                );
            }
            // Sub voucher number
            if ($request->filled('CboMandate')) {
                $query->where(
                    'budget_vouchers.day_of_number',
                    $request->CboMandate
                );
            }
            // Sub Account filter
            if ($request->filled('cboAccountSub')) {
                $query->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
            }
            //status
            if ($request->has('cboStatus')) {
                if ($request->cboStatus == '2') {
                    // Only non-deleted
                    $query->whereNull('budget_vouchers.deleted_at');
                } elseif ($request->cboStatus == '3') {
                    // Only deleted
                    $query->onlyTrashed();
                } else {
                    // All records
                    $query->withTrashed();
                }
            } else {
                // Default: non-deleted
                $query->whereNull('budget_vouchers.deleted_at');
            }
            //To do
            if ($request->filled('cboExpenseType')) {

                $expenseType = (int) $request->cboExpenseType;

                if ($expenseType > 1) {
                    $query->where('budget_vouchers.expense_type_id', $expenseType - 1);
                }

                // expenseType == 1 -> no filter
            }
            //Date
            // Date filter
            if ($request->filled('end_date')) {
                $query->whereDate('budget_vouchers.transaction_date', '<=', $request->end_date);
            }
            $data = $query->get();

            Log::info('Exported BeginVoucher Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            $export = new BeginExport(
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

            return redirect()->route('budgetVoucher.index', $params);
        }
    }

    public function exportPaymentDeadline(Request $request, $params)
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

            // Sub Account filter
            if ($request->filled('cboAccountSub')) {
                $query->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
            }
            //status
            if ($request->has('cboStatus')) {
                if ($request->cboStatus == '2') {
                    // Only non-deleted
                    $query->whereNull('budget_vouchers.deleted_at');
                } elseif ($request->cboStatus == '3') {
                    // Only deleted
                    $query->onlyTrashed();
                } else {
                    // All records
                    $query->withTrashed();
                }
            } else {
                // Default: non-deleted
                $query->whereNull('budget_vouchers.deleted_at');
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

            $export = new paymentDeadlineExport($data, $ministryId);

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

            return redirect()->route('budgetDirectPayment.paymentDeadline.index', $params);
        }
    }

    public function indexRoyaltyVoucher(RoyaltyVoucherDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $expenseType = ExpenseType::where('id', 7)->get();
        $program = Program::where('ministry_id', $data->id)->get();
        $accountSub = AccountSub::where('ministry_id', $data->id)->get();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::royalty.royaltyVoucher.index', [
            'data' => $data,
            'params' => $params,
            'program' => $program,
            'accountSub' => $accountSub,
            'expenseType' => $expenseType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
    }

    public function createRoyaltyVoucher($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::where('id', 7)->get();

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
            ->orderBy('legal_id', 'asc')->get();

        return view('budgetplan::royalty.royaltyVoucher.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('budgetMandate', $budgetMandate)
            ->with('program', $program);
    }

    public function storeRoyaltyVoucher(Request $request, $params)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>  'nullable',
            'cboLegalId' => 'required',
            'legalName' =>  'required',
            'cbotemporaryId' =>  'nullable',
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

            $budgetMandate = BudgetMandate::where('legal_id', $validated['cboLegalId'])
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
                // 'legal_number'      => $validated['cboLegalNumber'],
                'legal_id' => $validated['cboLegalId'],
                'legal_name'      => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                'status' => 'done',
                'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
                'attachments'    => json_encode($stored),
                'transaction_date'           => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
            ]);

            $this->recalculateAndSaveReport($beginVoucher);

            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->latest()->first();

            $dataCheck = BudgetVoucher::where('legal_id', $validated['cboLegalId'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->get();

            $totalBudget = $dataCheck->sum('budget');

            if ($budgetMandate->budget != $totalBudget) {
                $budgetMandate->update([
                    'status' => 'todo',
                    'is_archived' => 1,
                ]);
            } else {
                $budgetMandate->update([
                    'status' => 'done',
                    'is_archived' => 2,
                ]);
            }

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->expense_type_id = $lastVoucher?->expense_type_id ?? 0;
            $beginVoucher->save();

            // $budgetMandate->update([
            //     'status' => 'done',
            //     'is_archived' => 2,
            // ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('royaltyVoucher.index', $params);
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

    public function editRoyaltyVoucher($params, $id)
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

        return view('budgetplan::royalty.royaltyVoucher.edit')
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

    public function updateRoyaltyVoucher(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboLegalNumber' =>   'nullable',
            'cboLegalId' =>  'required',
            'cbotemporaryId' =>  'nullable',
            'cbodayOfNumber' =>  'required',
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
                // ->where('expense_type_id', $validated['cboExpenseType'])
                // ->where('is_archived', 1)
                // ->where('status', 'todo')
                ->first();

            $beginCredit = BeginVoucher::where('account_sub_id', $validated['cboSubAccount'])
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
                'legal_id'    => $validated['cboLegalId'],
                'legal_name'    => $validated['legalName'],
                'temporary_id'      => $validated['cbotemporaryId'] ?? null,
                'day_of_number'      => $validated['cbodayOfNumber'],
                // 'status' => 'done',
                // 'is_archived' => 2,
                'description' => strip_tags($validated['txtDescription']),
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
            return redirect()->route('royaltyVoucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('royaltyVoucher.index', $params);
        }
    }

    public function destroyRoyaltyVoucher($params, $id)
    {
        $id = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {
            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('royaltyVoucher.index', $params);
    }

    public function restoreRoyaltyVoucher($params, $id)
    {
        $pid = decode_params($id);
        $ministry   = Ministry::where('id', decode_params($params))->first();

        $voucher = BudgetVoucher::withTrashed()->whereKey($pid)->first();

        $mandate = BudgetMandate::where('legal_id', $voucher->legal_id)
            ->where('account_sub_id', $voucher->account_sub_id)
            ->where('program_id', $voucher->program_id)
            ->where('program_sub_id', $voucher->program_sub_id)
            ->where('cluster_id', $voucher->cluster_id)
            ->where('ministry_id', $ministry->id)
            ->first();

        if ($voucher->attachments) {

            $attachments = json_decode($voucher->attachments ?? '[]', true) ?? [];
            $restoredFiles = [];

            foreach ($attachments as $filePath) {
                Storage::disk('public')->delete($filePath);
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

        return redirect()->route('royaltyVoucher.index', $params);
    }

    public function exportRoyaltyVoucher(Request $request, $params)
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

            // Sub Account filter
            if ($request->filled('cboAccountSub')) {
                $query->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
            }
            //status
            if ($request->has('cboStatus')) {
                if ($request->cboStatus == '2') {
                    // Only non-deleted
                    $query->whereNull('budget_vouchers.deleted_at');
                } elseif ($request->cboStatus == '3') {
                    // Only deleted
                    $query->onlyTrashed();
                } else {
                    // All records
                    $query->withTrashed();
                }
            } else {
                // Default: non-deleted
                $query->whereNull('budget_vouchers.deleted_at');
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

            $export = new paymentDeadlineExport($data, $ministryId);

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

            return redirect()->route('royaltyVoucher.index', $params);
        }
    }
}
