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
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
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
                        action="{{ route('budgetAllocation.update', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId, 'id' => $module->id]) }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.budget.allocation') }}</label>
                                    <!-- Updated name from 'budgetAllocation' to 'amount' -->
                                    <input type="number" step="any" min="0" class="form-control" name="amount"
                                        required value="{{ old('amount', $module->amount) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('amount')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <!-- Label and Skip Toggle Switch -->
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label mb-0">ជ្រើសរើសជុំ (ជុំទី១-ទី៤)</label>
                                        <div class="form-check form-switch mb-0">
                                            <!-- Added name="skip_rounds" to remember state on validation error -->
                                            <input class="form-check-input" type="checkbox" role="switch" id="skipRounds"
                                                name="skip_rounds" value="1"
                                                {{ old('skip_rounds', $module->rounds === null ? '1' : '') ? 'checked' : '' }}
                                                style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted" for="skipRounds"
                                                style="cursor: pointer;">
                                                រំលង / មិនជ្រើសរើស
                                            </label>
                                        </div>
                                    </div>

                                    <div id="roundsContainer">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-radio" type="radio" name="rounds"
                                                id="round1" value="1"
                                                {{ old('rounds', $module->rounds) == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round1"
                                                style="cursor: pointer;">ជុំទី១</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-radio" type="radio" name="rounds"
                                                id="round2" value="2"
                                                {{ old('rounds', $module->rounds) == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round2"
                                                style="cursor: pointer;">ជុំទី២</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-radio" type="radio" name="rounds"
                                                id="round3" value="3"
                                                {{ old('rounds', $module->rounds) == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round3"
                                                style="cursor: pointer;">ជុំទី៣</label>
                                        </div>

                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input round-radio" type="radio" name="rounds"
                                                id="round4" value="4"
                                                {{ old('rounds', $module->rounds) == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="round4"
                                                style="cursor: pointer;">ជុំទី៤</label>
                                        </div>
                                    </div>

                                    @error('rounds')
                                        <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.expense.type') }}</label>
                                    <!-- Updated name from 'cboExpenseType' to 'expense_type_id' -->
                                    <select class="form-control" data-trigger id="cboExpenseType" name="cboExpenseType"
                                        required data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($expenseTypes as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $module->budget_expense_type_id == $item->id ? 'selected' : '' }}>
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
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropProvince = document.getElementById('province');
            const dropProvinceChoice = new Choices(dropProvince, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. Rounds Skip Logic ---
            const skipRounds = document.getElementById('skipRounds');
            const roundRadios = document.querySelectorAll('.round-radio');

            function toggleRounds() {
                roundRadios.forEach(radio => {
                    if (skipRounds.checked) {
                        radio.checked = false;
                        radio.disabled = true;
                    } else {
                        radio.disabled = false;
                    }
                });
            }

            if (skipRounds) {
                skipRounds.addEventListener('change', toggleRounds);
                toggleRounds(); // Run immediately on page load to set correct edit state
            }

            // --- 2. Form Submission & Validation Logic ---
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);
            var clickedButton = null;
            var submitButtons = form.querySelectorAll('button[type="submit"]');

            submitButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    clickedButton = this;
                });
            });

            form.addEventListener('submit', function(e) {
                var valid = pristine.validate();

                if (!valid) {
                    e.preventDefault();
                } else {
                    submitButtons.forEach(function(button) {
                        button.disabled = true;
                        if (button === clickedButton) {
                            button.innerHTML += '...';
                        }
                    });

                    if (clickedButton && clickedButton.name) {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = clickedButton.name;
                        hiddenInput.value = clickedButton.value;
                        form.appendChild(hiddenInput);
                    }
                }
            });
        });
    </script>
@endsection
