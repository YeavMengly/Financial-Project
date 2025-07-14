<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\InitialVoucherController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

Route::middleware('PermissionCheck')->prefix('initial-voucher')->name('initialVoucher.')->controller(InitialVoucherController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/edit/{params}', 'edit')->name('edit');
    Route::get('/destroy/{params}', 'destroy')->name('destroy');
    Route::get('/show/{params}', 'show')->name('show');
});

Route::prefix('initial-voucher')->name('initialVoucher.')->controller(InitialVoucherController::class)->group(function () {
    Route::post('/store', 'store')->name('store');
    Route::post('/update/{params}', 'update')->name('update');
});
