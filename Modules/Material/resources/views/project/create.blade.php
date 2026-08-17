@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        /* =========================================================
                 * TABLE FIXES FOR DROPDOWNS
                 * ========================================================= */
        #itemTable {
            table-layout: fixed !important;
            width: 100% !important;
        }

        #itemTable th {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        #itemTable td {
            vertical-align: middle !important;
            padding: 6px 8px !important;
            overflow: visible !important;
            position: relative;
        }

        #itemTableWrapper {
            overflow: visible !important;
        }

        /* =========================================================
                 * CHOICES.JS STYLING
                 * ========================================================= */
        .choices {
            margin-bottom: 0 !important;
            width: 100% !important;
            position: relative;
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
        }

        .choices__list--dropdown {
            z-index: 1055 !important;
            border-radius: 0 0 4px 4px !important;
            border: 1px solid #ced4da !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            background-color: #ffffff !important;
        }

        .choices__list--dropdown .choices__item--selectable {
            padding: 8px 10px !important;
            font-size: 13px !important;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0b5ed7 !important;
            color: #ffffff !important;
        }

        .choices__list--dropdown .choices__input {
            font-size: 13px !important;
            padding: 4px 8px !important;
            margin-bottom: 6px !important;
            border-radius: 3px !important;
        }

        /* =========================================================
                 * DISABLED / SKIPPED STATE
                 * ========================================================= */
        #itemTableWrapper.table-skipped {
            opacity: 0.6;
        }

        #itemTableWrapper.table-skipped .choices {
            pointer-events: none;
            background-color: #e9ecef !important;
        }
    </style>
@endsection

