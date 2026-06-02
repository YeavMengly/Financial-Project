<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\AgencyController;

Route::middleware('PermissionCheck')->controller(AgencyController::class)->group(function () {
    Route::get('/agency', 'getIndex')->name('initialAgency.index');
    Route::get('/{params}/agency', 'index')->name('agency.index');
    Route::get('/{params}/agency/create', 'create')->name('agency.create');
    Route::get('/{params}/agency/edit/{id}', 'edit')->name('agency.edit');
    Route::get('/{params}/agency/destroy/{id}', 'destroy')->name('agency.destroy');

    // Executive Unit
    Route::get('/{params}/agency/{executiveId}/executive-unit', 'executiveIndex')->name('executiveUnit.index');
    Route::get('/{params}/agency/{executiveId}/executive-unit/create', 'executiveCreate')->name('executiveUnit.create');
    Route::get('/{params}/agency/{executiveId}/executive-unit/edit/{id}', 'executiveEdit')->name('executiveUnit.edit');
    Route::get('/{params}/agency/{executiveId}/executive-unit/destroy/{id}', 'executiveDestroy')->name('executiveUnit.destroy');

});
Route::controller(AgencyController::class)->group(function () {
    Route::post('/{params}/agency/store', 'store')->name('agency.store');
    Route::post('/{params}/agency/update/{id}', 'update')->name('agency.update');
    Route::get('/{params}/agency/restore/{id}', 'restore')->name('agency.restore');

    Route::post('/{params}/agency/{executiveId}/executive-unit/store', 'executiveStore')->name('executiveUnit.store');
    Route::post('/{params}/agency/{executiveId}/executive-unit/update/{id}', 'executiveUpdate')->name('executiveUnit.update');
    Route::get('/{params}/agency/{executiveId}/executive-unit/restore/{id}', 'executiveRestore')->name('executiveUnit.restore');    
    
});
