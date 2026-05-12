<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\StatesAssetsVehiclesController;

Route::middleware('PermissionCheck')->controller(StatesAssetsVehiclesController::class)->group(function () {
    Route::get('/states-assets-vehicles', 'index')->name('states.assets.vehicles.index');
});