<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ProgramController;

Route::middleware('PermissionCheck')->controller(ProgramController::class)->group(function () {
    Route::get('program/{params}', 'index')->name('program.index');
    Route::get('program/{params}/create', 'create')->name('program.create');
    Route::get('program/{params}/edit', 'edit')->name('program.edit');
    Route::get('program/destroy/{params}', 'destroy')->name('program.destroy');
});
Route::controller(ProgramController::class)->group(function () {
    Route::post('program/{params}/store', 'store')->name('program.store');
    Route::post('program/update/{params}', 'update')->name('program.update');
});
