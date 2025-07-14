<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanBudget\App\Http\Controllers\LoanBudgetVoucherController;

Route::middleware('PermissionCheck')->controller(LoanBudgetVoucherController::class)->group(function () {
    Route::get('/initial-voucher/{params}/voucher', 'index')->name('voucher.index');
    Route::get('/initial-voucher/{params}/voucher/create', 'create')->name('voucher.create');
    Route::get('/initial-voucher/{params}/voucher/destroy', 'destroy')->name('voucher.destroy');

    //  Error
    Route::get('/initial-voucher/{params}/voucher/edit', 'edit')->name('voucher.edit');
});
Route::controller(LoanBudgetVoucherController::class)->group(function () {
    Route::post('/initial-voucher/{params}/voucher/store', 'store')->name('voucher.store');

    // Erorr 
    Route::post('/initial-voucher/voucher/update/{params}', 'update')->name('voucher.update');
});
