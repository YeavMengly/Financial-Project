<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\ProgramController;

// Route::middleware('PermissionCheck')->controller(ProgramController::class)->group(function () {
//     Route::get('program/{params}', 'index')->name('program.index');
//     Route::get('program/{params}/create', 'create')->name('program.create');
//     Route::get('program/{params}/edit', 'edit')->name('program.edit');
//     Route::get('program/destroy/{params}', 'destroy')->name('program.destroy');
// });
// Route::controller(ProgramController::class)->group(function () {
//     Route::post('program/{params}/store', 'store')->name('program.store');
//     Route::post('program/update/{params}', 'update')->name('program.update');
// });

Route::middleware(['PermissionCheck'])->controller(ProgramController::class)->group(function () {
     Route::get('program/program/', 'getIndex')->name('initialProgram.index'); // GET: initial-budget/
    Route::get('/program/{params}', 'index')->name('program.index');
    Route::get('/program/{params}/create', 'create')->name('program.create');
    Route::get('/program/{params}/edit/{id}', 'edit')->name('program.edit');
    Route::get('/program/{params}/destroy/{id}', 'destroy')->name('program.destroy');
    Route::get('/program/{params}/sub/{pId}', 'subIndex')->name('program.sub.index');
    Route::get('/program/{params}/sub/create/{pId}', 'subCreate')->name('program.sub.create');
    Route::get('/program/{params}/sub/edit/{pId}/{id}', 'subEdit')->name('program.sub.edit');
    Route::get('/program/{params}/sub/destroy/{pId}/{id}', 'subDestroy')->name('program.sub.destroy');
});
Route::controller(ProgramController::class)->group(function () {
    Route::post('/program/{params}/store', 'store')->name('program.store');
    Route::post('/program/{params}/update/{id}', 'update')->name('program.update');
    Route::get('/program/{params}/restore/{id}', 'restore')->name('program.restore');
    Route::post('/program/{params}/sub/store/{pId}', 'subStore')->name('program.sub.store');
    Route::post('/program/{params}/sub/update/{pId}/{id}', 'subUpdate')->name('program.sub.update');
    Route::get('/program/{params}/sub/restore/{pId}/{id}', 'subRestore')->name('program.sub.restore');
});
