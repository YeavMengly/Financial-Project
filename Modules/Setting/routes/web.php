<?php

use Illuminate\Support\Facades\Route;

Route::prefix('setting')->middleware(['auth'])->group(function () {
    require_once __DIR__.'/role.php';
    require_once __DIR__.'/category.php';
    require_once __DIR__.'/user.php';
    require_once __DIR__.'/system.php';
    require_once __DIR__.'/keys.php';
});
