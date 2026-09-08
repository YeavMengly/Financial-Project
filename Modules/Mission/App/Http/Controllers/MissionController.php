<?php

namespace Modules\Mission\App\Http\Controllers;

use App\DataTables\Mission\InitialMissionsDataTable;
use App\DataTables\Mission\MissionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Levels;
use App\Models\Content\Ministry;
use App\Models\Content\Employee;
use App\Models\Content\Positions;
use App\Models\Mission\Mission;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MissionController extends Controller
{

    public function getIndex(InitialMissionsDataTable $dataTable)
    {
        return $dataTable->render('mission::missions.initialMissions.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MissionDataTable $dataTable, $params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $position = Positions::all();
        $level = Levels::all();
        $provinces = Province::all();
        return $dataTable->render('mission::missions.index', [
            'params' => $params,
            'ministry' => $ministry,
            'position' => $position,
            'level' => $level,
            'provinces' => $provinces
        ]);
    }

    public function getByLevel(Request $request)
    {
        if ($request->position_id) {
            $data = Levels::select('id', 'position_id', 'name')
                ->where('position_id', $request->position_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->name}</option>";
            }

            return response($html);
        }

        return response('');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $position = Positions::all();
        // $employee = Employee::all();


        return view('mission::missions.create')
            ->with('ministry', $ministry)
            ->with('position', $position)
            // ->with('employee', $employee)
            ->with('params', $params)
        ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validatedData = $request->validate([
            'id_number'     => 'required|integer',
            'legal_date'  => 'required|date',
            'cboName'      => 'required',
            'cboPosition'  => 'required',
            'cboLevel'             => 'required',
            'cboProvince'        => 'required',
            'start_date'  => 'required|date',
            'end_date'  => 'required|date',
            'txtDescription' => 'required|string|max:9999',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $ministry   = Ministry::where('id', $id)->first();

            $mission = Mission::create([
                'ministry_id' => $ministry->id,
                'name_list_id' => $request->cboName,
                'position_id' => $request->cboPosition,
                'level_id' => $request->cboLevel,
                'province_id' => $request->cboProvince,
                'legal_number' => $request->id_number,
                'legal_date' => $request->legal_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->txtDescription
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('beginMandate.index', $params);
            }

            return redirect()->route('beginMandate.create', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('beginMandate.index', $params);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('mission::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('mission::edit');
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
