<?php

use Illuminate\Support\Facades\Route;
// routes
use Modules\BudgetPlan\App\Http\Controllers\BudgetVoucherController;

Route::middleware('PermissionCheck')
    ->controller(BudgetVoucherController::class)
    ->group(function () {
        Route::get('voucher/', 'getIndex')->name('initialVoucher.index');
        Route::get('voucher/{params}', 'index')->name('budgetVoucher.index');
        Route::get('voucher/{params}/create', 'create')->name('budgetVoucher.create');
        Route::get('voucher/{params}/edit/{id}', 'edit')->name('budgetVoucher.edit');
        Route::get('voucher/{params}/destroy/{id}', 'destroy')->name('budgetVoucher.destroy');

        Route::get('voucher/{params}/early-balance', 'getEarlyBalance')
            ->name('budgetVoucher.getEarlyBalance');
    });

Route::controller(BudgetVoucherController::class)->group(function () {
    Route::post('voucher/{params}/store', 'store')->name('budgetVoucher.store');
    Route::post('voucher/{params}/update/{id}', 'update')->name('budgetVoucher.update');
    Route::get('voucher/{params}/export', 'export')->name('budgetVoucher.export');
});

// Route::get('/begin-voucher/by-program/program-subs', [BudgetVoucherController::class, 'getProgram'])
//     ->name('budgetVoucher.by.program_sub');

// Route::get('/begin-voucher/by-program/agencies', [BudgetVoucherController::class, 'getAgency'])
//     ->name('budgetVoucher.by.agency');

// Route::get('/begin-voucher/by-program-sub/clusters', [BudgetVoucherController::class, 'getProgramSub'])
//     ->name('budgetVoucher.by.cluster');

// Route::get('/begin-voucher/data', [BudgetVoucherController::class, 'getBeginVoucher'])
//     ->name('beginVoucher.getData');

// Route::post(
//     '/budget-voucher/early-balance/{params}',
//     [BudgetVoucherController::class, 'getEarlyBalance']
// )->name('budgetVoucher.getEarlyBalance');
