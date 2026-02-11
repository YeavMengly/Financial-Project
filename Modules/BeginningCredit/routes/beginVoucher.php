<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginVoucherController;

Route::middleware('PermissionCheck')->controller(BeginVoucherController::class)->group(function () {
    Route::get('credit-approved/', 'getIndex')->name('initialBudgetVoucher.index');
    Route::get('{params}/credit-approved/', 'index')->name('beginVoucher.index');
    Route::get('{params}/credit-approved/create', 'create')->name('beginVoucher.create');
    Route::get('{params}/credit-approved/edit/{id}', 'edit')->name('beginVoucher.edit');
    Route::get('{params}/credit-approved/destroy/{id}', 'destroy')->name('beginVoucher.destroy');
});

Route::controller(BeginVoucherController::class)->group(function () {
    Route::post('{params}/credit-approved/store', 'store')->name('beginVoucher.store');
    Route::post('{params}/credit-approved/update/{id}', 'update')->name('beginVoucher.update');
    Route::get('{params}/credit-approved/export', 'export')->name('beginVoucher.export');
});


Route::get('/begin-voucher/by-program/program-subs', [BeginVoucherController::class, 'getByProgramId'])
    ->name('beginVoucher.by.program_sub');

Route::get('/begin-voucher/by-program/agencies', [BeginVoucherController::class, 'getByAgency'])
    ->name('beginVoucher.by.agency');

Route::get('/begin-voucher/by-program-sub/clusters', [BeginVoucherController::class, 'getByProgramSubId'])
    ->name('beginVoucher.by.cluster');
