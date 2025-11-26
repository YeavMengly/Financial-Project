<?php

use Illuminate\Support\Facades\Route;
use Modules\Duel\App\Http\Controllers\DuelEntryController;

Route::middleware('PermissionCheck')->controller(DuelEntryController::class)->group(function () {
    Route::get('duel/entry/', 'getIndex')->name('initialDuelEntry.index');

    Route::get('duel/entry/{params}', 'index')->name('duelEntry.index');
    Route::get('duel/entry/{params}/create', 'create')->name('duelEntry.create');
    Route::get('duel/entry/{params}/edit/{id}', 'edit')->name('duelEntry.edit');
    Route::get('duel/entry/{params}/destroy/{id}', 'destroy')->name('duelEntry.destroy');
});
Route::controller(DuelEntryController::class)->group(function () {
    Route::post('duel/entry/{params}/store', 'store')->name('duelEntry.store');
    Route::post('duel/entry/{params}/update/{id}', 'update')->name('duelEntry.update');
});
