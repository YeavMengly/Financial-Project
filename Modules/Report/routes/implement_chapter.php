<?php
use Illuminate\Support\Facades\Route;
use Modules\Report\App\Http\Controllers\CostImplementChapterController;

Route::middleware('PermissionCheck')->controller(CostImplementChapterController::class)->group(function () {
    Route::get('/cost_implement/chapter/voucher', 'index')->name('cost.implement.chapter.index');
    Route::get('/cost_implement/chapter/mandate', 'indexMandate')->name('cost.implement.chapterMandate.index');
});