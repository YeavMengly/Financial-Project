<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginVoucherController;

Route::middleware('PermissionCheck')->controller(BeginVoucherController::class)->group(function () {
    Route::get('begin/voucher/', 'getIndex')->name('initialBudgetVoucher.index');
    Route::get('begin/voucher/{params}/', 'index')->name('beginVoucher.index');
    Route::get('begin/voucher/{params}/create', 'create')->name('beginVoucher.create');
    Route::get('begin/voucher/{params}/edit/{id}', 'edit')->name('beginVoucher.edit');
    Route::get('begin/voucher/{params}/destroy/{id}', 'destroy')->name('beginVoucher.destroy');
});

Route::controller(BeginVoucherController::class)->group(function () {
    Route::post('begin/voucher{params}/store', 'store')->name('beginVoucher.store');
    Route::post('begin/voucher/{params}/update/{id}', 'update')->name('beginVoucher.update');
    // Route::get('/get-by-programid', 'getByProgramId')->name('beginVoucher.by.program_id');
    // Route::get('/edit-by-programid', 'editByProgramId')->name('beginVoucher.by.program_id');
    Route::get('begin/voucher/{params}/export', 'export')->name('beginVoucher.export');
});


Route::get('/begin-voucher/by-program/program-subs', [BeginVoucherController::class, 'editByProgramId'])
    ->name('beginVoucher.by.program_sub');

Route::get('/begin-voucher/by-program/agencies', [BeginVoucherController::class, 'editByAgency'])
    ->name('beginVoucher.by.agency');

Route::get('/begin-voucher/by-program-sub/clusters', [BeginVoucherController::class, 'editByProgramSubId'])
    ->name('beginVoucher.by.cluster');
