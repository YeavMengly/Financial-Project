<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialEntryDataTable;
use App\DataTables\Material\MaterialEntryDataTable;
use App\Exports\Material\MaterialEntriesExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Material\MaterialEntry;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;;

class MaterialEntryController extends Controller
{

    public function getIndex(InitialMaterialEntryDataTable $dataTable)
    {
        return $dataTable->render('material::materialEntry.initialMaterialEntry.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MaterialEntryDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $materialEntry = MaterialEntry::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('material::materialEntry.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialEntry' => $materialEntry
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();
        $project = Projects::where('ministry_id', $ministry->id)->get();

        return view('material::materialEntry.create', [
            'params' => $params,
            'unitType' => $unitType,
            'ministry' => $ministry,
            'project' => $project
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboProject' => 'required',
            'p_name'    => 'required|string|max:255',
            'unit'          => 'required',
            'qty'      => 'required',
            'price'         => 'required',
            'source'    => 'required|string|max:255',
            'p_year'    => 'required|string|max:255',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $project = Projects::where('id', $validated['cboProject'])->first();
            $materialTotal = (int)$validated['qty'] * (float)$validated['price'];

            MaterialEntry::create([
                'ministry_id'  => $ministry->id,
                'project_id' => $project->id,
                'p_name'   => $validated['p_name'],
                'p_year'   => $validated['p_year'],
                'unit'         => $unitType->name,
                'qty'     => $validated['qty'],
                'price'        => $validated['price'],
                'total_price'   => $materialTotal,
                'source'        => $validated['source'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('material::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();
        $project = Projects::where('ministry_id', $ministry->id)->first();
        $module = MaterialEntry::where('id', $project->id)
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('material::materialEntry.edit')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType)
            ->with('project', $project)
            ->with('module', $module);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboProject' => 'required',
            'p_name'    => 'required|string|max:255',
            'p_year'    => 'required|string|max:255',
            'unit'          => 'required',
            'qty'      => 'required',
            'price'         => 'required',
            'source'    => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $materialTotal = (int)$validated['qty'] * (float)$validated['price'];
            $materialEntry = MaterialEntry::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $materialEntry->update([
                'project_id' => $validated['cboProject'],
                'p_name'   => $validated['p_name'],
                'p_year'   => $validated['p_year'],
                'unit'         => $unitType->name,
                'qty'     => $validated['qty'],
                'price'        => $validated['price'],
                'total_price'   => $materialTotal,
                'source'        => $validated['source'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function export(Request $request, $params)
    {

        try {
            $ministryId = decode_params($params);
            $query = MaterialEntry::query()
                // ->leftJoin('budget_voucher_loans', 'begin_vouchers.account_sub_id', '=', 'budget_voucher_loans.account_sub_id')
                ->where('material_entries.ministry_id', $ministryId)
                ->select(
                    'material_entries.*',
                );

            // Apply filters...
            $data = $query->get();

            $query->orderBy('created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported MaterialExport Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចូលទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('materialEntry.index', $params);
            }

            // Pass filtered data + ministry id into export
            $export = new MaterialEntriesExport($data, $ministryId);

            // you can pass $request if you want to use date filters/text in header
            return $export->export($request);
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចូលទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }
}
