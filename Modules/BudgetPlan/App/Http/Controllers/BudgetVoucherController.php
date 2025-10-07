<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\BudgetVoucherDataTable;
use App\DataTables\Budget\InitialVoucherDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Program;
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
        $taskType = TaskType::all();
        $agency = Agency::all();
        $budgetVoucher = BudgetVoucher::where('ministry_id', $data->id)->get();

        return $dataTable->render('budgetplan::budgetVoucher.index', [
            'data' => $data,
            'params' => $params,
            'taskType' => $taskType,
            'agency' => $agency,
            'budgetVoucher' => $budgetVoucher
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

        $beginVoucher = BeginVoucher::select(
            'begin_vouchers.id',
            'begin_vouchers.no as voucher_no',
            'begin_vouchers.account_sub_id',
            'begin_vouchers.no',
            'account_subs.no as sub_no',
            'account_subs.name as sub_name'
        )
            ->join('account_subs', 'begin_vouchers.account_sub_id', '=', 'account_subs.no')
            ->where('begin_vouchers.ministry_id', $ministry->id)
            ->get();

        return view('budgetplan::budgetVoucher.create')
            ->with('accountSub', $accountSub)
            ->with('agency', $agency)
            ->with('taskType', $taskType)
            ->with('params', $params)
            ->with('beginVoucher', $beginVoucher)
            ->with('program', $program);
    }

    public function getEarlyBalance(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $request->validate([
            'account_sub_id' => 'required',
            'no'             => 'required'
        ]);

        $begin = BeginVoucher::with('loans')
            ->where('ministry_id', $ministryId)
            ->where('account_sub_id', $request->account_sub_id)
            ->where('no', $request->no)
            ->first();

        if (!$begin) {
            return response()->json([
                'fin_law'            => 0,
                'credit_movement'    => 0,
                'new_credit_status'  => 0,
                'credit'             => 0,
                'deadline_balance'   => 0,
                'exists'             => false,
            ]);
        }

        $loan = $begin->loans;
        $credit_movement = (($loan->total_increase ?? 0) - ($loan->decrease ?? 0));

        return response()->json([
            'fin_law'            => (float) ($begin->fin_law ?? 0),
            'credit_movement'    => (float) $credit_movement,
            'new_credit_status'  => (float) ($begin->new_credit_status ?? 0),
            'credit'             => (float) ($begin->credit ?? 0),
            'deadline_balance'   => (float) ($begin->deadline_balance ?? 0),
            'exists'             => true,
        ]);
    }


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
                $beginVoucher = BeginVoucher::where('no', $validated['no'])
                    ->where('account_sub_id', $validated['cboSubAccount'])
                    ->where('agency_id', $validated['cboAgency'])
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

                $applyValue      = (float) $validated['budget'];
                $currentCredit   = (float) ($beginVoucher->credit ?? 0);
                $remainingCredit = $currentCredit - $applyValue;

                if ($remainingCredit < 0) {
                    flash()
                        ->translate('en')
                        ->option('timeout', 2000)
                        ->error('ឥណទានមិនអាចតិចជាងសូន្យ។', 'បញ្ហា')
                        ->flash();

                    return back(); // redirect back so it shows in the view
                }

                // store attachments
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
                    'account_sub_id' => $validated['cboSubAccount'],
                    'no'             => $validated['no'],
                    'txtDescription' => strip_tags($validated['txtDescription']),
                    'budget'         => $applyValue,
                    'task_type'      => $validated['task_type'], // make sure this matches your column name
                    'attachments'    => json_encode($stored),
                    'date'           => $validated['date'],
                ]);
                $this->recalculateAndSaveReport($beginVoucher);

                $beginVoucher->refresh();
                $lastVoucher = BudgetVoucher::where('no', $validated['no'])
                    ->where('account_sub_id', $validated['cboSubAccount'])
                    ->where('agency_id', $validated['cboAgency'])
                    ->latest()->first();

                $beginVoucher->apply = $lastVoucher?->budget ?? 0;
                $beginVoucher->save();
            });


            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetVoucher.index', $params);
        } catch (\Throwable $e) {
            Log::error('BudgetVoucher store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

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
        $taskType = TaskType::all();

        // the voucher we are editing
        $module = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        // list of sub accounts from BeginVoucher join
        $beginVoucher = BeginVoucher::select(
            'begin_vouchers.id',
            'begin_vouchers.no',
            'begin_vouchers.account_sub_id',
            'account_subs.no as sub_no',
            'account_subs.name as sub_name'
        )
            ->join('account_subs', 'begin_vouchers.account_sub_id', '=', 'account_subs.no')
            ->where('begin_vouchers.ministry_id', $ministry->id)
            ->get();

        // dd($beginVoucher);

        return view('budgetplan::budgetVoucher.edit')
            ->with('taskType', $taskType)
            ->with('agency', $agency)
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

        // $params = decode_params($params);
        // $id = decode_params($id);

        // dd($validated);

        DB::beginTransaction();
        try {
            // DB::transaction(function () use ($validated, $request, $params, $id) {

            $ministry = Ministry::where('id', decode_params($params))->first();

            // dd($id);
            $voucher = BudgetVoucher::where('id', $id)
                ->where('ministry_id', $ministry->id)->first(); // ✅ correct

            $beginCredit = BeginVoucher::where('no', $validated['no'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('agency_id', $validated['cboAgency'])
                ->where('ministry_id', $ministry->id)
                ->first();

            // dd($voucher);


            if (!$beginCredit) {
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

            // Recalculate report
            $this->recalculateAndSaveReport($beginCredit);

            // Update apply field
            $beginCredit->refresh();
            $lastVoucher = BudgetVoucher::where('no', $validated['no'])
                ->where('account_sub_id', $validated['cboSubAccount'])
                ->where('ministry_id', $ministry->id)->latest()->first();
            $beginCredit->apply = $lastVoucher?->budget ?? 0;
            $beginCredit->save();
            // });
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
        // $params = decode_params($params);
        $ministry   = Ministry::where('id', decode_params($params))->first();
        $voucher = BudgetVoucher::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

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



    public function restore($params)
    {
        $id = decode_params($params);
        $beginCredit = BeginVoucher::withTrashed()->find($id);

        if ($beginCredit) {
            $beginCredit->restore();
            flash()->success(__('messages.restore_success'))->flash();
        }

        return redirect()->route('budgetplan.index');
    }

    private function recalculateAndSaveReport(BeginVoucher $beginVoucher)
    {
        $newApplyTotal = BudgetVoucher::where('no', $beginVoucher->no)
            ->where('account_sub_id', $beginVoucher->account_sub_id)
            ->where('agency_id', $beginVoucher->agency_id)
            ->latest('created_at')
            ->value('budget') ?? 0;

        $beginVoucher->early_balance = $this->calculateEarlyBalance($beginVoucher);

        $beginVoucher->apply = $newApplyTotal;
        $credit = $beginVoucher->new_credit_status - $beginVoucher->deadline_balance;
        $beginVoucher->credit = $credit;
        $beginVoucher->deadline_balance = $beginVoucher->early_balance + $beginVoucher->apply;
        $beginVoucher->credit = $beginVoucher->new_credit_status - $beginVoucher->deadline_balance;
        $beginVoucher->law_average = $beginVoucher->deadline_balance > 0 ? ($beginVoucher->deadline_balance / $beginVoucher->fin_law) * 100 : 0;
        $beginVoucher->law_correction =  $beginVoucher->deadline_balance > 0 ? ($beginVoucher->deadline_balance /  $beginVoucher->new_credit_status) * 100 : 0;
        $beginVoucher->save();
    }

    private function calculateEarlyBalance($beginCredit)
    {
        $budgetVoucher = BudgetVoucher::where('no', $beginCredit->no)
            ->where('account_sub_id', $beginCredit->account_sub_id)
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
}
