<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ProgramController;

Route::middleware(['PermissionCheck'])->controller(ProgramController::class)->group(function () {
    Route::get('program/program/', 'getIndex')->name('initialProgram.index'); // GET: initial-budget/

    // Program
    Route::get('/program/{params}', 'index')->name('program.index');
    Route::get('/program/{params}/create', 'create')->name('program.create');
    Route::get('/program/{params}/edit/{id}', 'edit')->name('program.edit');
    Route::get('/program/{params}/destroy/{id}', 'destroy')->name('program.destroy');

    // Program Sub
    Route::get('/program/{params}/sub/{pId}', 'subIndex')->name('program.sub.index');
    Route::get('/program/{params}/sub/{pId}/create', 'subCreate')->name('program.sub.create');
    Route::get('/program/{params}/sub/{pId}/edit/{id}', 'subEdit')->name('program.sub.edit');
    Route::get('/program/{params}/sub/{pId}/destroy/{id}', 'subDestroy')->name('program.sub.destroy');

    // Cluster
    Route::get('/program/{params}/sub/{pId}/cluster/{pSubId}', 'clusterIndex')->name('cluster.index');
    Route::get('/program/{params}/sub/{pId}/cluster/{pSubId}/create', 'clusterCreate')->name('cluster.create');
    Route::get('/program/{params}/sub/{pId}/cluster/{pSubId}/edit/{id}', 'clusterEdit')->name('cluster.edit');
    Route::get('/program/{params}/sub/{pId}/cluster/{pSubId}/destroy/{id}', 'clusterDestroy')->name('cluster.destroy');
});
Route::controller(ProgramController::class)->group(function () {

    // Program
    Route::post('/program/{params}/store', 'store')->name('program.store');
    Route::post('/program/{params}/update/{id}', 'update')->name('program.update');
    Route::get('/program/{params}/restore/{id}', 'restore')->name('program.restore');

    // Program Sub
    Route::post('/program/{params}/sub/{pId}/store/', 'subStore')->name('program.sub.store');
    Route::post('/program/{params}/sub/{pId}/update/{id}', 'subUpdate')->name('program.sub.update');
    Route::get('/program/{params}/sub/{pId}/restore/{id}', 'subRestore')->name('program.sub.restore');

    // Cluster
    Route::post('/program/{params}/sub/{pId}/cluster/{pSubId}/store/', 'clusterStore')->name('cluster.store');
    Route::post('/program/{params}/sub/{pId}/cluster/{pSubId}/update/{id}', 'clusterUpdate')->name('cluster.update');
    Route::get('/program/{params}/sub/{pId}/cluster/{pSubId}/restore/{id}', 'clusterRestore')->name('cluster.restore');
});
