<?php
use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Http\Controllers\RoleController;

Route::middleware(['PermissionCheck'])->controller(RoleController::class)->group(function() {
    Route::get("/role", 'index')->name("role.index");
    Route::get("/role/create", 'create')->name("role.create");
    Route::post("/role/store", 'store')->name("role.store");
    Route::get("/role/edit/{id}", 'edit')->name("role.edit");
    Route::post("/role/update/{id}", 'update')->name("role.update");
    Route::get("/role/destroy/{id}", 'destroy')->name("role.destroy");
    Route::get("/role/restore/{id}", 'restore')->name("role.restore");
});
