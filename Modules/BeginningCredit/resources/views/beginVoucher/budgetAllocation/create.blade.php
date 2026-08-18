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
                    {{-- <form id="pristine-valid-example" novalidate method="POST"
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
                    </form> --}}
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
                            {{-- <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.budget.allocation') }}</label>
                                    <input type="number" step="any" min="0" class="form-control" name="amount"
                                        id="amount_input" required value="{{ old('amount') }}" placeholder="0"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.budget.allocation') }}</label>
                                    <input type="number" step="any" min="0" class="form-control" name="amount"
                                        id="amount_input" required value="{{ old('amount') }}" placeholder="0"
                                        data-pristine-required-message="{{ __('messages.required') }}" />

                                    <!-- ADD THIS SO YOU CAN SEE VALIDATION FAILURES! -->
                                    @error('amount')
                                        <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                                    @enderror

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

                            <!-- 4. Checkbox for Rounds (ជុំទី១ - ទី៤) -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <!-- Label and Skip Toggle Switch -->
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label mb-0">ជ្រើសរើសជុំ (ជុំទី១-ទី៤)</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="skipRounds"
                                                style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted" for="skipRounds"
                                                style="cursor: pointer;">
                                                រំលង / មិនជ្រើសរើស
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Radio Buttons Container -->
                                    <div id="roundsContainer">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-checkbox" type="checkbox" name="rounds[]"
                                                id="round1" value="1"
                                                {{ is_array(old('rounds')) && in_array(1, old('rounds')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round1"
                                                style="cursor: pointer;">ជុំទី១</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-checkbox" type="checkbox" name="rounds[]"
                                                id="round2" value="2"
                                                {{ is_array(old('rounds')) && in_array(2, old('rounds')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round2"
                                                style="cursor: pointer;">ជុំទី២</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-checkbox" type="checkbox" name="rounds[]"
                                                id="round3" value="3"
                                                {{ is_array(old('rounds')) && in_array(3, old('rounds')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round3"
                                                style="cursor: pointer;">ជុំទី៣</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-checkbox" type="checkbox" name="rounds[]"
                                                id="round4" value="4"
                                                {{ is_array(old('rounds')) && in_array(4, old('rounds')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round4"
                                                style="cursor: pointer;">ជុំទី៤</label>
                                        </div>
                                    </div>

                                    @error('rounds')
                                        <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- 5. Expense Type -->
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
            const form = document.getElementById('pristine-valid-example');
            const pristine = new Pristine(form);

            const baseFinLawInput = document.getElementById('base_fin_law');
            const amountInput = document.getElementById('amount_input');
            const remainingInput = document.getElementById('remaining_fin_law');
            const overBudgetMsg = document.getElementById('over_budget_msg');

            const skipRounds = document.getElementById('skipRounds');
            const roundCheckboxes = document.querySelectorAll('.round-checkbox');
            const submitButtons = form.querySelectorAll('button[type="submit"]');

            const baseFinLaw = parseFloat(baseFinLawInput.getAttribute('data-raw-value')) || 0;

            // ==========================================
            // 1. Budget Math & Validation Logic
            // ==========================================
            function checkBudgetIsValid() {
                const enteredAmount = parseFloat(amountInput.value) || 0;

                const checkedCheckboxes = document.querySelectorAll('.round-checkbox:checked').length;
                const isSkipped = skipRounds ? skipRounds.checked : false;

                const multiplier = (isSkipped || checkedCheckboxes === 0) ? 1 : checkedCheckboxes;
                const totalDeduction = enteredAmount * multiplier;
                const remaining = baseFinLaw - totalDeduction;

                remainingInput.value = new Intl.NumberFormat('en-US').format(remaining);

                if (remaining < 0) {
                    remainingInput.classList.add('is-invalid', 'text-danger');
                    overBudgetMsg.classList.remove('d-none');
                    return false;
                } else {
                    remainingInput.classList.remove('is-invalid', 'text-danger');
                    overBudgetMsg.classList.add('d-none');
                    return true;
                }
            }

            amountInput.addEventListener('input', checkBudgetIsValid);
            roundCheckboxes.forEach(cb => {
                cb.addEventListener('change', checkBudgetIsValid);
            });

            // ==========================================
            // 2. Skip Rounds Toggle Logic
            // ==========================================
            if (skipRounds) {
                skipRounds.addEventListener('change', function() {
                    roundCheckboxes.forEach(checkbox => {
                        if (this.checked) {
                            checkbox.checked = false;
                            // Instead of disabled = true (which breaks form posts/validation), 
                            // we uncheck them. We let the backend handle empty arrays safely.
                            checkbox.disabled = false;
                            checkbox.style.opacity = '0.5';
                        } else {
                            checkbox.style.opacity = '1';
                        }
                    });
                    checkBudgetIsValid();
                });
            }

            checkBudgetIsValid();

            // ==========================================
            // 3. Form Submission & Button Locking
            // ==========================================
            let clickedButton = null;

            submitButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    clickedButton = this;
                });
            });

            form.addEventListener('submit', function(e) {
                const isPristineValid = pristine.validate();
                const isBudgetValid = checkBudgetIsValid();

                // If either validation fails, stop the submission
                if (!isPristineValid || !isBudgetValid) {
                    e.preventDefault();
                    if (!isBudgetValid) {
                        amountInput.focus();
                    }
                } else {
                    // VALIDATION PASSED: Lock buttons and let the native form submit proceed normally
                    submitButtons.forEach(function(button) {
                        button.disabled = true;
                        if (button === clickedButton) {
                            button.innerHTML += '...';
                        }
                    });

                    if (clickedButton && clickedButton.name) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = clickedButton.name;
                        hiddenInput.value = clickedButton.value;
                        form.appendChild(hiddenInput);
                    }
                    // Do NOT call e.preventDefault() here, allowing the form to submit naturally!
                }
            });
        });
    </script>
@endsection
