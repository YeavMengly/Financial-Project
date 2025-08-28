<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialAccountDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialAccountDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialAccount.index');
    }
}
