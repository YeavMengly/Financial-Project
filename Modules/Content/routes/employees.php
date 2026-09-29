<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\EmployeeController;

Route::middleware('PermissionCheck')->controller(EmployeeController::class)->group(function () {
    Route::get('employees/', 'index')->name('employees.index');
    Route::get('employees/create', 'create')->name('employees.create');
    Route::get('employees/edit/{params}', 'edit')->name('employees.edit');
    Route::get('employees/destroy/{params}', 'destroy')->name('employees.destroy');
});
Route::controller(EmployeeController::class)->group(function () {
    Route::post('employees/store', 'store')->name('employees.store');
    Route::post('employees/update/{params}', 'update')->name('employees.update');
    Route::get('employees/restore/{params}', 'restore')->name('employees.restore');
});
Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
