<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Http\Controllers\ApiKeyController;

Route::middleware(['PermissionCheck'])->controller(ApiKeyController::class)->group(function () {
    Route::get('/keys', 'index')->name('keys.index');
    Route::get('/keys/create', 'create')->name('keys.create');
    Route::get('/keys/edit/{id}', 'edit')->name('keys.edit');
    Route::get('/keys/destroy/{id}', 'destroy')->name('keys.destroy');
});
Route::controller(ApiKeyController::class)->group(function () {
    Route::post('/keys/store', 'store')->name('keys.store');
    Route::post('/keys/update/{id}', 'update')->name('keys.update');
    Route::get('/keys/restore/{id}', 'restore')->name('keys.restore');
});
