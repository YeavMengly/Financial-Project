<?php

use Illuminate\Support\Facades\Route;
use Modules\BeginningCredit\App\Http\Controllers\DepartController;

Route::middleware('PermissionCheck')->prefix('depart')->name('depart.')->controller(DepartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::get('/edit/{params}', 'edit')->name('edit');
    Route::get('/destroy/{params}', 'destroy')->name('destroy');
});
Route::prefix('depart')->name('depart.')->controller(DepartController::class)->group(function () {
    Route::post('/store', 'store')->name('store');
    Route::post('/update/{params}', 'update')->name('update');
});
