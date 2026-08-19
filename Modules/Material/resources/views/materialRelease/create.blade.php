@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.material.release') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __('menus.material.release') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate method="POST"
                        action="{{ route('materialRelease.store', $params) }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            {{-- <div class="col-md-12"> --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="cboProject" class="form-label font-size-13 text-muted">
                                        {{ __('forms.project') }}
                                    </label>
                                    <select class="form-control" data-trigger id="cboProject" name="cboProject" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($project as $item)
                                            <option value="{{ $item->id }}"
                                                data-location_number="{{ $item->project_id }}">
                                                {{ $item->stock_number }} - {{ $item->stock_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agency">{{ __('forms.agency') }}</label>
                                    <select class="form-control" data-trigger id="agency" name="agency" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($Agency as $item)
                                            <option value="{{ $item->id }}"
                                                data-location_number="{{ $item->id }}">
                                                {{ $item->name }} 
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agency')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="p_name" class="form-label font-size-13 text-muted">
                                        {{ __('forms.item.name') }}
                                    </label>
                                    <select class="form-control" data-trigger id="p_name" name="p_name" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($MaterialEntry as $item)
                                            <option value="{{ $item->id }}"
                                                data-location_number="{{ $item->id }}">
                                                {{ $item->p_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('p_name')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="unit" class="form-label font-size-13 text-muted">
                                        {{ __('forms.unit') }}
                                    </label>
                                    <select class="form-control" data-trigger id="dropUnit" name="unit" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($unitType as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="quantity_request">{{ __('forms.quantity.request') }}</label>
                                    <input type="number" min="0" name="quantity_request" required
                                        class="form-control"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('quantity_request')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="quantity_total">{{ __('forms.price') }}ឯកតា</label>
                                    <input type="number" min="0" name="quantity_total" required class="form-control"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('quantity_total')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="source">{{ __('forms.source') }}</label>

                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="skipSource"
                                                style="cursor: pointer;">

                                            <label class="form-check-label font-size-12 text-muted" for="skipSource"
                                                style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>

                                    <input type="text" name="source" id="source" class="form-control"
                                        placeholder="{{ __('forms.source') }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('source')
                                        <div class="pristine-error text-help text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                            </div>
                            <!-- Project Year -->
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="p_year">{{ __('forms.pro.year') }}</label>

                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipProYear" style="cursor: pointer;">

                                            <label class="form-check-label font-size-12 text-muted" for="skipProYear"
                                                style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>

                                    <input type="text" name="p_year" id="p_year" class="form-control"
                                        placeholder="{{ __('forms.pro.year') }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('p_year')
                                        <div class="pristine-error text-help text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_release" class="form-label">កាលបរិច្ឆេទ</label>
                                    <input type="text" id="datepicker-basic" name="date_release" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('date_release')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="location_number_use">
                                        {{ __('forms.location.number') }}
                                    </label>
                                    <input type="text" class="form-control" id="location_number_use"
                                        name="location_number_use" list="location_number_list" required />
                                    <datalist id="location_number_list">
                                        @foreach ($waterEntity as $item)
                                            <option value="{{ $item->location_number }}">
                                                {{ $item->location_number }} - {{ $item->title_entity }}
                                            </option>
                                        @endforeach
                                    </datalist>

                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="invoice">{{ __('forms.invoice') }}</label>
                                    <input type="text" class="form-control" name="invoice" required />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">{{ __('forms.date') }}</label>
                                    <input type="text" id="date" name="date" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('date')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="use_start" class="form-label">{{ __('forms.use.start') }}</label>
                                    <input type="text" id="use_start" name="use_start" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('use_start')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="use_end" class="form-label">{{ __('forms.use.end') }}</label>
                                    <input type="text" id="use_end" name="use_end" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('use_end')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kilo">{{ __('forms.kilo') }}</label>
                                    <input type="text" class="form-control" name="kilo" required />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cost_total">{{ __('forms.cost.total') }}</label>
                                    <input type="text" class="form-control" name="cost_total" required />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div> --}}

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                {{-- <button class="btn btn-info" type="submit">{{ __('buttons.save.create') }}</button> --}}

                                {{-- <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button> --}}
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('materialRelease.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        const dateInput = document.getElementById('datepicker-basic');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }

        const useDateInput = document.getElementById('use_start');
        if (useDateInput) {
            flatpickr(useDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: useDateInput.value || null
            });
        }

        const endDateInput = document.getElementById('use_end');
        if (endDateInput) {
            flatpickr(endDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: endDateInput.value || null
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEntity = document.getElementById('dropEntity');
            const locationInput = document.getElementById('location_number_use');

            // បើអ្នកប្រើ Choices.js
            const entityChoices = new Choices(selectEntity, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: '{{ __('forms.search...') }}',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // ពេលមានការជ្រើស entity
            selectEntity.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option && option.dataset.location_number) {
                    locationInput.value = option.dataset.location_number;
                } else {
                    locationInput.value = '';
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProject');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('p_name');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('dropUnit');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('agency');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
         
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('pristine-valid-example');

            if (!form) {
                console.error('Form not found');
                return;
            }

            const pristine = new Pristine(form);
            const sourceInput = document.getElementById('source');
            const skipSourceCheckbox = document.getElementById('skipSource');

            const proYearInput = document.getElementById('p_year');
            const skipProYearCheckbox = document.getElementById('skipProYear');


            // ==========================================
            // CLEAR FIELD ERRORS
            // ==========================================
            function clearFieldErrors(input) {

                if (!input) return;

                const parentGroup = input.closest('.form-group');

                if (parentGroup) {

                    parentGroup
                        .querySelectorAll('.pristine-error, .text-help')
                        .forEach(el => el.remove());

                    parentGroup.classList.remove(
                        'has-danger',
                        'has-error'
                    );
                }

                input.classList.remove(
                    'is-invalid',
                    'border-danger'
                );
            }


            // ==========================================
            // SETUP SKIP FIELD
            // ==========================================
            function setupSkipField(checkbox, input) {
                if (!checkbox || !input) return;
                // ------------------------------------------
                // Initial state
                // ------------------------------------------
                if (checkbox.checked) {
                    input.value = '';
                    input.disabled = true;
                } else {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                }
                // ------------------------------------------
                // Add Pristine validator
                // ------------------------------------------
                pristine.addValidator(
                    input,
                    function(value) {
                        // If skipped, always valid
                        if (checkbox.checked) {
                            return true;
                        }
                        // Otherwise required
                        return value.trim() !== '';
                    },
                    "{{ __('messages.required') }}",
                    1,
                    true
                );
                // ------------------------------------------
                // Checkbox change
                // ------------------------------------------
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // ==============================
                        // SKIP
                        // ==============================
                        input.value = '';
                        input.disabled = true;
                        input.removeAttribute('required');
                        clearFieldErrors(input);
                        pristine.reset(input)
                        input.classList.remove(
                            'is-invalid',
                            'border-danger'
                        );
                        input.classList.add(
                            'border-success',
                            'bg-success-subtle'
                        );
                    } else {
                        // ==============================
                        // REQUIRED AGAIN
                        // ==============================
                        input.disabled = false;
                        input.setAttribute('required', 'required');
                        input.classList.remove(
                            'border-success',
                            'bg-success-subtle'
                        );

                        clearFieldErrors(input);

                        pristine.reset(input);
                    }
                });
            }
            // ==========================================
            // INITIALIZE
            // ==========================================
            setupSkipField(
                skipSourceCheckbox,
                sourceInput
            );
            setupSkipField(
                skipProYearCheckbox,
                proYearInput
            );
            // ==========================================
            // FORM SUBMIT
            // ==========================================
            form.addEventListener('submit', function(e) {

                e.preventDefault();
                // ------------------------------------------
                // Save which button was clicked
                // ------------------------------------------
                const submitter = e.submitter;

                if (submitter && submitter.name === 'action') {

                    let hidden = form.querySelector(
                        'input[name="action"]'
                    );
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'action';

                        form.appendChild(hidden);
                    }

                    hidden.value = submitter.value;
                }
                const isValid = pristine.validate();

                console.log('Form valid:', isValid);
                console.log('Errors:', pristine.getErrors());
                if (!isValid) {
                    return;
                }
                HTMLFormElement.prototype.submit.call(form);
            });

        });
    </script>
@endsection
