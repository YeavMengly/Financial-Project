<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\CostImplementAgencyController;

Route::middleware('PermissionCheck')->controller(CostImplementAgencyController::class)->group(function () {
    Route::get('/cost_implement/agency', 'index')->name('cost.implement.agency.index');
});