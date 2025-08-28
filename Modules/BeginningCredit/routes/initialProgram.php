<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialProgramController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(InitialProgramController::class)->group(function () {
    Route::get('initial/program/', 'index')->name('initialProgram.index'); // GET: initial-budget/
    // Route::get('/show/{params}', 'show')->name('initialProgram.show'); // GET: initial-budget/show/{id}
});
