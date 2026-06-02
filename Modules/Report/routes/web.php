<?php

use Illuminate\Support\Facades\Route;

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
Route::prefix('reports')->middleware(['auth'])->group(function () {
    require_once __DIR__ . '/implement_agency.php';
    require_once __DIR__ . '/implement_program.php';
    require_once __DIR__ . '/implement_importants.php';
    require_once __DIR__ . '/assets_vehicles.php';
});