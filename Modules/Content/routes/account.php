<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AccountController;


Route::middleware('PermissionCheck')->controller(AccountController::class)->group(function () {
    // Route::get('{params}/chapter/{chId}/accounts/', 'getIndex')->name('initialAccount.index'); // GET: initial-budget/
    Route::get('chapter/{params}/accounts/{chId}/', 'index')->name('accounts.index');
    Route::get('chapter/{params}/accounts/{chId}/create', 'create')->name('accounts.create');
    Route::get('chapter/{params}/accounts/{chId}/edit/{id}', 'edit')->name('accounts.edit');
    Route::get('chapter/{params}/accounts/{chId}/destroy/{id}', 'destroy')->name('accounts.destroy');
});
Route::controller(AccountController::class)->group(function () {
    Route::post('chapter/{params}/accounts/{chId}/store', 'store')->name('accounts.store');
    Route::post('chapter/{params}/accounts/{chId}/update/{id}', 'update')->name('accounts.update');
    Route::get('chapter/{params}/accounts/{chId}/restore/{id}', 'restore')->name('accounts.restore');
});
