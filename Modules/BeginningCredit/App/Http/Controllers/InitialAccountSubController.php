<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AccountSubDataTable;
use App\DataTables\AnnualOpen\InititalAccountSubDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialAccountSubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InititalAccountSubDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialAccountSub.index');
    }
}
