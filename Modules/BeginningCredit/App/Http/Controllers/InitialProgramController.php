<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialProgramDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialProgramDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialProgram.index');
    }
}
