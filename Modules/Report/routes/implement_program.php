<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\CostImplementProgramController;

Route::middleware('PermissionCheck')->controller(CostImplementProgramController::class)->group(function () {
    Route::get('/cost_implement/program', 'index')->name('cost.implement.program.index');
});