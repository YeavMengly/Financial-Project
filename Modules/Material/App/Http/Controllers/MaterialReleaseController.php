<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialReleaseDataTable;
use App\Http\Controllers\Controller;
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
    public function index()
    {
        return view('material::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('material::create');
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
}
