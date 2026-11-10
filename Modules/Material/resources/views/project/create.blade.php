@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        /* =========================================================
                                 * TABLE WRAPPER & SCROLL FIXES
                                 * ========================================================= */
        #itemTableWrapper,
        .table-responsive {
            width: 100% !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
            position: relative !important;
            display: block;
        }

        #itemTableWrapper {
            overflow-x: auto !important;
            padding-top: 300px !important;
            margin-top: -300px !important;
        }

        #itemTable {
            table-layout: auto !important;
            width: 100% !important;
            min-width: 1300px !important;
        }

        #itemTable th {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        #itemTable td {
            vertical-align: middle !important;
            padding: 6px 8px !important;
            overflow: visible !important;
            position: relative !important;
            pointer-events: auto !important;
        }

        #itemTable input.form-control,
        .form-group input,
        .form-group textarea {
            pointer-events: auto !important;
            position: relative;
            z-index: 5;
        }

        /* Base style when switch is unchecked */
        .skip-label {
            color: #495057;
            transition: all 0.2s ease-in-out;
        }

        /* Bold blue highlight when switch is checked */
        .form-check-input:checked~.skip-label {
            color: #223fe6 !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        /* =========================================================
                                 * CHOICES.JS STYLING & STRICT INPUT-WIDTH CONTROL
                                 * ========================================================= */
        .choices {
            margin-bottom: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            position: relative !important;
            overflow: visible !important;
        }

        .choices__inner {
            min-height: 38px !important;
            height: 38px !important;
            padding: 2px 8px !important;
            font-size: 13px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            background-color: #fff !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        .choices__list--single {
            padding: 0 !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .choices__list--single .choices__item {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 34px !important;
            width: 100% !important;
            float: none !important;
        }

        .choices.is-open .choices__list--dropdown,
        .choices__list--dropdown {
            position: absolute !important;
            bottom: calc(100% + 6px) !important;
            top: auto !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            transform: none !important;
            z-index: 9999999 !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            box-shadow: 0 -15px 35px rgba(0, 0, 0, 0.3) !important;
            background-color: #ffffff !important;
            margin-bottom: 4px !important;
            box-sizing: border-box !important;
        }

        .choices__list--dropdown .choices__item--selectable {
            padding: 8px 10px !important;
            font-size: 13px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0b5ed7 !important;
            color: #ffffff !important;
        }

        .choices__list--dropdown .choices__input {
            font-size: 13px !important;
            padding: 6px 10px !important;
            margin-bottom: 6px !important;
            border-radius: 3px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #itemTableWrapper.table-skipped {
            opacity: 0.6;
        }

        #itemTableWrapper.table-skipped .choices {
            pointer-events: none;
            background-color: #e9ecef !important;
        }

        /* =========================================================
                 * FIX SWITCH CLICKABILITY & OVERLAPS
                 * ========================================================= */
        .form-check.form-switch {
            position: relative !important;
            z-index: 100 !important;
            pointer-events: auto !important;
        }

        .form-check-input {
            cursor: pointer !important;
            position: relative !important;
            z-index: 101 !important;
        }

        .skip-label {
            cursor: pointer !important;
            position: relative !important;
            z-index: 101 !important;
        }

        /* Fix negative margin container overlap */
        #itemTableWrapper {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
@endsection

@section('content')
    <!-- PAGE TITLE -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.project') }}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ __('menus.project') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ __('menus.entry') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $ministry->year ?? '' }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('buttons.create') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM CARD SECTION -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" action="{{ route('project.store', $params) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- =====================================================
                            SECTION 1: MAIN INFORMATION
                        ====================================================== --}}
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                    <input type="text" id="stock_number" name="stock_number" required
                                        class="form-control" tabindex="1"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('stock_number') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                    <input type="text" id="stock_name" name="stock_name" required class="form-control"
                                        tabindex="2" data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('stock_name') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="company_name">{{ __('forms.company.name') }}</label>
                                    <input type="text" id="company_name" name="company_name" required
                                        class="form-control" tabindex="3"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('company_name') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_voucher">
                                        កាលបរិច្ឆេទ{{ __('forms.warehouse.voucher') }}ក្រុមហ៊ុន
                                    </label>
                                    <input type="text" id="warehouse_voucher" name="warehouse_voucher" required
                                        class="form-control" tabindex="4"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('warehouse_voucher') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                    <input type="text" id="user_entry" name="user_entry" required class="form-control"
                                        tabindex="5" data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('user_entry') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_receiver">{{ __('forms.receiver') }}</label>
                                    <input type="text" id="user_receiver" name="user_receiver" required
                                        class="form-control" tabindex="6"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('user_receiver') }}">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date" class="form-label">
                                        {{ __('forms.date') }}បញ្ចូលឃ្លាំង
                                    </label>
                                    <input type="text" id="date" name="date" class="form-control" tabindex="7"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('date') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="titleInput" class="form-label mb-0">
                                            {{ __('forms.title') }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipTitleInput" style="cursor:pointer;">
                                            <label class="form-check-label font-size-12 text-muted skip-label"
                                                for="skipTitleInput" style="cursor:pointer;">
                                                រំលង / មិនភ្ជាប់
                                            </label>
                                        </div>
                                    </div>
                                    <input type="text" id="titleInput" name="title" class="form-control"
                                        tabindex="8" data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('title')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="fileInput" class="form-label mb-0">
                                            {{ __('forms.file.type') }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipFileInput" style="cursor:pointer;">
                                            <label class="form-check-label font-size-12 text-muted skip-label"
                                                for="skipFileInput" style="cursor:pointer;">
                                                រំលង / មិនភ្ជាប់
                                            </label>
                                        </div>
                                    </div>
                                    <input type="file" id="fileInput" name="file[]" class="form-control"
                                        accept=".pdf,.doc,.docx" multiple data-max-size="5"
                                        data-allowed-extensions="pdf,doc,docx">

                                    <small class="form-text text-muted">
                                        Allowed types: PDF, DOC, DOCX (Max: 5MB per file)
                                    </small>

                                    @error('file.*')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- =====================================================
                            SECTION 2: PROJECT TABLE
                        ====================================================== --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">
                                {{ __('forms.sub.project') }}
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="skipItemTable"
                                    name="skipItemTable" value="1" style="cursor:pointer;"
                                    {{ old('skipItemTable') ? 'checked' : '' }}>
                                <label class="form-check-label font-size-12 text-muted skip-label" for="skipItemTable">
                                    រំលង / មិនភ្ជាប់
                                </label>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-12 mt-3" id="itemTableWrapper">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-nowrap" id="itemTable">
                                    <thead>
                                        <tr>
                                            <th style="width:200px; min-width:200px;" class="align-middle">
                                                {{ __('forms.project') }}
                                            </th>
                                            <th style="width:200px; min-width:200px;" class="align-middle">
                                                ប្រភេទ (Category)
                                            </th>
                                            <th style="width:200px; min-width:200px;" class="align-middle">
                                                ប្រភេទរង (Sub-category)
                                            </th>
                                            <th style="width:150px; min-width:200px;" class="align-middle">
                                                {{ __('forms.sub.account') }}
                                            </th>
                                            <th style="width:150px; min-width:200px;" class="align-middle">
                                                {{ __('forms.program') }}
                                            </th>
                                            <th style="width:150px; min-width:200px;" class="align-middle">
                                                {{ __('forms.program.sub') }}
                                            </th>
                                            <th style="width:150px; min-width:200px;" class="align-middle">
                                                {{ __('forms.cluster') }}
                                            </th>
                                            <th style="width:70px; min-width:70px;" class="text-center align-middle">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-row-index="0">
                                            <td>
                                                <input type="text" name="sub_pro[]"
                                                    class="form-control table-required" required>
                                            </td>
                                            <td>
                                                <div class="category-wrapper">
                                                    <div class="d-flex mb-2 category-item">
                                                        <input type="text" class="form-control me-1"
                                                            name="category[0][]" placeholder="បញ្ចូលប្រភេទ">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-sm addCategoryBtn">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="subcategory-wrapper">
                                                    <div class="d-flex mb-2 subcategory-item">
                                                        <input type="text" class="form-control me-1"
                                                            name="sub_category[0][]" placeholder="បញ្ចូលប្រភេទរង">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-sm addSubCategoryBtn"
                                                            style="visibility: hidden;">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select cboSubAccount" name="accountSub[]">
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($accountSub as $bv)
                                                        <option value="{{ $bv->no }}">
                                                            {{ $bv->no }}-{{ $bv->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select cboProgram" name="program[]">
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($program as $p)
                                                        <option value="{{ $p->id }}">
                                                            {{ $p->no }}-{{ $p->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select cboProgramSub" name="cboProgramSub[]">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select cboCluster" name="cboCluster[]">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-success addRow">+</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- =====================================================
                            SECTION 3: NOTE, REFER & ACTIONS
                        ====================================================== --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vRefer" class="form-label">{{ __('forms.refer') }}</label>
                                    <textarea name="refer" id="vRefer" rows="5" class="form-control" required tabindex="9"
                                        data-pristine-required-message="{{ __('messages.required') }}">{{ old('refer') }}</textarea>
                                    @error('refer')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vNote" class="form-label">{{ __('forms.note') }}</label>
                                    <textarea name="note" id="vNote" rows="5" class="form-control" required tabindex="10"
                                        data-pristine-required-message="{{ __('messages.required') }}">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width:80px;">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-outline-secondary" href="{{ route('project.index', $params) }}">
                                    <i class="bx bx-arrow-back me-1"></i>
                                    {{ __('buttons.back') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        /* =========================================================
         * 1. CHOICES.JS INSTANCE MANAGER (WEAKMAP)
         * ========================================================= */
        const choicesMap = new WeakMap();

        function initChoicesInstance(element) {
            if (!element) return null;
            if (choicesMap.has(element)) return choicesMap.get(element);
            const instance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false,
                position: 'auto',
                removeItemButton: false
            });
            choicesMap.set(element, instance);
            element.classList.add('choices-initialized');
            return instance;
        }

        function updateRowIndices() {
            $('#itemTable tbody tr').each(function(newIndex) {
                $(this).attr('data-row-index', newIndex);
                $(this).find('.category-item input').each(function() {
                    $(this).attr('name', `category[${newIndex}][]`);
                });
                $(this).find('.subcategory-item input').each(function() {
                    $(this).attr('name', `sub_category[${newIndex}][]`);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            /* =========================================================
             * 2. PLUGINS INITIALIZATION (SELECTS, SUMMERNOTE, FLATPICKR)
             * ========================================================= */
            document.querySelectorAll('.cboProgram, .cboSubAccount, .cboProgramSub, .cboCluster').forEach(function(
                select) {
                initChoicesInstance(select);
            });

            if ($('#vNote').length) $('#vNote').summernote({
                height: 120
            });
            if ($('#vRefer').length) $('#vRefer').summernote({
                height: 120
            });

            const dateInput = document.getElementById('date');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true
                });
            }

            /* =========================================================
             * 3. PRISTINE VALIDATION SETUP
             * ========================================================= */
            let pristine;
            const pristineConfig = {
                classTo: 'form-group',
                errorClass: 'has-danger',
                successClass: 'has-success',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'pristine-error text-help'
            };

            function reinitPristine() {
                if (pristine) pristine.destroy();
                if (form) pristine = new Pristine(form, pristineConfig, true);
            }

            function clearPristineError(input) {
                const parent = input.closest('.form-group');
                if (!parent) return;
                parent.classList.remove('has-danger', 'has-success');
                const error = parent.querySelector('.pristine-error');
                if (error) error.remove();
            }

            /* =========================================================
             * 4. FIELD SKIP TOGGLES (TITLE & FILE)
             * ========================================================= */
            function setupSkipToggle(checkboxId, inputId) {
                const checkbox = document.getElementById(checkboxId);
                const input = document.getElementById(inputId);
                if (!checkbox || !input) return;

                const requiredMessage = input.getAttribute('data-pristine-required-message') ||
                    "{{ __('messages.required') }}";

                function updateState() {
                    clearPristineError(input);

                    if (checkbox.checked) {
                        input.value = '';
                        input.disabled = true;
                        input.removeAttribute('required');
                        input.removeAttribute('data-pristine-required-message');
                        input.classList.add('bg-success-subtle', 'text-success-emphasis', 'border-success');
                    } else {
                        input.disabled = false;
                        input.removeAttribute('disabled');
                        input.setAttribute('required', 'required');
                        input.setAttribute('data-pristine-required-message', requiredMessage);
                        input.classList.remove('bg-success-subtle', 'text-success-emphasis', 'border-success');
                    }
                    reinitPristine();
                }

                checkbox.addEventListener('change', updateState);
                updateState();
            }

            setupSkipToggle('skipTitleInput', 'titleInput');
            setupSkipToggle('skipFileInput', 'fileInput');

            /* =========================================================
             * 5. TABLE SKIP TOGGLE (DELEGATED EVENT FIX)
             * ========================================================= */
            const itemTableWrapper = document.getElementById('itemTableWrapper');
            const itemTable = document.getElementById('itemTable');

            function applyTableSkipState(isSkipped) {
                if (!itemTableWrapper || !itemTable) return;

                // 1. Toggle visual dimming
                itemTableWrapper.classList.toggle('table-skipped', isSkipped);

                // 2. Enable/Disable every input, select, and button inside the table
                itemTable.querySelectorAll('input, select, button').forEach(el => {
                    el.disabled = isSkipped;
                    if (isSkipped) {
                        el.removeAttribute('required');
                    } else {
                        if (el.classList.contains('table-required')) {
                            el.setAttribute('required', 'required');
                        }
                    }
                });

                if (typeof reinitPristine === 'function') {
                    reinitPristine();
                }
            }

            // Use document-level delegation so even if the switch is redrawn, it still responds instantly
            $(document).on('change click', '#skipItemTable', function() {
                applyTableSkipState(this.checked);
            });

            // Run on initial page load to respect old('skipItemTable') state
            const initialSkipSwitch = document.getElementById('skipItemTable');
            if (initialSkipSwitch) {
                applyTableSkipState(initialSkipSwitch.checked);
            }
            /* =========================================================
             * 6. DYNAMIC DROPDOWNS: PROGRAM -> SUB PROGRAM
             * ========================================================= */
            $(document).on('change', '.cboProgram', function() {
                const row = $(this).closest('tr');
                const programId = $(this).val();
                const subProgramElement = row.find('.cboProgramSub')[0];
                const clusterElement = row.find('.cboCluster')[0];

                const subProgramChoices = choicesMap.get(subProgramElement);
                const clusterChoices = choicesMap.get(clusterElement);

                if (subProgramChoices) {
                    subProgramChoices.clearChoices();
                    subProgramChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }

                if (!programId) return;

                $.ajax({
                    url: "{{ route('project.by.program_sub', $params) }}",
                    type: 'GET',
                    data: {
                        program_id: programId
                    },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 &&
                            subProgramChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });
                            subProgramChoices.setChoices(formattedOptions, 'value', 'label',
                                true);
                            subProgramChoices.showDropdown();
                        }
                    }
                });
            });

            /* =========================================================
             * 7. DYNAMIC DROPDOWN: SUB PROGRAM -> CLUSTER
             * ========================================================= */
            $(document).on('change', '.cboProgramSub', function() {
                const row = $(this).closest('tr');
                const programSubId = $(this).val();
                const clusterElement = row.find('.cboCluster')[0];
                const clusterChoices = choicesMap.get(clusterElement);

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }

                if (!programSubId) return;

                $.ajax({
                    url: "{{ route('project.by.cluster', $params) }}",
                    type: 'GET',
                    data: {
                        program_sub_id: programSubId
                    },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 && clusterChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });
                            clusterChoices.setChoices(formattedOptions, 'value', 'label', true);
                            clusterChoices.showDropdown();
                        }
                    }
                });
            });

            /* =========================================================
             * 8. TABLE ROW & CATEGORY INPUT MANAGEMENT
             * ========================================================= */
            $(document).on('click', '.addCategoryBtn', function(e) {
                e.preventDefault();
                const tr = $(this).closest('tr');
                const mainRowIndex = tr.attr('data-row-index') || tr.index();

                tr.find('.category-wrapper').append(`
                    <div class="d-flex mb-2 category-item">
                        <input type="text" class="form-control me-1" name="category[${mainRowIndex}][]" placeholder="បញ្ចូលប្រភេទ">
                        <button type="button" class="btn btn-outline-danger btn-sm removeCategoryBtn">-</button>
                    </div>
                `);

                tr.find('.subcategory-wrapper').append(`
                    <div class="d-flex mb-2 subcategory-item">
                        <input type="text" class="form-control me-1" name="sub_category[${mainRowIndex}][]" placeholder="បញ្ចូលប្រភេទរង">
                        <button type="button" class="btn btn-outline-danger btn-sm removeCategoryBtn" style="visibility: hidden;">-</button>
                    </div>
                `);
            });

            $(document).on('click', '.removeCategoryBtn', function(e) {
                e.preventDefault();
                const tr = $(this).closest('tr');
                const itemDiv = $(this).closest('.category-item, .subcategory-item');
                const index = itemDiv.index();

                tr.find('.category-wrapper .category-item').eq(index).remove();
                tr.find('.subcategory-wrapper .subcategory-item').eq(index).remove();
            });

            $(document).on('click', '.addRow', function(e) {
                e.preventDefault();
                const tableBody = $('#itemTable tbody');
                let rowIndex = tableBody.children('tr').length;

                const newRowHtml = `
                    <tr data-row-index="${rowIndex}">
                        <td>
                            <input type="text" name="sub_pro[]" class="form-control table-required" required>
                        </td>
                        <td>
                            <div class="category-wrapper">
                                <div class="d-flex mb-2 category-item">
                                    <input type="text" class="form-control me-1" name="category[${rowIndex}][]" placeholder="បញ្ចូលប្រភេទ">
                                    <button type="button" class="btn btn-outline-success btn-sm addCategoryBtn">+</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="subcategory-wrapper">
                                <div class="d-flex mb-2 subcategory-item">
                                    <input type="text" class="form-control me-1" name="sub_category[${rowIndex}][]" placeholder="បញ្ចូលប្រភេទរង">
                                    <button type="button" class="btn btn-outline-success btn-sm addSubCategoryBtn" style="visibility: hidden;">+</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <select class="form-select cboSubAccount" name="accountSub[]">
                                <option value="">ជ្រើសរើស</option>
                                @foreach ($accountSub as $bv)
                                    <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboProgram" name="program[]">
                                <option value="">ជ្រើសរើស</option>
                                @foreach ($program as $p)
                                    <option value="{{ $p->id }}">{{ $p->no }}-{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboProgramSub" name="cboProgramSub[]">
                                <option value="">{{ __('forms.search...') }}</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboCluster" name="cboCluster[]">
                                <option value="">{{ __('forms.search...') }}</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger removeRow">-</button>
                        </td>
                    </tr>`;

                tableBody.append(newRowHtml);
                const appendedRow = tableBody.children().last();
                appendedRow.find('select').each(function() {
                    initChoicesInstance(this);
                });
                updateRowIndices();
                handleTableSkip();
            });

            $(document).on('click', '.removeRow', function(e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                row.find('select').each(function() {
                    const instance = choicesMap.get(this);
                    if (instance) instance.destroy();
                });
                row.remove();
                updateRowIndices();
                reinitPristine();
            });

            /* =========================================================
             * 9. FORM SUBMISSION & VALIDATION HANDLING
             * ========================================================= */
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if ($('#vNote').length) $('#vNote').val($('#vNote').summernote('code'));
                    if ($('#vRefer').length) $('#vRefer').val($('#vRefer').summernote('code'));

                    const skipTableSwitch = document.getElementById('skipItemTable');
                    const isTableSkipped = skipTableSwitch && skipTableSwitch.checked;

                    if (isTableSkipped) {
                        $('#itemTable').find('input, select').prop('disabled', true).removeAttr('required');
                    }

                    let valid = false;
                    try {
                        valid = pristine ? pristine.validate() : true;
                    } catch (err) {
                        console.error("Pristine validation error:", err);
                        valid = true;
                    }

                    if (valid) {
                        // DO NOT re-enable inputs that were intentionally skipped/disabled!
                        // Only disable the submit button to prevent double clicks
                        const submitBtn = document.getElementById('insertToTableBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                កំពុងរក្សាទុក...
                            `;
                        }
                        form.submit();
                    } else {
                        const errorElement = form.querySelector('.has-danger, .pristine-error');
                        if (errorElement) {
                            errorElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });
            }
        });
    </script>
@endsection
