<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ChapterController;

Route::middleware('PermissionCheck')->controller(ChapterController::class)->group(function () {
    Route::get('chapter/', 'getIndex')->name('initialChapter.index'); // GET: initial-budget/
    Route::get('chapter/{params}', 'index')->name('chapters.index');
    Route::get('chapter/{params}/create', 'create')->name('chapters.create');
    Route::get('chapter/{params}/edit/{id}', 'edit')->name('chapters.edit');
    Route::get('chapter/{params}/destroy/{id}', 'destroy')->name('chapters.destroy');
});
Route::controller(ChapterController::class)->group(function () {
    Route::post('chapter/{params}/store', 'store')->name('chapters.store');
    Route::post('chapter/{params}/update/{id}', 'update')->name('chapters.update');
    Route::get('chapter/{params}/restore/{id}', 'restore')->name('chapters.restore');
});
