<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialBudgetVoucherDataTable;
use App\DataTables\BeginVoucherDataTable;
use App\Exports\ReportBook;
use App\Http\Controllers\Controller;
use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Content\Chapter;
use App\Models\Content\Cluster;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeginVoucherController extends Controller
{

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
     * AJAX: Fetch program sub-options by program ID.
     */
    public function editByProgramId(Request $request)
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

    public function editByAgency(Request $request)
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

    public function editByProgramSubId(Request $request)
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
        $validatedData = $request->validate([
            'cboProgram'     => 'required',
            'cboProgramSub'  => 'required',
            'cluster_id'     => 'required',
            'cboAgency'      => 'required',
            'cboSubAccount'  => 'required',
            'no'             => 'required',
            'fin_law'        => 'required|integer|min:1',
            'current_loan'   => 'required|integer|min:1',
            'txtDescription' => 'required|string|max:9999',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {

            $ministry   = Ministry::where('id', $id)->first();
            $program    = Program::where('id', $validatedData['cboProgram'])->first();
            $programSub = ProgramSub::where('program_id', $program->id)
                ->where('id', $validatedData['cboProgramSub'])
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

            $valueNo = $ministry->no . $program->no .  $programSub->no . '0' . $validatedData['no'];

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

            $beginCredit = BeginVoucher::create([
                'ministry_id'       => $ministry->id,
                'agency_id'         => $validatedData['cboAgency'],
                'program_id'        => $validatedData['cboProgram'],
                'program_sub_id'    => $validatedData['cboProgramSub'],
                'chapter_id'        => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'        => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'    => $validatedData['cboSubAccount'],
                'no'                => $valueNo,
                'txtDescription'    => strip_tags($validatedData['txtDescription']),
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

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
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
            'cboAgency'      => 'required',
            'cboSubAccount'  => 'required',
            'no'             => 'required',
            'fin_law'        => 'required|integer|min:1',
            'current_loan'   => 'required|integer|min:1',
            'txtDescription' => 'required|string|max:9999',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $program    = Program::findOrFail($validatedData['cboProgram']);
            $programSub = ProgramSub::where('program_id', $program->id)
                ->where('id', $validatedData['cboProgramSub'])
                ->firstOrFail();


            $beginCredit = BeginVoucher::where('id', $id)
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

            $valueNo = $ministry->no . $program->no . $programSub->no . '0' . $validatedData['no'];

            $currentApplyTotal = BudgetVoucher::where('no', $validatedData['no'])
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

            $beginCredit->update([
                'agency_id'        => $validatedData['cboAgency'],
                'program_id'       => $validatedData['cboProgram'],
                'program_sub_id'   => $validatedData['cboProgramSub'],
                'chapter_id'       => substr($validatedData['cboSubAccount'], 0, 2),
                'account_id'       => substr($validatedData['cboSubAccount'], 0, 4),
                'account_sub_id'   => $validatedData['cboSubAccount'],
                'no'               => $valueNo,
                'txtDescription'   => strip_tags($validatedData['txtDescription']),
                'fin_law'          => $validatedData['fin_law'],
                'current_loan'     => $validatedData['current_loan'],
                'new_credit_status' => $new_credit_status,
                'apply'            => $currentApplyTotal,
                'deadline_balance' => $deadline_balance,
                'early_balance'    => $early_balance,
                'credit'           => $credit,
                'law_average'      => $law_average,
                'law_correction'   => $law_correction,
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
        $beginCredit = BeginVoucher::where('id', $id)->first();
        $beginCredit->delete();

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

            if ($request->filled('no')) {
                $query->where('no', 'like', "%{$request->no}%");
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

    private function preventIfMinistryDeleted($ministryId)
    {
        $ministry = Ministry::withTrashed()->findOrFail($ministryId);

        if (!is_null($ministry->deleted_at)) {
            abort(403, 'This ministry is deleted. You can only view records.');
        }
    }
}
