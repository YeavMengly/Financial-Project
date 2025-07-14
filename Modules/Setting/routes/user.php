<?php
use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Http\Controllers\UsersController;

Route::middleware(['PermissionCheck'])->controller(UsersController::class)->group(function() {
    Route::get("/user", 'index')->name("user.index");
    Route::get("/user/create", 'create')->name("user.create");
    Route::post("/user/store", 'store')->name("user.store");
    Route::get("/user/edit/{id}", 'edit')->name("user.edit");
    Route::post("/user/update/{id}", 'update')->name("user.update");
    Route::get("/user/destroy/{id}", 'destroy')->name("user.destroy");
    Route::get("/user/restore/{id}", 'restore')->name("user.restore");
    Route::get("/user/password/{id}", 'password')->name("user.password");
    Route::post("/user/password-change/{id}", 'passwordChange')->name("user.change");
});
