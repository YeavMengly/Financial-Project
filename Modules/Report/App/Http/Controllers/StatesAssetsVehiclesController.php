<?php

namespace Modules\Report\App\Http\Controllers;

use App\DataTables\Report\StatesAssetsVehiclesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatesAssetsVehiclesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StatesAssetsVehiclesDataTable $dataTable)
    {
         $ministries = Ministry::all();
        return $dataTable->render('report::report.states_assets.vehicles.index', [
            'ministries' => $ministries
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('report::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('report::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
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
