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
});
