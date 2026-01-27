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

Route::prefix('ministries')->middleware(['auth'])->group(function () {

    // require_once __DIR__ . '/Ministry.php';

    // require_once __DIR__ . '/chapter.php';
    // require_once __DIR__ . '/account.php';
    // require_once __DIR__ . '/accountSub.php';

    // require_once __DIR__ . '/program.php';

    // require_once __DIR__ . '/agency.php';

    require_once __DIR__ . '/beginVoucher.php';
    require_once __DIR__ . '/beginMandate.php';
});
