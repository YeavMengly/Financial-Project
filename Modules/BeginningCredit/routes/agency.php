<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\AgencyController;

Route::middleware('PermissionCheck')->controller(AgencyController::class)->group(function () {
    Route::get('agency/{params}', 'index')->name('agency.index');
    Route::get('agency/{params}/create', 'create')->name('agency.create');
    Route::get('agency/edit/{params}', 'edit')->name('agency.edit');
    Route::get('agency/destroy/{params}', 'destroy')->name('agency.destroy');
});
Route::controller(AgencyController::class)->group(function () {
    Route::post('agency/store/{params}', 'store')->name('agency.store');
    Route::post('agency/update/{params}', 'update')->name('agency.update');
});
