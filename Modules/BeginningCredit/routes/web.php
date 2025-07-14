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
//     Route::resource('beginningcredit', BeginningCreditController::class)->names('beginningcredit');
// });


Route::prefix('beginningcredit')->middleware(['auth'])->group(function () {

    require_once __DIR__ . '/chapter.php';
    require_once __DIR__ . '/account.php';
    require_once __DIR__ . '/subAccount.php';
    require_once __DIR__ . '/depart.php';
    require_once __DIR__ . '/subDepart.php';
    require_once __DIR__ . '/agency.php';

    require_once __DIR__ . '/beginCredit.php';
    require_once __DIR__ . '/beginCreditMandate.php';

    require_once __DIR__ . '/initialBudget.php';
    require_once __DIR__ . '/initialBudgetMandate.php';
});
