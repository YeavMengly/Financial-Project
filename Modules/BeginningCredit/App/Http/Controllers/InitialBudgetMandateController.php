<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialBudgetMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\InitialBudget;
use App\Models\BeginCredit\Ministry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitialBudgetMandateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InitialBudgetMandateDataTable $dataTable)
    {
        $initialBudget = Ministry::select('year')->distinct()->orderByDesc('year')->get();

        return $dataTable->render('beginningcredit::initialBudgetMandate.index', ['initialBudget' => $initialBudget]);
    }
}
