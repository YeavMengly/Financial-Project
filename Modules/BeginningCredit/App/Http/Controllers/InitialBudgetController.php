<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialBudgetDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\InitialBudget;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitialBudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialBudgetDataTable $dataTable)
    {
        $initialBudget = InitialBudget::select('year')->distinct()->orderByDesc('year')->get();

        return $dataTable->render('beginningcredit::initialBudget.index', ['initialBudget' => $initialBudget]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beginningcredit::initialBudget.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:initial_budgets,year'],
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។',
        ]);

        DB::beginTransaction();

        try {
            $validateData['description'] = strip_tags($validateData['description']);
            InitialBudget::create($validateData);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('initialBudget.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            if ($e instanceof QueryException && $e->getCode() == 23000 && str_contains($e->getMessage(), 'initial_budgets_year_unique')) {
                $errorMessage = 'ឆ្នាំនេះបានបញ្ចូលរួចហើយ។';
            } else {
                $errorMessage = 'បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ។';
            }

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();

            return redirect()->route('initialBudget.index');
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
    public function edit($params)
    {
        $id =  decode_params($params);
        $data = InitialBudget::where('id', $id)->first();

        return view('beginningcredit::initialBudget.edit')->with('data', $data)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {

        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:initial_budgets,year'],
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។',
        ]);

        $id = decode_params($params);
        // Find the existing InitialBudget

        DB::beginTransaction();

        try {
            $initialBudget = InitialBudget::where('id', $id)->first();
            $validateData['description'] = strip_tags($validateData['description']);
            $initialBudget->update($validateData);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('បានកែប្រែជោគជ័យ', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('initialBudget.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            $errorMessage = 'បញ្ហាក្នុងការកែប្រែទិន្នន័យ។';

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();

            return redirect()->route('initialBudget.index');
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
            $initialBudget = InitialBudget::where('id', $id)->firstOrFail();
            $initialBudget->delete();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('លុបជោគជ័យ', 'ជោគជ័យ') // Khmer: Successfully deleted
                ->flash();

            return redirect()->route('initialBudget.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុបទិន្នន័យ', 'បញ្ហា') // Khmer: Error deleting record
                ->flash();

            return redirect()->route('initialBudget.index');
        }
    }
}
