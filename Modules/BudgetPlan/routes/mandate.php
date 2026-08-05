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

        Route::get('procurement/', 'getIndexProcurement')->name('initialProcurement.index');
        Route::get('procurement/{params}', 'indexProcurement')->name('budgetProcurement.index');
        Route::get('procurement/{params}/create', 'createProcurement')->name('budgetProcurement.create');
        Route::get('procurement/{params}/edit/{id}', 'editProcurement')->name('budgetProcurement.edit');
        Route::get('procurement/{params}/destroy/{id}', 'destroyProcurement')->name('budgetProcurement.destroy');

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
        //training
        Route::get('training/expense-record', 'getIndexExpenseRecordTraining')->name('initialTraining.expenseRecord.index');
        Route::get('training/expense-record/{params}', 'getIndexExpenseRecordBookTraining')->name('budgetTraining.expenseRecord.index');
        Route::get('training/expense-record/{params}/create', 'createExpenseRecordTraining')->name('budgetTraining.expenseRecord.create');
        Route::get('training/expense-record/{params}/edit/{id}', 'editExpenseRecordTraining')->name('budgetTraining.expenseRecord.edit');
        Route::get('training/expense-record/{params}/destroy/{id}', 'destroyExpenseRecordTraining')->name('budgetTraining.expenseRecord.destroy');
    });

Route::controller(BudgetMandateController::class)->group(function () {
    // Expenditure Gurantee 
    Route::post('mandate/{params}/store', 'store')->name('budgetMandate.store');
    Route::post('mandate/{params}/update/{id}', 'update')->name('budgetMandate.update');
    Route::get('mandate/{params}/restore/{id}', 'restore')->name('budgetMandate.restore');

    // Pexpenditure Procurement 
    Route::post('procurement/{params}/store', 'storeProcurement')->name('budgetProcurement.store');
    Route::post('procurement/{params}/update/{id}', 'updateProcurement')->name('budgetProcurement.update');
    Route::get('procurement/{params}/restore/{id}', 'restoreProcurement')->name('budgetProcurement.restore');

    // Report Export
    Route::get('mandate/{params}/export', 'export')->name('budgetMandate.export');
    Route::get('procurement/{params}/exportProcurement', 'exportProcurement')->name('budgetProcurement.exportProcurement');
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

    // Procurement
    // These routes are for ajax request
    Route::get('procurement/get-by-program/program-subs', 'getByProgramId')->name('budgetProcurement.by.program_sub');
    Route::get('procurement/get-by-program/agencies', 'getByAgency')->name('budgetProcurement.by.agency');
    Route::get('procurement/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetProcurement.by.cluster');
    // These routes are for edit page ajax request
    Route::get('procurement/edit-by-program/program-subs', 'editByProgramId')->name('budgetProcurement.edit.program_sub');
    Route::get('procurement/edit-by-program/agencies', 'editByAgency')->name('budgetProcurement.edit.agency');
    Route::get('procurement/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetProcurement.edit.cluster');

    Route::get('procurement/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetProcurement.getEarlyBalance');
    Route::get('procurement/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetProcurement.editEarlyBalance');

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

    Route::get('direct/payment/expense-record/get-by-program/program-subs', 'getByProgramId')->name('budgetDirectPayment.expenseRecord.by.program_sub');
    Route::get('direct/payment/expense-record/get-by-program/agencies', 'getByAgency')->name('budgetDirectPayment.expenseRecord.by.agency');
    Route::get('direct/payment/expense-record/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetDirectPayment.expenseRecord.by.cluster');

    Route::get('direct/payment/expense-record/edit-by-program/program-subs', 'editByProgramId')->name('budgetDirectPayment.expenseRecord.edit.program_sub');
    Route::get('direct/payment/expense-record/edit-by-program/agencies', 'editByAgency')->name('budgetDirectPayment.expenseRecord.edit.agency');
    Route::get('direct/payment/expense-record/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetDirectPayment.expenseRecord.edit.cluster');

    Route::get('direct/payment/expense-record/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetDirectPayment.expenseRecord.getEarlyBalance');
    Route::get('direct/payment/expense-record/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetDirectPayment.expenseRecord.editEarlyBalance');

    Route::get('direct/payment/expense-record/{params}/exportExpenseRecordBook', 'exportExpenseRecordBook')->name('budgetDirectPayment.expenseRecord.exportExpenseRecordBook');
    /// Training
    Route::post('training/expense-record/{params}/store', 'storeExpenseRecordTraing')->name('budgetTraining.expenseRecord.store');
    Route::post('training/expense-record/{params}/update/{id}', 'updateExpenseRecordTraining')->name('budgetTraining.expenseRecord.update');
    Route::get('training/expense-record/{params}/restore/{id}', 'restoreExpenseRecordTraining')->name('budgetTraining.expenseRecord.restore');

    Route::get('training/expense-record/get-by-program/program-subs', 'getByProgramId')->name('budgetTraining.expenseRecord.by.program_sub');
    Route::get('training/expense-record/get-by-program/agencies', 'getByAgency')->name('budgetTraining.expenseRecord.by.agency');
    Route::get('training/expense-record/get-by-program-sub/clusters', 'getByProgramSubId')->name('budgetTraining.expenseRecord.by.cluster');

    Route::get('training/expense-record/edit-by-program/program-subs', 'editByProgramId')->name('budgetTraining.expenseRecord.edit.program_sub');
    Route::get('training/expense-record/edit-by-program/agencies', 'editByAgency')->name('budgetTraining.expenseRecord.edit.agency');
    Route::get('training/expense-record/edit-by-program-sub/clusters', 'editByProgramSubId')->name('budgetTraining.expenseRecord.edit.cluster');

    Route::get('training/expense-record/{params}/get-early-balance', 'getEarlyBalance')
        ->name('budgetTraining.expenseRecord.getEarlyBalance');
    Route::get('training/expense-record/{params}/edit-early-balance', 'editEarlyBalance')
        ->name('budgetTraining.expenseRecord.editEarlyBalance');

    Route::get('training/expense-record/{params}/exportExpenseRecordBook', 'exportExpenseRecordBookTraining')->name('budgetTraining.expenseRecord.exportExpenseRecordBook');
});
