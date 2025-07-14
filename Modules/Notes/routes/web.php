<?php

use Illuminate\Support\Facades\Route;
use Modules\Notes\App\Http\Controllers\NotesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('notes')->middleware(['auth'])->group(function () {
    Route::middleware('PermissionCheck')->controller(NotesController::class)->group(function () {
        Route::get('/', 'index')->name('notes.index');
        Route::get('/create', 'create')->name('notes.create');
        Route::get('/edit/{params}', 'edit')->name('notes.edit');
        Route::get('/destroy/{params}', 'destroy')->name('notes.destroy');
    });
    Route::controller(NotesController::class)->group(function () {
        Route::post('/store', 'store')->name('notes.store');
        Route::post('/update/{params}', 'update')->name('notes.update');
        Route::get('/restore/{params}', 'restore')->name('notes.restore');
    });
});
