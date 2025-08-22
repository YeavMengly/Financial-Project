<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialAgencyController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

Route::middleware('PermissionCheck')->controller(InitialAgencyController::class)->group(function () {
    Route::get('agency/', 'index')->name('initialAgency.index');
});
