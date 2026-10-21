<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\CostImplementHeaderExpenseTypeController;

Route::middleware('PermissionCheck')->controller(CostImplementHeaderExpenseTypeController::class)->group(function () {
    Route::get('/cost_implement/header_expense_type', 'index')->name('cost.implement.headerExpenseType.index');
});