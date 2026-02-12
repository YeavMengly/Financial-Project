<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginVoucherController;

// All routes in this file
Route::middleware('PermissionCheck')->controller(BeginVoucherController::class)->group(function () {
    // These routes are for page view
    Route::get('credit-approved/', 'getIndex')->name('initialBudgetVoucher.index');
    Route::get('{params}/credit-approved/', 'index')->name('beginVoucher.index');
    Route::get('{params}/credit-approved/create', 'create')->name('beginVoucher.create');
    Route::get('{params}/credit-approved/edit/{id}', 'edit')->name('beginVoucher.edit');
    Route::get('{params}/credit-approved/destroy/{id}', 'destroy')->name('beginVoucher.destroy');
});

// These routes are for form submit and ajax request, so no need to apply PermissionCheck middleware
Route::controller(BeginVoucherController::class)->group(function () {
    // These routes are for form submit
    Route::post('{params}/credit-approved/store', 'store')->name('beginVoucher.store');
    Route::post('{params}/credit-approved/update/{id}', 'update')->name('beginVoucher.update');
    Route::get('{params}/credit-approved/export', 'export')->name('beginVoucher.export');

    // These routes are for ajax request
    Route::get('/begin-voucher/get-by-program/program-subs', 'getByProgramId')->name('beginVoucher.by.program_sub');
    Route::get('/begin-voucher/get-by-program/agencies', 'getByAgency')->name('beginVoucher.by.agency');
    Route::get('/begin-voucher/get-by-program-sub/clusters', 'getByProgramSubId')->name('beginVoucher.by.cluster');
    // These routes are for edit page ajax request
    Route::get('/begin-voucher/edit-by-program/program-subs', 'editByProgramId')->name('beginVoucher.edit.program_sub');
    Route::get('/begin-voucher/edit-by-program/agencies', 'editByAgency')->name('beginVoucher.edit.agency');
    Route::get('/begin-voucher/edit-by-program-sub/clusters', 'editByProgramSubId')->name('beginVoucher.edit.cluster');
});
