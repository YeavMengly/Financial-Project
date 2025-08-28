<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialChapterDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialChapterDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialChapter.index');
    }
}
