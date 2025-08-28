<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialProgramSubDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialProgramSubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialProgramSubDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialProgramSub.index');
    }
}
