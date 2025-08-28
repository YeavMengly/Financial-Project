<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ProgramSubController;

Route::middleware('PermissionCheck')->controller(ProgramSubController::class)->group(function () {
    Route::get('initial/program/sub/{params}', 'index')->name('programSub.index');
    Route::get('initial/program/sub/{params}/create', 'create')->name('programSub.create');
    Route::get('initial/program/sub/edit/{params}', 'edit')->name('programSub.edit');
    Route::get('initial/program/sub/destroy/{id}', 'destroy')->name('programSub.destroy');
});
Route::controller(ProgramSubController::class)->group(function () {
    Route::post('program/sub/{params}/store', 'store')->name('programSub.store');
    Route::post('program/sub/update/{params}', 'update')->name('programSub.update');
});
