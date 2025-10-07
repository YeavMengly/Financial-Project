<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\BeginMandateController;

Route::middleware('PermissionCheck')->controller(BeginMandateController::class)->group(function () {
    Route::get('begin/mandate/', 'getIndex')->name('initialBudgetMandate.index');
    Route::get('begin/mandate/{params}/', 'index')->name('beginMandate.index');
    Route::get('begin/mandate/{params}/create', 'create')->name('beginMandate.create');
    Route::get('begin/mandate/{params}/edit/{id}', 'edit')->name('beginMandate.edit');
    Route::get('begin/mandate/{params}/destroy/{id}', 'destroy')->name('beginMandate.destroy');
});

Route::controller(BeginMandateController::class)->group(function () {
    Route::post('begin/mandate{params}/store', 'store')->name('beginMandate.store');
    Route::post('begin/mandate/{params}/update/{id}', 'update')->name('beginMandate.update');
    Route::get('/get-by-programid', 'getByProgramId')->name('beginMandate.by.program_id');
});
