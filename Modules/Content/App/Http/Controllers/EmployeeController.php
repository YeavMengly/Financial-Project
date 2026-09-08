<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\EmployeeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EmployeeDataTable $dataTable)
    {
        return $dataTable->render('content::content.employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content::content.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_number' => [
                'required',

            ],
            'account_number' => [
                'required',

            ],
            'name_kh' => [
                'required',

            ],
            'name_latin' => [
                'required',
            ],
            'status' => ['nullable', 'boolean'], // ✅ ADD
        ]);

        DB::beginTransaction();
        try {

            Employee::firstOrCreate([
                'id_number' => $request->id_number,
                'account_number' => $request->account_number,
                'name_kh' => $request->name_kh,
                'name_latin' => $request->name_latin,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return $request->submit == 'save'
                ? redirect()->route('employees.index',)
                : redirect()->route('employees.index',);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // If unique index blocked a duplicate, you can show friendly message
            flash()->translate('en')->option('timeout', 2000)
                ->error('This record already exists.', 'Duplicate')->flash();

            return redirect()->route('employees.index',);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('employees.index',);
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
        $decoded = decode_params($params);

        $id = is_array($decoded) ? $decoded[0] : $decoded;

        $module = Employee::findOrFail($id);

        return view('content::content.employees.edit', [
            'module' => $module,
            'params' => $params,
        ]);
    }

    public function update(Request $request, $params): RedirectResponse
    {
        $request->validate([
            'id_number' => ['required'],
            'account_number' => ['required'],
            'name_kh' => ['required'],
            'name_latin' => ['required'],
            'status' => ['nullable', 'boolean'], // ✅ ADD
        ]);

        DB::beginTransaction();

        try {
            $decoded = decode_params($params);

            $id = is_array($decoded) ? $decoded[0] : $decoded;

            $employee = Employee::findOrFail($id);

            $employee->update([
                'id_number' => $request->id_number,
                'account_number' => $request->account_number,
                'name_kh' => $request->name_kh,
                'name_latin' => $request->name_latin,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('employees.index');
        } catch (Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('employees.index');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $employee = Employee::where('id', $id)->first();
        $employee->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('employees.index', $params);
    }

    public function restore($params)
    {
        $id = decode_params($params);
        Employee::withTrashed()->whereKey($id)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('employees.index');
    }
}
