<?php

use Illuminate\Support\Facades\Route;
use Modules\Duel\App\Http\Controllers\DuelReleaseController;

Route::middleware('PermissionCheck')->controller(DuelReleaseController::class)->group(function () {
    Route::get('duel/release/', 'getIndex')->name('initialDuelRelease.index');
    Route::get('duel/release/{params}', 'index')->name('duelRelease.index');
    Route::get('duel/release/{params}/create', 'create')->name('duelRelease.create');
    Route::get('duel/release/{params}/edit/{id}', 'edit')->name('duelRelease.edit');
    Route::get('duel/release/{params}/destroy/{id}', 'destroy')->name('duelRelease.destroy');
});
Route::controller(DuelReleaseController::class)->group(function () {
    Route::post('duel/release/{params}/store', 'store')->name('duelRelease.store');
    Route::post('duel/release/{params}/update/{id}', 'update')->name('duelRelease.update');
    Route::get('duel/release/by-stock_number/{params}', 'getByStockID')->name('duelRelease.by.stock_number');
    Route::get('duel/release/{params}/export', 'export')->name('duelRelease.export');
});
