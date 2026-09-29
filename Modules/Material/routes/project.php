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

     // These routes are for ajax request
    Route::get('project/get-by-program/program-subs', 'getByProgramId')->name('project.by.program_sub');
    Route::get('project/get-by-program/agencies', 'getByAgency')->name('project.by.agency');
    Route::get('project/get-by-program-sub/clusters', 'getByProgramSubId')->name('project.by.cluster');
   
    // These routes are for edit page ajax request
    Route::get('project/edit-by-program/program-subs', 'editByProgramId')->name('project.edit.program_sub');
    Route::get('project/edit-by-program/agencies', 'editByAgency')->name('project.edit.agency');
    Route::get('project/edit-by-program-sub/clusters', 'editByProgramSubId')->name('project.edit.cluster');
    
});
