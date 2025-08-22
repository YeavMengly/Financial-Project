<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ProgramController;

Route::middleware('PermissionCheck')->controller(ProgramController::class)->group(function () {
    Route::get('/program/{params}', 'index')->name('programs.index');
    Route::get('/program/{params}/create', 'create')->name('programs.create');
    Route::get('/program/edit/{params}', 'edit')->name('programs.edit');
    Route::get('/program/destroy/{params}', 'destroy')->name('programs.destroy');

});
Route::controller(ProgramController::class)->group(function () {
    Route::post('/program/{params}/store', 'store')->name('programs.store');
    Route::post('/program/update/{params}', 'update')->name('programs.update');
});
