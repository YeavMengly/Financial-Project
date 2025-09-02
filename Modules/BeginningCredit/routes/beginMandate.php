<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginMandateController;

Route::middleware('PermissionCheck')->controller(BeginMandateController::class)->group(function () {
    // Route::get('/beginCredit', 'index')->name('beginCredit.index');
    Route::get('/beginCreditMandate/create', 'create')->name('beginCreditMandate.create');
    Route::get('/beginCreditMandate/edit/{params}', 'edit')->name('beginCreditMandate.edit');
    Route::get('/beginCreditMandate/destroy/{params}', 'destroy')->name('beginCreditMandate.destroy');

    Route::get('/general', '')->name('general.index');

    Route::get('initial-budget-mandate/{params}/beginCreditMandate', 'index')->name('beginCreditMandate.index');
    Route::get('initial-budget-mandate/{params}/beginCreditMandate/create', 'create')->name('beginCreditMandate.create');
});
Route::controller(BeginMandateController::class)->group(function () {
    Route::post('initial-budget-mandate/{params}/beginCreditMandate/store', 'store')->name('beginCreditMandate.store');
    Route::post('/beginCreditMandate/update/{params}', 'update')->name('beginCreditMandate.update');
});
