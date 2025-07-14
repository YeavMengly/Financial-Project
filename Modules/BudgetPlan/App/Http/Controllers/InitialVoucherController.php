<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\InitialVoucherDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BudgetPlan\InitialVoucher;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitialVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialVoucherDataTable $dataTable)
    {
        $initialVoucher = InitialBudget::all();

        return $dataTable->render('budgetplan::initialVoucher.index', ['initialVoucher' => $initialVoucher]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = InitialBudget::orderBy('year', 'desc')
            ->get();;
        return view('budgetplan::initialVoucher.create')->with('data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validateData = $request->validate([
            'cboYear' => ['required', 'exists:initial_budgets,id'],
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'cboYear.required' => 'សូមជ្រើសរើសឆ្នាំ។',
            'cboYear.exists' => 'ឆ្នាំដែលបានជ្រើសមិនត្រឹមត្រូវទេ។',
        ]);

        try {
            DB::beginTransaction();

            // Get selected year from InitialBudget
            $initialBudget = InitialBudget::findOrFail($validateData['cboYear']);
            // $year = $initialBudget->year;

            // Check manually if this year already exists in InitialVoucher
            if (InitialVoucher::where('year', $initialBudget)->exists()) {
                return back()->withErrors([
                    'cboYear' => 'ឆ្នាំនេះបានបញ្ចូលរួចហើយ។'
                ])->withInput();
            }

            // Store to InitialVoucher
            InitialVoucher::create([
                'year' => $validateData['cboYear'],
                'title' => $validateData['title'],
                'sub_title' => $validateData['sub_title'],
                'description' => strip_tags($validateData['description']),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('initialVoucher.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            $errorMessage = 'បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ។';

            if (
                $e instanceof QueryException &&
                $e->getCode() == 23000 &&
                str_contains($e->getMessage(), 'initial_vouchers_year_unique')
            ) {
                $errorMessage = 'ឆ្នាំនេះបានបញ្ចូលរួចហើយ។';
            }

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();

            return redirect()->route('initialVoucher.index');
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('budgetplan::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id =  decode_params($params);
        $data = InitialVoucher::where('id', $id)->first();

        return view('budgetplan::initialVoucher.edit')->with('data', $data)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        // Validate input (ignore unique year conflict for current record)
        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:initial_vouchers,year'],
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។', // Khmer: This year has already been entered.
        ]);

        $id = decode_params($params);
        // Find the existing InitialBudget

        DB::beginTransaction();

        try {
            $initialBudget = InitialVoucher::where('id', $id)->first();
            // Sanitize description
            $validateData['description'] = strip_tags($validateData['description']);

            // Update the record
            $initialBudget->update($validateData);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('បានកែប្រែជោគជ័យ', 'ជោគជ័យ') // Khmer: Updated successfully
                ->flash();

            return redirect()->route('initialVoucher.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            $errorMessage = 'បញ្ហាក្នុងការកែប្រែទិន្នន័យ។'; // Khmer: Error while updating data

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();

            return redirect()->route('initialVoucher.index');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);

        DB::beginTransaction();

        try {
            $initialVoucher = InitialVoucher::where('id', $id)->firstOrFail();
            $initialVoucher->delete();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('លុបជោគជ័យ', 'ជោគជ័យ') // Khmer: Successfully deleted
                ->flash();

            return redirect()->route('initialVoucher.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុបទិន្នន័យ', 'បញ្ហា') // Khmer: Error deleting record
                ->flash();

            return redirect()->route('initialVoucher.index');
        }
    }
}
