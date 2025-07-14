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

// Route::group([], function () {
//     Route::resource('budgetplan', BudgetPlanController::class)->names('budgetplan');
// });

Route::prefix('budgetplan')->middleware(['auth'])->group(function () {
    require_once __DIR__ . '/voucher.php';
    require_once __DIR__ . '/mandate.php';
    require_once __DIR__ . '/initialVoucher.php';
    require_once __DIR__ . '/initialMandate.php';
});
