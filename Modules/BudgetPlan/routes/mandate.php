<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\BudgetMandateController;

Route::middleware('PermissionCheck')
    ->controller(BudgetMandateController::class)
    ->group(function () {
        Route::get('mandate/', 'getIndex')->name('initialMandate.index');
        Route::get('mandate/{params}', 'index')->name('budgetMandate.index');
        Route::get('mandate/{params}/create', 'create')->name('budgetMandate.create');
        Route::get('mandate/{params}/edit/{id}', 'edit')->name('budgetMandate.edit');
        Route::get('mandate/{params}/destroy/{id}', 'destroy')->name('budgetMandate.destroy');
    });

Route::controller(BudgetMandateController::class)->group(function () {
    Route::post('mandate/{params}/store', 'store')->name('budgetMandate.store');
    Route::post('mandate/{params}/update/{id}', 'update')->name('budgetMandate.update');
    Route::get('mandate/{params}/export', 'export')->name('budgetMandate.export');

    // These routes are for ajax request
    Route::get('mandate/get-by-program/program-subs', 'getByProgramId')->name('budgetMandate.by.program_sub');
    Route::get('mandate/get-by-program/agencies', 'getByAgency')->name('budgetMandate.by.agency');
    Route::get('mandate/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetMandate.by.cluster');
    // These routes are for edit page ajax request
    Route::get('mandate/edit-by-program/program-subs', 'editByProgramId')->name('budgetMandate.edit.program_sub');
    Route::get('mandate/edit-by-program/agencies', 'editByAgency')->name('budgetMandate.edit.agency');
    Route::get('mandate/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetMandate.edit.cluster');


    Route::get('mandate/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetMandate.getEarlyBalance');

    Route::get('mandate/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetMandate.editEarlyBalance');
});
