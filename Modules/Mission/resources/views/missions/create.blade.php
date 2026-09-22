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

    <style>
        .form-group.has-danger .choices__inner {
            border-color: #f14c5c !important;
        }

        .form-group.has-success .choices__inner {
            border-color: #198754 !important;
        }
    </style>

    <style>
        .choices .choices__inner {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        .choices.is-invalid .choices__inner {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.15);
        }

        .choices.is-valid .choices__inner {
            border-color: #198754 !important;
            box-shadow: 0 0 0 0.1rem rgba(25, 135, 84, 0.15);
        }

        .choices+.pristine-error {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
    </style>
@endsection
{{-- @section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
@endsection --}}
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

                            <li class="breadcrumb-item active"><a
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
                            method="POST">
                            @csrf
                            <div class="row">

                                <div class="row">

                                    <div class="col-lg-2 col-md-3">

                                        <div class="form-group mb-3">
                                            <label for="cboDocument" class="form-label font-size-13 text-muted">ជ្រើសរើស
                                                ប្រភេទឯកសារបេសកកម្ម</label>

                                            <select class="form-select" id="cboDocument" name="cboDocument" required
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                                @foreach ($document as $doc)
                                                    <option value="{{ $doc->id }}">
                                                        {{ $doc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('cboDocument')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-3">
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

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="legal_date"
                                                class="form-label font-size-13 text-muted">{{ __('forms.select_legal_date') }}</label>
                                            <input type="text" id="legal_date" name="legal_date" class="form-control"
                                                placeholder="{{ __('forms.select_legal_date') }}" required
                                                data-pristine-required-message="{{ __('messages.required') }}"
                                                tabindex="2" />
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="start_date"
                                                class="form-label font-size-13 text-muted">{{ __('menus.start_date') }}</label>
                                            <input type="text" id="start_date" name="start_date" class="form-control"
                                                required placeholder="{{ __('forms.select_date') }}"
                                                value="{{ request('start_date') }}"
                                                data-pristine-required-message="{{ __('messages.required') }}" />
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="end_date"
                                                class="form-label font-size-13 text-muted">{{ __('menus.end_date') }}</label>
                                            <input type="text" id="end_date" name="end_date" class="form-control"
                                                placeholder="{{ __('forms.select_date') }}"
                                                value="{{ request('end_date') }}" required
                                                data-pristine-required-message="{{ __('messages.required') }}" />
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
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
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="fileInput" class="form-label mb-0">
                                                    {{ __('forms.file.type') }}
                                                </label>

                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="skipFileInput" style="cursor: pointer;">

                                                    <label class="form-check-label font-size-12 text-muted"
                                                        for="skipFileInput" style="cursor: pointer;">
                                                        រំលង
                                                    </label>
                                                </div>
                                            </div>

                                            <input type="file" id="fileInput" name="file" tabindex="10"
                                                class="form-control" tabindex="15" accept=".pdf,.doc,.docx"
                                                data-allowed-extensions="pdf,doc,docx"
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                            <small class="form-text text-muted">
                                                Allowed types: PDF (Max: 10MB per file)
                                            </small>

                                            @error('file')
                                                <div class="pristine-error text-help text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            @error('file.*')
                                                <div class="pristine-error text-help text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboProgram" class="form-label font-size-13 text-muted">
                                                {{ __('forms.program') }}
                                            </label>
                                            <select class="form-select" id="cboProgram" name="cboProgram"
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                                @foreach ($program as $p)
                                                    <option value="{{ $p->id }}">
                                                        {{ $p->no }}-
                                                        {{ $p->title }}</option>
                                                @endforeach
                                            </select>
                                            @error('cboProgram')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboProgramSub" class="form-label font-size-13 text-muted">
                                                {{ __('forms.program.sub') }}
                                            </label>
                                            <select id="cboProgramSub" class="form-select" name="cboProgramSub"
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                            </select>
                                            @error('cboProgramSub')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboCluster" class="form-label font-size-13 text-muted">
                                                {{ __('forms.cluster') }}
                                            </label>
                                            <select id="cboCluster" class="form-select" name="cboCluster"
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                            </select>

                                            @error('cboCluster')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboAgency" class="form-label font-size-13 text-muted">
                                                {{ __('forms.agency') }}
                                            </label>
                                            <select id="cboAgency" class="form-select" name="cboAgency" required
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                            </select>
                                            @error('cboAgency')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> --}}

                                    {{-- <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboSubAccount" class="form-label text-muted">
                                                {{ __('forms.sub.account') }}
                                            </label>
                                            <select class="form-control" id="cboSubAccount" name="cboSubAccount"
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                                @foreach ($accountSub as $as)
                                                    <option value="{{ $as->id }}">
                                                        {{ $as->no }} -
                                                        {{ $as->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('cboSubAccount')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="vDescription">{{ __('forms.document.description') }}</label>
                                            <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                                data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                            @error('txtDescription')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                {{ __('forms.mission.employee') }}
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
                                                        <div class="col-lg-2 col-md-4">
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

                                                                    @foreach ($employees as $emp)
                                                                        <option value="{{ $emp->id }}">
                                                                            {{ $emp->name_kh }} - {{ $emp->name_latin }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>

                                                            </div>
                                                        </div>

                                                        {{-- Position --}}
                                                        <div class="col-lg-2 col-md-4">
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

                                                                    @foreach ($positions as $pos)
                                                                        <option value="{{ $pos->id }}">
                                                                            {{ $pos->name }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>

                                                            </div>
                                                        </div>

                                                        {{-- Level --}}
                                                        {{-- <div class="col-lg-2 col-md-4">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted">
                                                                    {{ __('forms.level') }}
                                                                </label>

                                                                <select class="form-select employee-level"
                                                                    name="cboLevel[]" required disabled
                                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                                    <option value="">
                                                                        {{ __('forms.search...') }}
                                                                    </option>

                                                                </select>

                                                            </div>
                                                        </div> --}}

                                                        <!-- Leader -->
                                                        <div class="col-lg-2 col-md-4">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted d-block">
                                                                    {{ __('forms.leader') }}
                                                                </label>

                                                                <div class="form-check">
                                                                    <input type="radio"
                                                                        class="form-check-input employee-leader"
                                                                        name="leader_index" value="0"
                                                                        style="width:22px;height:22px;cursor:pointer;">

                                                                    <label class="form-check-label ms-2"
                                                                        style="padding-top:3px;cursor:pointer;">
                                                                        {{ __('forms.leader') }}
                                                                    </label>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        {{-- Assign Budget --}}
                                                        <div class="col-lg-2 col-md-4">
                                                            <div class="form-group mb-3">

                                                                <label class="form-label font-size-13 text-muted d-block">
                                                                    {{ __('forms.assign') }}
                                                                </label>

                                                                <input type="hidden" class="assign-budget-value"
                                                                    name="assign_budget[]" value="0">

                                                                <div class="form-check">

                                                                    <input type="checkbox"
                                                                        class="form-check-input assign-budget"
                                                                        name="assign_budget_checkbox[]"
                                                                        style="width:22px;height:22px;cursor:pointer;">

                                                                    <label class="form-check-label ms-2"
                                                                        style="padding-top:3px;cursor:pointer;">
                                                                        {{ __('forms.assign') }}
                                                                    </label>

                                                                </div>

                                                            </div>
                                                        </div>

                                                        {{-- Remove --}}
                                                        <div class="col-lg-2 col-md-4">

                                                            <button type="button" class="btn btn-danger btn-remove-row"
                                                                disabled>
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

        {{-- ំMessage Alter Require Leader --}}
        <div class="modal fade" id="leaderRequiredModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            សូមជ្រើសរើសប្រធាន
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        សូមជ្រើសរើសបុគ្គលិកម្នាក់ជាប្រធានបេសកកម្ម
                        មុនពេលរក្សាទុក។
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            យល់ព្រម
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    e.preventDefault();

                    var invalidField = form.querySelector('.pristine-error');

                    if (invalidField) {

                        invalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Choices.js
                        var choicesContainer = invalidField
                            .closest('.choices');

                        if (choicesContainer) {
                            var choicesButton = choicesContainer.querySelector(
                                '.choices__inner'
                            );

                            if (choicesButton) {
                                choicesButton.click();
                            }
                        } else {
                            invalidField.focus();
                        }
                    }

                    return false;
                }
            });
        });
    </script>

    {{-- Alert message that's leader in employee-row  --}}
    <script>
        $(document).ready(function() {

            $('#pristine-valid-example').on('submit', function(event) {

                /*
                |--------------------------------------------------------------------------
                | Check selected leader
                |--------------------------------------------------------------------------
                */
                const leader = document.querySelector(
                    '#employeeRows .employee-leader:checked'
                );

                /*
                |--------------------------------------------------------------------------
                | Require leader before submit
                |--------------------------------------------------------------------------
                */
                if (!leader) {

                    event.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'សូមជ្រើសរើសប្រធាន',
                        text: 'សូមជ្រើសរើសបុគ្គលិកម្នាក់ជាប្រធានបេសកកម្មជាមុនសិន។',
                        confirmButtonText: 'យល់ព្រម',
                        confirmButtonColor: '#dc3545',
                        allowOutsideClick: false
                    });

                    return false;
                }

            });

        });
    </script>

    {{-- UI Searching Dropdown  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboDocument');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false,

            });
        });

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

    <script>
        window.pristine = new Pristine(document.getElementById('pristine-valid-example'), {
            classTo: 'form-group', // Adds success/danger classes to this wrapper
            errorClass: 'has-danger', // Class applied on validation error
            successClass: 'has-success', // Class applied on success
            errorTextParent: 'form-group',
            errorTextTag: 'div',
            errorTextClass: 'pristine-error text-help text-danger mt-1'
        });
    </script>

    <script>
        // Automatically clear/re-check validation when any Choices dropdown changes
        $(document).on('change', '#cboDocument, #cboProvince, #cboName, #cboPosition', function() {
            if (typeof pristine !== 'undefined') {
                pristine.validate(this);
            }
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
    {{-- <script>
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
    </script> --}}
    {{-- <script>
        $(document).on('change', '.employee-position', function() {
            const positionId = $(this).val();

            if (!positionId) {
                return;
            }

            $.ajax({
                url: "{{ route('missions.by.level') }}",
                type: "GET",
                data: {
                    position_id: positionId
                },
                dataType: "json",

                beforeSend: function() {
                    console.log('Loading levels...');
                },

                success: function(response) {
                    if (response.success) {
                        console.log('Levels:', response.data);

                        $.each(response.data, function(index, level) {
                            console.log(
                                'Level ID:',
                                level.id,
                                'Level Name:',
                                level.name
                            );
                        });
                    }
                },

                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        });
    </script> --}}

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
    <script>
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
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Choices instances
            |--------------------------------------------------------------------------
            */
            const choicesInstances = new WeakMap();


            /*
            |--------------------------------------------------------------------------
            | Initialize Choices
            |--------------------------------------------------------------------------
            */
            function initChoices(select) {

                if (!select) {
                    return null;
                }

                if (choicesInstances.has(select)) {
                    return choicesInstances.get(select);
                }

                const choices = new Choices(select, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'ស្វែងរក...',
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ស្វែងរក...',
                    shouldSort: false,
                    allowHTML: false,
                    searchResultLimit: 100,
                    noResultsText: 'រកមិនឃើញ',
                    noChoicesText: 'មិនមានទិន្នន័យ'
                });

                choicesInstances.set(select, choices);

                return choices;
            }


            /*
            |--------------------------------------------------------------------------
            | Destroy Choices
            |--------------------------------------------------------------------------
            */
            function destroyChoices(select) {

                if (!select) {
                    return;
                }

                const choices = choicesInstances.get(select);

                if (choices) {
                    choices.destroy();
                    choicesInstances.delete(select);
                }
            }



            /*
            |--------------------------------------------------------------------------
            | Initialize employee row
            |--------------------------------------------------------------------------
            */
            function initEmployeeRow(row) {

                const employeeSelect = row.querySelector('.employee-name');
                const positionSelect = row.querySelector('.employee-position');
                const levelSelect = row.querySelector('.employee-level');

                initChoices(employeeSelect);
                initChoices(positionSelect);
                initChoices(levelSelect);

                /*
                |--------------------------------------------------------------------------
                | Employee validation
                |--------------------------------------------------------------------------
                */
                employeeSelect?.addEventListener('change', function() {

                    updateValidation(this);
                });


                /*
                |--------------------------------------------------------------------------
                | Position -> Level AJAX
                |--------------------------------------------------------------------------
                */
                positionSelect?.addEventListener('change', function() {

                    updateValidation(this);

                    const positionId = this.value;

                    loadLevel(row, positionId);
                });


                /*
                |--------------------------------------------------------------------------
                | Level validation
                |--------------------------------------------------------------------------
                */
                levelSelect?.addEventListener('change', function() {

                    updateValidation(this);
                });


                /*
                |--------------------------------------------------------------------------
                | Assign budget
                |--------------------------------------------------------------------------
                */
                const checkbox = row.querySelector('.assign-budget');
                const hiddenValue = row.querySelector('.assign-budget-value');

                checkbox?.addEventListener('change', function() {

                    hiddenValue.value = this.checked ? '1' : '0';
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Validation border
            |--------------------------------------------------------------------------
            */
            function updateValidation(select) {

                if (!select) {
                    return;
                }

                const choicesContainer = select.closest('.choices');

                if (!choicesContainer) {
                    return;
                }

                if (select.value) {

                    choicesContainer.classList.remove('is-invalid');
                    choicesContainer.classList.add('is-valid');

                } else {

                    choicesContainer.classList.remove('is-valid');
                    choicesContainer.classList.add('is-invalid');
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Load Level
            |--------------------------------------------------------------------------
            */
            function loadLevel(row, positionId, selectedLevel = null) {

                const levelSelect = row.querySelector('.employee-level');

                if (!levelSelect) {
                    return;
                }

                const levelChoices = choicesInstances.get(levelSelect);

                if (!levelChoices) {
                    return;
                }

                levelChoices.clearStore();

                levelChoices.setChoices([{
                    value: '',
                    label: '{{ __('forms.loading') }}...',
                    disabled: true
                }], 'value', 'label', true);

                levelChoices.disable();

                if (!positionId) {

                    levelChoices.clearStore();

                    levelChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        disabled: true
                    }], 'value', 'label', true);

                    levelChoices.disable();

                    return;
                }

                $.ajax({
                    url: "{{ route('missions.by.level') }}",
                    type: "GET",
                    data: {
                        level_id: positionId
                    },

                    success: function(response) {

                        levelChoices.clearStore();

                        if (response && response.length > 0) {

                            const choices = response.map(function(level) {

                                return {
                                    value: String(level.id),
                                    label: level.name,
                                    selected: selectedLevel &&
                                        String(level.id) === String(selectedLevel)
                                };

                            });

                            levelChoices.setChoices(
                                choices,
                                'value',
                                'label',
                                true
                            );

                            levelChoices.enable();

                        } else {

                            levelChoices.setChoices([{
                                value: '',
                                label: '{{ __('messages.no_data') }}',
                                disabled: true
                            }], 'value', 'label', true);

                            levelChoices.disable();
                        }
                    },

                    error: function(xhr) {

                        console.error(xhr);

                        levelChoices.clearStore();

                        levelChoices.setChoices([{
                            value: '',
                            label: '{{ __('messages.error') }}',
                            disabled: true
                        }], 'value', 'label', true);

                        levelChoices.disable();
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Add employee row
            |--------------------------------------------------------------------------
            */
            $('#btnAddRow').on('click', function() {

                /*
                |--------------------------------------------------------------------------
                | Clone FIRST row before Choices changes it
                |--------------------------------------------------------------------------
                */
                const firstRow = $('.employee-row:first')[0];

                /*
                | We create a clean row manually instead of cloning
                | the Choices-generated HTML.
                */
                const row = $(`
                    <div class="employee-row border rounded p-3 mb-3">

                        <div class="row align-items-end">

                            <div class="col-lg-2 col-md-4">
                                <div class="form-group mb-3">

                                    <label class="form-label font-size-13 text-muted">
                                        {{ __('forms.name') }}
                                    </label>

                                    <select class="form-select employee-name"
                                            name="cboName[]"
                                            required
                                            data-pristine-required-message="{{ __('messages.required') }}">

                                        <option value="">
                                            {{ __('forms.search...') }}
                                        </option>

                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->name_kh }} - {{ $emp->name_latin }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="form-group mb-3">

                                    <label class="form-label font-size-13 text-muted">
                                        {{ __('forms.position') }}
                                    </label>

                                    <select class="form-select employee-position"
                                            name="cboPosition[]"
                                            required
                                            data-pristine-required-message="{{ __('messages.required') }}">

                                        <option value="">
                                            {{ __('forms.search...') }}
                                        </option>

                                        @foreach ($positions as $pos)
                                            <option value="{{ $pos->id }}">
                                                {{ $pos->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="form-group mb-3">

                                    <label class="form-label font-size-13 text-muted d-block">
                                        {{ __('forms.assign') }}
                                    </label>

                                    <input type="hidden"
                                        class="assign-budget-value"
                                        name="assign_budget[]"
                                        value="0">

                                    <div class="form-check">

                                        <input type="checkbox"
                                            class="form-check-input assign-budget"
                                            name="assign_budget_checkbox[]"
                                            style="width:22px;height:22px;cursor:pointer;">

                                        <label class="form-check-label ms-2"
                                            style="padding-top:3px;cursor:pointer;">
                                            {{ __('forms.assign') }}
                                        </label>

                                    </div>
                                </div>
                            </div>

                           <div class="col-lg-2 col-md-4">
                                <button type="button"
                                    class="btn btn-danger btn-remove-row"
                                    >
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                $('#employeeRows').append(row);

                /*
                |--------------------------------------------------------------------------
                | Initialize Choices on new row
                |--------------------------------------------------------------------------
                */
                initEmployeeRow(row[0]);

                updateRemoveButtons();
            });


            /*
            |--------------------------------------------------------------------------
            | Remove employee row
            |--------------------------------------------------------------------------
            */
            $(document).on('click', '.btn-remove-row', function() {

                const row = $(this).closest('.employee-row')[0];

                if (!row) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Check whether the removed row was the leader
                |--------------------------------------------------------------------------
                */
                const wasLeader = row.querySelector('.employee-leader')?.checked ?? false;



                /*
                |--------------------------------------------------------------------------
                | Destroy Choices before removing
                |--------------------------------------------------------------------------
                */
                row.querySelectorAll('select').forEach(function(select) {
                    destroyChoices(select);
                });

                /*
                |--------------------------------------------------------------------------
                | Remove employee row
                |--------------------------------------------------------------------------
                */
                row.remove();

                /*
                |--------------------------------------------------------------------------
                | Update leader radio indexes
                |--------------------------------------------------------------------------
                */
                updateLeaderIndexes();


                /*
                |--------------------------------------------------------------------------
                | If the leader was removed, select the first employee as leader
                |--------------------------------------------------------------------------
                */
                if (wasLeader) {

                    const firstRow = document.querySelector(
                        '#employeeRows .employee-row:first-child'
                    );

                    if (firstRow) {

                        const firstLeader = firstRow.querySelector(
                            '.employee-leader'
                        );

                        if (firstLeader) {
                            firstLeader.checked = true;
                        }

                    }

                }
                /*
                   |--------------------------------------------------------------------------
                   | Update remove buttons
                   |--------------------------------------------------------------------------
                   */
                updateRemoveButtons();
            });


            /*
            |--------------------------------------------------------------------------
            | Update remove buttons
            |--------------------------------------------------------------------------
            */
            function updateRemoveButtons() {

                const rows = $('.employee-row');

                if (rows.length <= 1) {

                    rows.find('.btn-remove-row')
                        .prop('disabled', true);

                } else {

                    rows.find('.btn-remove-row')
                        .prop('disabled', false);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Initialize first row
            |--------------------------------------------------------------------------
            */
            document.querySelectorAll('.employee-row').forEach(function(row) {

                initEmployeeRow(row);

            });

            updateRemoveButtons();

        });

        /*
           |--------------------------------------------------------------------------
           | Update leader indexes
           |--------------------------------------------------------------------------
           */
        function updateLeaderIndexes() {

            document
                .querySelectorAll('#employeeRows .employee-row')
                .forEach(function(row, index) {

                    const leaderRadio = row.querySelector(
                        '.employee-leader'
                    );

                    if (leaderRadio) {
                        leaderRadio.value = index;
                    }

                });

        }
    </script>

    {{-- <script>
        $(document).on('change', '.assign-budget', function() {

            $('.assign-budget').not(this).prop('checked', false);
            $('.assign-budget-value').val(0);

            const row = $(this).closest('.employee-row');
            const hiddenInput = row.find('.assign-budget-value');

            hiddenInput.val(this.checked ? 1 : 0);
        });
    </script> --}}

    <script>
        // Handle assign budget checkbox for each employee row 
        $(document).on('change', '.assign-budget', function() {
            const row = $(this).closest('.employee-row');
            if ($(this).is(':checked')) { // Assign budget = 1 
                row.find('.assign-budget-value').val(1);
            } else { // Remove budget assignment 
                row.find('.assign-budget-value').val(0);
            }
        }); // When employee is selected/changed 
        $(document).on('change', '.employee-name', function() {
            const rows = $('.employee-row'); // If there is only ONE employee 
            if (rows.length === 1) {
                const row = $(this).closest('.employee-row'); // Automatically assign budget 
                row.find('.assign-budget-value').val(1);
                row.find('.assign-budget').prop('checked', true).prop('disabled', true);
            } else { // Multiple employees // Allow each employee to be selected independently
                $('.assign-budget').prop('disabled', false);
            }
        });
    </script>

    <script>
        $(document).on('change', '.employee-name', function() {

            // Get all employee rows
            const rows = $('.employee-row');

            // If mission has only ONE person
            if (rows.length === 1) {

                const row = $(this).closest('.employee-row');

                // Automatically assign budget = 1
                row.find('.assign-budget-value').val(1);

                // Optional: show checkbox as checked
                row.find('.assign-budget')
                    .prop('checked', true)
                    .prop('disabled', true);

            } else {

                // More than one person
                const row = $(this).closest('.employee-row');

                // Allow user to choose
                row.find('.assign-budget').prop('disabled', false);
            }
        });
    </script>

    {{-- Skip File Input --}}
    <script>
        const fileInput = document.getElementById('fileInput');
        const skipFileCheckbox = document.getElementById('skipFileInput');

        const setupSkipField = ({
            checkbox,
            input,
            defaultValue = '',
            restoreValidation = () => {}
        }) => {
            if (!checkbox || !input) return;

            checkbox.addEventListener('change', function() {
                const parentGroup = input.closest('.form-group');

                if (this.checked) {
                    input.value = '';
                    input.disabled = true;

                    input.classList.add('border-success', 'bg-success-subtle');
                    parentGroup?.classList.add('has-success');

                    input.removeAttribute('required');
                    input.removeAttribute('min');
                    input.removeAttribute('data-pristine-required-message');

                    pristine.reset(input);
                } else {
                    input.value = defaultValue;
                    input.disabled = false;

                    input.classList.remove('border-success', 'bg-success-subtle');
                    parentGroup?.classList.remove('has-success');

                    restoreValidation();
                }

                refreshPristine();
            });
        };

        setupSkipField({
            checkbox: skipFileCheckbox,
            input: fileInput,
            restoreValidation: () => {
                fileInput.setAttribute('required', true);
                fileInput.setAttribute('data-pristine-required-message', window.BudgetFormConfig
                    .translations.required);
            }
        });
    </script>

    {{-- Filter Program , Program-Sub, Cluster, Account-Sub --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProgram');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    {{-- Ajax fetch route --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========= Choices Instances =========
            let programSubChoices = new Choices('#cboProgramSub', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            let clusterChoices = new Choices('#cboCluster', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            // ========= Helpers =========
            function resetSelect(selector) {
                $(selector).html(`<option value="">{{ __('forms.search...') }}</option>`);
            }

            function resetChoices(selector, instance) {
                instance.destroy();
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "ស្វែងរក..."
                });
            }

            function loadOptions({
                url,
                data,
                targetSelect,
                instanceRefSetter
            }) {
                $.ajax({
                    url,
                    type: "GET",
                    data,
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter();
                    },
                    error: function() {
                        // optional: keep empty if error
                        resetSelect(targetSelect);
                    }
                });
            }

            // ========= Script 1: Program -> ProgramSub =========
            function handleProgramChangeForProgramSub(programId) {
                resetSelect('#cboProgramSub');
                programSubChoices = resetChoices('#cboProgramSub', programSubChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('missions.by.program_sub') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => {
                        programSubChoices = resetChoices('#cboProgramSub', programSubChoices);
                    }
                });
            }

            // ========= Script 2: Program -> Agency =========
            // function handleProgramChangeForAgency(programId) {
            //     resetSelect('#cboAgency');
            //     agencyChoices = resetChoices('#cboAgency', agencyChoices);

            //     if (!programId) return;

            //     loadOptions({
            //         url: "{{ route('missions.by.agency') }}",
            //         data: {
            //             program_id: programId
            //         },
            //         targetSelect: '#cboAgency',
            //         instanceRefSetter: () => {
            //             agencyChoices = resetChoices('#cboAgency', agencyChoices);
            //         }
            //     });
            // }

            // ========= Script 3: ProgramSub -> Cluster =========
            function handleProgramSubChangeForCluster(programSubId) {
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);

                if (!programSubId) return;

                loadOptions({
                    url: "{{ route('missions.by.cluster') }}",
                    data: {
                        program_sub_id: programSubId
                    },
                    targetSelect: '#cboCluster',
                    instanceRefSetter: () => {
                        clusterChoices = resetChoices('#cboCluster', clusterChoices);
                    }
                });
            }

            // ========= Events =========
            $('#cboProgram').on('change', function() {
                const programId = $(this).val();

                // when program changes -> always clear cluster too
                handleProgramChangeForProgramSub(programId);
                // handleProgramChangeForAgency(programId);
                handleProgramSubChangeForCluster(null); // reset cluster
            });

            $('#cboProgramSub').on('change', function() {
                const programSubId = $(this).val();
                handleProgramSubChangeForCluster(programSubId);
            });

        });
    </script>
@endsection
