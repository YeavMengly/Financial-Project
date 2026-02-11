<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetMandateDataTable;
use App\DataTables\Budget\InitialMandateDataTable;
use App\Exports\BeginMandateExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginMandate;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\Content\Program;
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

    /**
     * Display a listing of the resource.
     */
    public function index(BudgetMandateDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $taskType = TaskType::all();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetMandate.index', [
            'data' => $data,
            'params' => $params,
            'taskType' => $taskType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
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
        $taskType = TaskType::all();

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
            ->with('taskType', $taskType)
            ->with('params', $params)
            ->with('beginMandate', $beginMandate)
            ->with('program', $program);
    }

    public function getEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $request->validate([
            'account_sub_id' => 'required',
            'no'             => 'required'
        ]);

        $beginMandate = BeginMandate::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('account_sub_id', $request->account_sub_id)
            ->where('no', $request->no)
            ->first();

        if (!$beginMandate) {
            return response()->json([
                'fin_law'            => 0,
                'credit_movement'    => 0,
                'new_credit_status'  => 0,
                'credit'             => 0,
                'deadline_balance'   => 0,
                'exists'             => false,
            ]);
        }

        $loan = $beginMandate->loans;
        $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

        return response()->json([
            'fin_law'            => (float) ($beginMandate->fin_law ?? 0),
            'credit_movement'    => (float) $credit_movement,
            'new_credit_status'  => (float) ($beginMandate->new_credit_status ?? 0),
            'credit'             => (float) ($beginMandate->credit ?? 0),
            'deadline_balance'   => (float) ($beginMandate->deadline_balance ?? 0),
            'exists'             => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request, $params)
    // {
    //     $validated = $request->validate([
    //         'cboAgency'       => 'required',
    //         'cboSubAccount'   => 'required',
    //         'no'              => 'required',
    //         'budget'          => 'required|numeric|min:0',
    //         'task_type'       => 'required',
    //         'attachments'     => 'nullable|array',
    //         'attachments.*'   => 'file|mimes:pdf,doc,docx|max:2048',
    //         'date'            => 'required|date',
    //         'txtDescription'  => 'required',
    //     ]);
    //     try {
    //         $ministryId = decode_params($params);
    //         $ministry   = Ministry::where('id', $ministryId)->first();

    //         DB::transaction(function () use ($request, $validated, $ministry) {
    //             $beginMandate = BeginMandate::where('no', $validated['no'])
    //                 ->where('account_sub_id', $validated['cboSubAccount'])
    //                 // ->where('agency_id', $validated['cboAgency'])
    //                 ->where('ministry_id', $ministry->id)
    //                 ->first();

    //             if (!$beginMandate) {
    //                 flash()
    //                     ->translate('en')
    //                     ->option('timeout', 2000)
    //                     ->error('មិនមានទិន្ន័យ', 'បញ្ហា')
    //                     ->flash();

    //                 return back()->withInput();
    //             }

    //             $applyValue      = (float) $validated['budget'];
    //             $currentCredit   = (float) ($beginMandate->credit ?? 0);
    //             $remainingCredit = $currentCredit - $applyValue;

    //             if ($remainingCredit < 0) {
    //                 flash()
    //                     ->translate('en')
    //                     ->option('timeout', 2000)
    //                     ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
    //                     ->flash();

    //                 return back();
    //             }

    //             $stored = [];
    //             if ($request->hasFile('attachments')) {
    //                 foreach ($request->file('attachments') as $file) {
    //                     if ($file->isValid()) {
    //                         $stored[] = $file->store('certificateDatas', 'public');
    //                     }
    //                 }
    //             }

    //             BudgetMandate::create([
    //                 'ministry_id'    => $ministry->id,
    //                 'agency_id'      => $validated['cboAgency'],
    //                 'account_sub_id' => $validated['cboSubAccount'],
    //                 'no'             => $validated['no'],
    //                 'txtDescription' => strip_tags($validated['txtDescription']),
    //                 'budget'         => $applyValue,
    //                 'task_type'      => $validated['task_type'],
    //                 'attachments'    => json_encode($stored),
    //                 'date'           => $validated['date'],
    //             ]);
    //             $this->recalculateAndSaveReport($beginMandate);

    //             $beginMandate->refresh();
    //             $lastVoucher = BudgetMandate::where('no', $validated['no'])
    //                 ->where('account_sub_id', $validated['cboSubAccount'])
    //                 ->where('agency_id', $validated['cboAgency'])
    //                 ->latest()->first();

    //             $beginMandate->apply = $lastVoucher?->budget ?? 0;
    //             $beginMandate->save();
    //         });

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         return redirect()->route('budgetMandate.index', $params);
    //     } catch (\Throwable $e) {
    //         Log::error('BudgetVoucher store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

    //         flash()->translate('en')->option('timeout', 2000)
    //             ->error($e->getMessage(), 'បញ្ហា')->flash();

    //         return back()->withInput();
    //     }
    // }

    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'no'              => 'required',
            'budget'          => 'required|numeric|min:0',
            'task_type'       => 'required',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|mimes:pdf,doc,docx|max:2048',
            'date'            => 'required|date',
            'txtDescription'  => 'required',
        ]);
        try {
            $ministryId = decode_params($params);
            $ministry   = Ministry::where('id', $ministryId)->first();

            DB::transaction(function () use ($request, $validated, $ministry) {
                $beginMandate = BeginMandate::where('no', $validated['no'])
                    ->where('account_sub_id', $validated['cboSubAccount'])
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

                BudgetMandate::create([
                    'ministry_id'    => $ministry->id,
                    'agency_id'      => $validated['cboAgency'],
                    'account_sub_id' => $validated['cboSubAccount'],
                    'no'             => $validated['no'],
                    'txtDescription' => strip_tags($validated['txtDescription']),
                    'budget'         => $applyValue,
                    'task_type'      => $validated['task_type'],
                    'attachments'    => json_encode($stored),
                    'date'           => $validated['date'],
                ]);

                $this->recalculateAndSaveReport($beginMandate);

                $beginMandate->refresh();
                $lastMandate = BudgetMandate::where('no', $validated['no'])
                    ->where('account_sub_id', $validated['cboSubAccount'])
                    ->where('agency_id', $validated['cboAgency'])
                    ->latest()->first();

                $beginMandate->apply = $lastMandate?->budget ?? 0;
                $beginMandate->save();
            });

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
        $taskType = TaskType::all();

        $module = BudgetMandate::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

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
            ->with('taskType', $taskType)
            ->with('agency', $agency)
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
            'cboAgency'       => 'required',
            'cboSubAccount'   => 'required',
            'no'              => 'required',
            'budget'          => 'required|numeric|min:0',
            'task_type'       => 'required',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|mimes:pdf,doc,docx|max:2048',
            'date'            => 'required|date',
            'txtDescription'  => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $mandate = BudgetMandate::where('id', $id)
                ->where('ministry_id', $ministry->id)->first();

            $beginCredit = BeginMandate::where('no', $validated['no'])
                ->where('account_sub_id', $validated['cboSubAccount'])
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
                'account_sub_id' => $validated['cboSubAccount'],
                'no' => $beginCredit->no,
                'budget' => $applyValue,
                'task_type' => $validated['task_type'],
                'attachments' => json_encode($storedFilePaths),
                'date' => $validated['date'],
                'txtDescription' => strip_tags($validated['txtDescription']),
            ]);

            $this->recalculateAndSaveReport($beginCredit);

            $beginCredit->refresh();
            $lastMandater = BudgetMandate::where('no', $validated['no'])
                ->where('account_sub_id', $validated['cboSubAccount'])
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        // $params = decode_params($params);
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
        $beginCredit = BeginMandate::where('no', $mandate->no)
            ->where('account_sub_id', $mandate->account_sub_id)
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

    private function recalculateAndSaveReport(BeginMandate $beginMandate)
    {
        $newApplyTotal = BudgetMandate::where('no', $beginMandate->no)
            ->where('account_sub_id', $beginMandate->account_sub_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $beginMandate->early_balance = $this->calculateEarlyBalance($beginMandate);

        $beginMandate->apply = $newApplyTotal;
        $credit = $beginMandate->new_credit_status - $beginMandate->deadline_balance;
        $beginMandate->credit = $credit;
        $beginMandate->deadline_balance = $beginMandate->early_balance + $beginMandate->apply;
        $beginMandate->credit = $beginMandate->new_credit_status - $beginMandate->deadline_balance;
        $beginMandate->law_average = $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance / $beginMandate->fin_law) * 100 : 0;
        $beginMandate->law_correction =  $beginMandate->deadline_balance > 0 ? ($beginMandate->deadline_balance /  $beginMandate->new_credit_status) * 100 : 0;
        $beginMandate->save();
    }

    private function calculateEarlyBalance($data)
    {
        $budgetMandate = BudgetMandate::where('no', $data->no)
            ->where('account_sub_id', $data->account_sub_id)
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

            $query = BeginMandate::query()
                ->leftJoin('budget_mandate_loans', 'begin_mandates.account_sub_id', '=', 'budget_mandate_loans.account_sub_id')
                ->where('begin_mandates.ministry_id', $ministryId)
                ->select(
                    'begin_mandates.*',
                    'budget_mandate_loans.internal_increase as loan_internal_increase',
                    'budget_mandate_loans.unexpected_increase as loan_unexpected_increase',
                    'budget_mandate_loans.additional_increase as loan_additional_increase',
                    'budget_mandate_loans.total_increase as loan_total_increase',
                    'budget_mandate_loans.decrease as loan_decrease',
                    'budget_mandate_loans.editorial as loan_editorial'
                );


            // === Filters (PREFIX table name!) ===
            if ($request->filled('agency')) {
                $query->where('begin_mandates.agency_id', $request->agency);
            }

            if ($request->filled('account')) {
                $query->where('begin_mandates.account_id', $request->account);
            }

            if ($request->filled('accountSub')) {
                $query->where('begin_mandates.account_sub_id', $request->accountSub);
            }

            if ($request->filled('no')) {
                $query->where('begin_mandates.no', 'like', "%{$request->no}%");
            }

            if ($request->filled('txtDescription')) {
                $query->where('begin_mandates.txtDescription', 'like', "%{$request->txtDescription}%");
            }

            $query->orderBy('begin_mandates.created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported BeginMandate Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចេញទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('budgetMandate.index', $params);
            }

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

            return redirect()->route('budgetMandate.index', $params);
        }
    }
}
