<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialProgramSubController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(InitialProgramSubController::class)->group(function () {
    Route::get('initial/program/sub', 'index')->name('initialProgramSub.index'); // GET: initial-budget/
    // Route::get('/show/{params}', 'show')->name('initialProgram.show'); // GET: initial-budget/show/{id}
});
