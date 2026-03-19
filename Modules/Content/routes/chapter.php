<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ChapterController;

Route::middleware('PermissionCheck')->controller(ChapterController::class)->group(function () {
    Route::get('/chapter', 'getIndex')->name('initialChapter.index');
    Route::get('{params}/chapter', 'index')->name('chapters.index');
    Route::get('{params}/chapter/create', 'create')->name('chapters.create');
    Route::get('{params}/chapter/edit/{id}', 'edit')->name('chapters.edit');
    Route::get('{params}/chapter/destroy/{id}', 'destroy')->name('chapters.destroy');
});
Route::controller(ChapterController::class)->group(function () {
    Route::post('{params}/chapter/store', 'store')->name('chapters.store');
    Route::post('{params}/chapter/update/{id}', 'update')->name('chapters.update');
    Route::get('{params}/chapter/restore/{id}', 'restore')->name('chapters.restore');
});
