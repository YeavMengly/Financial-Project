<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ExpenseTypeController;

Route::middleware('PermissionCheck')->controller(ExpenseTypeController::class)->group(function () {
    Route::get('expense/', 'index')->name('expenseType.index');
    Route::get('expense/create', 'create')->name('expenseType.create');
    Route::get('expense/edit/{params}', 'edit')->name('expenseType.edit');
    Route::get('expense/destroy/{params}', 'destroy')->name('expenseType.destroy');
});
Route::controller(ExpenseTypeController::class)->group(function () {
    Route::post('expense/store', 'store')->name('expenseType.store');
    Route::post('expense/update/{params}', 'update')->name('expenseType.update');
    Route::get('expense/restore/{params}', 'restore')->name('expenseType.restore');
});
