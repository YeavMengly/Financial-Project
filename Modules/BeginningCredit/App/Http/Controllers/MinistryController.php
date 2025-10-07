<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\MinistryDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MinistryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MinistryDataTable $dataTable)
    {
        $ministries = Ministry::all();

        return $dataTable->render('beginningcredit::ministries.index', [
            'ministries' => $ministries
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beginningcredit::ministries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:ministries,year'],
            'title' => ['required', 'string'],
            'refer' => ['required', 'string'],
            'name' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។',
        ]);

        DB::beginTransaction();

        try {
            $validateData['name'] = strip_tags($validateData['name']);
            Ministry::create([
                ...$validateData,
                'no' => '32'
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('ministries.index');
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

            return redirect()->route('ministries.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id =  decode_params($params);
        $data = Ministry::where('id', $id)->first();

        return view('beginningcredit::ministries.edit')->with('data', $data)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $validateData = $request->validate([
            'year' => ['required', 'digits:4', 'unique:ministries,year'],
            'title' => ['required', 'string'],
            'refer' => ['required', 'string'],
            'name' => ['required', 'string'],
        ], [
            'year.unique' => 'ឆ្នាំនេះត្រូវបានបញ្ចូលរួចហើយ។',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministries = Ministry::where('id', $id)->first();

            $validateData['name'] = strip_tags($validateData['name']);
            $ministries->update($validateData);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('បានកែប្រែជោគជ័យ', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('ministries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            $errorMessage = 'បញ្ហាក្នុងការកែប្រែទិន្នន័យ។';

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($errorMessage, 'បញ្ហា')
                ->flash();

            return redirect()->route('ministries.index');
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

            $ministries = Ministry::where('id', $id)->firstOrFail();
            $ministries->delete();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('លុបជោគជ័យ', 'ជោគជ័យ') // Khmer: Successfully deleted
                ->flash();

            return redirect()->route('ministries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុបទិន្នន័យ', 'បញ្ហា') // Khmer: Error deleting record
                ->flash();

            return redirect()->route('ministries.index');
        }
    }

    public function restore($id)
    {
        $pid = decode_params($id);

        Ministry::withTrashed()->whereKey($pid)->restore();
        
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('ministries.index');
    }
}
