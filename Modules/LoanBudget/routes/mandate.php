<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanBudget\App\Http\Controllers\LoanBudgetMandateController;

Route::middleware('PermissionCheck')->controller(LoanBudgetMandateController::class)->group(function () {
    Route::get('mandate/', 'getIndex')->name('mandateLoan.index');
    Route::get('mandate/{params}', 'index')->name('mandate.index');
    Route::get('mandate/{params}/create', 'create')->name('mandate.create');
    Route::get('mandate/{params}/edit/{id}', 'edit')->name('mandate.edit');
    Route::get('mandate/{params}/destroy/{id}', 'destroy')->name('mandate.destroy');
});
Route::controller(LoanBudgetMandateController::class)->group(function () {
    Route::post('mandate/{params}/store', 'store')->name('mandate.store');
    Route::post('mandate/{params}/update/{id}', 'update')->name('mandate.update');

    // These routes are for ajax request
    Route::get('mandate/get-by-program/program-subs', 'getByProgramId')->name('mandate.by.program_sub');
    Route::get('mandate/get-by-program/agencies', 'getByAgency')->name('mandate.by.agency');
    Route::get('mandate/get-by-program-sub/clusters', 'getByProgramSubId')->name('mandate.by.cluster');
    // These routes are for edit page ajax request
    Route::get('mandate/edit-by-program/program-subs', 'editByProgramId')->name('mandate.edit.program_sub');
    Route::get('mandate/edit-by-program/agencies', 'editByAgency')->name('mandate.edit.agency');
    Route::get('mandate/edit-by-program-sub/clusters', 'editByProgramSubId')->name('mandate.edit.cluster');
});
