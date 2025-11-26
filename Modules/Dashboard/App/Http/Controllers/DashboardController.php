<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $report = DB::table("categories")
            ->select(
                "categories.id",
                "categories.name",
                "categories.order"
            )
            ->orderBy('categories.order', 'ASC')

            ->get();

        $chapter = DB::table('begin_vouchers')
            ->select(
                'ministry_id',
                DB::raw('SUM(fin_law) as total_fin_law'),
                DB::raw('SUM(current_loan) as total_current_loan'),
            )
            ->groupBy('ministry_id')
            ->get();

        // dd($chapter);

        return view('dashboard::index')
        ->with("chapter", $chapter)
            ->with("report", $report);

        // ->with("chapter", $chapter);
    }
}
