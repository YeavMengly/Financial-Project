<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AccountSubController;

Route::middleware('PermissionCheck')->controller(AccountSubController::class)->group(function () {
    Route::get('account/sub/', 'getIndex')->name('initialAccountSub.index');
    Route::get('account/sub/{params}', 'index')->name('accountSub.index');
    Route::get('account/sub/{params}/create', 'create')->name('accountSub.create');
    Route::get('account/sub/{params}/edit/{id}', 'edit')->name('accountSub.edit');
    Route::get('account/sub/{params}/destroy/{id}', 'destroy')->name('accountSub.destroy');
});
Route::controller(AccountSubController::class)->group(function () {
    Route::post('account/sub/{params}/store', 'store')->name('accountSub.store');
    Route::post('account/sub/{params}/update/{id}', 'update')->name('accountSub.update');
    Route::get('account/sub/{params}/restore/{id}', 'restore')->name('accountSub.restore');
});
