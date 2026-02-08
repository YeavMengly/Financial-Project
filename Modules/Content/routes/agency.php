<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AgencyController;

Route::middleware('PermissionCheck')->controller(AgencyController::class)->group(function () {
    Route::get('/agency', 'getIndex')->name('initialAgency.index');
    Route::get('/{params}/agency', 'index')->name('agency.index');
    Route::get('/{params}/agency/create', 'create')->name('agency.create');
    Route::get('/{params}/agency/edit/{id}', 'edit')->name('agency.edit');
    Route::get('/{params}/agency/destroy/{id}', 'destroy')->name('agency.destroy');
});
Route::controller(AgencyController::class)->group(function () {
    Route::post('/{params}/agency/store', 'store')->name('agency.store');
    Route::post('/{params}/agency/update/{id}', 'update')->name('agency.update');
    Route::get('/{params}/agency/restore/{id}', 'restore')->name('agency.restore');
});
