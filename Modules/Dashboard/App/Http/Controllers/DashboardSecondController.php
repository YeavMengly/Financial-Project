<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardSecondController extends Controller
{
    public function index(Request $request)
    {
        $todoStatus = $request->input('cboTodo', 2);

        $ministriesQuery = DB::table('ministries')
            ->select('id', 'no', 'year', 'title', 'refer', 'name', 'status')
            ->orderBy('year', 'desc');

        if ($todoStatus == 2) {
            $ministriesQuery->where('is_archived', 1);
        } else {
            $ministriesQuery->where('is_archived', 2);
        }
        $ministries = $ministriesQuery->get();
        // First available year from current filtered rows
        $defaultYear = $ministries->first()->year ?? date('Y');
        // Requested year
        $requestYear = $request->input('year');

        $yearExists = $ministries->contains('year', $requestYear);
        $year = ($request->filled('year') && $yearExists)
            ? $requestYear
            : $defaultYear;

        // Fetch vouchers
        $beginReportQuery = DB::table('begin_vouchers')
            ->join('ministries', 'begin_vouchers.ministry_id', '=', 'ministries.id')
            ->select('begin_vouchers.*');

        if ($todoStatus == 2) {
            $beginReportQuery->where('ministries.year', $year)
                ->where('ministries.is_archived', 1);
        } else {
            $beginReportQuery->where('ministries.year', $year)
                ->where('ministries.is_archived', 2);
        }
        return view('dashboard::index_second', [
            'ministries' => $ministries,
            'selectedYear' => $year,
        ]);
    }
}
