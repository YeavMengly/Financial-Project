<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AccountSubController;

Route::middleware('PermissionCheck')->controller(AccountSubController::class)->group(function () {
    Route::get('account/sub/{params}', 'index')->name('accountSub.index');
    Route::get('account/sub/create/{params}', 'create')->name('accountSub.create');
    Route::get('account/sub/edit/{params}', 'edit')->name('accountSub.edit');
    Route::get('account/sub/destroy/{params}', 'destroy')->name('accountSub.destroy');
});
Route::controller(AccountSubController::class)->group(function () {
    Route::post('account/sub/{params}/store', 'store')->name('accountSub.store');
    Route::post('account/sub/update/{params}', 'update')->name('accountSub.update');
});
