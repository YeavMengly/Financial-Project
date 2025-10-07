<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\MinistryController;

/*
|--------------------------------------------------------------------------
| Initial Budget Routes (2025 Structure)
|--------------------------------------------------------------------------
| These routes manage the initial budget entries for the fiscal year 2025.
| They include index, create, edit, destroy, show, store, and update logic.
*/

// Routes with permission check middleware
Route::middleware('PermissionCheck')->controller(MinistryController::class)->group(function () {
    Route::get('/', 'index')->name('ministries.index');
    Route::get('/create', 'create')->name('ministries.create');
    Route::get('/edit/{params}', 'edit')->name('ministries.edit');
    Route::get('/destroy/{params}', 'destroy')->name('ministries.destroy');
});

Route::controller(MinistryController::class)->group(function () {
    Route::post('/store', 'store')->name('ministries.store');
    Route::post('/update/{params}', 'update')->name('ministries.update');
    Route::get('/restore/{id}', 'restore')->name('ministries.restore');
});
