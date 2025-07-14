<?php

use Illuminate\Support\Facades\Route;
use Modules\General\App\Http\Controllers\GuaranteeReport\GuaranteeController;

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

Route::middleware('PermissionCheck')->controller(GuaranteeController::class)->group(function () {
    Route::get('/guarantee', 'index')->name('guarantee.index');
});
