<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AccountController;


Route::middleware('PermissionCheck')->controller(AccountController::class)->group(function () {
    Route::get('accounts/', 'getIndex')->name('initialAccount.index'); // GET: initial-budget/
    Route::get('accounts/{params}', 'index')->name('accounts.index');
    Route::get('accounts/{params}/create', 'create')->name('accounts.create');
    Route::get('accounts/{params}/edit/{id}', 'edit')->name('accounts.edit');
    Route::get('accounts/{params}/destroy/{id}', 'destroy')->name('accounts.destroy');
});
Route::controller(AccountController::class)->group(function () {
    Route::post('accounts/{params}/store', 'store')->name('accounts.store');
    Route::post('accounts/{params}/update/{id}', 'update')->name('accounts.update');
    Route::get('accounts/{params}/restore/{id}', 'restore')->name('accounts.restore');
});
