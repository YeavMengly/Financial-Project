<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AccountController;


Route::middleware('PermissionCheck')->controller(AccountController::class)->group(function () {
    Route::get('accounts/{params}', 'index')->name('accounts.index');
    Route::get('accounts/{params}/create', 'create')->name('accounts.create');
    Route::get('accounts/edit/{params}', 'edit')->name('accounts.edit');
    Route::get('accounts/destroy/{params}', 'destroy')->name('accounts.destroy');
});
Route::controller(AccountController::class)->group(function () {
    Route::post('accounts/{params}/store', 'store')->name('accounts.store');
    Route::post('accounts/update/{params}', 'update')->name('accounts.update');
});
