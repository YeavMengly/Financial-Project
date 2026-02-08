<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ProgramController;

Route::middleware(['PermissionCheck'])->controller(ProgramController::class)->group(function () {
    Route::get('program', 'getIndex')->name('initialProgram.index'); // GET: initial-budget/

    // Program
    Route::get('/{params}/program', 'index')->name('program.index');
    Route::get('/{params}/program/create', 'create')->name('program.create');
    Route::get('/{params}/program/edit/{id}', 'edit')->name('program.edit');
    Route::get('/{params}/program/destroy/{id}', 'destroy')->name('program.destroy');

    // Program Sub
    Route::get('/{params}/program/{pId}/sub/', 'subIndex')->name('program.sub.index');
    Route::get('/{params}/program/{pId}/sub/create', 'subCreate')->name('program.sub.create');
    Route::get('/{params}/program/{pId}/sub/edit/{id}', 'subEdit')->name('program.sub.edit');
    Route::get('/{params}/program/{pId}/sub/destroy/{id}', 'subDestroy')->name('program.sub.destroy');

    // Cluster
    Route::get('/{params}/program/{pId}/sub/{pSubId}/cluster/', 'clusterIndex')->name('cluster.index');
    Route::get('/{params}/program/{pId}/sub/{pSubId}/cluster/create', 'clusterCreate')->name('cluster.create');
    Route::get('/{params}/program/{pId}/sub/{pSubId}/cluster/edit/{id}', 'clusterEdit')->name('cluster.edit');
    Route::get('/{params}/program/{pId}/sub/{pSubId}/cluster/destroy/{id}', 'clusterDestroy')->name('cluster.destroy');
});
Route::controller(ProgramController::class)->group(function () {

    // Program
    Route::post('/{params}/program/store', 'store')->name('program.store');
    Route::post('/{params}/program/update/{id}', 'update')->name('program.update');
    Route::get('/{params}/program/restore/{id}', 'restore')->name('program.restore');

    // Program Sub
    Route::post('/{params}/program/{pId}/sub/store/', 'subStore')->name('program.sub.store');
    Route::post('/{params}/program/{pId}/sub/update/{id}', 'subUpdate')->name('program.sub.update');
    Route::get('/{params}/program/{pId}/sub/restore/{id}', 'subRestore')->name('program.sub.restore');

    // Cluster
    Route::post('/{params}/program/{pId}/sub/{pSubId}/cluster/store/', 'clusterStore')->name('cluster.store');
    Route::post('/{params}/program/{pId}/sub/{pSubId}/cluster/update/{id}', 'clusterUpdate')->name('cluster.update');
    Route::get('{params}/program/{pId}/sub/{pSubId}/cluster/restore/{id}', 'clusterRestore')->name('cluster.restore');
});