@section('content')
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
                            <a href="javascript:void(0);">{{ $ministry->year }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('buttons.create') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" action="{{ route('project.store', $params) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- =====================================================
                            MAIN INFORMATION
                        ====================================================== --}}
                        <div class="row">

                            {{-- STOCK NUMBER --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                    <input type="text" id="stock_number" name="stock_number" required
                                        class="form-control" tabindex="1"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- STOCK NAME --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                    <input type="text" id="stock_name" name="stock_name" required class="form-control"
                                        tabindex="2" data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- COMPANY --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="company_name">{{ __('forms.company.name') }}</label>
                                    <input type="text" id="company_name" name="company_name" required
                                        class="form-control" tabindex="3"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- WAREHOUSE VOUCHER --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_voucher">
                                        កាលបរិច្ឆេទ{{ __('forms.warehouse.voucher') }}ក្រុមហ៊ុន
                                    </label>
                                    <input type="text" id="warehouse_voucher" name="warehouse_voucher" required
                                        class="form-control" tabindex="4"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- USER ENTRY --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                    <input type="text" id="user_entry" name="user_entry" required class="form-control"
                                        tabindex="5" data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- USER RECEIVER --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_receiver">{{ __('forms.receiver') }}</label>
                                    <input type="text" id="user_receiver" name="user_receiver" required
                                        class="form-control" tabindex="6"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- DATE --}}
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date" class="form-label">
                                        {{ __('forms.date') }}បញ្ចូលឃ្លាំង
                                    </label>
                                    <input type="text" id="date" name="date" class="form-control" tabindex="7"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                </div>
                            </div>

                            {{-- TITLE --}}
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="titleInput" class="form-label mb-0">
                                            {{ __('forms.title') }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipTitleInput" style="cursor:pointer;">
                                            <label class="form-check-label font-size-12 text-muted" for="skipTitleInput"
                                                style="cursor:pointer;">
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

                            {{-- FILE --}}
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="fileInput" class="form-label mb-0">
                                            {{ __('forms.file.type') }}
                                        </label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipFileInput" style="cursor:pointer;">
                                            <label class="form-check-label font-size-12 text-muted" for="skipFileInput"
                                                style="cursor:pointer;">
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
                            PROJECT TABLE
                        ====================================================== --}}

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">
                                {{ __('forms.project') }}
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="skipItemTable"
                                    name="skipItemTable" value="1" style="cursor:pointer;">
                                <label class="form-check-label font-size-12 text-muted" for="skipItemTable">
                                    រំលង / មិនភ្ជាប់
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 mt-3" id="itemTableWrapper">
                            <table class="table table-bordered" id="itemTable">
                                <thead>
                                    <tr>
                                        <th style="width:200px; min-width:200px;" class="align-middle">
                                            {{ __('forms.project') }}
                                        </th>
                                        <th style="width:200px; min-width:200px;">
                                            {{ __('forms.sub.account') }}
                                        </th>
                                        <th style="width:200px; min-width:200px;">
                                            {{ __('forms.program') }}
                                        </th>
                                        <th style="width:200px; min-width:200px;">
                                            {{ __('forms.program.sub') }}
                                        </th>
                                        <th style="width:200px; min-width:200px;">
                                            {{ __('forms.cluster') }}
                                        </th>
                                        <th style="width:60px; min-width:70px;" class="text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="sub_pro[]" class="form-control table-required"
                                                required>
                                        </td>
                                        <td>
                                            <select class="form-control cboSubAccount" name="accountSub[]">
                                                <option value="">ជ្រើសរើស</option>
                                                @foreach ($accountSub as $bv)
                                                    <option value="{{ $bv->no }}">
                                                        {{ $bv->no }}-{{ $bv->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control cboProgram" name="program[]">
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

                        {{-- =====================================================
                            NOTE + REFER
                        ====================================================== --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vNote" class="form-label">{{ __('forms.note') }}</label>
                                    <textarea name="note" id="vNote" rows="5" class="form-control" required tabindex="10"
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('note')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vRefer" class="form-label">{{ __('forms.refer') }}</label>
                                    <textarea name="refer" id="vRefer" rows="5" class="form-control" required tabindex="9"
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('refer')
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
                                <a class="btn btn-dark" href="{{ route('project.index', $params) }}">
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
         * CHOICES.JS INSTANCE MANAGER (WEAKMAP)
         * ========================================================= */
        const choicesMap = new WeakMap();

        function initChoicesInstance(element) {
            if (!element) return null;

            // Return existing instance if already initialized
            if (choicesMap.has(element)) {
                return choicesMap.get(element);
            }

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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            /* =========================================================
             * 1. INITIALIZE PLUGINS & STATIC DROPDOWNS
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
             * 2. PRISTINE VALIDATION INSTANCE MANAGER
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
             * 3. TITLE / FILE SKIP TOGGLES
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
             * 4. TABLE SKIP TOGGLE
             * ========================================================= */
            const skipItemTable = document.getElementById('skipItemTable');
            const itemTable = document.getElementById('itemTable');
            const itemTableWrapper = document.getElementById('itemTableWrapper');

            function updateItemTableState() {
                if (!skipItemTable || !itemTable) return;

                const skipped = skipItemTable.checked;

                if (itemTableWrapper) {
                    itemTableWrapper.classList.toggle('table-skipped', skipped);
                }

                itemTable.querySelectorAll('tbody tr').forEach(function(row) {
                    row.querySelectorAll('input').forEach(function(input) {
                        clearPristineError(input);
                        if (skipped) {
                            input.value = '';
                            input.disabled = true;
                            input.removeAttribute('required');
                            input.classList.add('bg-success-subtle', 'text-success-emphasis',
                                'border-success');
                        } else {
                            input.disabled = false;
                            input.classList.remove('bg-success-subtle', 'text-success-emphasis',
                                'border-success');
                            if (input.classList.contains('table-required')) {
                                input.setAttribute('required', 'required');
                            }
                        }
                    });

                    row.querySelectorAll('select').forEach(function(select) {
                        const choicesInstance = choicesMap.get(select);
                        if (skipped) {
                            select.value = '';
                            select.disabled = true;
                            select.classList.add('bg-success-subtle', 'text-success-emphasis',
                                'border-success');
                            if (choicesInstance) choicesInstance.disable();
                        } else {
                            select.disabled = false;
                            select.classList.remove('bg-success-subtle', 'text-success-emphasis',
                                'border-success');
                            if (choicesInstance) choicesInstance.enable();
                        }
                    });

                    row.querySelectorAll('.addRow, .removeRow').forEach(function(button) {
                        button.disabled = skipped;
                    });
                });

                reinitPristine();
            }

            if (skipItemTable) {
                skipItemTable.addEventListener('change', updateItemTableState);
                updateItemTableState();
            }

            reinitPristine();

            /* =========================================================
             * 5. DYNAMIC DROPDOWNS: PROGRAM -> SUB PROGRAM
             * ========================================================= */
            $(document).on('change', '.cboProgram', function() {
                const row = $(this).closest('tr');
                const programId = $(this).val();

                const subProgramElement = row.find('.cboProgramSub')[0];
                const clusterElement = row.find('.cboCluster')[0];

                const subProgramChoices = initChoicesInstance(subProgramElement);
                const clusterChoices = initChoicesInstance(clusterElement);

                // Clear Choices lists dynamically
                if (subProgramChoices) {
                    subProgramChoices.clearChoices();
                    subProgramChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true,
                        disabled: false
                    }], 'value', 'label', true);
                }

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true,
                        disabled: false
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
                                value: item.value,
                                label: item.label,
                                selected: false
                            }));

                            // Prepend placeholder
                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });

                            subProgramChoices.setChoices(formattedOptions, 'value', 'label',
                                true);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching Sub Programs:', xhr);
                    }
                });
            });

            /* =========================================================
             * 6. DYNAMIC DROPDOWNS: SUB PROGRAM -> CLUSTER
             * ========================================================= */
            $(document).on('change', '.cboProgramSub', function() {
                const row = $(this).closest('tr');
                const programSubId = $(this).val();

                const clusterElement = row.find('.cboCluster')[0];
                const clusterChoices = initChoicesInstance(clusterElement);

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true,
                        disabled: false
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
                                value: item.value,
                                label: item.label,
                                selected: false
                            }));

                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });

                            clusterChoices.setChoices(formattedOptions, 'value', 'label', true);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching Clusters:', xhr);
                    }
                });
            });

            /* =========================================================
             * 7. ADD & REMOVE TABLE ROWS
             * ========================================================= */
            $(document).on('click', '.addRow', function() {
                const tableBody = $('#itemTable tbody');
                const newRowHtml = `
                    <tr>
                        <td>
                            <input type="text" name="sub_pro[]" class="form-control table-required" required>
                        </td>
                        <td>
                            <select class="form-control cboSubAccount" name="accountSub[]">
                                <option value="">ជ្រើសរើស</option>
                                @foreach ($accountSub as $bv)
                                    <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control cboProgram" name="program[]">
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

                // Initialize Choices instances for the newly appended selects
                const appendedRow = tableBody.children().last();
                appendedRow.find('select').each(function() {
                    initChoicesInstance(this);
                });

                reinitPristine();
            });

            $(document).on('click', '.removeRow', function() {
                const row = $(this).closest('tr');
                row.find('select').each(function() {
                    const choicesInstance = choicesMap.get(this);
                    if (choicesInstance) choicesInstance.destroy();
                });
                row.remove();
                reinitPristine();
            });

            /* =========================================================
             * 8. FORM SUBMIT EVENT
             * ========================================================= */
            if (form) {
                form.addEventListener('submit', function(e) {
                    if ($('#vNote').length) {
                        $('#vNote').val($('#vNote').summernote('code'));
                    }
                    if ($('#vRefer').length) {
                        $('#vRefer').val($('#vRefer').summernote('code'));
                    }

                    const valid = pristine.validate();

                    if (!valid) {
                        e.preventDefault();
                        return false;
                    }

                    return true;
                });
            }
        });
    </script>
@endsection
