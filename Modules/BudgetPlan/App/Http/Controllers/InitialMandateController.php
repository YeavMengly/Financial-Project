<?php

namespace Modules\BudgetPlan\App\Http\Controllers;

use App\DataTables\Budget\InitialMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\BeginCreditMandate;
use App\Models\BeginCredit\Ministry;
use App\Models\BudgetPlan\InitialMandate;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitialMandateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialMandateDataTable $dataTable)
    {
        $initialVoucher = Ministry::all();
        return $dataTable->render('budgetplan::initialMandate.index', ['initialVoucher' => $initialVoucher]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = BeginCreditMandate::all();
        return view('budgetplan::initialMandate.create')->with('data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate year as 4-digit year only
        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:initial_mandates,year'],
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។', // Khmer: This year has already been entered.
        ]);

        DB::beginTransaction();

        try {
            $validateData['description'] = strip_tags($validateData['description']);
            InitialMandate::create($validateData);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('initialMandate.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            // Check for duplicate year (SQL error code 23000, error number 1062)
            if ($e instanceof QueryException && $e->getCode() == 23000 && str_contains($e->getMessage(), 'initial_vouchers_year_unique')) {
                $errorMessage = 'ឆ្នាំនេះបានបញ្ចូលរួចហើយ។'; // Khmer: This year has already been entered.
            } else {
                $errorMessage = 'បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ។'; // Generic error in Khmer
            }

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();
            return redirect()->route('initialMandate.index');
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
    public function edit($id)
    {
        return view('budgetplan::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
