<?php

namespace Modules\LoanBudget\App\Http\Controllers;

use App\DataTables\Budget\InitialVoucherDataTable;
use App\DataTables\BudgetLoans\InitialVoucherLoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\InitialBudget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialVoucherLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialVoucherLoanDataTable $dataTable)
    {

        $voucherLoan = InitialBudget::all();
        return $dataTable->render('loanbudget::voucherLoan.index', ['voucherLoan' => $voucherLoan]);
        // return view('loanbudget::voucherLoan.index');
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
