<?php

namespace Modules\Report\App\Http\Controllers;

use App\DataTables\Report\CostImplementChapterVoucherDataTable;
use App\DataTables\Report\CostImplementChapterMandateDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CostImplementChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CostImplementChapterVoucherDataTable $dataTable, Request $request)
    {
        $ministries = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name')
            ->orderBy('year', 'desc')
            ->get();

        $defaultYear = $ministries->first()->year ?? date('Y');

        // Support both 'year' and 'yearFilter' inputs
        $year = $request->input('yearFilter', $request->input('year', $defaultYear));
        $ministryId = $request->input('ministry_id');

        return $dataTable->render('report::report.cost_implement.chapter.voucher.index', [
            'ministries' => $ministries,
            'selectedYear' => $year,
            'selectedMinistry' => $ministryId,
        ]);
    }
    public function indexMandate(CostImplementChapterMandateDataTable $dataTable, Request $request)
    {
        $ministries = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name')
            ->orderBy('year', 'desc')
            ->get();

        $defaultYear = $ministries->first()->year ?? date('Y');

        // Support both 'year' and 'yearFilter' inputs
        $year = $request->input('yearFilter', $request->input('year', $defaultYear));
        $ministryId = $request->input('ministry_id');

        return $dataTable->render('report::report.cost_implement.chapter.mandate.index', [
            'ministries' => $ministries,
            'selectedYear' => $year,
            'selectedMinistry' => $ministryId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report::create');
    }

    /** 
     * Store a newly created resource in storage.
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     //
    // }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('report::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('report::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id): RedirectResponse
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
