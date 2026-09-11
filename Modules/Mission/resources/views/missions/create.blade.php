@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- preloader css -->
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content.missions') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">
                                    <span>{{ $ministry->year }}</span></a>
                            </li>
                            {{-- <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ __('menus.credit') }}</a>
                            </li> --}}
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __('menus.content.missions') }}</a>
                            </li>
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
                    <div>
                        <form id="pristine-valid-example" action="{{ route('missions.store', $params) }}" novalidate
                            method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="id_number"
                                                    class="form-label font-size-13 text-muted">{{ __('forms.legal.id') }}</label>
                                                <input type="number" min="1" name="legal_number" required
                                                    data-pristine-required-message="{{ __('messages.required') }}"
                                                    data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                                    data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" class="form-control"
                                                    tabindex="1" />
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="legal_date"
                                                    class="form-label font-size-13 text-muted">{{ __('forms.select_legal_date') }}</label>
                                                <input type="text" id="legal_date" name="legal_date" class="form-control"
                                                    placeholder="{{ __('forms.select_legal_date') }}" required
                                                    data-pristine-required-message="{{ __('messages.required') }}"
                                                    tabindex="2" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group mb-3">
                                                <label for="start_date"
                                                    class="form-label font-size-13 text-muted">{{ __('menus.start_date') }}</label>
                                                {{-- <label class="visually-hidden" for="start_date">{{ __('menus.start_date') }}</label> --}}
                                                <input type="text" id="start_date" name="start_date" class="form-control"
                                                    required placeholder="{{ __('forms.select_date') }}"
                                                    value="{{ request('start_date') }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                            </div>
                                        </div>

                                        <!-- End Date -->
                                        <div class="col-sm-6">
                                            <div class="form-group mb-3">
                                                <label for="end_date"
                                                    class="form-label font-size-13 text-muted">{{ __('menus.end_date') }}</label>
                                                {{-- <label class="visually-hidden" for="end_date">{{ __('menus.end_date') }}</label> --}}
                                                <input type="text" id="end_date" name="end_date" class="form-control"
                                                    placeholder="{{ __('forms.select_date') }}"
                                                    value="{{ request('end_date') }}" required
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="cboProvince" class="form-label font-size-13 text-muted">
                                                    ទីកន្លែង {{ __('forms.province') }}
                                                </label>
                                                <select class="form-select" id="cboProvince" name="cboProvince" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($provinces as $province)
                                                        <option value="{{ $province->id }}">
                                                            {{ $province->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('cboProvince')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="vDescription">{{ __('forms.mission.description') }}</label>
                                                <textarea id="vDescription" name="txtDescription" required tabindex="2"></textarea>
                                                <div class="invalid-feedback">
                                                    {{ __('messages.required') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">


                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                {{ __('forms.employee') }}
                                            </h5>

                                            <button type="button" class="btn btn-primary btn-sm" id="btnAddRow">
                                                {{ __('forms.add') }}
                                            </button>
                                        </div>

                                        <div class="card-body">

                                            <div id="employeeRows">

                                                {{-- First row --}}
                                                <div class="employee-row border rounded p-3 mb-3">

                                                    <div class="row align-items-end">

                                                        {{-- Employee --}}
                                                        <div class="col-lg-3 col-md-4">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted">
                                                                    {{ __('forms.name') }}
                                                                </label>

                                                                <select class="form-select employee-name" name="cboName[]"
                                                                    required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                                    <option value="">
                                                                        {{ __('forms.search...') }}
                                                                    </option>

                                                                    @foreach ($employee as $emp)
                                                                        <option value="{{ $emp->id }}">
                                                                            {{ $emp->name_kh }} - {{ $emp->name_latin }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>

                                                            </div>
                                                        </div>


                                                        {{-- Position --}}
                                                        <div class="col-lg-3 col-md-3">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted">
                                                                    {{ __('forms.position') }}
                                                                </label>

                                                                <select class="form-select employee-position"
                                                                    name="cboPosition[]" required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                                    <option value="">
                                                                        {{ __('forms.search...') }}
                                                                    </option>

                                                                    @foreach ($position as $pos)
                                                                        <option value="{{ $pos->level_id }}">
                                                                            {{ $pos->name }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>

                                                            </div>
                                                        </div>


                                                        {{-- Level --}}
                                                        <div class="col-lg-3 col-md-3">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted">
                                                                    {{ __('forms.level') }}
                                                                </label>

                                                                <select class="form-select employee-level"
                                                                    name="cboLevel[]" required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                                    <option value="">
                                                                        {{ __('forms.search...') }}
                                                                    </option>

                                                                </select>

                                                            </div>
                                                        </div>


                                                        {{-- Assign Budget --}}
                                                        <div class="col-lg-2 col-md-2">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted d-block">
                                                                    {{ __('forms.assign') }}
                                                                </label>

                                                                <input type="hidden" class="assign-budget-value"
                                                                    name="assign_budget[]" value="0">

                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                        class="form-check-input assign-budget"
                                                                        style="width: 22px; height: 22px; cursor: pointer;"
                                                                        id="assignBudget">

                                                                    <label class="form-check-label ms-2"
                                                                        for="assignBudget"
                                                                        style="padding-top: 3px; cursor: pointer;">
                                                                        {{ __('forms.assign') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Remove --}}
                                                        <div class="col-lg-1 col-md-1 mb-3">

                                                            <button type="button"
                                                                class="btn btn-danger btn-remove-row w-30" disabled>

                                                                <i class="bx bx-trash"></i>

                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <button class="btn btn-info" type="submit">{{ __('buttons.save.create') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('missions.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
    </script>

    {{-- Saved Btn --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);

            // Track which button was clicked
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
                    // Form is invalid, stop submission
                    e.preventDefault();
                } else {
                    // Form is valid, prevent multiple clicks
                    submitButtons.forEach(function(button) {
                        button.disabled = true;
                        // Optional: add a small loading text
                        if (button === clickedButton) {
                            button.innerHTML += '...';
                        }
                    });

                    // Append the clicked button's name and value so Laravel receives it
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

    {{-- UI Searching Dropdown  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProvince');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false,

            });
        });
    </script>

    {{-- UI Date Filter  --}}
    <script>
        const dateInput = document.getElementById('legal_date');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }
    </script>

    {{-- Filter Dropdown  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboName');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            const cboNamePicker = preloader('#cboLevel', {
                url: "{{ route('missions.by.level') }}",
                method: 'GET',
                data: {},
                success: function(html) {
                    $('#cboLevel').html(html);
                    choices.setChoices($('#cboLevel option').map(function() {
                        return {
                            value: $(this).val(),
                            label: $(this).text()
                        };
                    }).get(), 'value', 'label', true);
                },
                error: function() {
                    // optional: keep empty if error
                    $('#cboLevel').html('<option value="">{{ __('forms.search...') }}</option>');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboPosition');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboLevel');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    {{-- Filter Date Lock --}}
    <script>
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        if (startDateInput) {
            flatpickr(startDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: startDateInput.value || null
            });
        }

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
        const legalDatePicker = flatpickr('#legal_date', {
            dateFormat: 'Y-m-d'
        });

        const startDatePicker = flatpickr('#start_date', {
            dateFormat: 'Y-m-d',

            onChange: function(selectedDates) {

                if (selectedDates.length > 0) {

                    endDatePicker.set('minDate', selectedDates[0]);

                    // If current end date is before start date, clear it
                    if (
                        endDatePicker.selectedDates.length > 0 &&
                        endDatePicker.selectedDates[0] < selectedDates[0]
                    ) {
                        endDatePicker.clear();
                    }
                } else {

                    endDatePicker.set('minDate', null);
                }
            }
        });

        const endDatePicker = flatpickr('#end_date', {
            dateFormat: 'Y-m-d'
        });
    </script>

    {{-- Keep this code --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('missionForm');

            if (!form) {
                return;
            }

            // Initialize Pristine
            const pristine = new Pristine(form, {
                classTo: 'form-group',
                errorClass: 'is-invalid',
                successClass: 'is-valid',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'invalid-feedback'
            });

            // Submit validation
            form.addEventListener('submit', function(e) {

                e.preventDefault();

                // Validate all fields
                const valid = pristine.validate();

                if (!valid) {

                    // Find first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');

                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        firstInvalid.focus();
                    }

                    return;
                }

                // Prevent double submit
                const btnSave = document.getElementById('btnSave');

                if (btnSave) {
                    btnSave.disabled = true;

                    btnSave.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                កំពុងរក្សាទុក...
            `;
                }

                // Submit form
                form.submit();
            });

        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('missionForm');

            if (!form) {
                return;
            }

            const pristine = new Pristine(form, {
                classTo: 'form-group',
                errorClass: 'is-invalid',
                successClass: 'is-valid',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'invalid-feedback'
            });

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                // --------------------------------
                // Validate normal form fields
                // --------------------------------
                let valid = pristine.validate();

                // --------------------------------
                // Validate dynamic employee rows
                // --------------------------------
                $('.employee-row').each(function() {

                    const row = $(this);

                    const name = row.find('.employee-name');
                    const position = row.find('.employee-position');
                    const level = row.find('.employee-level');

                    // Employee name
                    if (!name.val()) {
                        valid = false;

                        name.addClass('is-invalid');

                        if (!name.next('.invalid-feedback').length) {
                            name.after(`
                        <div class="invalid-feedback">
                            {{ __('messages.required') }}
                        </div>
                    `);
                        }
                    } else {
                        name.removeClass('is-invalid');
                        name.next('.invalid-feedback').remove();
                    }

                    // Position
                    if (!position.val()) {
                        valid = false;

                        position.addClass('is-invalid');

                        if (!position.next('.invalid-feedback').length) {
                            position.after(`
                        <div class="invalid-feedback">
                            {{ __('messages.required') }}
                        </div>
                    `);
                        }
                    } else {
                        position.removeClass('is-invalid');
                        position.next('.invalid-feedback').remove();
                    }

                    // Level
                    if (!level.val()) {
                        valid = false;

                        level.addClass('is-invalid');

                        if (!level.next('.invalid-feedback').length) {
                            level.after(`
                        <div class="invalid-feedback">
                            {{ __('messages.required') }}
                        </div>
                    `);
                        }
                    } else {
                        level.removeClass('is-invalid');
                        level.next('.invalid-feedback').remove();
                    }

                });

                // --------------------------------
                // Stop if validation failed
                // --------------------------------
                if (!valid) {

                    const firstInvalid = form.querySelector('.is-invalid');

                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        firstInvalid.focus();
                    }

                    return;
                }

                // --------------------------------
                // Prevent double submit
                // --------------------------------
                const btnSave = document.getElementById('btnSave');

                if (btnSave) {
                    btnSave.disabled = true;

                    btnSave.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                កំពុងរក្សាទុក...
            `;
                }

                // --------------------------------
                // Submit
                // --------------------------------
                form.submit();
            });

        });
    </script>

    {{-- Validation Date --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('missionForm');

            if (!form) {
                return;
            }

            const pristine = new Pristine(form);

            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            // Validate end date
            if (endDate) {

                pristine.addValidator(
                    endDate,
                    function(value) {

                        if (!value || !startDate.value) {
                            return true;
                        }

                        const start = new Date(startDate.value);
                        const end = new Date(value);

                        return end >= start;

                    },
                    'ថ្ងៃបញ្ចប់ ត្រូវតែធំជាង ឬស្មើថ្ងៃចាប់ផ្តើម',
                    5,
                    false
                );
            }

        });
    </script>

    {{-- Added New Row for Employee --}}
    <script>
        $(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | Add New Row
            |--------------------------------------------------------------------------
            */
            $('#btnAddRow').on('click', function() {

                let row = $('.employee-row:first').clone();

                // Reset all selects
                row.find('select').val('');

                // Make required fields required
                row.find('.employee-name').prop('required', true);
                row.find('.employee-position').prop('required', true);
                row.find('.employee-level').prop('required', true);

                // Reset level
                row.find('.employee-level')
                    .prop('disabled', true)
                    .prop('required', true)
                    .html(`
            <option value="">
                {{ __('forms.search...') }}
            </option>
        `);

                // Reset assign
                row.find('.assign-budget')
                    .prop('checked', false);

                row.find('.assign-budget-value')
                    .val('0');

                // Enable remove icon/link
                row.find('.btn-remove-row')
                    .removeClass('disabled')
                    .css('pointer-events', 'auto');

                // Add new row
                $('#employeeRows').append(row);

                // Update remove buttons
                updateRemoveButtons();
            });

            /*
            |--------------------------------------------------------------------------
            | Remove Row
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '.btn-remove-row', function() {

                $(this)
                    .closest('.employee-row')
                    .remove();

                updateRemoveButtons();
            });


            /*
            |--------------------------------------------------------------------------
            | Enable / Disable Remove Buttons
            |--------------------------------------------------------------------------
            */

            function updateRemoveButtons() {

                let rows = $('.employee-row');

                if (rows.length === 1) {

                    rows.find('.btn-remove-row')
                        .prop('disabled', true);

                } else {

                    rows.find('.btn-remove-row')
                        .prop('disabled', false);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Position Change -> Load Level
            |--------------------------------------------------------------------------
            */

            $(document).on('change', '.employee-position', function() {

                let positionId = $(this).val();

                let row = $(this).closest('.employee-row');

                let levelSelect = row.find('.employee-level');


                // Reset level
                levelSelect
                    .prop('disabled', true)
                    .html(`
                <option value="">
                    {{ __('forms.loading') }}...
                </option>
            `);


                if (!positionId) {

                    levelSelect
                        .html(`
                    <option value="">
                        {{ __('forms.search...') }}
                    </option>
                `);

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | AJAX
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url: "{{ route('position.levels') }}",

                    type: "GET",

                    data: {
                        level_id: positionId
                    },

                    success: function(response) {

                        levelSelect.empty();

                        levelSelect.append(`
                    <option value="">
                        {{ __('forms.search...') }}
                    </option>
                `);


                        if (response.length > 0) {

                            $.each(response, function(index, level) {

                                levelSelect.append(`
                            <option value="${level.id}">
                                ${level.name}
                            </option>
                        `);

                            });

                            levelSelect.prop('disabled', false);

                        } else {

                            levelSelect.append(`
                        <option value="">
                            {{ __('messages.no_data') }}
                        </option>
                    `);

                            levelSelect.prop('disabled', true);
                        }
                    },


                    error: function(xhr) {

                        console.error(xhr);

                        levelSelect
                            .html(`
                        <option value="">
                            {{ __('messages.error') }}
                        </option>
                    `)
                            .prop('disabled', true);
                    }

                });

            });


            /*
            |--------------------------------------------------------------------------
            | Initial
            |--------------------------------------------------------------------------
            */

            updateRemoveButtons();

        });
    </script>

    <script>
        $(document).on('change', '.assign-budget', function() {

            const row = $(this).closest('.employee-row');

            const hiddenInput = row.find('.assign-budget-value');

            hiddenInput.val(this.checked ? 1 : 0);

        });
    </script>
@endsection
