<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginVoucherController;

Route::middleware('PermissionCheck')->controller(BeginVoucherController::class)->group(function () {
    Route::get('begin/voucher/{params}/', 'index')->name('beginVoucher.index');
    Route::get('begin/voucher/{params}/create', 'create')->name('beginVoucher.create');
    Route::get('begin/voucher/edit/{params}', 'edit')->name('beginVoucher.edit');
    Route::get('begin/voucher/destroy/{params}', 'destroy')->name('beginVoucher.destroy');

    // Route::get('/general', '')->name('general.index');
});
Route::controller(BeginVoucherController::class)->group(function () {
    Route::post('begin/voucher{params}/store', 'store')->name('beginVoucher.store');
    Route::post('begin/voucher/update/{params}', 'update')->name('beginVoucher.update');
    Route::get('/get-by-programid', 'getByProgramId')->name('beginVoucher.by.program_id');
});
