<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ProgramController;
use Modules\Dashboard\App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get("/", "index")->name("dashboard.index");
        // ✅ program subs for popup (AJAX)

        // ✅ AJAX: get program subs for popup
        Route::get("/program/{program}/subs", "getProgramSubs")
            ->name("dashboard.program.subs");
    });
});


Route::get('/program-sub/{program}', [DashboardController::class, 'getProgramSubs']);
Route::get(
    '/dashboard/program-sub/{programSub}/clusters',
    [DashboardController::class, 'getClusters']
);
