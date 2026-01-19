<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\ExpenseTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\ExpenseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(ExpenseTypeDataTable $dataTable)
    {

        return $dataTable->render(
            'content::content.expenseType.index'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content::content.expenseType.create',);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_kh' => [
                'required',

            ],
            'name_en' => [
                'required',
            ],
            'status' => ['nullable', 'boolean'], // ✅ ADD
        ]);

        DB::beginTransaction();
        try {

            ExpenseType::firstOrCreate([
                'name_kh' => $request->name_kh,
                'name_en' => $request->name_en,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return $request->submit == 'save'
                ? redirect()->route('expenseType.index',)
                : redirect()->route('expenseType.index',);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // If unique index blocked a duplicate, you can show friendly message
            flash()->translate('en')->option('timeout', 2000)
                ->error('This record already exists.', 'Duplicate')->flash();

            return redirect()->route('expenseType.index',);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('expenseType.index',);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('content::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id = decode_params($params);
        $module = ExpenseType::where('id', $id)->first();
        return view('content::content.expenseType.edit', [
            'module' => $module,
            'params' => $params,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $id = decode_params($params);


        $request->validate([
            'name_kh' => [
                'required',

            ],
            'name_en' => [
                'required',

            ],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            $expenseType = ExpenseType::where('id', $id)
                ->first();

            $expenseType->update([
                'name_kh' => $request->name_kh,
                'name_en' => $request->name_en,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            DB::commit();

            flash()->translate('en')->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('expenseType.index',);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            flash()->translate('en')->option('timeout', 2000)
                ->error('Duplicate data detected.', 'Duplicate')
                ->flash();

            return back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $expenseType = ExpenseType::where('id', $id)->first();
        $expenseType->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('expenseType.index', $params);
    }

    public function restore($params)
    {
        $id = decode_params($params);
        ExpenseType::withTrashed()->whereKey($id)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('expenseType.index');
    }
}
