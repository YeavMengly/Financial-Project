<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Http\Controllers\CategoryController;

Route::middleware(['PermissionCheck'])->controller(CategoryController::class)->group(function () {
    Route::get('/category', 'index')->name('category.index');
    Route::get('/category/create', 'create')->name('category.create');
    Route::get('/category/edit/{id}', 'edit')->name('category.edit');
    Route::get('/category/destroy/{id}', 'destroy')->name('category.destroy');
    Route::get('/category/sub/{cateId}', 'subIndex')->name('category.sub.index');
    Route::get('/category/sub/create/{cateId}', 'subCreate')->name('category.sub.create');
    Route::get('/category/sub/edit/{cateId}/{id}', 'subEdit')->name('category.sub.edit');
    Route::get('/category/sub/destroy/{cateId}/{id}', 'subDestroy')->name('category.sub.destroy');
});
Route::controller(CategoryController::class)->group(function () {
    Route::post('/category/store', 'store')->name('category.store');
    Route::post('/category/update/{id}', 'update')->name('category.update');
    Route::get('/category/restore/{id}', 'restore')->name('category.restore');
    Route::post('/category/sub/store/{cateId}', 'subStore')->name('category.sub.store');
    Route::post('/category/sub/update/{cateId}/{id}', 'subUpdate')->name('category.sub.update');
    Route::get('/category/sub/restore/{cateId}/{id}', 'subRestore')->name('category.sub.restore');
});
