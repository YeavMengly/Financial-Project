<?php

use Illuminate\Support\Facades\Route;
use Modules\Material\App\Http\Controllers\MaterialReleaseController;

Route::middleware('PermissionCheck')->controller(MaterialReleaseController::class)->group(function () {
    Route::get('material/release/', 'getIndex')->name('initialMaterialRelease.index');
    Route::get('material/release/{params}', 'index')->name('materialRelease.index');
    Route::get('material/release/{params}/create', 'create')->name('materialRelease.create');
    Route::get('material/release/{params}/edit/{id}', 'edit')->name('materialRelease.edit');
    Route::get('material/release/{params}/destroy/{id}', 'destroy')->name('materialRelease.destroy');
});
Route::controller(MaterialReleaseController::class)->group(function () {
    Route::post('material/release/{params}/store', 'store')->name('materialRelease.store');
    Route::post('material/release/{params}/update/{id}', 'update')->name('materialRelease.update');
});
