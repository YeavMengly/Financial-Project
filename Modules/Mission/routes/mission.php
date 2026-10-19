<?php

use Illuminate\Support\Facades\Route;
use Modules\Mission\App\Http\Controllers\MissionController;


Route::middleware('PermissionCheck')->controller(MissionController::class)->group(function () {
    Route::get('mission/', 'getIndex')->name('initialMission.index');
    Route::get('mission/{params}', 'index')->name('missions.index');
    Route::get('mission/{params}/create', 'create')->name('missions.create');
    Route::get(
        'mission/{params}/edit/{id}',
        'edit'
    )->name('missions.edit');
    Route::get('mission/{params}/destroy/{id}', 'destroy')->name('missions.destroy');
});
Route::controller(MissionController::class)->group(function () {
    Route::post('mission/{params}/store', 'store')->name('missions.store');
    Route::post(
        'mission/{params}/update/{id}',
        'update'
    )->name('missions.update');
    Route::get('mission/{params}/restore', 'restore')->name('missions.restore');
    Route::get('{params}/mission/export', 'export')->name('missions.export');


    Route::get('mission/get-by-level', 'getByLevel')->name('missions.by.level');
    Route::get('mission/position/levels', 'getByPositionLevel')->name('position.levels');

    // Info Details
    Route::get('mission/{params}/show/{id}/details', 'show')->name('missions.show');

    // Add to payment
    Route::post('/missions/payment-total/{params}', 'paymentTotal')->name('missions.paymentTotal');
    Route::post(
        'mission/{params}/update-payment-status',
        'updatePaymentStatus'
    )->name('missions.updatePaymentStatus');

    // These routes are for ajax request
    Route::get('mission/get-by-program/program-subs', 'getByProgramId')->name('missions.by.program_sub');
    Route::get('mission/get-by-program/agencies', 'getByAgency')->name('missions.by.agency');
    Route::get('mission/get-by-program-sub/clusters', 'getByProgramSubId')->name('missions.by.cluster');
    // These routes are for edit page ajax request
    Route::get('mission/edit-by-program/program-subs', 'editByProgramId')->name('missions.edit.program_sub');
    Route::get('mission/edit-by-program/agencies', 'editByAgency')->name('missions.edit.agency');
    Route::get('mission/edit-by-program-sub/clusters', 'editByProgramSubId')->name('missions.edit.cluster');
});
// Route::get(
//     'mission/{params}/employee/{id}',
//     [MissionController::class, 'destroyEmployee']
// )->name('missions.employee.destroy');
Route::delete(
    'mission/{params}/employee/{id}',
    [MissionController::class, 'destroyEmployee']
)->name('missions.employee.destroy');
Route::get(
    'missions/payment-account',
    [MissionController::class, 'getPaymentAccount']
)->name('missions.paymentAccount');
