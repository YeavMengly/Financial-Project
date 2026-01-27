<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AgencyController;

Route::middleware('PermissionCheck')->controller(AgencyController::class)->group(function () {
    Route::get('agency/', 'getIndex')->name('initialAgency.index');
    Route::get('agency/{params}', 'index')->name('agency.index');
    Route::get('agency/{params}/create', 'create')->name('agency.create');
    Route::get('agency/{params}/edit/{id}', 'edit')->name('agency.edit');
    Route::get('agency/{params}/destroy/{id}', 'destroy')->name('agency.destroy');
});
Route::controller(AgencyController::class)->group(function () {
    Route::post('agency/store/{params}', 'store')->name('agency.store');
    Route::post('agency/{params}/update/{id}', 'update')->name('agency.update');
    Route::get('agency/{params}/restore/{id}', 'restore')->name('agency.restore');
});
