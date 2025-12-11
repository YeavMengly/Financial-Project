<?php

use Illuminate\Support\Facades\Route;
use Modules\Water\App\Http\Controllers\WaterController;

Route::middleware('PermissionCheck')->controller(WaterController::class)->group(function () {
    Route::get('water/', 'getIndex')->name('initialWater.index');
    Route::get('water/{params}', 'index')->name('water.index');
    Route::get('water/{params}/create', 'create')->name('water.create');
    Route::get('water/{params}/edit/{id}', 'edit')->name('water.edit');
    Route::get('water/{params}/destroy/{id}', 'destroy')->name('water.destroy');
});
Route::controller(WaterController::class)->group(function () {
    Route::post('water/{params}/store', 'store')->name('water.store');
    Route::post('water/{params}/update/{id}', 'update')->name('water.update');
    Route::get('water/{params}/restore/{id}', 'restore')->name('water.restore');

    Route::get('water/{params}/export', 'export')->name('water.export');
});
