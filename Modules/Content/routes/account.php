<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AccountController;


Route::middleware('PermissionCheck')->controller(AccountController::class)->group(function () {
    // Route::get('{params}/chapter/{chId}/accounts/', 'getIndex')->name('initialAccount.index'); // GET: initial-budget/
    Route::get('{params}/chapter/{chId}/accounts/', 'index')->name('accounts.index');
    Route::get('{params}/chapter/{chId}/accounts/create', 'create')->name('accounts.create');
    Route::get('{params}/chapter/{chId}/accounts/edit/{id}', 'edit')->name('accounts.edit');
    Route::get('{params}/chapter/{chId}/accounts/destroy/{id}', 'destroy')->name('accounts.destroy');
});
Route::controller(AccountController::class)->group(function () {
    Route::post('{params}/chapter/{chId}/accounts/store', 'store')->name('accounts.store');
    Route::post('{params}/chapter/{chId}/accounts/update/{id}', 'update')->name('accounts.update');
    Route::get('{params}/chapter/{chId}/accounts/restore/{id}', 'restore')->name('accounts.restore');
});
