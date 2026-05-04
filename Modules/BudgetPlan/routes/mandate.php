<?php

use Illuminate\Support\Facades\Route;
use Modules\BudgetPlan\App\Http\Controllers\BudgetMandateController;

Route::middleware('PermissionCheck')
    ->controller(BudgetMandateController::class)
    ->group(function () {
        Route::get('mandate/', 'getIndex')->name('initialMandate.index');
        Route::get('mandate/{params}', 'index')->name('budgetMandate.index');
        Route::get('mandate/{params}/create', 'create')->name('budgetMandate.create');
        Route::get('mandate/{params}/edit/{id}', 'edit')->name('budgetMandate.edit');
        Route::get('mandate/{params}/destroy/{id}', 'destroy')->name('budgetMandate.destroy');

        Route::get('advance/payment/', 'getIndexAdvancePay')->name('initialAdvancePayment.index');
        Route::get('advance/payment/{params}', 'getIndexAdvancePayment')->name('budgetAdvancePayment.index');
        Route::get('advance/payment/{params}/create', 'createAdvancePayment')->name('budgetAdvancePayment.create');
        Route::get('advance/payment/{params}/edit/{id}', 'editAdvancePayment')->name('budgetAdvancePayment.edit');
        Route::get('advance/payment/{params}/destroy/{id}', 'destroyAdvancePayment')->name('budgetAdvancePayment.destroy');

        Route::get('direct/payment/expense-record', 'getIndexExpenseRecord')->name('initialDirectPayment.expenseRecord.index');
        Route::get('direct/payment/expense-record/{params}', 'getIndexExpenseRecordBook')->name('budgetDirectPayment.expenseRecord.index');
        Route::get('direct/payment/expense-record/{params}/create', 'createExpenseRecord')->name('budgetDirectPayment.expenseRecord.create');
        Route::get('direct/payment/expense-record/{params}/edit/{id}', 'editExpenseRecord')->name('budgetDirectPayment.expenseRecord.edit');
        Route::get('direct/payment/expense-record/{params}/destroy/{id}', 'destroyExpenseRecord')->name('budgetDirectPayment.expenseRecord.destroy');
    });

Route::controller(BudgetMandateController::class)->group(function () {
    Route::post('mandate/{params}/store', 'store')->name('budgetMandate.store');
    Route::post('mandate/{params}/update/{id}', 'update')->name('budgetMandate.update');
    Route::get('mandate/{params}/restore/{id}', 'restore')->name('budgetMandate.restore');

    Route::get('mandate/{params}/export', 'export')->name('budgetMandate.export');
    Route::get('mandate/{params}/exportAdvancePayment', 'exportAdvancePayment')->name('budgetAdvancePayment.exportAdvancePayment');

    Route::post('advance/payment/{params}/store', 'storeAdvancePayment')->name('budgetAdvancePayment.store');
    Route::post('advance/payment/{params}/update/{id}', 'updateAdvancePayment')->name('budgetAdvancePayment.update');
    Route::get('advance/payment/{params}/restore/{id}', 'restoreAdvancePayment')->name('budgetAdvancePayment.restore');

    // These routes are for ajax request
    Route::get('mandate/get-by-program/program-subs', 'getByProgramId')->name('budgetMandate.by.program_sub');
    Route::get('mandate/get-by-program/agencies', 'getByAgency')->name('budgetMandate.by.agency');
    Route::get('mandate/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetMandate.by.cluster');
    // These routes are for edit page ajax request
    Route::get('mandate/edit-by-program/program-subs', 'editByProgramId')->name('budgetMandate.edit.program_sub');
    Route::get('mandate/edit-by-program/agencies', 'editByAgency')->name('budgetMandate.edit.agency');
    Route::get('mandate/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetMandate.edit.cluster');

    Route::get('mandate/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetMandate.getEarlyBalance');
    Route::get('mandate/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetMandate.editEarlyBalance');

    //Advance Payment
    // These routes are for ajax request
    Route::get('advance/payment/get-by-program/program-subs', 'getByProgramId')->name('budgetAdvancePayment.by.program_sub');
    Route::get('advance/payment/get-by-program/agencies', 'getByAgency')->name('budgetAdvancePayment.by.agency');
    Route::get('advance/payment/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetAdvancePayment.by.cluster');

    // These routes are for edit page ajax request
    Route::get('advance/payment/edit-by-program/program-subs', 'editByProgramId')->name('budgetAdvancePayment.edit.program_sub');
    Route::get('advance/payment/edit-by-program/agencies', 'editByAgency')->name('budgetAdvancePayment.edit.agency');
    Route::get('advance/payment/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetAdvancePayment.edit.cluster');

    Route::get('advance/payment/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetAdvancePayment.getEarlyBalance');
    Route::get('advance/payment/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetAdvancePayment.editEarlyBalance');

    Route::post('direct/payment/expense-record/{params}/store', 'storeExpenseRecord')->name('budgetDirectPayment.expenseRecord.store');
    Route::post('direct/payment/expense-record/{params}/update/{id}', 'updateExpenseRecord')->name('budgetDirectPayment.expenseRecord.update');
    Route::get('direct/payment/expense-record/{params}/restore/{id}', 'restoreExpenseRecord')->name('budgetDirectPayment.expenseRecord.restore');
});
