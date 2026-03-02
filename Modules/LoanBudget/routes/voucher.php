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

    // These routes are for ajax request
    Route::get('voucher/get-by-program/program-subs', 'getByProgramId')->name('voucher.by.program_sub');
    Route::get('voucher/get-by-program/agencies', 'getByAgency')->name('voucher.by.agency');
    Route::get('voucher/get-by-program-sub/clusters', 'getByProgramSubId')->name('voucher.by.cluster');
    // These routes are for edit page ajax request
    Route::get('voucher/edit-by-program/program-subs', 'editByProgramId')->name('voucher.edit.program_sub');
    Route::get('voucher/edit-by-program/agencies', 'editByAgency')->name('voucher.edit.agency');
    Route::get('voucher/edit-by-program-sub/clusters', 'editByProgramSubId')->name('voucher.edit.cluster');
});
