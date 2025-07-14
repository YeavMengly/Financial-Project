<?php
use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Http\Controllers\RoleController;
use Modules\Setting\App\Http\Controllers\SystemLogController;

Route::middleware(['PermissionCheck'])->controller(SystemLogController::class)->group(function() {
    Route::get("/system", 'index')->name("system.index");
    Route::get("/system/detail", 'detail')->name("system.detail");
});
