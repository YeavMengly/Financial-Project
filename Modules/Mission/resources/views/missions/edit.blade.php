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

        .employee-account-number.is-invalid,
        .payment-account-number.is-invalid {
            border-color: #dc3545 !important;
        }

        .employee-account-number.is-valid,
        .payment-account-number.is-valid {
            border-color: #198754 !important;
        }
    </style>
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

                            <li class="breadcrumb-item active"><a
                                    href="javascript: void(0);">{{ __('menus.content.missions') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
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
                        <form id="pristine-valid-example" novalidate method="POST"
                            action="{{ route('missions.update', ['params' => $params, 'id' => $mission->id]) }}"
                            autocomplete="off">
                            @csrf
                            <div class="row">

                                <div class="row">

                                    {{-- Document --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="cboDocument" class="form-label font-size-13 text-muted">
                                                ជ្រើសរើស ប្រភេទឯកសារបេសកកម្ម
                                            </label>

                                            <select class="form-select" id="cboDocument" name="cboDocument" required
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                                <option value="">
                                                    {{ __('forms.search...') }}
                                                </option>

                                                @foreach ($document as $doc)
                                                    <option value="{{ $doc->id }}"
                                                        {{ old('cboDocument', $mission->document_id) == $doc->id ? 'selected' : '' }}>
                                                        {{ $doc->name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            @error('cboDocument')
                                                <div class="pristine-error text-help">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>


                                    {{-- Legal Number --}}
                                    <div class="col-xl-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="legal_number" class="form-label font-size-13 text-muted">
                                                {{ __('forms.legal.id') }}
                                            </label>

                                            <input type="number" min="1" id="legal_number" name="legal_number"
                                                class="form-control" required
                                                value="{{ old('legal_number', $mission->legal_number) }}"
                                                data-pristine-required-message="{{ __('messages.required') }}"
                                                data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                                data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" tabindex="1">

                                        </div>
                                    </div>


                                    {{-- Legal Date --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="legal_date" class="form-label font-size-13 text-muted">
                                                {{ __('forms.select_legal_date') }}
                                            </label>

                                            <input type="text" id="legal_date" name="legal_date" class="form-control"
                                                placeholder="{{ __('forms.select_legal_date') }}"
                                                value="{{ old('legal_date', $mission->legal_date ? \Carbon\Carbon::parse($mission->legal_date)->format('Y-m-d') : '') }}"
                                                required data-pristine-required-message="{{ __('messages.required') }}"
                                                tabindex="2">

                                        </div>
                                    </div>


                                    {{-- Start Date --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="start_date" class="form-label font-size-13 text-muted">
                                                {{ __('menus.start_date') }}
                                            </label>

                                            <input type="text" id="start_date" name="start_date" class="form-control"
                                                required placeholder="{{ __('forms.select_date') }}"
                                                value="{{ old('start_date', $mission->start_date ? \Carbon\Carbon::parse($mission->start_date)->format('Y-m-d') : '') }}"
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                        </div>
                                    </div>


                                    {{-- End Date --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="end_date" class="form-label font-size-13 text-muted">
                                                {{ __('menus.end_date') }}
                                            </label>

                                            <input type="text" id="end_date" name="end_date" class="form-control"
                                                required placeholder="{{ __('forms.select_date') }}"
                                                value="{{ old('end_date', $mission->end_date ? \Carbon\Carbon::parse($mission->end_date)->format('Y-m-d') : '') }}"
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                        </div>
                                    </div>


                                    {{-- Province --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="cboProvince" class="form-label font-size-13 text-muted">
                                                ទីកន្លែង {{ __('forms.province') }}
                                            </label>

                                            <select class="form-select" id="cboProvince" name="cboProvince" required
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                                <option value="">
                                                    {{ __('forms.search...') }}
                                                </option>

                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}"
                                                        {{ old('cboProvince', $mission->province_id) == $province->id ? 'selected' : '' }}>
                                                        {{ $province->name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            @error('cboProvince')
                                                <div class="pristine-error text-help">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>

                                </div>


                                <div class="row">

                                    {{-- File --}}
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

                                            <input type="file" id="fileInput" name="fileName" class="form-control"
                                                accept=".pdf,.doc,.docx" data-allowed-extensions="pdf,doc,docx">

                                            <small class="form-text text-muted">
                                                Allowed types: PDF (Max: 10MB per file)
                                            </small>

                                            {{-- Existing file --}}
                                            @if ($mission->fileName)
                                                <div class="mt-2">

                                                    <small class="text-muted">
                                                        ឯកសារបច្ចុប្បន្ន:
                                                    </small>

                                                    <a href="{{ asset('storage/' . $mission->fileName) }}"
                                                        target="_blank" class="ms-1">
                                                        មើលឯកសារ
                                                    </a>

                                                </div>
                                            @endif

                                            @error('fileName')
                                                <div class="pristine-error text-help text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>


                                    {{-- Program --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="cboProgram" class="form-label font-size-13 text-muted">
                                                {{ __('forms.program') }}
                                            </label>

                                            <select class="form-select" id="cboProgram" name="cboProgram">

                                                <option value="">
                                                    {{ __('forms.search...') }}
                                                </option>

                                                @foreach ($program as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('cboProgram', $mission->program_id) == $p->id ? 'selected' : '' }}>
                                                        {{ $p->no }} - {{ $p->title }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            @error('cboProgram')
                                                <div class="pristine-error text-help">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>


                                    {{-- Program Sub --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="cboProgramSub" class="form-label font-size-13 text-muted">
                                                {{ __('forms.program.sub') }}
                                            </label>

                                            <select id="cboProgramSub" class="form-select" name="cboProgramSub">

                                                <option value="">
                                                    {{ __('forms.search...') }}
                                                </option>

                                            </select>

                                        </div>
                                    </div>


                                    {{-- Cluster --}}
                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">

                                            <label for="cboCluster" class="form-label font-size-13 text-muted">
                                                {{ __('forms.cluster') }}
                                            </label>

                                            <select id="cboCluster" class="form-select" name="cboCluster">

                                                <option value="">
                                                    {{ __('forms.search...') }}
                                                </option>

                                            </select>

                                            @error('cboCluster')
                                                <div class="pristine-error text-help">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="cboSubAccount" class="form-label text-muted">
                                                {{ __('forms.sub.account') }}
                                            </label>
                                            <select class="form-control" id="cboSubAccount" name="cboSubAccount"
                                                data-pristine-required-message="{{ __('messages.required') }}">
                                                <option value="">{{ __('forms.search...') }}</option>
                                                @foreach ($accountSub as $as)
                                                    <option
                                                        value="{{ $as->id }} {{ old('cboSubAccount', $mission->sub_account_id) == $as->id ? 'selected' : '' }}">
                                                        {{ $as->no }} -
                                                        {{ $as->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('cboSubAccount')
                                                <div class="pristine-error text-help">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="form-group mb-3">

                                            <label for="vDescription">
                                                {{ __('forms.document.description') }}
                                            </label>

                                            <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                                data-pristine-required-message="{{ __('messages.required') }}">{{ old('txtDescription', $mission->description) }}</textarea>

                                            @error('txtDescription')
                                                <div class="pristine-error text-help">
                                                    {{ $message }}
                                                </div>
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

                                                @foreach ($missionEmployees as $index => $missionEmployee)
                                                    <div class="employee-row border rounded p-3 mb-3">

                                                        <div class="row align-items-end">

                                                            {{-- Employee --}}
                                                            <div class="col-lg-2 col-md-4">
                                                                <div class="form-group mb-3">

                                                                    <label class="form-label font-size-13 text-muted">
                                                                        {{ __('forms.name') }}
                                                                    </label>

                                                                    <select class="form-select employee-name"
                                                                        name="cboName[]" required
                                                                        data-pristine-required-message="{{ __('messages.required') }}">

                                                                        <option value="">
                                                                            {{ __('forms.search...') }}
                                                                        </option>

                                                                        @foreach ($employees as $emp)
                                                                            <option value="{{ $emp->id }}"
                                                                                {{ old('cboName.' . $index, $missionEmployee->employee_id) == $emp->id ? 'selected' : '' }}>
                                                                                {{ $emp->name_kh }} -
                                                                                {{ $emp->name_latin }}
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
                                                                            <option value="{{ $pos->id }}"
                                                                                {{ old('cboPosition.' . $index, $missionEmployee->position_id) == $pos->id ? 'selected' : '' }}>
                                                                                {{ $pos->name }}
                                                                            </option>
                                                                        @endforeach

                                                                    </select>

                                                                </div>
                                                            </div>

                                                            {{-- Own Account Number --}}
                                                            <div class="col-lg-2 col-md-4">
                                                                <div class="form-group mb-3">

                                                                    <label class="form-label font-size-13 text-muted">
                                                                        {{ __('forms.account.number') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>

                                                                    <input type="text"
                                                                        class="form-control employee-account-number"
                                                                        name="account_number[]"
                                                                        value="{{ $missionEmployee->employee->account_number ?? '' }}"
                                                                        readonly required
                                                                        data-pristine-required-message="{{ __('messages.required') }}">

                                                                </div>
                                                            </div>

                                                            {{-- Payment Account --}}
                                                            <div class="col-lg-2 col-md-4">
                                                                <div class="form-group mb-3">

                                                                    <label class="form-label font-size-13 text-muted">
                                                                        {{ __('forms.payment.account') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>

                                                                    <select class="form-select payment-account-employee"
                                                                        name="payment_account_employee_id[]" required
                                                                        data-pristine-required-message="{{ __('messages.required') }}"
                                                                        data-selected-payment-account="{{ $missionEmployee->payment_account_employee_id ?? '' }}">

                                                                        <option value="">
                                                                            {{ __('forms.search...') }}
                                                                        </option>

                                                                        @foreach ($employees as $emp)
                                                                            <option value="{{ $emp->id }}"
                                                                                data-account-number="{{ $emp->account_number ?? '' }}"
                                                                                @selected((string) ($missionEmployee->payment_account_employee_id ?? '') === (string) $emp->id)>
                                                                                {{ $emp->name_kh }} -
                                                                                {{ $emp->name_latin }} -
                                                                                {{ $emp->account_number ?? '' }}
                                                                            </option>
                                                                        @endforeach

                                                                    </select>

                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                                <div class="form-group mb-3">

                                                                    @php
                                                                        $leaderExists =
                                                                            !empty($mission->leader_id) &&
                                                                            $missionEmployees->contains(
                                                                                'employee_id',
                                                                                $mission->leader_id,
                                                                            );

                                                                        $isLeader = $leaderExists
                                                                            ? $mission->leader_id ==
                                                                                $missionEmployee->employee_id
                                                                            : $index === 0;
                                                                    @endphp

                                                                    <div class="d-flex align-items-end flex-wrap gap-4">

                                                                        {{-- Leader --}}
                                                                        @if ($isLeader)
                                                                            <div>
                                                                                <label
                                                                                    class="form-label font-size-13 text-muted d-block">
                                                                                    {{ __('forms.leader') }}
                                                                                </label>

                                                                                <div class="form-check">
                                                                                    <input type="radio"
                                                                                        class="form-check-input employee-leader"
                                                                                        name="leader_index"
                                                                                        value="{{ $index }}"
                                                                                        checked
                                                                                        style="width:22px;height:22px;cursor:pointer;">

                                                                                    <label class="form-check-label ms-2"
                                                                                        style="padding-top:3px;cursor:pointer;">
                                                                                        {{ __('forms.leader') }}
                                                                                    </label>
                                                                                </div>

                                                                            </div>
                                                                        @endif
                                                                        {{-- Assign Budget --}}
                                                                        <div>
                                                                            <label
                                                                                class="form-label font-size-13 text-muted d-block">
                                                                                {{ __('forms.assign') }}
                                                                            </label>

                                                                            <input type="hidden"
                                                                                class="assign-budget-value"
                                                                                name="assign_budget[]"
                                                                                value="{{ $missionEmployee->assign_budget ?? 0 }}">

                                                                            <div class="form-check">
                                                                                <input type="checkbox"
                                                                                    class="form-check-input assign-budget"
                                                                                    name="assign_budget_checkbox[]"
                                                                                    style="width:22px;height:22px;cursor:pointer;"
                                                                                    {{ ($missionEmployee->assign_budget ?? 0) == 1 ? 'checked' : '' }}>

                                                                                <label class="form-check-label ms-2"
                                                                                    style="padding-top:3px;cursor:pointer;">
                                                                                    {{ __('forms.assign') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Remove --}}
                                                            <div class="col-lg-2 col-md-4">

                                                                {{-- Remove Button (Hidden for index 0 / First Row) --}}
                                                                {{-- <div class="col-lg-2 col-md-4"> --}}
                                                                @if (!$loop->first)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-remove-row"
                                                                        data-id="{{ $missionEmployee->id ?? '' }}">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                                @endif
                                                                {{-- </div> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">

                                <button class="btn btn-primary" type="submit" name="submit" value="save">
                                    {{ __('buttons.save') }}
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    {{ __('buttons.delete') }}
                                </a>

                                <a class="btn btn-dark" href="{{ route('missions.index', $params) }}">
                                    {{ __('buttons.back') }}
                                </a>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3"></div>

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
    {{-- <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script> --}}

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
            const element = document.getElementById('cboSubAccount');
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

    {{-- Laod request to save --}}
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
            | Get all employees currently selected in the mission
            |--------------------------------------------------------------------------
            */
            function getMissionEmployees() {

                const employees = [];

                document
                    .querySelectorAll('#employeeRows .employee-row')
                    .forEach(function(row) {

                        const employeeSelect =
                            row.querySelector('.employee-name');

                        if (!employeeSelect) {
                            return;
                        }

                        const employeeId =
                            employeeSelect.value;

                        if (!employeeId) {
                            return;
                        }

                        const selectedOption =
                            employeeSelect.options[
                                employeeSelect.selectedIndex
                            ];

                        const employeeName =
                            selectedOption?.textContent.trim() || '';

                        const accountInput =
                            row.querySelector(
                                '.employee-account-number'
                            );

                        const accountNumber =
                            accountInput?.value ||
                            selectedOption?.getAttribute(
                                'data-account-number'
                            ) ||
                            '';

                        employees.push({
                            id: String(employeeId),
                            name: employeeName,
                            account_number: accountNumber
                        });

                    });
                return employees;

                document.querySelectorAll('.employee-row').forEach(function(row) {
                    const employeeSelect = row.querySelector('.employee-name');
                    const accountInput = row.querySelector('.employee-account-number');
                    const paymentSelect = row.querySelector('.payment-account-employee');

                    // Restore the employee's own account number.
                    if (employeeSelect && accountInput) {
                        const selectedEmployee =
                            employeeSelect.options[employeeSelect.selectedIndex];

                        accountInput.value =
                            selectedEmployee?.dataset.accountNumber || '';
                    }

                    // Restore the saved payment account.
                    if (paymentSelect) {
                        const savedPaymentId =
                            paymentSelect.dataset.selectedPaymentAccount || '';

                        if (savedPaymentId) {
                            paymentSelect.value = savedPaymentId;
                        }
                    }
                });

                // Update own account number when employee changes.
                $(document).on('change', '.employee-name', function() {
                    const row = this.closest('.employee-row');
                    const selectedOption = this.options[this.selectedIndex];

                    row.querySelector('.employee-account-number').value =
                        selectedOption?.dataset.accountNumber || '';
                });

            }


            function updateEmployeeAccount(row) {

                const employeeSelect = row.querySelector('.employee-name');
                const accountInput = row.querySelector('.employee-account-number');

                if (!employeeSelect || !accountInput) {
                    return;
                }

                const employeeId = employeeSelect.value;

                // Clear the previous account number
                accountInput.value = '';

                if (!employeeId) {
                    return;
                }

                $.ajax({
                    url: "{{ route('missions.paymentAccount') }}",
                    type: "GET",
                    data: {
                        payment_account_employee_id: employeeId
                    },

                    success: function(response) {

                        // Avoid displaying a previous employee's response
                        if (employeeSelect.value != employeeId) {
                            return;
                        }

                        if (response.success && response.employee) {
                            accountInput.value =
                                response.employee.account_number || '';
                        } else {
                            accountInput.value = '';
                        }
                    },

                    error: function(xhr) {
                        console.error(
                            'Failed to load employee account number:',
                            xhr.responseText
                        );

                        accountInput.value = '';
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Update Payment Account options
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Person 1 -> Person 2 / ACC-002
            | Person 2 -> Person 2 / ACC-002
            | Person 3 -> Person 3 / ACC-003
            |
            |--------------------------------------------------------------------------
            */

            function updatePaymentAccountOptions() {

                const employees =
                    getMissionEmployees();


                document
                    .querySelectorAll(
                        '#employeeRows .employee-row'
                    )
                    .forEach(function(row) {

                        const employeeSelect =
                            row.querySelector('.employee-name');

                        const paymentSelect =
                            row.querySelector(
                                '.payment-account-employee'
                            );

                        if (!employeeSelect || !paymentSelect) {
                            return;
                        }

                        const currentEmployeeId =
                            String(
                                employeeSelect.value || ''
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | Remember existing payment account
                        |--------------------------------------------------------------------------
                        */
                        const oldValue =
                            paymentSelect.value;


                        /*
                        |--------------------------------------------------------------------------
                        | Get Choices instance
                        |--------------------------------------------------------------------------
                        */
                        const paymentChoices =
                            choicesInstances.get(
                                paymentSelect
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Build Choices options
                        |--------------------------------------------------------------------------
                        */
                        const choiceItems = [

                            {
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                disabled: false
                            }

                        ];


                        employees.forEach(function(employee) {

                            choiceItems.push({

                                value: employee.id,

                                label: employee.name +
                                    ' - ' +
                                    employee.account_number,

                                customProperties: {
                                    account_number: employee.account_number
                                }

                            });

                        });


                        /*
                        |--------------------------------------------------------------------------
                        | Update Choices
                        |--------------------------------------------------------------------------
                        */
                        if (paymentChoices) {

                            paymentChoices.clearChoices();

                            paymentChoices.setChoices(
                                choiceItems,
                                'value',
                                'label',
                                true
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Keep old payment account if still valid
                            |--------------------------------------------------------------------------
                            */
                            const oldValueExists =
                                employees.some(function(employee) {

                                    return employee.id ===
                                        String(oldValue);

                                });


                            if (
                                oldValue &&
                                oldValueExists
                            ) {

                                paymentChoices
                                    .setChoiceByValue(
                                        String(oldValue)
                                    );

                            }
                            /*
                            |--------------------------------------------------------------------------
                            | Default = employee's own account
                            |--------------------------------------------------------------------------
                            */
                            else if (currentEmployeeId) {

                                paymentChoices
                                    .setChoiceByValue(
                                        currentEmployeeId
                                    );

                            }

                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | Fallback if Choices isn't initialized
                            |--------------------------------------------------------------------------
                            */
                            paymentSelect.innerHTML = `
                            <option value="">
                                {{ __('forms.search...') }}
                            </option>
                        `;


                            employees.forEach(function(employee) {

                                const option =
                                    document.createElement('option');

                                option.value =
                                    employee.id;

                                option.textContent =
                                    employee.name +
                                    ' - ' +
                                    employee.account_number;

                                option.setAttribute(
                                    'data-account-number',
                                    employee.account_number
                                );

                                paymentSelect.appendChild(option);

                            });


                            if (oldValue) {

                                paymentSelect.value =
                                    oldValue;

                            } else if (currentEmployeeId) {

                                paymentSelect.value =
                                    currentEmployeeId;

                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Display payment account number
                        |--------------------------------------------------------------------------
                        */
                        updatePaymentAccountNumber(row);

                    });
            }

            function updatePaymentAccountNumber(row) {

                const paymentSelect =
                    row.querySelector(
                        '.payment-account-employee'
                    );

                const display =
                    row.querySelector(
                        '.payment-account-number'
                    );

                if (!paymentSelect || !display) {
                    return;
                }


                const selectedValue =
                    paymentSelect.value;

                if (!selectedValue) {

                    display.textContent = '';

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Find employee from current mission
                |--------------------------------------------------------------------------
                */
                const employees =
                    getMissionEmployees();

                const selectedEmployee =
                    employees.find(function(employee) {

                        return employee.id ===
                            String(selectedValue);

                    });


                if (selectedEmployee) {

                    display.textContent =
                        'Account: ' +
                        selectedEmployee.account_number;

                } else {

                    display.textContent = '';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Initialize employee row
            |--------------------------------------------------------------------------
            */
            function initEmployeeRow(row) {

                const employeeSelect =
                    row.querySelector('.employee-name');

                const positionSelect =
                    row.querySelector('.employee-position');

                const levelSelect =
                    row.querySelector('.employee-level');

                const paymentAccountSelect =
                    row.querySelector(
                        '.payment-account-employee'
                    );


                /*
                |--------------------------------------------------------------------------
                | Initialize Choices
                |--------------------------------------------------------------------------
                */
                initChoices(employeeSelect);

                initChoices(positionSelect);

                initChoices(levelSelect);

                initChoices(paymentAccountSelect);


                /*
                |--------------------------------------------------------------------------
                | Employee validation + Account
                |--------------------------------------------------------------------------
                */
                employeeSelect?.addEventListener(
                    'change',
                    function() {

                        updateValidation(this);

                        /*
                         * Update own account number.
                         */
                        updateEmployeeAccount(row);

                        /*
                         * Update payment accounts
                         * for every employee.
                         */
                        updatePaymentAccountOptions();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Position -> Level AJAX
                |--------------------------------------------------------------------------
                */
                positionSelect?.addEventListener(
                    'change',
                    function() {

                        updateValidation(this);

                        const positionId =
                            this.value;

                        loadLevel(
                            row,
                            positionId
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Level validation
                |--------------------------------------------------------------------------
                */
                levelSelect?.addEventListener(
                    'change',
                    function() {

                        updateValidation(this);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Payment Account changed
                |--------------------------------------------------------------------------
                */
                paymentAccountSelect?.addEventListener(
                    'change',
                    function() {

                        updateValidation(this);

                        updatePaymentAccountNumber(row);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Assign budget
                |--------------------------------------------------------------------------
                */
                const checkbox =
                    row.querySelector('.assign-budget');

                const hiddenValue =
                    row.querySelector('.assign-budget-value');

                checkbox?.addEventListener(
                    'change',
                    function() {

                        if (hiddenValue) {

                            hiddenValue.value =
                                this.checked ? '1' : '0';

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Initialize existing employee account
                |--------------------------------------------------------------------------
                */
                updateEmployeeAccount(row);

            }
            /*
            |--------------------------------------------------------------------------
            | Input validation border
            |--------------------------------------------------------------------------
            */
            function updateInputValidation(input) {

                if (!input) {
                    return false;
                }

                const value =
                    input.value ?
                    input.value.trim() :
                    '';

                const isValid =
                    value !== '';


                if (isValid) {

                    input.classList.remove(
                        'is-invalid'
                    );

                    input.classList.add(
                        'is-valid'
                    );

                } else {

                    input.classList.remove(
                        'is-valid'
                    );

                    input.classList.add(
                        'is-invalid'
                    );
                }

                return isValid;
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

                            {{-- Employee --}}
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

                            {{-- Position --}}
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

                                 {{-- Own Account Number --}}
                            <div class="col-lg-2 col-md-4">

                                <div class="form-group mb-3">

                                    <label class="form-label font-size-13 text-muted">
                                        {{ __('forms.account.number') }}
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control employee-account-number"
                                        name="account_number[]"
                                        value=""
                                        readonly>

                                </div>

                            </div>

                            {{-- Payment Account --}}
                            <div class="col-lg-2 col-md-4">

                                <div class="form-group mb-3">

                                    <label class="form-label font-size-13 text-muted">
                                        {{ __('forms.payment.account') }}
                                    </label>

                                    <select
                                        class="form-select payment-account-employee"
                                        name="payment_account_employee_id[]">

                                        <option value="">
                                            {{ __('forms.search...') }}
                                        </option>

                                    </select>
                                </div>

                            </div>
                              
                            {{-- Assign Budget --}}
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
                                        class="btn btn-danger btn-remove-row">

                                    <i class="bx bx-trash"></i>

                                </button>

                            </div>

                        </div>

                    </div>
                `);

                /*
                  |--------------------------------------------------------------------------
                  | Add row to DOM
                  |--------------------------------------------------------------------------
                  */
                $('#employeeRows').append(row);

                /*
                |--------------------------------------------------------------------------
                | Initialize Choices on new row
                |--------------------------------------------------------------------------
                */
                initEmployeeRow(row[0]);
                /*
                |--------------------------------------------------------------------------
                | IMPORTANT:
                | Update payment accounts after new row exists
                |--------------------------------------------------------------------------
                */
                updatePaymentAccountOptions();
                /*
                |--------------------------------------------------------------------------
                | Update remove buttons
                |--------------------------------------------------------------------------
                */

                updateRemoveButtons();
            });

            function updateEmployeeRowNumbers() {
                $('.employee-row').each(function(index) {
                    $(this).find('.row-number').text(index + 1);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Remove employee row
            |--------------------------------------------------------------------------
            */
            $(document).on('click', '.btn-remove-row', function() {
                const button = $(this);
                const row = button.closest('.employee-row');
                const rows = $('.employee-row'); // Get database ID 
                const recordId = button.attr(
                    'data-id'
                ); // ===================================================== // PREVENT DELETING FIRST ROW // ===================================================== 
                if (rows.index(row) === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'មិនអាចលុបបានទេ',
                        text: 'មិនអាចលុបជួរទី ១ បានទេ។',
                        confirmButtonText: 'យល់ព្រម'
                    });
                    return;
                } // ===================================================== // NEW UNSAVED ROW // ===================================================== 
                if (!recordId) { // Destroy Choices before removing row
                    row.find('select').each(function() {
                        if (typeof destroyChoices === 'function') {
                            destroyChoices(this);
                        }
                    });
                    row.remove();
                    updateLeaderIndexes();
                    assignFirstRowAsLeader();
                    updateRemoveButtons();
                    checkSingleRowState();
                    updateEmployeeRowNumbers();
                    return;
                } // ===================================================== // EXISTING DATABASE RECORD // ===================================================== 
                Swal.fire({
                    title: 'តើអ្នកពិតជាចង់លុបមែនទេ?',
                    text: 'ទិន្នន័យនេះនឹងត្រូវលុបចេញពីប្រព័ន្ធ!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'បាទ/ចាស លុប',
                    cancelButtonText: 'បោះបង់',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    } // Generate Laravel delete URL 
                    let deleteUrl =
                        "{{ route('missions.employee.destroy', ['params' => $params, 'id' => '__ID__']) }}";
                    deleteUrl = deleteUrl.replace('__ID__',
                        recordId
                    ); // ================================================= // SHOW LOADING // ================================================= 
                    button.prop('disabled', true);
                    button.html(
                        '<span class="spinner-border spinner-border-sm"></span>'
                    ); // ================================================= // DELETE FROM DATABASE // ================================================= 
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(
                            response
                        ) { // ========================================= // REMOVE ROW IMMEDIATELY AFTER DB SUCCESS // ========================================= 
                            row.find('select').each(function() {
                                if (typeof destroyChoices === 'function') {
                                    destroyChoices(this);
                                }
                            });
                            row.remove(); // Recalculate UI 
                            updateLeaderIndexes();
                            assignFirstRowAsLeader();
                            updateRemoveButtons();
                            checkSingleRowState();
                            updateEmployeeRowNumbers(); // Success message
                            Swal.fire({
                                icon: 'success',
                                title: 'បានលុបជោគជ័យ',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        },
                        error: function(xhr) {
                            console.error('Delete failed:', xhr.status, xhr
                                .responseText); // Restore button 
                            button.prop('disabled', false);
                            button.html('<i class="fa fa-trash"></i>');
                            Swal.fire({
                                icon: 'error',
                                title: 'មានបញ្ហា',
                                text: 'មិនអាចលុបទិន្នន័យបានទេ។ សូមព្យាយាមម្ដងទៀត។',
                                confirmButtonText: 'យល់ព្រម'
                            });
                        }
                    });
                });
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

            updateRemoveButtons();

            /*
            |--------------------------------------------------------------------------
            | Initialize searchable dropdowns for existing employee rows
            |--------------------------------------------------------------------------
            */
            $(document).ready(function() {

                document.querySelectorAll('.employee-row').forEach(function(row) {
                    initEmployeeRow(row);
                });

            });

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Build payment account options
            | after all initial rows are initialized.
            |--------------------------------------------------------------------------
            */
            updatePaymentAccountOptions();

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

    {{-- Assign Budget to Members --}}
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

    {{-- Load Payment Account --}}
    <script>
        function getPaymentAccount(row) {

            const paymentSelect =
                row.find('.payment-account-employee');

            const paymentEmployeeId =
                paymentSelect.val();

            const accountDisplay =
                row.find('.payment-account-number');

            if (!paymentEmployeeId) {

                accountDisplay.text('');

                return;
            }

            $.ajax({
                url: "{{ route('missions.paymentAccount') }}",
                type: "GET",

                data: {
                    payment_account_employee_id: paymentEmployeeId
                },

                success: function(response) {

                    if (
                        response.success &&
                        response.employee
                    ) {

                        accountDisplay.text(
                            'Account: ' +
                            response.employee.account_number
                        );

                    } else {

                        accountDisplay.text('');

                    }
                },

                error: function(xhr) {

                    console.error(
                        'Payment account error:',
                        xhr.responseText
                    );

                    accountDisplay.text('');
                }
            });

            $('.payment-account-employee').each(function() {
                const select = this;
                const savedId = String(
                    $(select).attr('data-selected-payment-account') || ''
                );

                if (savedId && $(select).find('option').filter(function() {
                        return String(this.value) === savedId;
                    }).length) {
                    $(select).val(savedId).trigger('change');
                }
            });
        }
    </script>
@endsection
