<?php

use Illuminate\Support\Facades\Route;
use Modules\Material\App\Http\Controllers\ProjectsController;

Route::middleware('PermissionCheck')->controller(ProjectsController::class)->group(function () {
    Route::get('project/', 'getIndex')->name('initialProject.index');
    Route::get('project/{params}', 'index')->name('project.index');
    Route::get('project/{params}/create', 'create')->name('project.create');
    Route::get('project/{params}/edit/{id}', 'edit')->name('project.edit');
    Route::get('project/{params}/destroy/{id}', 'destroy')->name('project.destroy');
});
Route::controller(ProjectsController::class)->group(function () {
    Route::post('project/{params}/store', 'store')->name('project.store');
    Route::post('project/{params}/update/{id}', 'update')->name('project.update');
    Route::get('project/{params}/restore/{id}', 'restore')->name('project.restore');
});
