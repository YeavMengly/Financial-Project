<?php

use Illuminate\Support\Facades\Route;
use Modules\Electric\App\Http\Controllers\electricEntityController;

Route::middleware('PermissionCheck')->controller(electricEntityController::class)->group(function () {
    Route::get('electric/entity/', 'getIndex')->name('initialElectricEntity.index');

    Route::get('electric/entity/{params}', 'index')->name('electricEntity.index');
    Route::get('electric/entity/{params}/create', 'create')->name('electricEntity.create');
    Route::get('electric/entity/{params}/edit/{id}', 'edit')->name('electricEntity.edit');
    Route::get('electric/entity/{params}/destroy/{id}', 'destroy')->name('electricEntity.destroy');
});
Route::controller(electricEntityController::class)->group(function () {
    Route::post('electric/entity/{params}/store', 'store')->name('electricEntity.store');
    Route::post('electric/entity/{params}/update/{id}', 'update')->name('electricEntity.update');
    Route::get('electric/entity/{params}/restore/{id}', 'restore')->name('electricEntity.restore');
});
