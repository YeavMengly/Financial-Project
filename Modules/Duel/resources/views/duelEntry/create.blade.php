@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.duel.entry') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.duel') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.entry') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        {{-- <form id="pristine-valid-example" action="{{ route('duelEntry.store', $params) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="project" class="form-label font-size-13 text-muted">
                                            {{ __('forms.project') }}
                                        </label>
                                        <select class="form-control" data-trigger id="dropProject" name="project" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($projects as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->stock_number}}-{{ $item->stock_name }}
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

                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="source">{{ __('forms.source') }}</label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipSource" style="cursor: pointer;">

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
                                            <label for="pro_year">{{ __('forms.pro.year') }}</label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipProYear" style="cursor: pointer;">

                                                <label class="form-check-label font-size-12 text-muted" for="skipProYear"
                                                    style="cursor: pointer;">
                                                    រំលង
                                                </label>
                                            </div>
                                        </div>

                                        <input type="text" name="pro_year" id="pro_year" class="form-control"
                                            placeholder="{{ __('forms.pro.year') }}"
                                            data-pristine-required-message="{{ __('messages.required') }}">

                                        @error('pro_year')
                                            <div class="pristine-error text-help text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 mt-3">
                                    <div class="col-xl-12 col-md-12">
                                        <table class="table table-bordered" id="itemTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('forms.item.name') }}</th>
                                                    <th width="240">{{ __('forms.quantity') }} (លីត្រ)</th>
                                                    <th width="240">{{ __('forms.price') }} (លីត្រ)</th>
                                                    <th width="80">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select id="cboItem" class="form-control" name="item_name[]"
                                                            required>
                                                            <option value="">{{ __('forms.search...') }}</option>
                                                            @foreach ($duelType as $item)
                                                                <option value="{{ $item->id }}">
                                                                    {{ $item->name_km }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <input type="number" min="0" name="quantity[]"
                                                            class="form-control" required>
                                                    </td>

                                                    <td>
                                                        <input type="number" min="0" name="price[]"
                                                            class="form-control" required>
                                                    </td>

                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-success addRow">+</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('duelEntry.index', $params) }}">{{ __('buttons.back') }}</a>

                            </div>
                        </form> --}}

                        <form id="pristine-valid-example" action="{{ route('duelEntry.store', $params) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row g-3">

                                {{-- =========================
            PROJECT INFORMATION
        ========================== --}}
                                <div class="col-xl-4 col-lg-5">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-transparent">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-folder me-1"></i>
                                                {{ __('forms.project') }}
                                            </h5>
                                        </div>

                                        <div class="card-body">

                                            {{-- Project --}}
                                            <div class="form-group mb-3">
                                                <label for="dropProject" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.project') }}
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select class="form-control" data-trigger id="dropProject" name="project"
                                                    required
                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                    <option value="">
                                                        {{ __('forms.search...') }}
                                                    </option>

                                                    @foreach ($projects as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ old('project') == $item->id ? 'selected' : '' }}>
                                                            {{ $item->stock_number }} - {{ $item->stock_name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('project')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>


                                            {{-- Source --}}
                                            <div class="form-group mb-3">

                                                <div class="d-flex justify-content-between align-items-center mb-1">

                                                    <label for="source" class="form-label mb-0">
                                                        {{ __('forms.source') }}
                                                    </label>

                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="skipSource" style="cursor:pointer;">

                                                        <label class="form-check-label font-size-12 text-muted"
                                                            for="skipSource" style="cursor:pointer;">
                                                            រំលង
                                                        </label>
                                                    </div>

                                                </div>

                                                <input type="text" name="source" id="source" class="form-control"
                                                    value="{{ old('source') }}" placeholder="{{ __('forms.source') }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                @error('source')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>


                                            {{-- Project Year --}}
                                            <div class="form-group mb-3">

                                                <div class="d-flex justify-content-between align-items-center mb-1">

                                                    <label for="pro_year" class="form-label mb-0">
                                                        {{ __('forms.pro.year') }}
                                                    </label>

                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="skipProYear" style="cursor:pointer;">

                                                        <label class="form-check-label font-size-12 text-muted"
                                                            for="skipProYear" style="cursor:pointer;">
                                                            រំលង
                                                        </label>
                                                    </div>

                                                </div>

                                                <input type="text" name="pro_year" id="pro_year" class="form-control"
                                                    value="{{ old('pro_year') }}" placeholder="{{ __('forms.pro.year') }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                @error('pro_year')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>

                                        </div>
                                    </div>
                                </div>


                                {{-- =========================
            ITEM DETAILS
        ========================== --}}
                                <div class="col-xl-8 col-lg-7">

                                    <div class="card shadow-sm">

                                        <div class="card-header bg-transparent">
                                            <div class="d-flex justify-content-between align-items-center">

                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-fuel-pump me-1"></i>
                                                    {{ __('forms.item.name') }}
                                                </h5>

                                                <span class="badge bg-primary">
                                                    {{ __('forms.quantity') }} / {{ __('forms.price') }}
                                                </span>

                                            </div>
                                        </div>

                                        <div class="card-body">

                                            <div class="table-responsive">

                                                <table class="table table-bordered table-hover align-middle mb-0"
                                                    id="itemTable">

                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>
                                                                {{ __('forms.item.name') }}
                                                                <span class="text-danger">*</span>
                                                            </th>

                                                            <th width="220">
                                                                {{ __('forms.quantity') }}
                                                                <small class="text-muted">(លីត្រ)</small>
                                                                <span class="text-danger">*</span>
                                                            </th>

                                                            <th width="220">
                                                                {{ __('forms.price') }}
                                                                <small class="text-muted">(លីត្រ)</small>
                                                                <span class="text-danger">*</span>
                                                            </th>

                                                            <th width="80" class="text-center">
                                                                Action
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        <tr>

                                                            {{-- Item --}}
                                                            <td>
                                                                <select id="cboItem" class="form-control"
                                                                    name="item_name[]" required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                                    <option value="">
                                                                        {{ __('forms.search...') }}
                                                                    </option>

                                                                    @foreach ($duelType as $item)
                                                                        <option value="{{ $item->id }}">
                                                                            {{ $item->name_km }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            </td>


                                                            {{-- Quantity --}}
                                                            <td>
                                                                <input type="number" min="0" step="any"
                                                                    name="quantity[]" class="form-control"
                                                                    placeholder="{{ __('forms.quantity') }}" required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                            </td>


                                                            {{-- Price --}}
                                                            <td>
                                                                <input type="number" min="0" step="any"
                                                                    name="price[]" class="form-control"
                                                                    placeholder="{{ __('forms.price') }}" required
                                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                            </td>


                                                            {{-- Action --}}
                                                            <td class="text-center">

                                                                <button type="button"
                                                                    class="btn btn-success btn-sm addRow"
                                                                    title="Add Item">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>

                                                            </td>

                                                        </tr>

                                                    </tbody>

                                                </table>

                                            </div>

                                            {{-- Empty/help message --}}
                                            <div class="text-muted font-size-12 mt-2">
                                                <i class="bi bi-info-circle me-1"></i>
                                                អ្នកអាចចុច <strong>+</strong> ដើម្បីបន្ថែមទំនិញ/ប្រេងបន្ថែម។
                                            </div>

                                        </div>
                                    </div>

                                </div>


                                {{-- =========================
            ACTION BUTTONS
        ========================== --}}
                                <div class="col-12">

                                    <div class="card shadow-sm">

                                        <div class="card-body">

                                            <div class="d-flex flex-wrap justify-content-start gap-2">

                                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">

                                                    <i class="bi bi-save me-1"></i>
                                                    {{ __('buttons.save') }}

                                                </button>


                                                <a href="{{ url()->current() }}" class="btn btn-danger">

                                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                                    {{ __('buttons.delete') }}

                                                </a>


                                                <a href="{{ route('duelEntry.index', $params) }}" class="btn btn-dark">

                                                    <i class="bi bi-arrow-left me-1"></i>
                                                    {{ __('buttons.back') }}

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#vNote').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });

        $(document).ready(function() {
            $('#vRefer').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboSubDepartSelect = document.getElementById('cboAgency');
            const cboSubDepartChoice = new Choices(cboSubDepartSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        // ✅ Flatpickr "Basic"
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropDuel = document.getElementById('dropDuel');
            const dropDuelChoice = new Choices(dropDuel, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const dropUnit = document.getElementById('dropUnit');
            const dropUnitChoice = new Choices(dropUnit, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const dropProject = document.getElementById('dropProject');
            const dropProjectChoice = new Choices(dropProject, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const table = document.querySelector('#itemTable tbody');

            table.addEventListener('click', function(e) {

                if (e.target.classList.contains('addRow')) {

                    let newRow = table.rows[0].cloneNode(true);

                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                    newRow.querySelector('.addRow')
                        .classList.remove('btn-success', 'addRow')
                    newRow.querySelector('button')
                        .classList.add('btn-danger', 'removeRow')
                    newRow.querySelector('button')
                        .innerHTML = '-'

                    table.appendChild(newRow);
                }

                if (e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                }

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

            const proYearInput = document.getElementById('pro_year');
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
