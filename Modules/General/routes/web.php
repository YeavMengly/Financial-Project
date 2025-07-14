<?php

use Illuminate\Support\Facades\Route;
use Modules\General\App\Http\Controllers\GeneralController;

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

// Route::group([], function () {
//     Route::resource('general', GeneralController::class)->names('general');
// });
Route::prefix('general')->middleware(['auth'])->group(function () {
    require_once __DIR__ . '/guarantee.php';
    // require_once __DIR__ . '/mandate.php';
});
