<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialChapterController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->prefix('initial-chapter')->controller(InitialChapterController::class)->group(function () {
    Route::get('/', 'index')->name('initialChapter.index'); // GET: initial-budget/
});
