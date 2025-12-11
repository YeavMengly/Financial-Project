<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialReleaseDataTable;
use App\DataTables\Material\MaterialReleaseDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\Ministry;
use App\Models\Material\MaterialRelease;
use Illuminate\Http\Request;

class MaterialReleaseController extends Controller
{

    public function getIndex(InitialMaterialReleaseDataTable $dataTable)
    {
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

        return $dataTable->render('material::materialRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialRelease' => $materialRelease
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        return view('material::materialRelease.create')
            ->with('params', $params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
