<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanBudget\App\Http\Controllers\LoanBudgetMandateController;

Route::middleware('PermissionCheck')->controller(LoanBudgetMandateController::class)->group(function () {
    Route::get('/initial-mandate/{params}/mandate', 'index')->name('mandate.index');
    Route::get('/initial-mandate/{params}/mandate/create', 'create')->name('mandate.create');
    Route::get('/initial-mandate/{params}/mandate/destroy', 'destroy')->name('mandate.destroy');

    // Error
    Route::get('/initial-mandate/{params}/mandate/edit', 'edit')->name('mandate.edit');
});
Route::controller(LoanBudgetMandateController::class)->group(function () {
    Route::post('/initial-mandate/{params}/mandate/store', 'store')->name('mandate.store');

    //Error
    // Route::post('/initial-mandate/{params}/mandate/update/{params}', 'update')->name('mandate.update');
});
