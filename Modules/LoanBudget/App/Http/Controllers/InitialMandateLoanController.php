<?php

namespace Modules\LoanBudget\App\Http\Controllers;

use App\DataTables\BudgetLoans\InitialMandateLoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\InitialBudget;
use Illuminate\Http\Request;

class InitialMandateLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialMandateLoanDataTable $dataTable)
    {
        $mandateLoan = InitialBudget::all();
        return $dataTable->render('loanbudget::mandateLoan.index', ['mandateLoan' => $mandateLoan]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('loanbudget::create');
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
        return view('loanbudget::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('loanbudget::edit');
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
