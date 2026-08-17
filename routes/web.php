<?php

use App\Livewire\BudgetPlan\AdvancePayment;
use App\Livewire\BudgetPlan\ExpenseRecordFile;
use App\Livewire\BudgetPlan\Payment;
use App\Livewire\BudgetPlan\PaymentDeadline;
use App\Livewire\Document\EditFileDocument;
use App\Livewire\BudgetPlan\GaranteeFile;
use App\Livewire\BudgetPlan\ProcurementFile;
use App\Livewire\BudgetPlan\ExpenseRecordTrainingFile;
use App\Livewire\BudgetPlan\PaymentDeadlineTraining;
use App\Livewire\Duel\Release;
use App\Livewire\Project\EditFileProject;
use App\Livewire\project\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    } else {
        return view('auth.login');
    }
});

Route::prefix('document')->middleware(['auth'])->group(function () {
    Route::get('/edit-doc/{params}', EditFileDocument::class)->name('document.edit.doc');
});
Route::prefix('budgetplan/mandates')->middleware(['auth'])->group(function () {
    Route::get('{params}/gurantee-file/{id}', GaranteeFile::class)->name('garantee.edit.doc');
    Route::get('{params}/advance-payment-file/{id}', AdvancePayment::class)->name('advancePayment.edit.doc');
    Route::get('{params}/expense-record-file/{id}', ExpenseRecordFile::class)->name('expenseRecord.edit.doc');
    Route::get('{params}/procurement-file/{id}', ProcurementFile::class)->name('procurement.edit.doc');
    Route::get('{params}/expense-record-training-file/{id}', ExpenseRecordTrainingFile::class)->name('expenseRecordTraining.edit.doc');
});
Route::prefix('budgetplan/voucher')->middleware(['auth'])->group(function () {
    Route::get('{params}/payment-file/{id}', Payment::class)->name('payment.edit.doc');
    Route::get('{params}/payment-deadline-file/{id}', PaymentDeadline::class)->name('paymentDeadline.edit.doc');
    Route::get('{params}/payment-deadline-training-file/{id}', PaymentDeadlineTraining::class)->name('paymentDeadlineTraining.edit.doc');
});
Route::prefix('duel')->middleware(['auth'])->group(function () {
    Route::get('{params}/duel-release-file/{id}', Release::class)->name('duelRelease.edit.doc');
     
});
Route::prefix('project')->middleware(['auth'])->group(function () {
    Route::get('{params}/project-file/{id}', EditFileProject::class)->name('project.edit.doc');
     
});

