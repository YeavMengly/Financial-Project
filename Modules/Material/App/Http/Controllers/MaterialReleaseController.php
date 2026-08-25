<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialReleaseDataTable;
use App\DataTables\Material\MaterialReleaseDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Material\MaterialEntry;
use App\Models\Material\MaterialRelease;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;;

class MaterialReleaseController extends Controller
{

    public function getIndex(InitialMaterialReleaseDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
        return $dataTable->render('material::materialRelease.initialMaterialRelease.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MaterialReleaseDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $materialRelease = MaterialRelease::where('ministry_id', $ministry->id)->get();
        $project = Projects::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('material::materialRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialRelease' => $materialRelease,
            'project' => $project
        ]);
        // return view('maintenance.maintenance');
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
        $MaterialEntry = MaterialEntry::where('ministry_id', $ministry->id)->get();
        $Agency = Agency::where('ministry_id', $ministry->id)->get();

        return view('material::materialRelease.create', [
            'params' => $params,
            'unitType' => $unitType,
            'ministry' => $ministry,
            'project' => $project,
            'MaterialEntry' => $MaterialEntry,
            'Agency' => $Agency
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
            'quantity_request'      => 'required',
            'quantity_total'         => 'required',
            'source'    => 'nullable|string|max:255',
            'p_year'    => 'nullable|string|max:255',
            'date_release'    => 'required',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $project = Projects::where('id', $validated['cboProject'])->first();
            $materialTotal = (int)$validated['quantity_request'] * (float)$validated['quantity_total'];

            MaterialEntry::create([
                'ministry_id'  => $ministry->id,
                'project_id' => $project->id,
                'p_name'   => $validated['p_name'],
                'p_year'   => $validated['p_year'] ?? '',
                'unit'         => $unitType->name,
                'quantity_request'     => $validated['quantity_request'],
                'quantity_total'        => $validated['quantity_total'],
                'total_price'   => $materialTotal,
                'source'        => $validated['source'] ?? null,
                'date_release'        => $validated['date_release'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('materialRelease.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialRelease.index', $params);
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
    public function edit($id)
    {
        return view('material::edit');
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

    public function export(Request $request, $params)
    {

        return view('errors.404');
    }
}
