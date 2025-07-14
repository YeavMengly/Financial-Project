<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\BudgetMandateController;

Route::middleware('PermissionCheck')->controller(BudgetMandateController::class)->group(function () {
    Route::get('/initial-mandate/{params}/mandate', 'index')->name('budget-mandate.index');
    Route::get('/initial-mandate/{params}/mandate/create', 'create')->name('budget-mandate.create');
    Route::get('/mandate/destroy/{params}', 'destroy')->name('budget-mandate.destroy');

    // Error
    Route::get('/initial-mandate/{params}/mandate/edit', 'edit')->name('budget-mandate.edit');
});

Route::controller(BudgetMandateController::class)->group(function () {
    Route::post('initial-voucher/{params}/mandate/store', 'store')->name('budget-mandate.store');
    Route::get('/mandate/restore/{params}', 'restore')->name('budget-mandate.restore');
    Route::get('/mandate/create/{subAccountId}/{programCode}/early-balance', 'getEarlyBalance')
        ->name('budget-mandate.getEarlyBalance');
    Route::get('/mandate/edit/{subAccountId}/{programCode}/early-balance', 'getEarlyBalance')
        ->name('budget-mandate.getEarlyBalance');

    // Error
    Route::post('/mandate/update/{params}', 'update')->name('budget-mandate.update');
});
