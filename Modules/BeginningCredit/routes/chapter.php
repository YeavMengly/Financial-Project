<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ChapterController;


Route::middleware('PermissionCheck')->controller(ChapterController::class)->group(function () {
    Route::get('/chapter', 'index')->name('chapter.index');
    Route::get('/chapter/create', 'create')->name('chapter.create');
    Route::get('/chapter/edit/{params}', 'edit')->name('chapter.edit');
    Route::get('/chapter/destroy/{params}', 'destroy')->name('chapter.destroy');
});
Route::controller(ChapterController::class)->group(function () {
    Route::post('/chapter/store', 'store')->name('chapter.store');
    Route::post('/chapter/update/{params}', 'update')->name('chapter.update');
    // Route::get('/chapter/restore/{params}', 'restore')->name('chapter.restore');
});
