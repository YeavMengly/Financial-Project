<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\SubAccountController;

Route::middleware('PermissionCheck')->controller(SubAccountController::class)->group(function () {
    Route::get('/sub.account', 'index')->name('subAccount.index');
    Route::get('/sub.account/create', 'create')->name('subAccount.create');
    Route::get('/sub.account/edit/{params}', 'edit')->name('subAccount.edit');
    Route::get('/sub.account/destroy/{params}', 'destroy')->name('subAccount.destroy');
});
Route::controller(SubAccountController::class)->group(function () {
    Route::post('/sub.account/store', 'store')->name('subAccount.store');
    Route::post('/sub.account/update/{params}', 'update')->name('subAccount.update');
    // Route::get('/sub.account/restore/{params}', 'restore')->name('subAccount.restore');
});
