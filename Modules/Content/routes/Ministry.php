<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\MinistryController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(MinistryController::class)->group(function () {
    Route::get('ministries/', 'index')->name('ministries.index');
    Route::get('ministries/create', 'create')->name('ministries.create');
    Route::get('ministries/edit/{params}', 'edit')->name('ministries.edit');
    Route::get('ministries/destroy/{params}', 'destroy')->name('ministries.destroy');
});

Route::controller(MinistryController::class)->group(function () {
    Route::post('ministries/store', 'store')->name('ministries.store');
    Route::post('ministries/update/{params}', 'update')->name('ministries.update');
    Route::get('ministries/restore/{id}', 'restore')->name('ministries.restore');
});
