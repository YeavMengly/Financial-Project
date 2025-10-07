<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanBudget\App\Http\Controllers\LoanBudgetVoucherController;

Route::middleware('PermissionCheck')->controller(LoanBudgetVoucherController::class)->group(function () {
    Route::get('voucher/', 'getIndex')->name('voucherLoan.index');
    Route::get('voucher/{params}', 'index')->name('voucher.index');
    Route::get('voucher/{params}/create', 'create')->name('voucher.create');
    Route::get('voucher/{params}/edit/{id}', 'edit')->name('voucher.edit');
    Route::get('voucher/{params}/destroy/{id}', 'destroy')->name('voucher.destroy');
});
Route::controller(LoanBudgetVoucherController::class)->group(function () {
    Route::post('voucher/{params}/store', 'store')->name('voucher.store');
    Route::post('voucher/{params}/update/{id}', 'update')->name('voucher.update');
});
