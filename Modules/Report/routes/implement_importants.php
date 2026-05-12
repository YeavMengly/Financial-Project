<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\CostImplementImportantsController;

Route::middleware('PermissionCheck')->controller(CostImplementImportantsController::class)->group(function () {
    Route::get('/cost_implement/importants', 'index')->name('cost.implement.importants.index');
});