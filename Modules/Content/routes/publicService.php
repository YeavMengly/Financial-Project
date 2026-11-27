<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\PublicServiceController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(PublicServiceController::class)->group(function () {
    Route::get('publicServices/', 'index')->name('publicServices.index');
    Route::get('publicServices/create', 'create')->name('publicServices.create');
    Route::get('publicServices/edit/{params}', 'edit')->name('publicServices.edit');
    Route::get('publicServices/destroy/{params}', 'destroy')->name('publicServices.destroy');
});

Route::controller(PublicServiceController::class)->group(function () {
    Route::post('publicServices/store', 'store')->name('publicServices.store');
    Route::post('publicServices/update/{params}', 'update')->name('publicServices.update');
    Route::get('publicServices/restore/{params}', 'restore')->name('publicServices.restore');
});
