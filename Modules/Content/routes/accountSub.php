<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AccountSubController;

Route::middleware('PermissionCheck')->controller(AccountSubController::class)->group(function () {
    Route::get('{params}/chapter/{chId}/accounts/{accId}/sub', 'index')->name('accountSub.index');
    Route::get('{params}/chapter/{chId}/accounts/{accId}/sub/create', 'create')->name('accountSub.create');
    Route::get('{params}/chapter/{chId}/accounts/{accId}/sub/edit/{id}', 'edit')->name('accountSub.edit');
    Route::get('{params}/chapter/{chId}/accounts/{accId}/sub/destroy/{id}', 'destroy')->name('accountSub.destroy');
});
Route::controller(AccountSubController::class)->group(function () {
    Route::post('{params}/chapter/{chId}/accounts/{accId}/sub/store', 'store')->name('accountSub.store');
    Route::post('{params}/chapter/{chId}/accounts/{accId}/sub/update/{id}', 'update')->name('accountSub.update');
    Route::get('{params}/chapter/{chId}/accounts/{accId}/sub/restore/{id}', 'restore')->name('accountSub.restore');
});
