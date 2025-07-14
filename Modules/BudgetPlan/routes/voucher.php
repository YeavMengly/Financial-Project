<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\BudgetVoucherController;

Route::middleware('PermissionCheck')->controller(BudgetVoucherController::class)->group(function () {

    Route::get('/initial-voucher/{params}/voucher', 'index')->name('budget-voucher.index');
    Route::get('/initial-voucher/{params}/voucher/create', 'create')->name('budget-voucher.create');
    Route::get('/voucher/destroy/{params}', 'destroy')->name('budget-voucher.destroy');

    // Error
    Route::get('/initial-voucher/{params}/voucher/edit', 'edit')->name('budget-voucher.edit');
});

Route::controller(BudgetVoucherController::class)->group(function () {
    Route::post('initial-voucher/{params}/voucher/store', 'store')->name('budget-voucher.store');
    Route::get('/voucher/restore/{params}', 'restore')->name('budget-voucher.restore');
    Route::get('/voucher/create/{subAccountId}/{programCode}/early-balance', 'getEarlyBalance')
        ->name('budget-voucher.getEarlyBalance');
    Route::get('/voucher/edit/{subAccountId}/{programCode}/early-balance', 'getEarlyBalance')
        ->name('budget-voucher.getEarlyBalance');

    // Error
    Route::post('/voucher/update/{params}', 'update')->name('budget-voucher.update');
});
