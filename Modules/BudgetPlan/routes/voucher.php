<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\BudgetVoucherController;

Route::middleware('PermissionCheck')
    ->controller(BudgetVoucherController::class)
    ->group(function () {
        Route::get('voucher/', 'getIndex')->name('initialVoucher.index');
        Route::get('voucher/{params}', 'index')->name('budgetVoucher.index');
        Route::get('voucher/{params}/create', 'create')->name('budgetVoucher.create');
        Route::get('voucher/{params}/edit/{id}', 'edit')->name('budgetVoucher.edit');
        Route::get('voucher/{params}/destroy/{id}', 'destroy')->name('budgetVoucher.destroy');

        Route::get('voucher/{params}/get-early-balance', 'getEarlyBalance')
            ->name('budgetVoucher.getEarlyBalance');

        Route::get('voucher/{params}/edit-early-balance', 'editEarlyBalance')
            ->name('budgetVoucher.editEarlyBalance');
    });

Route::controller(BudgetVoucherController::class)->group(function () {
    Route::post('voucher/{params}/store', 'store')->name('budgetVoucher.store');
    Route::post('voucher/{params}/update/{id}', 'update')->name('budgetVoucher.update');
    Route::get('voucher/{params}/restore/{id}', 'restore')->name('budgetVoucher.restore');
    Route::get('voucher/{params}/export', 'export')->name('budgetVoucher.export');

    // These routes are for ajax request
    Route::get('voucher/get-by-program/program-subs', 'getByProgramId')->name('budgetVoucher.by.program_sub');
    Route::get('voucher/get-by-program/agencies', 'getByAgency')->name('budgetVoucher.by.agency');
    Route::get('voucher/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetVoucher.by.cluster');
    Route::get('voucher/get-by-expense/legal-number', 'getByExpenseId')->name('budgetVoucher.get.expense_type_id');

    // These routes are for edit page ajax request
    Route::get('voucher/edit-by-program/program-subs', 'editByProgramId')->name('budgetVoucher.edit.program_sub');
    Route::get('voucher/edit-by-program/agencies', 'editByAgency')->name('budgetVoucher.edit.agency');
    Route::get('voucher/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetVoucher.edit.cluster');
    Route::get('voucher/edit-by-expense/legal-number', 'editByExpenseId')->name('budgetVoucher.edit.expense_type_id');
});
