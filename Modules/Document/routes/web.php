<?php

use Illuminate\Support\Facades\Route;
use Modules\Document\App\Http\Controllers\DocumentController;

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

Route::prefix('document')->middleware(['auth'])->group(function () {
    Route::middleware('PermissionCheck')->controller(DocumentController::class)->group(function () {
        Route::get('/', 'index')->name('document.index');
        Route::get('/create', 'create')->name('document.create');
        Route::get('/edit/{params}', 'edit')->name('document.edit');
        Route::get('/destroy/{params}', 'destroy')->name('document.destroy');
    });
    Route::controller(DocumentController::class)->group(function () {
        Route::post('/store', 'store')->name('document.store');
        Route::post('/update/{params}', 'update')->name('document.update');
        Route::get('/restore/{params}', 'restore')->name('document.restore');
        Route::get('/get-by-cateoryid', 'getByCategoryId')->name('document.by.category_id');
    });
});
