<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetVoucherDataTable;
use App\DataTables\Budget\InitialVoucherDataTable;
use App\Exports\BeginExport;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class BudgetVoucherController extends Controller
{

    // Display listing of year
    public function getIndex(InitialVoucherDataTable $dataTable)
    {
        return $dataTable->render('budgetplan::initialVoucher.index');
    }

    // Filter Date
    public function getLegalDate(Request $request)
    {
        $voucherNumber = $request->get('voucher_number');

        $legalDate = BudgetVoucher::where('payment_voucher_number', $voucherNumber)
            ->value('legal_date');

        return response()->json([
            'legal_date' => $legalDate
                ? \Carbon\Carbon::parse($legalDate)->format('Y-m-d')
                : null
        ]);
    }

    /**
     * Display a listing of the resource.
     * @param  string|int  $params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(BudgetVoucherDataTable $dataTable, $params)
    {
        // Decode the incoming parameters
        $id = decode_params($params);
        // Retrieve the ministry record matching the decoded ID
        $data = Ministry::where('id', $id)->first();

        // Fetch all expense types for select inputs or filtering
        $expenseType = ExpenseType::all();

        // Fetch associated 
        $accountSub = AccountSub::where('ministry_id', $id)->get();
        $agency = Agency::where('ministry_id', $id)->get();
        $program = Program::where('ministry_id', $id)->orderBy('no', 'asc')->get();
        $budgetVoucher = BudgetVoucher::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetVoucher.index', [
            'data'          => $data,
            'params'        => $params,
            'expenseType'   => $expenseType,
            'program'       => $program,
            'agency'        => $agency,
            'budgetVoucher' => $budgetVoucher,
            'accountSub'    => $accountSub
        ]);
    }

    /**
     * AJAX: Fetch program sub-options by program ID request.
     */
    /**
     * Retrieve sub-programs by Program ID and render HTML <option> tags.
     */
    public function getByProgramId(Request $request)
    {
        // Check if program_id exists in the request
        if ($request->program_id) {
            // Query sub-program records filtering by program_id
            $data = ProgramSub::select('id', 'program_id', 'no', 'decription')
                ->where('program_id', $request->program_id)
                ->get();

            // Store selected ID for marking option as selected (if provided)
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

    /**
     * Retrieve sub-programs for editing purposes with a default placeholder option.
     */
    public function editByProgramId(Request $request)
    {
        // Return default option placeholder if program_id is missing
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        // Query sub-program records matching the given program_id
        $data = ProgramSub::select('id', 'no', 'decription')
            ->where('program_id', $request->program_id)
            ->get();

        // Cast selected ID to string for strict equality matching
        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        // Build HTML options list
        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }
        return response($html);
    }

    /**
     * Retrieve agencies by Program ID and render HTML <option> tags.
     */
    public function getByAgency(Request $request)
    {
        // Check if program_id parameter exists in the request
        if ($request->program_id) {
            // Retrieve agency records filtered by the given program_id
            $data = Agency::select('id', 'program_id', 'no', 'name')
                ->where('program_id', $request->program_id)
                ->get();

            // Store selected ID for marking option as selected (if provided)
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

    /**
     * Retrieve agencies by Program ID for edit forms, including a default placeholder option.
     */
    public function editByAgency(Request $request)
    {
        // Return default placeholder option if program_id is missing
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        // Query agency records matching the given program_id
        $data = Agency::select('id', 'no', 'name')
            ->where('program_id', $request->program_id)
            ->get();

        // Cast selected ID to string for strict equality matching
        $selectedId = (string) $request->selected_id;
        $html = '<option value="">ស្វែងរក...</option>';

        // Build HTML options list
        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->name}</option>";
        }

        return response($html);
    }

    /**
     * Retrieve cluster options by sub-program ID and render HTML <option> elements.
     */
    public function getByProgramSubId(Request $request)
    {
        // Check if program_sub_id parameter exists in the request
        if ($request->program_sub_id) {

            // Retrieve cluster records filtered by the specified program_sub_id
            $data = Cluster::select('id', 'program_sub_id', 'no', 'decription')
                ->where('program_sub_id', $request->program_sub_id)
                ->get();

            // Store selected ID for marking option as selected (if provided)
            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                // Perform strict string comparison to check if current option is selected
                $selected = ((string)$selectedId === (string)$d->id) ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
            }

            return response($html);
        }
        return response('');
    }

    /**
     * Retrieve cluster options by sub-program ID for edit forms, including a default placeholder option.
     */
    public function editByProgramSubId(Request $request)
    {
        // Return default placeholder option if program_sub_id is missing
        if (!$request->program_sub_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        // Query cluster records matching the specified program_sub_id
        $data = Cluster::select('id', 'no', 'decription')
            ->where('program_sub_id', $request->program_sub_id)
            ->get();

        // Cast selected ID to string for strict type comparison
        $selectedId = (string) $request->selected_id;

        // Initialize HTML string with default "Search..." placeholder option in Khmer
        $html = '<option value="">ស្វែងរក...</option>';

        // Build list of <option> tags and mark selected item
        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    /**
     * Show the form for creating a new resource.
     * @param  string|int  $params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create($params)
    {
        // Decode the incoming parameters
        $id = decode_params($params);

        $ministry = Ministry::where('id', $id)->first();

        // Fetch lookup datasets filtered by ministry ID
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $program = Program::where('ministry_id', $ministry->id)->orderBy('no', 'asc')->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::all();

        // Query initial vouchers joined with matching sub-account names
        $beginVoucher = BeginVoucher::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_vouchers.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id);
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

        return view('budgetplan::budgetVoucher.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('expenseType', $expenseType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('program', $program);
    }

    /**
     * @param  string|int  $params
     */
    public function getEarlyBalance(Request $request, $params)
    {
        return $this->processEarlyBalance($request, $params, showMessage: true);
    }

    /**
     * @param  string|int  $params
     */
    public function editEarlyBalance(Request $request, $params)
    {
        return $this->processEarlyBalance($request, $params, showMessage: false);
    }

    /**
     * Core handler logic shared between create and edit endpoints.
     * @param  \Illuminate\Http\Request  $request
     * @param  string|int  $params
     * @return \Illuminate\Http\RedirectResponse
     */

    private function processEarlyBalance(Request $request, $params, bool $showMessage = false)
    {
        $ministryId = decode_params($params);

        // 1. Fetch the initial BeginVoucher record
        $beginVoucher = BeginVoucher::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('program_id', $request->program_id)
            ->where('program_sub_id', $request->program_sub_id)
            ->where('cluster_id', $request->cluster_id)
            ->where('account_sub_id', $request->account_sub_id)
            ->first();

        if (!$beginVoucher) {
            $response = [
                'fin_law'           => 0,
                'credit_movement'   => 0,
                'new_credit_status' => 0,
                'credit'            => 0,
                'deadline_balance'  => 0,
                'exists'            => false,
            ];

            if ($showMessage) {
                $response['message'] = 'មិនមានទិន្នន័យបង្ហាញ.';
            }

            return response()->json($response);
        }

        // 2. Query non-deleted BudgetVouchers for real-time calculations
        $budgetQuery = BudgetVoucher::withoutTrashed()->where([
            'account_sub_id' => $beginVoucher->account_sub_id,
            'program_id'     => $beginVoucher->program_id,
            'program_sub_id' => $beginVoucher->program_sub_id,
            'cluster_id'     => $beginVoucher->cluster_id,
            'ministry_id'    => $beginVoucher->ministry_id,
        ]);

        // 3. If editing, exclude the current voucher being edited from used budget calculation
        if ($request->filled('voucher_id')) {
            $budgetQuery->where('id', '!=', $request->voucher_id);
        }

        // 4. Calculate actual used budget and remaining credit
        $totalUsedBudget = (float) $budgetQuery->sum('budget');
        $newCreditStatus = (float) ($beginVoucher->new_credit_status ?? 0);
        $availableCredit = $newCreditStatus - $totalUsedBudget;

        // 5. Return calculated JSON response
        return response()->json([
            'fin_law'           => (float) ($beginVoucher->fin_law ?? 0),
            'credit_movement'   => $this->calculateCreditMovement($beginVoucher->loans),
            'new_credit_status' => $newCreditStatus,
            'credit'            => (float) $availableCredit,
            'deadline_balance'  => (float) $totalUsedBudget,
            'exists'            => true,
        ]);
    }

    private function calculateCreditMovement($loans): float
    {
        if (!$loans) {
            return 0.0;
        }

        if ($loans instanceof \Illuminate\Support\Collection || is_iterable($loans)) {
            return (float) (collect($loans)->sum('total_increase') - collect($loans)->sum('decrease'));
        }

        return (float) (($loans->total_increase ?? 0) - ($loans->decrease ?? 0));
    }

    /**
     * Store a newly created budget voucher record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|int  $params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $params)
    {
        // Validate request inputs
        $validated = $request->validate([
            'legalID'          => 'required',
            'paymentVoucher'   => 'required',
            'legalNumber'      => 'nullable|string',
            'legalName'        => 'nullable|string',
            'cboProgram'       => 'required',
            'cboProgramSub'    => 'required',
            'cboCluster'       => 'required',
            'cboAgency'        => 'required',
            'cboSubAccount'    => 'required',
            'budget'           => 'required|numeric|min:0',
            'cboExpenseType'   => 'required',
            'txtDescription'   => 'required',
            'attachments'      => 'required|file|max:51200',
            'transactionDate'  => 'required|date',
            'requestDate'      => 'required|date',
            'legalDate'        => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Decode ministry parameters and fetch target record
            $id = decode_params($params);
            $ministry   = Ministry::where('id', $id)->first();

            // Locate corresponding initial voucher
            $beginVoucher = beginVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('ministry_id', $ministry->id)
                ->first();

            // Flash error if initial voucher is missing
            if (!$beginVoucher) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
                    ->flash();

                return back()->withInput();
            }

            // Verify available credit balance
            $applyValue      = (float) $validated['budget'];
            $currentCredit   = (float) ($beginVoucher->credit ?? 0);
            $remainingCredit = $currentCredit - $applyValue;

            if ($remainingCredit < 0) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('ឥណទានមិនគ្រប់គ្រាន់។', 'បញ្ហា')
                    ->flash();

                return back();
            }

            // Store file consistently in 'sources/voucher/pdf' on the public disk
            $path_store = 'uploads/voucher/' . date('Y-m-d');
            if (! File::exists($path_store)) {
                File::makeDirectory($path_store, 0777, true, true);
            }
            $filePath = $request->file('attachments')->store($path_store, 'public');

            // Create new budget voucher entry
            BudgetVoucher::create([
                'ministry_id'            => $ministry->id,
                'agency_id'              => $validated['cboAgency'],
                'program_id'             => $validated['cboProgram'],
                'program_sub_id'         => $validated['cboProgramSub'],
                'cluster_id'             => $validated['cboCluster'],
                'account_sub_id'         => $validated['cboSubAccount'],
                'no'                     => $beginVoucher->no,
                'fin_law'                => $beginVoucher->fin_law,
                'budget'                 => $applyValue,
                'expense_type_id'        => $validated['cboExpenseType'],
                'legal_id'               => $validated['legalID'],
                'payment_voucher_number' => $validated['paymentVoucher'],
                'legal_number'           => $validated['legalNumber'] ?? null,
                'legal_name'             => $validated['legalName'] ?? null,
                'status'                 => 'todo',
                'is_archived'            => 1,
                'description'            => strip_tags($validated['txtDescription']),
                'attachments'            => $filePath,
                'transaction_date'       => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
                'legal_date'             => $validated['legalDate'],
            ]);

            // Recalculate financial report metrics
            $this->recalculateAndSaveReport($beginVoucher);

            // Update applied balance on initial voucher
            $beginVoucher->refresh();
            $lastVoucher = BudgetVoucher::where('account_sub_id', $validated['cboSubAccount'])
                ->where('program_id', $validated['cboProgram'])
                ->where('program_sub_id', $validated['cboProgramSub'])
                ->where('cluster_id', $validated['cboCluster'])
                ->where('agency_id', $validated['cboAgency'])
                ->latest()->first();

            $beginVoucher->apply = $lastVoucher?->budget ?? 0;
            $beginVoucher->save();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            // Redirect based on user action context
            if ($request->has('submit')) {
                return redirect()->route('budgetVoucher.index', $params);
            }
            return redirect()->route('budgetVoucher.create', $params);
        } catch (\Throwable $e) {
            DB::rollBack();

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
     *  Show the form for editing the specified resource.
     *
     * @param  string|int  $params Encrypted/encoded parameter containing ministry ID
     * @param  string|int  $id Encrypted/encoded parameter containing voucher ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($params, $id)
    {
        // Decode encoded parameters
        $id       = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        // Fetch related dropdown datasets filtered 
        $agency      = Agency::where('ministry_id', $ministry->id)->get();
        $expenseType = ExpenseType::all();
        $accountSub  = AccountSub::where('ministry_id', $ministry->id)->get();

        // Locate active budget voucher record
        $module = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->where('is_archived', 1)
            ->first();

        // Return warning flash if voucher record is not found or processed
        if (!$module) {
            flash()->translate('en')->option('timeout', 2000)
                ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
            return back()->withInput();
        }

        // Retrieve program hierarchy for selection inputs
        $program    = Program::where('ministry_id', $ministry->id)->orderBy('no', 'asc')->get();
        $programId  = Program::where('ministry_id', $ministry->id)
            ->findOrFail($module->program_id);
        $programSub = ProgramSub::where('ministry_id', $ministry->id)
            ->where('program_id', $module->program_id)->get();

        // Query initial vouchers joined with matching sub-account names
        $beginVoucher = BeginVoucher::query()
            ->join('account_subs', function ($join) use ($ministry) {
                $join->on('begin_vouchers.account_sub_id', '=', 'account_subs.no')
                    ->where('account_subs.ministry_id', '=', $ministry->id);
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
            'legalID'          => 'required',
            'paymentVoucher'   => 'required',
            'legalNumber'      => 'nullable|string',
            'legalName'        => 'nullable|string|max:255',
            'cboProgram'       => 'required',
            'cboProgramSub'    => 'required',
            'cboCluster'       => 'required',
            'cboAgency'        => 'required',
            'cboSubAccount'    => 'required',
            'cboExpenseType'   => 'required',
            'budget'           => 'required',
            'txtDescription'   => 'required',
            'transactionDate'  => 'required|date',
            'requestDate'      => 'required|date',
            'legalDate'        => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();

            $voucher = BudgetVoucher::where([
                'id' => $id,
                'ministry_id'    => $ministry->id,
            ])->first();

            $beginVoucher = BeginVoucher::where([
                'account_sub_id' => $validated['cboSubAccount'],
                'program_id'     => $validated['cboProgram'],
                'program_sub_id' => $validated['cboProgramSub'],
                'cluster_id'     => $validated['cboCluster'],
                'ministry_id'    => $ministry->id
            ])->first();

            if (!$beginVoucher) {
                flash()->translate('en')->option('timeout', 2000)
                    ->error('មិនមានទិន្ន័យ', 'បញ្ហា')->flash();
                return back()->withInput();
            }

            // 1. Calculate credit limit (re-add current voucher budget before validating new amount)
            $applyValue = $validated['budget'];
            // $availableCredit = $beginVoucher->credit + $voucher->budget;

            // if (($availableCredit - $applyValue) < 0) {
            //     flash()->translate('en')->option('timeout', 2000)
            //         ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')->flash();
            //     return back()->withInput();
            // }
            // 2. Update Voucher
            $voucher->update([
                'agency_id'              => $validated['cboAgency'],
                'program_id'             => $validated['cboProgram'],
                'program_sub_id'         => $validated['cboProgramSub'],
                'cluster_id'             => $validated['cboCluster'],
                'account_sub_id'         => $validated['cboSubAccount'],
                'no'                     => $beginVoucher->no,
                'fin_law'                => $beginVoucher->fin_law,
                'budget'                 => $applyValue,
                'expense_type_id'        => $validated['cboExpenseType'],
                'legal_id'               => $validated['legalID'],
                'payment_voucher_number' => $validated['paymentVoucher'],
                'legal_number'           => $validated['legalNumber'] ?? null,
                'legal_name'             => $validated['legalName'] ?? null,
                'status'                 => 'todo',
                'is_archived'            => 1,
                'description'            => strip_tags($validated['txtDescription']),
                'transaction_date'       => $validated['transactionDate'],
                'request_date'           => $validated['requestDate'],
                'legal_date'             => $validated['legalDate'],
            ]);

            // 3. Recalculate and update BeginVoucher balances
            $this->recalculateAndSaveReport($beginVoucher);

            DB::commit();

            flash()->translate('en')->option('timeout', 2000)
                ->success('success_msg', 'successful')->flash();

            return redirect()->route('budgetVoucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('budgetVoucher.index', $params);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        // 1. Decode parameters and retrieve target voucher safely (throws 404 if not found)
        $id = decode_params($id);

        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        // 2. ✅ Delete attached files

        if ($voucher->attachments) {
            $filePath = $voucher->attachments;

            if (Storage::disk('public')->exists($filePath)) {
                $trashPath = 'trash/' . $filePath;

                // Ensure trash directory exists before moving
                $trashDir = dirname($trashPath);
                if (!Storage::disk('public')->exists($trashDir)) {
                    Storage::disk('public')->makeDirectory($trashDir);
                }

                Storage::disk('public')->move($filePath, $trashPath);

                $voucher->attachments = $trashPath;
                $voucher->save();
            }
        }


        // 3. Delete the voucher record
        $voucher->delete();

        // 4. Recalculate report data if a matching initial credit voucher exists
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
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budgetVoucher.index', $params);
    }

    public function restore($params, $id)
    {
        // Decode the incoming parameters
        $pid = decode_params($id);

        // Restore the soft-deleted database record
        $voucher = BudgetVoucher::withTrashed()->whereKey($pid)->first();

        if ($voucher->attachments) {
            $filePath = $voucher->attachments;

            if (Storage::disk('public')->exists($filePath)) {
                $originalPath = preg_replace('/^trash\//', '', $filePath);

                Storage::disk('public')->move($filePath, $originalPath);

                $voucher->attachments = $originalPath;
                $voucher->save();
            }
        }
        // Restore the soft-deleted database record
        $voucher->restore();


        // Recalculate report data if a matching initial credit voucher exists
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

    private function recalculateAndSaveReport(BeginVoucher $beginVoucher): void
    {
        // 1. Common query filter - explicitly excluding soft-deleted records
        $query = BudgetVoucher::withoutTrashed()->where([
            'account_sub_id' => $beginVoucher->account_sub_id,
            'program_id'     => $beginVoucher->program_id,
            'program_sub_id' => $beginVoucher->program_sub_id,
            'cluster_id'     => $beginVoucher->cluster_id,
            'ministry_id'    => $beginVoucher->ministry_id,
        ]);

        // 2. Fetch aggregates directly from SQL (only active/non-deleted records)
        $totalBudget  = (float) (clone $query)->sum('budget');
        $latestBudget = (float) (clone $query)->latest('created_at')->value('budget') ?? 0;

        // 3. Compute balances
        $earlyBalance    = max(0, $totalBudget - $latestBudget);
        $deadlineBalance = $earlyBalance + $latestBudget;

        // 4. Update model properties
        $beginVoucher->early_balance    = $earlyBalance;
        $beginVoucher->apply            = $latestBudget;
        $beginVoucher->deadline_balance = $deadlineBalance;
        $beginVoucher->credit           = $beginVoucher->new_credit_status - $deadlineBalance;

        // 5. Calculate percentages with division-by-zero safety
        $beginVoucher->law_average = ($deadlineBalance > 0 && !empty($beginVoucher->fin_law))
            ? ($deadlineBalance / $beginVoucher->fin_law * 100)
            : 0;

        $beginVoucher->law_correction = ($deadlineBalance > 0 && !empty($beginVoucher->new_credit_status))
            ? ($deadlineBalance / $beginVoucher->new_credit_status * 100)
            : 0;

        $beginVoucher->save();
    }

    public function export(Request $request, $params)
    {
        try {
            $id = decode_params($params);

            $query = BudgetVoucher::query();

            $query->leftJoin('begin_vouchers', function ($join) use ($id) {
                $join->on('budget_vouchers.account_sub_id', '=', 'begin_vouchers.account_sub_id')
                    ->on('budget_vouchers.no', '=', 'begin_vouchers.no')
                    ->on('budget_vouchers.program_id', '=', 'begin_vouchers.program_id')
                    ->where('begin_vouchers.ministry_id', $id);
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

            // Program
            if ($request->filled('cboProgram')) {
                $query->where('budget_vouchers.program_id', $request->cboProgram);
            }

            // Sub Account filter
            if ($request->filled('cboAccountSub')) {
                $query->where('budget_vouchers.account_sub_id', $request->cboAccountSub);
            }

            if ($request->filled('cboAgency')) {
                $query->where('budget_vouchers.agency_id', $request->cboAgency);
            }

            //ExpenseType
            if ($request->filled('cboExpenseType')) {
                $query->where('budget_vouchers.expense_type_id', $request->cboExpenseType);
            }

            // Date filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('budget_vouchers.legal_date', '>=', $request->start_date)
                    ->whereDate('budget_vouchers.transaction_date', '<=', $request->end_date);
            } else {
                if ($request->filled('start_date')) {
                    $query->whereDate('budget_vouchers.legal_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('budget_vouchers.transaction_date', '<=', $request->end_date);
                }
            }

            $data = $query->get();

            Log::info('Exported BeginVoucher Count', [
                'ministry_id' => $id,
                'count'       => $data->count(),
            ]);

            $export = new BeginExport(
                $data,
                $id,
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
}
