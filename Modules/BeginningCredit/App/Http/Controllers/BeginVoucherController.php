<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialBudgetVoucherDataTable;
use App\DataTables\BeginVoucherDataTable;
use App\DataTables\BudgetAllocationDataTable;
use App\Exports\AnnualReport;
use App\Exports\ReportBook;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginMandate;
use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BudgetAllocation;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Content\Chapter;
use App\Models\Content\Cluster;
use App\Models\Content\ExpenseType;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginVoucherController extends Controller
{

    //   public function __construct()
    public function getIndex(InitialBudgetVoucherDataTable $dataTable)
    {
        $module = Ministry::all();

        return $dataTable->render('beginningcredit::beginVoucher.initialBudgetVoucher.index', ['module' => $module]);
    }
    /**
     * Display a listing of the resource.
     */

    public function index(BeginVoucherDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $chapter = Chapter::where('ministry_id', $ministry->id)
            ->get();
        $account = Account::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('beginningcredit::beginVoucher.index', [
            'ministry'   => $ministry,
            'params' => $params,
            'chapter' => $chapter,
            'account' => $account,
            'agency' => $agency,
            'accountSub' => $accountSub,
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
        $id        = decode_params($params);
        $ministry  = Ministry::where('id', $id)->first();
        $agency    = Agency::where('ministry_id', $ministry->id)->get();
        $program   = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        return view('beginningcredit::beginVoucher.create')
            ->with('ministry', $ministry)
            ->with('accountSub', $accountSub)
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('agency', $agency)
            ->with('program', $program);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        // 1. Create Cache Lock Signature to prevent duplicate concurrent requests
        $userId = auth()->check() ? auth()->id() : request()->ip();
        $requestSignature = 'begin_voucher_store_' . $userId . '_' . md5(json_encode($request->except('_token')));

        // 2. Lock for 10 seconds
        if (!\Illuminate\Support\Facades\Cache::add($requestSignature, true, 10)) {
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('Please wait, your request is already being processed.', 'Warning')
                ->flash();

            return back()->withInput();
        }

        $validatedData = $request->validate([
            'cboProgram'     => 'required',
            'cboProgramSub'  => 'required',
            'cboCluster'     => 'required',
            'cboAgency'      => 'required',
            'cboSubAccount'  => 'required',
            'fin_law'        => 'required|integer|min:1',
            'current_loan'   => 'required|integer|min:1',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {

            $ministry   = Ministry::where('id', $id)->first();
            $program    = Program::where('id', $validatedData['cboProgram'])->first();
            $programSub = ProgramSub::where('program_id', $program->id)
                ->where('id', $validatedData['cboProgramSub'])
                ->first();
            $cluster    = Cluster::where('id', $validatedData['cboCluster'])
                ->where('program_id', $validatedData['cboProgram'])
                ->where('program_sub_id', $validatedData['cboProgramSub'])->first();

            $validatedData['internal_increase']   = $validatedData['internal_increase']   ?? 0;
            $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
            $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
            $validatedData['decrease']            = $validatedData['decrease']            ?? 0;
            $validatedData['editorial']           = $validatedData['editorial']           ?? 0;

            // សរុប =​ កើន + មិនបានគ្រោងទុក + បំពេញបន្ថែម
            $total_increase   = $validatedData['internal_increase'] +
                $validatedData['unexpected_increase'] +
                $validatedData['additional_increase'];

            // ឥណទានថ្មី = ឥណទានបច្ចុប្បន្ន +​ សរុប​ - ថយ - វិចារណកម្ម
            $new_credit_status = $validatedData['current_loan'] +
                $total_increase -
                $validatedData['decrease'] -
                $validatedData['editorial'];

            $valueNo = $ministry->no . $program->no .  $programSub->no . $cluster->no;

            $currentApplyTotal = BudgetVoucher::where('no', $valueNo)
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->sum('budget');

            $early_balance     = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance  = $early_balance + $currentApplyTotal;
            $credit            = $new_credit_status - $deadline_balance;

            $law_average   = $validatedData['fin_law']
                ?  ($deadline_balance / $validatedData['fin_law']) * 100
                : 0;

            $law_correction = $new_credit_status
                ? ($deadline_balance / $new_credit_status) * 100
                : 0;

            $chapter = Chapter::where('no', substr($validatedData['cboSubAccount'], 0, 2))
                ->where('ministry_id', $ministry->id)
                ->first();

            $beginCredit = BeginVoucher::create([
                'ministry_id'       => $ministry->id,
                'type_id'           => $chapter->type_id ?? null,
                'agency_id'         => $validatedData['cboAgency'],
                'program_id'        => $validatedData['cboProgram'],
                'program_sub_id'    => $validatedData['cboProgramSub'],
                'chapter_id'        => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'        => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'    => $validatedData['cboSubAccount'],
                'cluster_id'        => $validatedData['cboCluster'],
                'no'                => $valueNo,
                'txtDescription'    => $cluster->decription ?? null,
                'fin_law'           => $validatedData['fin_law'],
                'current_loan'      => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply'             => $currentApplyTotal,
                'deadline_balance'  => $deadline_balance,
                'early_balance'     => $early_balance,
                'credit'            => $credit,
                'law_average'       => $law_average,
                'law_correction'    => $law_correction,
            ]);

            $BeginMandate = BeginMandate::create([
                'ministry_id'       => $ministry->id,
                'type_id'           => $chapter->type_id ?? null,
                'agency_id'         => $validatedData['cboAgency'],
                'program_id'        => $validatedData['cboProgram'],
                'program_sub_id'    => $validatedData['cboProgramSub'],
                'chapter_id'        => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'        => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'    => $validatedData['cboSubAccount'],
                'cluster_id'        => $validatedData['cboCluster'],
                'no'                => $valueNo,
                'txtDescription'    => $cluster->decription ?? null,
                'fin_law'           => $validatedData['fin_law'],
                'current_loan'      => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply'             => $currentApplyTotal,
                'deadline_balance'  => $deadline_balance,
                'early_balance'     => $early_balance,
                'credit'            => $credit,
                'law_average'       => $law_average,
                'law_correction'    => $law_correction,
            ]);

            $this->ResavedData($beginCredit);
            $this->ResavedDataMandate($BeginMandate);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 1000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('beginVoucher.index', $params);
            }

            return redirect()->route('beginVoucher.create', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('beginVoucher.index', $params);
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
    public function edit($params, $id)
    {
        $id       = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();

        $module = BeginVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        $program     = Program::where('ministry_id', $ministry->id)->get();
        $programId   = Program::findOrFail($module->program_id);
        $programSub  = ProgramSub::where('ministry_id', $ministry->id)
            ->where('program_id', $module->program_id)->get();
        $agency      = Agency::where('ministry_id', $ministry->id)->get();
        $accountSub  = AccountSub::where('ministry_id', $ministry->id)->get();

        return view('beginningcredit::beginVoucher.edit')
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
            'cboAgency'      => 'required',
            'cboSubAccount'  => 'required',
            'fin_law'        => 'required|integer|min:1',
            'current_loan'   => 'required|integer|min:1',
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


            // Get data query
            $beginVoucher = BeginVoucher::where('id', $id)
                ->where('program_id', $program->id)
                ->where('ministry_id', $ministry->id)
                ->first();

            // Get data query
            $beginMandate = BeginMandate::where('id', $id)
                ->where('program_id', $program->id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $validatedData['internal_increase']   = $validatedData['internal_increase']   ?? 0;
            $validatedData['unexpected_increase'] = $validatedData['unexpected_increase'] ?? 0;
            $validatedData['additional_increase'] = $validatedData['additional_increase'] ?? 0;
            $validatedData['decrease']            = $validatedData['decrease']            ?? 0;
            $validatedData['editorial']           = $validatedData['editorial']           ?? 0;

            // សរុប =​ កើន + មិនបានគ្រោងទុក + បំពេញបន្ថែម
            $total_increase   = $validatedData['internal_increase'] +
                $validatedData['unexpected_increase'] +
                $validatedData['additional_increase'];

            // ឥណទានថ្មី = ឥណទានបច្ចុប្បន្ន +​ សរុប​ - ថយ - វិចារណកម្ម
            $new_credit_status = $validatedData['current_loan'] +
                $total_increase -
                $validatedData['decrease'] -
                $validatedData['editorial'];

            $valueNo = $ministry->no . $program->no .  $programSub->no . $cluster->no;

            $currentApplyTotal = BudgetVoucher::where('no', $valueNo)
                ->where('account_sub_id', $validatedData['cboSubAccount'])
                ->where('agency_id', $validatedData['cboAgency'])
                ->sum('budget');

            $early_balance     = $currentApplyTotal > 0 ? $currentApplyTotal : 0;
            $deadline_balance  = $early_balance + $currentApplyTotal;
            $credit            = $new_credit_status - $deadline_balance;

            $law_average   = $validatedData['fin_law']
                ?  ($deadline_balance / $validatedData['fin_law']) * 100
                : 0;

            $law_correction = $new_credit_status
                ? ($deadline_balance / $new_credit_status) * 100
                : 0;

            $beginVoucher->update([
                'ministry_id'       => $ministry->id,
                'agency_id'         => $validatedData['cboAgency'],
                'program_id'        => $validatedData['cboProgram'],
                'program_sub_id'    => $validatedData['cboProgramSub'],
                'chapter_id'        => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'        => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'    => $validatedData['cboSubAccount'],
                'cluster_id'                => $validatedData['cboCluster'],
                'no'                => $valueNo,
                'txtDescription'    => $cluster->decription ?? null,
                'fin_law'           => $validatedData['fin_law'],
                'current_loan'      => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply'             => $currentApplyTotal,
                'deadline_balance'  => $deadline_balance,
                'early_balance'     => $early_balance,
                'credit'            => $credit,
                'law_average'       => $law_average,
                'law_correction'    => $law_correction,
            ]);

            $beginMandate->update([
                'ministry_id'       => $ministry->id,
                'agency_id'         => $validatedData['cboAgency'],
                'program_id'        => $validatedData['cboProgram'],
                'program_sub_id'    => $validatedData['cboProgramSub'],
                'chapter_id'        => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'        => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'    => $validatedData['cboSubAccount'],
                'cluster_id'                => $validatedData['cboCluster'],
                'no'                => $valueNo,
                'txtDescription'    => $cluster->decription ?? null,
                'fin_law'           => $validatedData['fin_law'],
                'current_loan'      => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply'             => $currentApplyTotal,
                'deadline_balance'  => $deadline_balance,
                'early_balance'     => $early_balance,
                'credit'            => $credit,
                'law_average'       => $law_average,
                'law_correction'    => $law_correction,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success(
                    'success_msg',
                    'successful'
                )
                ->flash();

            return redirect()->route('beginVoucher.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាព: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('beginVoucher.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);

        $beginVoucher = BeginVoucher::where('id', $id)->first();
        $beginMandate = BeginMandate::where('id', $id)->first();

        $beginVoucher->delete();
        $beginMandate->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('beginVoucher.index', $params);
    }

    /**
     * Helper: Recalculate and save begin voucher data.
     */
    private function ResavedData(BeginVoucher $data)
    {
        $newApplyTotal = BudgetVoucher::where('no', $data->no)
            ->where('account_sub_id', $data->account_sub_id)
            ->where('agency_id', $data->agency_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $data->apply            = $newApplyTotal;
        $data->deadline_balance = $data->early_balance + $data->apply;
        $data->credit           = $data->new_credit_status - $data->deadline_balance;

        $data->law_average      = $data->deadline_balance > 0
            ? ($data->deadline_balance / $data->fin_law) * 100 : 0;

        $data->law_correction   = $data->deadline_balance > 0
            ? ($data->deadline_balance / $data->new_credit_status) * 100 : 0;

        $data->save();
    }

    private function ResavedDataMandate(BeginMandate $data)
    {
        $newApplyTotal = BudgetMandate::where('no', $data->no)
            ->where('account_sub_id', $data->account_sub_id)
            ->where('agency_id', $data->agency_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $data->apply            = $newApplyTotal;
        $data->deadline_balance = $data->early_balance + $data->apply;
        $data->credit           = $data->new_credit_status - $data->deadline_balance;

        $data->law_average      = $data->deadline_balance > 0
            ? ($data->deadline_balance / $data->fin_law) * 100 : 0;

        $data->law_correction   = $data->deadline_balance > 0
            ? ($data->deadline_balance / $data->new_credit_status) * 100 : 0;

        $data->save();
    }

    // Export Data to Excel
    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);

            // Base query: full BeginVoucher models
            $query = BeginVoucher::query()
                ->where('ministry_id', $ministryId);

            // Apply the same filters as in DataTable::query()
            if ($request->filled('agency')) {
                $query->where('agency_id', $request->agency);
            }

            if ($request->filled('account')) {
                $query->where('account_id', $request->account);
            }

            if ($request->filled('accountSub')) {
                $query->where('account_sub_id', $request->accountSub);
            }

            if ($request->filled('cluster')) {
                $query->where('cluster_id', $request->cluster);
            }

            if ($request->filled('txtDescription')) {
                $query->where('txtDescription', 'like', "%{$request->txtDescription}%");
            }

            $query->orderBy('created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported BeginVoucher Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចេញទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('beginVoucher.index', $params);
            }

            // Pass filtered data + ministry id into export
            $export = new ReportBook($data, $ministryId);

            // you can pass $request if you want to use date filters/text in header
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

            return redirect()->route('beginVoucher.index', $params);
        }
    }

    public function exportReport(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);

            $query = BeginVoucher::query()
                ->join('types', 'begin_vouchers.type_id', '=', 'types.id')
                ->where('begin_vouchers.ministry_id', $ministryId)
                ->select('begin_vouchers.*', 'types.name as type_name',);

            if ($request->filled('agency')) {
                $query->where('agency_id', $request->agency);
            }

            if ($request->filled('account')) {
                $query->where('account_id', $request->account);
            }

            if ($request->filled('accountSub')) {
                $query->where('account_sub_id', $request->accountSub);
            }

            if ($request->filled('cluster')) {
                $query->where('cluster_id', $request->cluster);
            }

            if ($request->filled('txtDescription')) {
                $query->where('txtDescription', 'like', "%{$request->txtDescription}%");
            }

            $query->orderBy('created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported BeginVoucher Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចេញទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('beginVoucher.index', $params);
            }

            // Pass filtered data + ministry id into export
            $export = new AnnualReport($data, $ministryId);

            // you can pass $request if you want to use date filters/text in header
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

            return redirect()->route('beginVoucher.index', $params);
        }
    }

    /**
     * Budget Allocation Section.
     */

    /**
     * Display a listing of the resource.
     */
    public function indexBudgetAllocation(BudgetAllocationDataTable $dataTable, $params, $budgetAllocationId)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $expenseTypes = ExpenseType::all();
        $module = BeginVoucher::where('id', decode_params($budgetAllocationId))
            ->where('ministry_id', $ministry->id)
            ->first();

        return $dataTable->render('beginningcredit::beginVoucher.budgetAllocation.index', [
            'ministry'   => $ministry,
            'params' => $params,
            'budgetAllocationId' => $budgetAllocationId,
            'expenseTypes' => $expenseTypes,
            'module' => $module
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function createBudgetAllocation($params, $budgetAllocationId)
    {
        $id = decode_params($params);

        $ministry = Ministry::where('id', $id)->first();

        $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
            ->where('ministry_id', $ministry->id)
            ->firstOrFail();
        $expenseTypes = ExpenseType::all();
        $allocations = BudgetAllocation::where('ministry_id', $ministry->id)
            ->where('budget_begin_voucher_id', $beginVoucher->id)
            ->selectRaw('budget_expense_type_id, SUM(amount) as total_amount')
            ->groupBy('budget_expense_type_id', 'budget_begin_voucher_id')
            ->pluck('total_amount', 'budget_expense_type_id');
        $allocatedAmount = $allocations->sum();
        $remainingFinLaw = (float) $beginVoucher->fin_law - $allocatedAmount;

        return view('beginningcredit::beginVoucher.budgetAllocation.create')
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('expenseTypes', $expenseTypes)
            ->with('remainingFinLaw', $remainingFinLaw)
            ->with('allocatedAmount', $allocatedAmount)
            ->with('budgetAllocationId', $budgetAllocationId);
    }

    // public function storeBudgetAllocation(Request $request, $params, $budgetAllocationId)
    // {

    //     // 1. Create Cache Lock Signature to prevent duplicate concurrent requests
    //     $userId = auth()->check() ? auth()->id() : request()->ip();
    //     $requestSignature = 'budget_allocation_store_' . $userId . '_' . md5(json_encode($request->except('_token')));

    //     // 2. Lock for 10 seconds
    //     if (!\Illuminate\Support\Facades\Cache::add($requestSignature, true, 10)) {
    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('Please wait, your request is already being processed.', 'Warning')
    //             ->flash();

    //         return back()->withInput();
    //     }

    //     $validatedData = $request->validate([
    //         'amount'         => 'required|numeric|min:0.01',
    //         'cboExpenseType' => 'required|exists:expense_types,id',
    //         'rounds'         => 'nullable|integer|min:1|max:4', // Validate as single integer
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // 1. Fetch Ministry & Voucher
    //         $ministry = Ministry::where('id', decode_params($params))->first();
    //         $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
    //             ->where('ministry_id', $ministry->id)
    //             ->first();

    //         if (!$beginVoucher) {
    //             throw new \Exception('រកមិនឃើញឥណទានដើមគ្រាឡើយ');
    //         }

    //         // 2. Check remaining budget limit
    //         $currentAllocated = BudgetAllocation::where('ministry_id', $ministry->id)
    //             ->where('budget_begin_voucher_id', $beginVoucher->id)
    //             ->sum('amount');

    //         $remainingBudget = (float) $beginVoucher->fin_law - $currentAllocated;

    //         if ($validatedData['amount'] > $remainingBudget) {
    //             throw new \Exception('ទឹកប្រាក់បែងចែកលើសពីច្បាប់ហិរញ្ញវត្ថុដែលនៅសល់ (' . number_format($remainingBudget) . ')');
    //         }

    //         // 3. Save new allocation
    //         BudgetAllocation::create([
    //             'ministry_id'             => $ministry->id,
    //             'budget_begin_voucher_id' => $beginVoucher->id,
    //             'budget_expense_type_id'  => $validatedData['cboExpenseType'],
    //             'amount'                  => $validatedData['amount'],
    //             'rounds'                  => $validatedData['rounds'], // Saves as 1, 2, 3, 4, or null
    //         ]);

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 1000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         return redirect()->route('budgetAllocation.index', [
    //             'params'             => $params,
    //             'budgetAllocationId' => $budgetAllocationId,
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('BudgetAllocation Store Error: ' . $e->getMessage());

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 3000)
    //             ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return redirect()->back()->withInput();
    //     }
    // }
    public function storeBudgetAllocation(Request $request, $params, $budgetAllocationId)
    {
        // 1. VALIDATE FIRST! (Do not lock the cache if validation fails)
        $validatedData = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'cboExpenseType' => 'required|exists:expense_types,id',
            'rounds'         => 'nullable|array',
            'rounds.*'       => 'integer|min:1|max:4',
        ]);

        // 2. Create Cache Lock Signature (Only locks if form is perfectly valid)
        $userId = auth()->check() ? auth()->id() : request()->ip();
        $requestSignature = 'budget_allocation_store_' . $userId . '_' . md5(json_encode($request->except('_token')));

        if (!\Illuminate\Support\Facades\Cache::add($requestSignature, true, 10)) {
            flash()->translate('en')->option('timeout', 2000)->error('Please wait, your request is already being processed.', 'Warning')->flash();
            return back()->withInput();
        }

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
                ->where('ministry_id', $ministry->id)
                ->first();

            if (!$beginVoucher) throw new \Exception('រកមិនឃើញឥណទានដើមគ្រាឡើយ');

            // 3. SAFELY HANDLE ROUNDS (Fixes the Undefined Array Key Crash)
            $roundsInput = $request->input('rounds');
            $roundsToSave = empty($roundsInput) ? [null] : $roundsInput;

            // 4. Prevent Duplicate Database Entry for ANY of the selected rounds
            $existingAllocation = BudgetAllocation::where('budget_begin_voucher_id', $beginVoucher->id)
                ->where('budget_expense_type_id', $validatedData['cboExpenseType'])
                ->where(function ($query) use ($roundsToSave) {
                    if (in_array(null, $roundsToSave, true)) {
                        $query->whereNull('rounds');
                    } else {
                        $query->whereIn('rounds', $roundsToSave);
                    }
                })->exists();

            if ($existingAllocation) throw new \Exception('ប្រភេទចំណាយក្នុងជុំនេះត្រូវបានបែងចែករួចហើយ!');

            // 5. Check remaining budget limit
            $currentAllocated = BudgetAllocation::where('ministry_id', $ministry->id)
                ->where('budget_begin_voucher_id', $beginVoucher->id)
                ->sum('amount');

            $remainingBudget = (float) $beginVoucher->fin_law - $currentAllocated;

            // Multiply the amount by the number of rows we are creating
            $totalAmountToDeduct = $validatedData['amount'] * count($roundsToSave);

            if ($totalAmountToDeduct > $remainingBudget) {
                throw new \Exception('ទឹកប្រាក់បែងចែកសរុប (' . number_format($totalAmountToDeduct) . ') លើសពីច្បាប់ហិរញ្ញវត្ថុដែលនៅសល់ (' . number_format($remainingBudget) . ')');
            }

            // 6. Save multiple rows (one for each round)
            foreach ($roundsToSave as $round) {
                BudgetAllocation::create([
                    'ministry_id'             => $ministry->id,
                    'budget_begin_voucher_id' => $beginVoucher->id,
                    'budget_expense_type_id'  => $validatedData['cboExpenseType'],
                    'amount'                  => $validatedData['amount'],
                    'rounds'                  => $round, // Inserts 1, 2, 3, 4, or null
                ]);
            }

            DB::commit();

            // Release the lock upon success so the user can immediately add another entry
            \Illuminate\Support\Facades\Cache::forget($requestSignature);

            flash()->translate('en')->option('timeout', 1000)->success('success_msg', 'successful')->flash();

            return redirect()->route('budgetAllocation.index', [
                'params'             => $params,
                'budgetAllocationId' => $budgetAllocationId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Cache::forget($requestSignature); // Release lock on error

            Log::error('BudgetAllocation Store Error: ' . $e->getMessage());
            flash()->translate('en')->option('timeout', 3000)->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->back()->withInput();
        }
    }
    public function editBudgetAllocation($params, $budgetAllocationId, $id)
    {

        $ministry = Ministry::where('id', decode_params($params))->first();
        $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
            ->where('ministry_id', decode_params($params))
            ->first();

        $expenseTypes = ExpenseType::whereIn('id', [2, 3, 4, 5, 7])->get();
        // Fetch the specific Budget Allocation entry
        $module = BudgetAllocation::where('id', decode_params($id))
            ->where('budget_begin_voucher_id', $beginVoucher->id)
            ->first();

        if (!$module) {
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('ការស្វែងរកមិនបានជោគជ័យ។', 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetAllocation.index', [
                'params' => $params,
                'budgetAllocationId' => $budgetAllocationId
            ]);
        }

        return view('beginningcredit::beginVoucher.budgetAllocation.edit')
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('expenseTypes', $expenseTypes)
            ->with('budgetAllocationId', $budgetAllocationId)
            ->with('module', $module);
    }

    public function updateBudgetAllocation(Request $request, $params, $budgetAllocationId, $id)
    {
        // 1. Lock to prevent double-submit
        $userId = auth()->check() ? auth()->id() : request()->ip();
        $requestSignature = 'budget_allocation_update_' . $userId . '_' . md5(json_encode($request->except('_token')));

        if (!\Illuminate\Support\Facades\Cache::add($requestSignature, true, 10)) {
            flash()->translate('en')->option('timeout', 2000)->error('Please wait, processing.', 'Warning')->flash();
            return back()->withInput();
        }

        $validatedData = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'cboExpenseType' => 'required|exists:expense_types,id',
            // 'rounds'         => 'nullable|integer|max:4',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->firstOrFail();
            $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
                ->where('ministry_id', $ministry->id)->firstOrFail();

            // Find the exact allocation we are editing
            $allocation = BudgetAllocation::findOrFail($id);

            // 2. Prevent Duplicate Database Entry (IGNORE CURRENT RECORD)
            $existingAllocation = BudgetAllocation::where('budget_begin_voucher_id', $beginVoucher->id)
                ->where('budget_expense_type_id', $validatedData['cboExpenseType'])
                ->where('id', '!=', $allocation->id) // <--- CRITICAL: Ignore itself
                ->exists();

            if ($existingAllocation) throw new \Exception('ប្រភេទចំណាយនេះត្រូវបានបែងចែករួចហើយ!');

            // 3. Check remaining budget limit (IGNORE CURRENT RECORD AMOUNT)
            // Calculate how much is allocated to OTHER expenses
            $otherAllocated = BudgetAllocation::where('ministry_id', $ministry->id)
                ->where('budget_begin_voucher_id', $beginVoucher->id)
                ->where('id', '!=', $allocation->id) // <--- CRITICAL: Ignore itself
                ->sum('amount');

            $remainingBudget = (float) $beginVoucher->fin_law - $otherAllocated;

            if ($validatedData['amount'] > $remainingBudget) {
                throw new \Exception('ទឹកប្រាក់បែងចែកលើសពីច្បាប់ហិរញ្ញវត្ថុដែលនៅសល់ (' . number_format($remainingBudget) . ')');
            }

            // 4. Update the allocation
            $allocation->update([
                'budget_expense_type_id'  => $validatedData['cboExpenseType'],
                'amount'                  => $validatedData['amount'],
                // Add ?? null so it empties the field if user checked the skip button
                // 'rounds'                  => $validatedData['rounds'] ?? null,
            ]);

            DB::commit();

            flash()->translate('en')->option('timeout', 1000)->success('success_msg', 'successful')->flash();

            return redirect()->route('budgetAllocation.index', [
                'params'             => $params,
                'budgetAllocationId' => $budgetAllocationId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Cache::forget($requestSignature);

            Log::error('BudgetAllocation Update Error: ' . $e->getMessage());
            flash()->translate('en')->option('timeout', 3000)->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->back()->withInput();
        }
    }

    public function destroyBudgetAllocation($params, $budgetAllocationId, $id)
    {

        $ministry = Ministry::where('id', decode_params($params))->first();

        // Fetch the begin voucher
        $beginVoucher = BeginVoucher::where('id', decode_params($budgetAllocationId))
            ->where('ministry_id', $ministry->id)
            ->first();

        // Fetch the specific Budget Allocation entry
        $module = BudgetAllocation::where('id', decode_params($id))
            ->where('budget_begin_voucher_id', $beginVoucher->id)
            ->first();

        if (!$module) {
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('ការស្វែងរកមិនបានជោគជ័យ។', 'បញ្ហា')
                ->flash();

            return redirect()->route('budgetAllocation.index', [
                'params' => $params,
                'budgetAllocationId' => $budgetAllocationId
            ]);
        }

        $module->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budgetAllocation.index', [
            'params' => $params,
            'budgetAllocationId' => $budgetAllocationId
        ]);
    }
}
