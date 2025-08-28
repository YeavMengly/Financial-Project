<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCreditMandate;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BudgetMandateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BudgetMandateDataTable $dataTable, $id)
    {
        $params = decode_params($id);
        $initialMandateId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialMandate = Ministry::findOrFail($initialMandateId);

        foreach ($initialMandate as $item) {
            $item->id;
        }
        $year = $item->id;

        request()->merge(['year' => $year]);

        $taskType = TaskType::all();
        $agency = Agency::all();
        $budgetMandate = BudgetMandate::all();
        return $dataTable->render('budgetplan::mandate.index', [
            'params' => $params,
            'initialVoucher' => $initialMandate,
            'taskType' => $taskType,
            'agency' => $agency,
            'budgetMandate' => $budgetMandate
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $params = decode_params($id);
        $initialMandate = Ministry::findOrFail($params);

        $beginCreditMandate = BeginCreditMandate::where('year', $params)->get();
        $taskType = TaskType::all();
        return view('budgetplan::mandate.create')->with('beginCreditMandate', $beginCreditMandate)->with('taskType', $taskType)->with('params', $params)->with('initialMandate', $initialMandate);;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $params = decode_params($id);
        $initialMandateId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialMandate = Ministry::findOrFail($initialMandateId);

        foreach ($initialMandate as $item) {
            $item->id;
        }

        $year = $item->id;

        // $validated = $request->validate([
        //     'subAccountNumber' => 'required|numeric',
        //     'program' => 'required|exists:begin_credit_mandates,program',
        //     'budget' => 'required|numeric|min:0',
        //     'task_type' => 'required|exists:task_types,task',
        //     'attachments' => 'nullable|array',
        //     'attachments.*' => 'file|mimes:pdf|max:2048',
        //     'date' => 'required|date',
        //     'txtDescription' => 'required|string',
        // ]);

        $validated = $request->validate([
            'cboAgency'  => 'required',
            'cboSubDepart'  => 'required',
            'subAccountNumber' => 'required',
            'program' => 'required|exists:begin_credit_mandates,program',
            'budget' => 'required|numeric|min:0',
            'task_type' => 'required|exists:task_types,task',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf|max:2048',
            'date' => 'required|date',
            'txtDescription' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $year) {
                $beginCredit = BeginCreditMandate::where('program', $validated['program'])
                    ->where('subAccountNumber', $validated['subAccountNumber'])
                    ->firstOrFail();


                if (!$beginCredit->subAccountNumber) {
                    throw new \Exception('មិនមាន អនុគណនី ឬកូដកម្មវិធី។');
                }

                $applyValue = $validated['budget'];
                $remainingCredit = $beginCredit->credit - $applyValue;

                if ($remainingCredit < 0) {
                    throw new \Exception('ឥណាទានមិនអាចតិចជាងសូន្យ។');
                }

                // Handle attachments
                $storedFilePaths = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $storedFilePaths[] = $file->store('certificateDatas', 'public');
                        }
                    }
                }

                // Create BudgetVoucher record
                BudgetMandate::create([
                    'agencyNumber' => $validated['cboAgency'],
                    'subDepart' => $validated['cboSubDepart'],
                    'year' => $year,
                    'subAccountNumber' => $validated['subAccountNumber'],
                    'program' => $beginCredit->program,
                    'budget' => $applyValue,
                    'task_type' => $validated['task_type'],
                    'attachments' => json_encode($storedFilePaths),
                    'date' => $validated['date'],
                    'txtDescription' => strip_tags($validated['txtDescription']),

                ]);

                // Update BeginCredit fields
                $this->recalculateAndSaveMandate($beginCredit);

                $beginCredit->refresh();
                $lastMandate = BudgetMandate::where('program', $validated['program'])->latest()->first();
                $beginCredit->apply = $lastMandate?->budget ?? 0;
                $beginCredit->save();
            });

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budget-mandate.index', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budget-mandate.index', $id);
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
    public function edit($params)
    {
        $id = decode_params($params);

        $budgetMandate = BudgetMandate::where('id', $id)->first();
        $beginCreditMandate = BeginCreditMandate::all();
        $taskType = TaskType::all();

        return view('budgetplan::mandate.edit')->with('budgetMandate', $budgetMandate)->with('beginCreditMandate', $beginCreditMandate)->with('taskType', $taskType)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $id = decode_params($params);

        $validated = $request->validate([
            'subAccountNumber' => 'required|numeric',
            'program' => 'required|exists:begin_credit_mandates,program',
            'budget' => 'required|numeric|min:0',
            'task_type' => 'required|exists:task_types,task',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf|max:2048',
            'date' => 'required|date',
            'txtDescription' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $id) {
                $mandate = BudgetMandate::where('id', $id)->firstOrFail(); // ✅ correct


                $beginCredit = BeginCreditMandate::where('program', $validated['program'])
                    ->where('subAccountNumber', $validated['subAccountNumber'])
                    ->firstOrFail();

                if (!$beginCredit->subAccountNumber) {
                    throw new \Exception('មិនមាន អនុគណនី ឬកូដកម្មវិធី។');
                }

                $applyValue = $validated['budget'];
                $remainingCredit = $beginCredit->credit - $applyValue;

                if ($remainingCredit < 0) {
                    throw new \Exception('ឥណទានមិនអាចតិចជាងសូន្យ។');
                }

                // Handle attachments
                $storedFilePaths = json_decode($mandate->attachments ?? '[]', true);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $storedFilePaths[] = $file->store('mandates', 'public');
                        }
                    }
                }

                // Update voucher
                $mandate->update([
                    'subAccountNumber' => $validated['subAccountNumber'],
                    'program' => $beginCredit->program,
                    'budget' => $applyValue,
                    'task_type' => $validated['task_type'],
                    'attachments' => json_encode($storedFilePaths),
                    'date' => $validated['date'],
                    'txtDescription' => strip_tags($validated['txtDescription']),
                ]);

                // Recalculate report
                $this->recalculateAndSaveMandate($beginCredit);

                // Update apply field
                $beginCredit->refresh();
                $lastMandate = BudgetMandate::where('program', $validated['program'])->latest()->first();
                $beginCredit->apply = $lastMandate?->budget ?? 0;
                $beginCredit->save();
            });

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budget-mandate.index', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budget-mandate.index', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $mandate = BudgetMandate::where('id', $id)->firstOrFail();

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
        $beginCredit = BeginCreditMandate::where('program', $mandate->program)
            ->where('subAccountNumber', $mandate->subAccountNumber)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveMandate($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budget-mandate.index');
    }

    public function getEarlyBalance($subAccountId, $programCode)
    {
        try {
            $beginCredit = BeginCreditMandate::where('subAccountNumber', $subAccountId)
                ->where('program', $programCode)
                ->first();

            $loan = $beginCredit ? $beginCredit->loans : null;

            if (!$beginCredit) {
                return response()->json([
                    'fin_law' => 0,
                    'credit_movement' => 0,
                    'new_credit_status' => 0,
                    'credit' => 0,
                    'deadline_balance' => 0,
                ]);
            }

            $loan = $beginCredit->loans;
            $credit_movement = ($loan->total_increase ?? 0) - ($loan->decrease ?? 0);

            return response()->json([
                'fin_law' => $beginCredit->fin_law ?? 0,
                'credit_movement' => $credit_movement,
                'new_credit_status' => $beginCredit->new_credit_status ?? 0,
                'credit' => $beginCredit->credit ?? 0,
                'deadline_balance' => $beginCredit->deadline_balance ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getEarlyBalance: " . $e->getMessage());

            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function recalculateAndSaveMandate(BeginCreditMandate $beginCredit)
    {
        $newApplyTotal = BudgetMandate::where('program', $beginCredit->program)
            ->where('subAccountNumber', $beginCredit->subAccountNumber)
            ->latest('created_at') // Order by latest created record
            ->value('budget') ?? 0;

        $beginCredit->early_balance = $this->calculateEarlyBalance($beginCredit);

        $beginCredit->apply = $newApplyTotal;
        $credit = $beginCredit->new_credit_status - $beginCredit->deadline_balance;
        $beginCredit->credit = $credit;
        $beginCredit->deadline_balance = $beginCredit->early_balance + $beginCredit->apply;
        $beginCredit->credit = $beginCredit->new_credit_status - $beginCredit->deadline_balance;
        $beginCredit->law_average = $beginCredit->deadline_balance > 0 ? ($beginCredit->deadline_balance / $beginCredit->fin_law) * 100 : 0;
        $beginCredit->law_correction =  $beginCredit->deadline_balance > 0 ? ($beginCredit->deadline_balance /  $beginCredit->new_credit_status) * 100 : 0;
        $beginCredit->save();
    }

    private function calculateEarlyBalance($beginCredit)
    {
        $budgetMandate = BudgetMandate::where('program', $beginCredit->program)
            ->where('subAccountNumber', $beginCredit->subAccountNumber)
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
}
