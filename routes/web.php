<?php

use App\Livewire\BudgetPlan\advancePayment;
use App\Livewire\BudgetPlan\payment;
use App\Livewire\Document\EditFileDocument;
use App\Livewire\BudgetPlan\GaranteeFile;
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
    Route::get('{params}/gurantee-file/{id}', garanteeFile::class)->name('garantee.edit.doc');
    Route::get('{params}/advance-payment-file/{id}', advancePayment::class)->name('advancePayment.edit.doc');
});
Route::prefix('budgetplan/voucher')->middleware(['auth'])->group(function () {
    Route::get('{params}/payment-file/{id}', payment::class)->name('payment.edit.doc');
});

