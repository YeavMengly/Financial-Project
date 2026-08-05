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

        Route::get('direct/payment/payment-deadline/', 'getPaymentDeadline')->name('initialDirectPayment.paymentDeadline.index');
        Route::get('direct/payment/payment-deadline/{params}', 'indexPaymentDeadline')->name('budgetDirectPayment.paymentDeadline.index');
        Route::get('direct/payment/payment-deadline/{params}/create', 'createPaymentDeadline')->name('budgetDirectPayment.paymentDeadline.create');
        Route::get('direct/payment/payment-deadline/{params}/edit/{id}', 'editPaymentDeadline')->name('budgetDirectPayment.paymentDeadline.edit');
        Route::get('direct/payment/payment-deadline/{params}/destroy/{id}', 'destroyPaymentDeadline')->name('budgetDirectPayment.paymentDeadline.destroy');

        Route::get('direct/payment/payment-deadline/{params}/get-early-balance', 'getEarlyBalance')
            ->name('budgetDirectPayment.paymentDeadline.getEarlyBalance');

        Route::get('direct/payment/payment-deadline/{params}/edit-early-balance', 'editEarlyBalance')
            ->name('budgetDirectPayment.paymentDeadline.editEarlyBalance');
        //training
        Route::get('training/payment-deadline/', 'getPaymentDeadlineTraining')->name('initialTraining.paymentDeadline.index');
        Route::get('training/payment-deadline/{params}', 'indexPaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.index');
        Route::get('training/payment-deadline/{params}/create', 'createPaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.create');
        Route::get('training/payment-deadline/{params}/edit/{id}', 'editPaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.edit');
        Route::get('training/payment-deadline/{params}/destroy/{id}', 'destroyPaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.destroy');

        Route::get('training/payment-deadline/{params}/get-early-balance', 'getEarlyBalance')
            ->name('budgetTraining.paymentDeadline.getEarlyBalance');
        Route::get('training/payment-deadline/{params}/edit-early-balance', 'editEarlyBalance')
            ->name('budgetTraining.paymentDeadline.editEarlyBalance');
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

    Route::post('direct/payment/payment-deadline/{params}/store', 'storePaymentDeadline')->name('budgetDirectPayment.paymentDeadline.store');
    Route::post('direct/payment/payment-deadline/{params}/update/{id}', 'updatePaymentDeadline')->name('budgetDirectPayment.paymentDeadline.update');
    Route::get('direct/payment/payment-deadline/{params}/restore/{id}', 'restorePaymentDeadline')->name('budgetDirectPayment.paymentDeadline.restore');
    Route::get('direct/payment/payment-deadline/{params}/exportPaymentDeadline', 'exportPaymentDeadline')->name('budgetDirectPayment.paymentDeadline.export');


    Route::get('direct/payment/payment-deadline/get-by-program/program-subs', 'getByProgramId')->name('budgetDirectPayment.paymentDeadline.by.program_sub');
    Route::get('direct/payment/payment-deadline/get-by-program/agencies', 'getByAgency')->name('budgetDirectPayment.paymentDeadline.by.agency');
    Route::get('direct/payment/payment-deadline/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetDirectPayment.paymentDeadline.by.cluster');
    Route::get('direct/payment/payment-deadline/get-by-expense/legal-id', 'getByExpenseIdPayment')->name('budgetDirectPayment.paymentDeadline.get.expense_type_id');

    // These routes are for edit page ajax request
    Route::get('direct/payment/payment-deadline/edit-by-program/program-subs', 'editByProgramId')->name('budgetDirectPayment.paymentDeadline.edit.program_sub');
    Route::get('direct/payment/payment-deadline/edit-by-program/agencies', 'editByAgency')->name('budgetDirectPayment.paymentDeadline.edit.agency');
    Route::get('direct/payment/payment-deadline/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetDirectPayment.paymentDeadline.edit.cluster');
    Route::get('direct/payment/payment-deadline/edit-by-expense/legal-id', 'editByExpenseIdPayment')->name('budgetDirectPayment.paymentDeadline.edit.expense_type_id');
    ///training
    Route::get('training/payment-deadline/get-by-program/program-subs', 'getByProgramId')->name('budgetTraining.paymentDeadline.by.program_sub');
    Route::get('training/payment-deadline/get-by-program/agencies', 'getByAgency')->name('budgetTraining.paymentDeadline.by.agency');
    Route::get('training/payment-deadline/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetTraining.paymentDeadline.by.cluster');
    Route::get('training/payment-deadline/get-by-expense/legal-id', 'getByExpenseIdPayment')->name('budgetTraining.paymentDeadline.get.expense_type_id');

    Route::post('training/payment-deadline/{params}/store', 'storePaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.store');
    Route::post('training/payment-deadline/{params}/update/{id}', 'updatePaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.update');
    Route::get('training/payment-deadline/{params}/restore/{id}', 'restorePaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.restore');
    Route::get('training/payment-deadline/{params}/exportPaymentDeadline', 'exportPaymentDeadlineTraining')->name('budgetTraining.paymentDeadline.export');

    Route::get('training/payment-deadline/edit-by-program/program-subs', 'editByProgramId')->name('budgetTraining.paymentDeadline.edit.program_sub');
    Route::get('training/payment-deadline/edit-by-program/agencies', 'editByAgency')->name('budgetTraining.paymentDeadline.edit.agency');
    Route::get('training/payment-deadline/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetTraining.paymentDeadline.edit.cluster');
    Route::get('training/payment-deadline/edit-by-expense/legal-id', 'editByExpenseIdPayment')->name('budgetTraining.paymentDeadline.edit.expense_type_id');
});
