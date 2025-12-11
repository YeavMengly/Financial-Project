<?php

use Illuminate\Support\Facades\Route;
use Modules\Electric\App\Http\Controllers\ElectricController;

Route::middleware('PermissionCheck')->controller(ElectricController::class)->group(function () {
    Route::get('electric/', 'getIndex')->name('initialElectric.index');
    Route::get('electric/{params}', 'index')->name('electric.index');
    Route::get('electric/{params}/create', 'create')->name('electric.create');
    Route::get('electric/{params}/edit/{id}', 'edit')->name('electric.edit');
    Route::get('electric/{params}/destroy/{id}', 'destroy')->name('electric.destroy');
});
Route::controller(ElectricController::class)->group(function () {
    Route::post('electric/{params}/store', 'store')->name('electric.store');
    Route::post('electric/{params}/update/{id}', 'update')->name('electric.update');
    Route::get('electric/{params}/restore/{id}', 'restore')->name('electric.restore');
    Route::get('electric/{params}/export', 'export')->name('electric.export');
});
