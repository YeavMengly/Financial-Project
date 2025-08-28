<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ChapterController;

Route::middleware('PermissionCheck')->controller(ChapterController::class)->group(function () {
    Route::get('chapter/{params}', 'index')->name('chapters.index');
    Route::get('chapter/{params}/create', 'create')->name('chapters.create');
    Route::get('chapter/edit/{params}', 'edit')->name('chapters.edit');
    Route::get('chapter/destroy/{params}', 'destroy')->name('chapters.destroy');
});
Route::controller(ChapterController::class)->group(function () {
    Route::post('chapter/{params}/store', 'store')->name('chapters.store');
    Route::post('chapter/update/{params}', 'update')->name('chapters.update');
});
