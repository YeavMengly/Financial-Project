<?php

use Illuminate\Support\Facades\Route;
use Modules\Mission\App\Http\Controllers\MissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('missions')->middleware(['auth'])->group(function () {
    Route::middleware('PermissionCheck')->controller(MissionController::class)->group(function () {
        Route::get('/', 'getIndex')->name('initialMissions.index');
        Route::get('initial_missions/{params}', 'index')->name('missions.index');
        Route::get('initial_missions/{params}/create', 'create')->name('missions.create');
        Route::get(
            'initial_missions/{params}/edit/{id}',
            'edit'
        )->name('missions.edit');
        Route::get('initial_missions/{params}/destroy/{id}', 'destroy')->name('missions.destroy');
    });
    Route::controller(MissionController::class)->group(function () {
        Route::post('initial_missions/{params}/store', 'store')->name('missions.store');
        Route::post(
            'initial_missions/{params}/update/{id}',
            'update'
        )->name('missions.update');
        Route::get('initial_missions/{params}/restore', 'restore')->name('missions.restore');

        Route::get('initial_missions/get-by-level', 'getByLevel')->name('missions.by.level');
        Route::get('initial_missions/position/levels', 'getByPositionLevel')->name('position.levels');

        // Info Details
        Route::get('initial_missions/{params}/show/{id}/details', 'show')->name('missions.show');

        // Add to payment
        Route::post('/missions/payment-total', 'paymentTotal')->name('missions.paymentTotal');
        Route::post(
            'initial_missions/{params}/update-payment-status',
            'updatePaymentStatus'
        )->name('missions.updatePaymentStatus');

        // These routes are for ajax request
        Route::get('initial_missions/get-by-program/program-subs', 'getByProgramId')->name('missions.by.program_sub');
        Route::get('initial_missions/get-by-program/agencies', 'getByAgency')->name('missions.by.agency');
        Route::get('initial_missions/get-by-program-sub/clusters', 'getByProgramSubId')->name('missions.by.cluster');
        // These routes are for edit page ajax request
        Route::get('initial_missions/edit-by-program/program-subs', 'editByProgramId')->name('missions.edit.program_sub');
        Route::get('initial_missions/edit-by-program/agencies', 'editByAgency')->name('missions.edit.agency');
        Route::get('initial_missions/edit-by-program-sub/clusters', 'editByProgramSubId')->name('missions.edit.cluster');
    });
});
// Route::get(
//     'initial_missions/{params}/employee/{id}',
//     [MissionController::class, 'destroyEmployee']
// )->name('missions.employee.destroy');
Route::delete(
    'initial_missions/{params}/employee/{id}',
    [MissionController::class, 'destroyEmployee']
)->name('missions.employee.destroy');
