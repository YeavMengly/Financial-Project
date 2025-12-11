<?php

use Illuminate\Support\Facades\Route;
use Modules\Material\App\Http\Controllers\MaterialController;

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

Route::prefix('inventory')->middleware(['auth'])->group(function () {
    require_once __DIR__ . '/material-entry.php';
    require_once __DIR__ . '/material-release.php';
});
