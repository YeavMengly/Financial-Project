@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.budget.allocation') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __('menus.budget.allocation') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" novalidate method="POST"
                        action="{{ route('budgetAllocation.store', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId]) }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- 1. Initial Remaining Fin Law (Stored in data attribute) -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.fin.law') }} (ដើមគ្រា)</label>
                                    <input type="text" class="form-control" id="base_fin_law" readonly
                                        data-raw-value="{{ $remainingFinLaw }}"
                                        value="{{ number_format($remainingFinLaw, 0) }}" />
                                </div>
                            </div>

                            <!-- 2. Input Amount (User enters value here) -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.budget.allocation') }}</label>
                                    <input type="number" step="any" min="0" class="form-control" name="amount"
                                        id="amount_input" required value="{{ old('amount') }}" placeholder="0"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <!-- 3. Dynamic Remaining Balance Field (Calculated automatically) -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.fin.law') }} (នៅសល់)</label>
                                    <input type="text" class="form-control" id="remaining_fin_law" readonly
                                        value="{{ number_format($remainingFinLaw, 0) }}" />
                                    <!-- Error message if budget exceeded -->
                                    <small id="over_budget_msg" class="text-danger d-none mt-1">
                                        ⚠️ ទឹកប្រាក់បែងចែកលើសពីច្បាប់ហិរញ្ញវត្ថុដែលនៅសល់!
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.expense.type') }}</label>
                                    <!-- Updated name from 'cboExpenseType' to 'expense_type_id' -->
                                    <select class="form-control" data-trigger id="cboExpenseType" name="cboExpenseType"
                                        required data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($expenseTypes as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('cboExpenseType') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name_kh }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboExpenseType')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('budgetAllocation.index', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId]) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const baseFinLawInput = document.getElementById('base_fin_law');
            const amountInput = document.getElementById('amount_input');
            const remainingInput = document.getElementById('remaining_fin_law');
            const overBudgetMsg = document.getElementById('over_budget_msg');

            // Parse initial base value from data-raw-value attribute
            const baseFinLaw = parseFloat(baseFinLawInput.getAttribute('data-raw-value')) || 0;

            function calculateRemaining() {
                // Get entered amount (default 0 if empty)
                const enteredAmount = parseFloat(amountInput.value) || 0;

                // Calculate remaining balance
                const remaining = baseFinLaw - enteredAmount;

                remainingInput.value = new Intl.NumberFormat('en-US').format(remaining);

                // Check for budget overflow
                if (remaining < 0) {
                    remainingInput.classList.add('is-invalid', 'text-danger');
                    overBudgetMsg.classList.remove('d-none');
                } else {
                    remainingInput.classList.remove('is-invalid', 'text-danger');
                    overBudgetMsg.classList.add('d-none');
                }
            }
            amountInput.addEventListener('input', calculateRemaining);

            calculateRemaining();
        });
    </script>
@endsection
