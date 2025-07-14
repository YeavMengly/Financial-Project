<?php

use App\Models\ChapterNumber;
use Illuminate\Support\Facades\Route;
use Modules\ChapterNumber\App\Http\Controllers\ChapterNumberController;

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
//     Route::resource('chapternumber', ChapterNumberController::class)->names('chapternumber');
// });

// Route::prefix('chapternumber')->middleware(['auth'])->group(function () {
//     Route::middleware('PermissionCheck')->controller(ChapterNumberController::class)->group(function () {
//         Route::get('/', 'index')->name('chapternumber.index');
//         Route::get('/create', 'create')->name('chapternumber.create');
//         Route::get('/edit/{params}', 'edit')->name('chapternumber.edit');
//         Route::get('/destroy/{params}', 'destroy')->name('chapternumber.destroy');
//     });
//     Route::controller(ChapterNumberController::class)->group(function () {
//         Route::post('/store', 'store')->name('chapternumber.store');
//         Route::post('/update/{params}', 'update')->name('chapternumber.update');
//         Route::get('/restore/{params}', 'restore')->name('chapternumber.restore');
//     });
// });
