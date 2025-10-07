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

        // $chapter = DB::table("chapters")
        //     ->get();
        return view('dashboard::index')
            ->with("report", $report);
            // ->with("chapter", $chapter);
    }
}
