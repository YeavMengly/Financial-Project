<?php

namespace Modules\Revenue\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DataTables\Revenue\InitialRevenueDataTable;
use App\DataTables\Revenue\BudgetRevenueDataTable;
use App\Models\Content\Ministry;
use App\Models\Content\Agency;
use App\Models\Content\Program;
use App\Models\Content\Chapter;
use App\Models\Content\ProgramSub;
use App\Models\Content\AccountSub;
use App\Models\Revenue\PaymentThroughPartnerBanks;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getIndex(InitialRevenueDataTable $dataTable)
    {

        return $dataTable->render('revenue::initialRevenue.index');
    }
    /**
     * Display a listing of the resource.
     * @param  string|int  $params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(BudgetRevenueDataTable $dataTable, $params)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        return $dataTable->render('revenue::budgetRevenue.index', [
            'data'          => $data,
            'params'        => $params,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id        = decode_params($params);
        $ministry  = Ministry::where('id', $id)->first();
        $agency    = Agency::where('ministry_id', $ministry->id)->get();
        $program   = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        // $paymentPathner = PaymentThroughPartnerBanks::all();

        return view('revenue::budgetRevenue.create')
            ->with('ministry', $ministry)
            ->with('accountSub', $accountSub)
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('agency', $agency)
            // ->with('paymentPathner', $paymentPathner)
            ->with('program', $program);
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
        return view('revenue::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('revenue::edit');
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
