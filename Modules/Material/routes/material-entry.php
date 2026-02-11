<?php

use Illuminate\Support\Facades\Route;
use Modules\Material\App\Http\Controllers\MaterialEntryController;

Route::middleware('PermissionCheck')->controller(MaterialEntryController::class)->group(function () {
    Route::get('material/entry/', 'getIndex')->name('initialMaterialEntry.index');
    Route::get('material/entry/{params}', 'index')->name('materialEntry.index');
    Route::get('material/entry/{params}/create', 'create')->name('materialEntry.create');
    Route::get('material/entry/{params}/edit/{id}', 'edit')->name('materialEntry.edit');
    Route::get('material/entry/{params}/destroy/{id}', 'destroy')->name('materialEntry.destroy');
});
Route::controller(MaterialEntryController::class)->group(function () {
    Route::post('material/entry/{params}/store', 'store')->name('materialEntry.store');
    Route::post('material/entry/{params}/update/{id}', 'update')->name('materialEntry.update');
    Route::get('material/entry/{params}/export', 'export')->name('materialEntry.export');
});
