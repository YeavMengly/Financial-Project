<?php

namespace Modules\Electric\App\Http\Controllers;

use App\DataTables\Electric\ElectricEntityDataTable;
use App\DataTables\Electric\InitialElectricEntityDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use App\Models\Electric\ElectricEntity;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectricEntityController extends Controller
{
    public function getIndex(InitialElectricEntityDataTable $dataTable)
    {
        return $dataTable->render('electric::electric.entity.initialElectricEntity.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ElectricEntityDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();

        return $dataTable->render('electric::electric.entity.index', [
            'params' => $params,
            'ministry' => $ministry,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $province = Province::all();
        return view('electric::electric.entity.create')
            ->with('params', $params)
            ->with('provinces', $province);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'txtTitleEntity'   => 'required|string|max:255',
            'txtLocationNmber' => 'required|string|max:255',
            'province'         => 'required|exists:provinces,id',
        ]);
        $id = decode_params($params);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', $id)->first();
            ElectricEntity::create([
                'ministry_id'     => $ministry->id,
                'title_entity'    => $validated['txtTitleEntity'],
                'location_number' => $validated['txtLocationNmber'],
                'province_id'     => $validated['province'],
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('electricEntity.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('electricEntity.index', $params);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('electric::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);

        $province = Province::all();
        $ministry = Ministry::where('id', decode_params($params))->first();
        $module = ElectricEntity::where('id', $id)
            ->where('ministry_id', $ministry->id)->first();

        return view('electric::electric.entity.edit')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('module', $module)
            ->with('provinces', $province);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'txtTitleEntity'   => 'required|string|max:255',
            'txtLocationNmber' => 'required|string|max:255',
            'province'         => 'required|exists:provinces,id',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $waterEntity = ElectricEntity::where('id', $id)->first();

            $waterEntity->update([
                'ministry_id'     => $ministry->id,
                'title_entity'    => $validated['txtTitleEntity'],
                'location_number' => $validated['txtLocationNmber'],
                'province_id'     => $validated['province'],
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('electricEntity.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('electricEntity.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $electricEntity = ElectricEntity::where('id', $id)->first();
        $electricEntity->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('electricEntity.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);

        ElectricEntity::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('electricEntity.index', $params);
    }
}
