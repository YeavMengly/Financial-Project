<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginningCreditController;

Route::middleware('PermissionCheck')->controller(BeginningCreditController::class)->group(function () {
    Route::get('initial-budget/{params}/beginCredit', 'index')->name('beginCredit.index');
    Route::get('initial-budget/{params}/beginCredit/create', 'create')->name('beginCredit.create');
    Route::get('initial-budget/beginCredit/edit/{params}', 'edit')->name('beginCredit.edit');
    Route::get('initial-budget/beginCredit/destroy/{params}', 'destroy')->name('beginCredit.destroy');

    Route::get('/general', '')->name('general.index');
});
Route::controller(BeginningCreditController::class)->group(function () {
    Route::post('initial-budget/{params}/beginCredit/store', 'store')->name('beginCredit.store');
    Route::post('initial-budget/beginCredit/update/{params}', 'update')->name('beginCredit.update');
});
// Route to show BeginCredits by agency
Route::get('initial-budget/{params}/agency/begin-credits', [BeginningCreditController::class, 'index'])->name('agency.beginCredits');
