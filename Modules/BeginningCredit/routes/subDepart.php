<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\SubDepartController;

Route::middleware('PermissionCheck')->controller(SubDepartController::class)->group(function () {
    Route::get('/sub.depart', 'index')->name('subDepart.index');
    Route::get('/sub.depart/create', 'create')->name('subDepart.create');
    Route::get('/sub.depart/edit/{params}', 'edit')->name('subDepart.edit');
    Route::get('/sub.depart/destroy/{params}', 'destroy')->name('subDepart.destroy');
});
Route::controller(SubDepartController::class)->group(function () {
    Route::post('/sub.depart/store', 'store')->name('subDepart.store');
    Route::post('/sub.depart/update/{params}', 'update')->name('subDepart.update');
    // Route::get('/sub.account/restore/{params}', 'restore')->name('subAccount.restore');
});
