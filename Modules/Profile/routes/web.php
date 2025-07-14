<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\App\Http\Controllers\ProfileController;

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

Route::prefix('profile')->middleware(['auth'])->group(function () {
    Route::controller(ProfileController::class)->group(function() {
        Route::get("/", 'index')->name("profile.index");
        Route::get("/password", 'password')->name("profile.password");
        Route::post("/password-change", 'passwordChange')->name("profile.change");
    });
});
