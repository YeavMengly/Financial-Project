<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AccountController;


Route::middleware('PermissionCheck')->controller(AccountController::class)->group(function () {
    Route::get('/account', 'index')->name('account.index');
    Route::get('/account/create', 'create')->name('account.create');
    Route::get('/account/edit/{params}', 'edit')->name('account.edit');
    Route::get('/account/destroy/{params}', 'destroy')->name('account.destroy');
});
Route::controller(AccountController::class)->group(function () {
    Route::post('/account/store', 'store')->name('account.store');
    Route::post('/account/update/{params}', 'update')->name('account.update');
});
