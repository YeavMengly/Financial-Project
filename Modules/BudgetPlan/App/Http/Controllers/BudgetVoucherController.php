<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetVoucherDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\BudgetPlan\InitialVoucher;
use App\Models\SubDepart;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BudgetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BudgetVoucherDataTable $dataTable, $id)
    {
        $params = decode_params($id);
        $initialVoucherId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialVoucher = Ministry::findOrFail($initialVoucherId);

        foreach ($initialVoucher as $item) {
            $item->id;
        }

        $taskType = TaskType::all();
        $agency = Agency::all();
        $budgetVoucher = BudgetVoucher::all();


        return $dataTable->render('budgetplan::voucher.index', [
            'params' => $params,
            'initialVoucher' => $initialVoucher,
            'taskType' => $taskType,
            'agency' => $agency,
            'budgetVoucher' => $budgetVoucher
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $params = decode_params($id);
        $initialVoucher = Ministry::findOrFail($params);
        $beginCredit = BeginCredit::where('year', $params)->with('agency')->get();
        $taskType = TaskType::all();

        return view('budgetplan::voucher.create')
            ->with('beginCredit', $beginCredit)
            ->with('taskType', $taskType)
            ->with('params', $params)
            ->with('initialVoucher', $initialVoucher);
    }

    public function store(Request $request, $id)
    {
        $params = decode_params($id);
        $initialVoucherId = is_array($params) && isset($params['id']) ? $params['id'] : $params;

        $initialVoucher = Ministry::findOrFail($initialVoucherId);

        foreach ($initialVoucher as $item) {
            $item->id;
        }

        $year = $item->id;

        $validated = $request->validate([
            'cboAgency'  => 'required',
            'cboSubDepart'  => 'required',
            'subAccountNumber' => 'required',
            'program' => 'required|exists:begin_credits,program',
            'budget' => 'required|numeric|min:0',
            'task_type' => 'required|exists:task_types,task',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf|max:2048',
            'date' => 'required|date',
            'txtDescription' => 'required|string',
        ]);

        // dd($validated);

        try {
            DB::transaction(function () use ($validated, $request, $year) {
                $beginCredit = BeginCredit::where('program', $validated['program'])
                    ->where('subAccountNumber', $validated['subAccountNumber'])
                    // ->where('subDepart', $validated['cboSubDepart'])
                    // ->where('agencyNumber', $validated['cboAgency'])
                    ->firstOrFail();

                if (!$beginCredit->subAccountNumber) {
                    throw new \Exception('មិនមាន អនុគណនី ឬកូដកម្មវិធី។');
                }

                $applyValue = $validated['budget'];
                $remainingCredit = $beginCredit->credit - $applyValue;

                if ($remainingCredit < 0) {
                    throw new \Exception('ឥណាទានមិនអាចតិចជាងសូន្យ។');
                }

                $storedFilePaths = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $storedFilePaths[] = $file->store('certificateDatas', 'public');
                        }
                    }
                }

                BudgetVoucher::create([
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

                $this->recalculateAndSaveReport($beginCredit);

                $beginCredit->refresh();
                $lastVoucher = BudgetVoucher::where('program', $validated['program'])->latest()->first();
                $beginCredit->apply = $lastVoucher?->budget ?? 0;
                $beginCredit->save();
            });

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budget-voucher.index', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budget-voucher.index', $id);
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
    public function edit($params)
    {
        $id = decode_params($params);

        $budgetVoucher = BudgetVoucher::where('id', $id)->first();
        $beginCredit = BeginCredit::all();
        $taskType = TaskType::all();

        return view('budgetplan::voucher.edit')->with('budgetVoucher', $budgetVoucher)->with('beginCredit', $beginCredit)->with('taskType', $taskType)->with('params', $params);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $id = decode_params($params);

        $validated = $request->validate([
            'subAccountNumber' => 'required|numeric',
            'program' => 'required|exists:begin_credits,program',
            'budget' => 'required|numeric|min:0',
            'task_type' => 'required|exists:task_types,task',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf|max:2048',
            'date' => 'required|date',
            'txtDescription' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $id) {
                $voucher = BudgetVoucher::where('id', $id)->firstOrFail(); // ✅ correct


                $beginCredit = BeginCredit::where('program', $validated['program'])
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
                $storedFilePaths = json_decode($voucher->attachments ?? '[]', true);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $storedFilePaths[] = $file->store('certificateDatas', 'public');
                        }
                    }
                }

                // Update voucher
                $voucher->update([
                    'subAccountNumber' => $validated['subAccountNumber'],
                    'program' => $beginCredit->program,
                    'budget' => $applyValue,
                    'task_type' => $validated['task_type'],
                    'attachments' => json_encode($storedFilePaths),
                    'date' => $validated['date'],
                    'txtDescription' => strip_tags($validated['txtDescription']),
                ]);

                // Recalculate report
                $this->recalculateAndSaveReport($beginCredit);

                // Update apply field
                $beginCredit->refresh();
                $lastVoucher = BudgetVoucher::where('program', $validated['program'])->latest()->first();
                $beginCredit->apply = $lastVoucher?->budget ?? 0;
                $beginCredit->save();
            });

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();


            return redirect()->route('budget-voucher.index', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('budget-voucher.index', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $voucher = BudgetVoucher::where('id', $id)->firstOrFail();

        // ✅ Delete attached files
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

        $voucher->delete();

        // Recalculate related data
        $beginCredit = BeginCredit::where('program', $voucher->program)
            ->where('subAccountNumber', $voucher->subAccountNumber)
            ->first();

        if ($beginCredit) {
            $this->recalculateAndSaveReport($beginCredit);
        }

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('budget-voucher.index');
    }



    public function restore($params)
    {
        $id = decode_params($params);
        $beginCredit = BeginCredit::withTrashed()->find($id);

        if ($beginCredit) {
            $beginCredit->restore();
            flash()->success(__('messages.restore_success'))->flash();
        }

        return redirect()->route('budgetplan.index');
    }

    public function getEarlyBalance($subAccountId, $programCode)
    {
        try {
            $beginCredit = BeginCredit::with('loans')
                ->where('subAccountNumber', $subAccountId)
                ->where('program', $programCode)
                ->first();

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

    private function recalculateAndSaveReport(BeginCredit $beginCredit)
    {
        $newApplyTotal = BudgetVoucher::where('program', $beginCredit->program)
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
        $budgetVoucher = BudgetVoucher::where('program', $beginCredit->program)
            ->where('subAccountNumber', $beginCredit->subAccountNumber)
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


    private function calculateTotals($budgetVoucher)
    {
        $totals = [];
        $totals['total_amount_overall'] = 0;

        $groupedByCode = $budgetVoucher->groupBy(function ($budgetVoucher) {
            return optional($budgetVoucher->report->subAccountKey->accountKey->key)->code ?? 'Unknown';
        });

        foreach ($groupedByCode as $codeId => $certificatesByCode) {
            $totals['code'][$codeId] = $this->calculateSumFields($certificatesByCode);
            $totals['total_amount_overall'] += $totals['code'][$codeId]['value_certificate'];

            // Group by accountKey within each codeId
            $groupedByAccountKey = $certificatesByCode->groupBy(function ($certificateData) {
                return optional($certificateData->report->subAccountKey->accountKey)->account_key ?? 'Unknown';
            });

            foreach ($groupedByAccountKey as $accountKeyId => $certificatesByAccountKey) {
                $totals['accountKey'][$codeId][$accountKeyId] = $this->calculateSumFields($certificatesByAccountKey);

                // Group by subAccountKey within each accountKey
                $groupedBySubAccountKey = $certificatesByAccountKey->groupBy(function ($certificateData) {
                    return optional($certificateData->report->subAccountKey)->sub_account_key ?? 'Unknown';
                });

                foreach ($groupedBySubAccountKey as $subAccountKeyId => $certificatesBySubAccountKey) {
                    $totals['subAccountKey'][$codeId][$accountKeyId][$subAccountKeyId] = $this->calculateSumFields($certificatesBySubAccountKey);

                    // Group by reportKey within each subAccountKey
                    $groupedByReportKey = $certificatesBySubAccountKey->groupBy(function ($certificateData) {
                        return optional($certificateData->report)->report_key ?? 'Unknown';
                    });

                    foreach ($groupedByReportKey as $reportKeyId => $certificatesByReportKey) {
                        $totals['reportKey'][$codeId][$accountKeyId][$subAccountKeyId][$reportKeyId] = $this->calculateSumFields($certificatesByReportKey);
                    }
                }
            }
        }

        return $totals;
    }

    private function calculateSumFields($budgetVoucherCollection)
    {
        $totals = [
            'budget' => 0,
            'amount' => 0,
        ];

        foreach ($budgetVoucherCollection as $budgetVoucher) {
            $totals['budget'] += $budgetVoucher->budget ?? 0;
            $totals['amount'] += $budgetVoucher->amount ?? 0;
        }

        return $totals;
    }
}
