<?php

use Illuminate\Support\Facades\Route;
use Modules\Revenue\App\Http\Controllers\RevenueController;

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

Route::middleware(['web', 'PermissionCheck'])->controller(RevenueController::class)->prefix('revenue')->group(function () {
    // Initial Revenue Index (Listing page)
    Route::get('/', 'getIndex')->name('initialRevenue.index');

    // Budget Revenue Index (Requires encoded parameters)
    Route::get('/budget/{params}', 'index')->name('revenue.index');

    // Other standard resource methods if you need them:
    Route::get('/create/{params}', 'create')->name('revenue.create');
    Route::post('/', 'store')->name('revenue.store');
    Route::get('/{id}/show', 'show')->name('revenue.show');
});