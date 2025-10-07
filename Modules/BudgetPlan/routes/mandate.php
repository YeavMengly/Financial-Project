<?php

use Illuminate\Support\Facades\Route;
// routes
use Modules\BudgetPlan\App\Http\Controllers\BudgetMandateController;

Route::middleware('PermissionCheck')
    ->controller(BudgetMandateController::class)
    ->group(function () {
        Route::get('mandate/', 'getIndex')->name('initialMandate.index');
        Route::get('mandate/{params}', 'index')->name('budgetMandate.index');
        Route::get('mandate/{params}/create', 'create')->name('budgetMandate.create');
        Route::get('mandate/{params}/edit/{id}', 'edit')->name('budgetMandate.edit');
        Route::get('mandate/{params}/destroy/{id}', 'destroy')->name('budgetMandate.destroy');

        // JSON numbers endpoint
        Route::get('Mandate/{params}/early-balance', 'getEarlyBalance')
            ->name('budgetMandate.getEarlyBalance');
    });

Route::controller(BudgetMandateController::class)->group(function () {
    Route::post('mandate/{params}/store', 'store')->name('budgetMandate.store');
    Route::post('mandate/{params}/update/{id}', 'update')->name('budgetMandate.update');
});
