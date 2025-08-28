<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialAgencyDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialAgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialAgencyDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialAgency.index');
    }
}
