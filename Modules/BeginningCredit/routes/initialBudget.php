<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\InitialBudgetController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->prefix('initial-budget')->name('initialBudget.')->controller(InitialBudgetController::class)->group(function () {
    Route::get('/', 'index')->name('index'); // GET: initial-budget/
    Route::get('/create', 'create')->name('create'); // GET: initial-budget/create
    Route::get('/edit/{params}', 'edit')->name('edit'); // GET: initial-budget/edit/{id}
    Route::get('/destroy/{params}', 'destroy')->name('destroy'); // GET: initial-budget/destroy/{id}
    Route::get('/show/{params}', 'show')->name('show'); // GET: initial-budget/show/{id}
});

// Routes without permission check (e.g. for POST actions)
Route::prefix('initial-budget')->name('initialBudget.')->controller(InitialBudgetController::class)->group(function () {
    Route::post('/store', 'store')->name('store'); // POST: initial-budget/store
    Route::post('/update/{params}', 'update')->name('update'); // POST: initial-budget/update/{id}
});
