<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialAccountSubController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(InitialAccountSubController::class)->group(function () {
    Route::get('initial/sub/account', 'index')->name('initialAccountSub.index'); // GET: initial-budget/
});
