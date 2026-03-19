<?php

use Illuminate\Support\Facades\Route;
use Modules\Water\App\Http\Controllers\WaterEntityController;

Route::middleware('PermissionCheck')->controller(WaterEntityController::class)->group(function () {
    Route::get('water/entity/', 'getIndex')->name('initialWaterEntity.index');

    Route::get('water/entity/{params}', 'index')->name('waterEntity.index');
    Route::get('water/entity/{params}/create', 'create')->name('waterEntity.create');
    Route::get('water/entity/{params}/edit/{id}', 'edit')->name('waterEntity.edit');
    Route::get('water/entity/{params}/destroy/{id}', 'destroy')->name('waterEntity.destroy');
});
Route::controller(WaterEntityController::class)->group(function () {
    Route::post('water/entity/{params}/store', 'store')->name('waterEntity.store');
    Route::post('water/entity/{params}/update/{id}', 'update')->name('waterEntity.update');
    Route::get('water/entity/{params}/restore/{id}', 'restore')->name('waterEntity.restore');
});
