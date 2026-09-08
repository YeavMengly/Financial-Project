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
        Route::get('initial_missions/{params}/edit', 'edit')->name('missions.edit');
        Route::get('initial_missions/{params}/destroy', 'destroy')->name('missions.destroy');
    });
    Route::controller(MissionController::class)->group(function () {
        Route::post('initial_missions/{params}/store', 'store')->name('missions.store');
        Route::post('initial_missions/{params}/update', 'update')->name('missions.update');
        Route::get('initial_missions/{params}/restore', 'restore')->name('missions.restore');

        Route::get('initial_missions/get-by-level', 'getByLevel')->name('missions.by.level');
    });
});
