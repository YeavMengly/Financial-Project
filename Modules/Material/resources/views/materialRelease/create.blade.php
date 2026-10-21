@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <style>
        /* Skipped field styling */
        .skip-active {
            background-color: #e7f1ff !important;
            border-color: #0d6efd !important;
            color: #0d6efd !important;
        }

        .choices-skip-active .choices__inner {
            background-color: #e7f1ff !important;
            border-color: #0d6efd !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.material.release') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.material.release') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{ route('materialRelease.store', $params) }}"
                        autocomplete="off" id="materialReleaseForm">
                        @csrf
                        <div class="row">
                            {{-- Main Project --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboProject" class="form-label font-size-13 text-muted">
                                        {{ __('forms.project') }}
                                    </label>
                                    <select class="form-control" id="cboProject" name="cboProject" tabindex="1" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($project as $item)
                                            <option value="{{ $item->project_id ?? $item->id }}">
                                                {{ $item->stock_number }} - {{ $item->stock_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboProject')
                                        <div class="text-danger font-size-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Sub Project --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3" id="subProjectGroup">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboSubProject" class="form-label font-size-13 text-muted mb-0">
                                            {{ __('forms.sub.project') }}
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            <input class="form-check-input mt-0 " type="checkbox" role="switch"
                                                id="skipSubProject" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted mb-0"
                                                for="skipSubProject" style="cursor: pointer;">
                                                រំលង/ មិនភ្ជាប់
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboSubProject" class="form-select" name="cboSubProject" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboSubProject')
                                        <div class="text-danger font-size-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hidden inputs for project dependencies -->
                            <input type="hidden" id="program_id" name="program_id">
                            <input type="hidden" id="program_sub_id" name="program_sub_id">
                            <input type="hidden" id="cluster_id" name="cluster_id">
                            <input type="hidden" id="account_sub_id" name="account_sub_id">

                            {{-- Agency --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agency"
                                        class="form-label font-size-13 text-muted">{{ __('forms.agency') }}</label>
                                    <select class="form-control" id="agency" name="agency" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($Agency as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('agency')
                                        <div class="text-danger font-size-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Date Release --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="datepicker-basic"
                                        class="form-label font-size-13 text-muted">កាលបរិច្ឆេទ</label>
                                    <input type="text" id="datepicker-basic" name="date_release" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}" required />
                                    @error('date_release')
                                        <div class="text-danger font-size-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dynamic Item Table --}}
                            <div class="col-xl-12 col-md-12 mt-3">
                                <table class="table table-bordered align-middle" id="itemTable">
                                    <thead>
                                        <tr>
                                            <th width="300">{{ __('forms.item.name') }}</th>
                                            <th width="150">{{ __('forms.unit') }}</th>
                                            <th width="150">{{ __('forms.quantity') }}</th>
                                            <th width="150">{{ __('forms.price') }}ឯកតា</th>
                                            <th width="200">{{ __('forms.source') }}</th>
                                            <th width="200">{{ __('forms.pro.year') }}</th>
                                            <th width="60" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="form-group">
                                                <select class="form-control item-select" name="p_name[]" required>
                                                    <option value="">ជ្រើសរើស</option>
                                                </select>
                                            </td>
                                            <td class="form-group">
                                                <select class="form-control unit-select" name="unit[]" required>
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($unitType as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="form-group">
                                                <input type="number" min="0" name="quantity[]"
                                                    class="form-control" required>
                                            </td>
                                            <td class="form-group">
                                                <input type="number" min="0" step="any" name="price[]"
                                                    class="form-control" required>
                                            </td>
                                            <td class="form-group">
                                                <input type="text" name="source[]" class="form-control source-input"
                                                    placeholder="{{ __('forms.source') }}">
                                            </td>
                                            <td class="form-group">
                                                <input type="text" name="p_year[]" class="form-control pro-year-input"
                                                    placeholder="{{ __('forms.pro.year') }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-success addRow">+</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button class="btn btn-primary" type="submit" name="submit" value="save"
                                    id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark" href="{{ route('materialRelease.index', $params) }}">
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
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    {{-- <script>
        window.materialReleaseConfig = {
            getProjectRoute: "{{ route('materialRelease.get.project') }}",
            getItemRoute: "{{ route('materialRelease.get.projectItem') }}",
            searchPlaceholder: "{{ __('forms.search...') }}"
        };

        let subProjectChoices = null;
        let agencyChoices = null;

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('materialReleaseForm');
            const submitBtn = document.getElementById('insertToTableBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    if (form.checkValidity()) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ${submitBtn.innerText}...`;
                    }
                });
            }

            // Initialize Flatpickr
            const dateInput = document.getElementById('datepicker-basic');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: dateInput.value || null
                });
            }

            // Project Choice Init
            const cboProject = document.getElementById('cboProject');
            if (cboProject) {
                new Choices(cboProject, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Sub-Project Choice Init
            const subProjectSelect = document.getElementById('cboSubProject');
            if (subProjectSelect) {
                subProjectChoices = new Choices(subProjectSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Agency Choice Init
            const agencySelect = document.getElementById('agency');
            if (agencySelect) {
                agencyChoices = new Choices(agencySelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Initialize row choices on first table row
            const tableBody = document.querySelector('#itemTable tbody');
            if (tableBody && tableBody.rows[0]) {
                initRowChoices(tableBody.rows[0]);
            }

            /* Item Fetch & Populate Logic (Accepts optional target element) */
            function fetchAndPopulateItems(params, targetSelect = null) {
                $.ajax({
                    url: window.materialReleaseConfig.getItemRoute,
                    type: "GET",
                    data: params,
                    dataType: "json",
                    success: function(data) {
                        const $targets = targetSelect ? $(targetSelect) : $('.item-select');

                        $targets.each(function() {
                            const select = this;
                            if (!select.choicesInstance) return;

                            // Save currently selected value before updating choices
                            const currentValue = select.choicesInstance.getValue(true);

                            select.choicesInstance.clearStore();

                            if (!Array.isArray(data) || data.length === 0) {
                                select.choicesInstance.setChoices([{
                                    value: '',
                                    label: 'មិនមានទិន្នន័យ',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                                return;
                            }

                            const formattedChoices = data.map(function(item) {
                                const itemVal = String(item.id);
                                return {
                                    value: itemVal,
                                    label: item.p_name || 'Item #' + item.id,
                                    selected: itemVal === String(currentValue),
                                    disabled: false,
                                    customProperties: {
                                        unit: item.unit || '',
                                        price: item.price || ''
                                    }
                                };
                            });

                            formattedChoices.unshift({
                                value: '',
                                label: 'ជ្រើសរើស',
                                selected: !currentValue,
                                disabled: true
                            });

                            select.choicesInstance.setChoices(formattedChoices, 'value',
                                'label', true);

                            // Restore existing selection if valid
                            if (currentValue) {
                                select.choicesInstance.setChoiceByValue(String(currentValue));
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Fetch Error:", error);
                    }
                });
            }

            /* Main Project Event Listener */
            if (cboProject) {
                cboProject.addEventListener('change', function() {
                    const projectId = this.value;

                    $('#program_id, #program_sub_id, #cluster_id, #account_sub_id').val('');

                    if (subProjectChoices) {
                        subProjectChoices.clearStore();
                        subProjectChoices.setChoices([{
                            value: '',
                            label: 'ជ្រើសរើស',
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }

                    if (!projectId) {
                        fetchAndPopulateItems({});
                        return;
                    }

                    $.ajax({
                        url: window.materialReleaseConfig.getProjectRoute,
                        type: "GET",
                        data: {
                            project_id: projectId
                        },
                        dataType: "json",
                        success: function(data) {
                            if (!subProjectChoices) return;
                            subProjectChoices.clearStore();

                            if (!Array.isArray(data) || data.length === 0) {
                                subProjectChoices.setChoices([{
                                    value: '',
                                    label: 'មិនមានទិន្នន័យ',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                            } else {
                                const choices = data.map(function(item) {
                                    return {
                                        value: String(item.value),
                                        label: item.label,
                                        customProperties: {
                                            program_id: item.program_id || '',
                                            program_sub_id: item.program_sub_id || '',
                                            cluster_id: item.cluster_id || '',
                                            account_sub_id: item.account_sub_id || ''
                                        }
                                    };
                                });
                                choices.unshift({
                                    value: '',
                                    label: 'ជ្រើសរើស',
                                    selected: true,
                                    disabled: true
                                });
                                subProjectChoices.setChoices(choices, 'value', 'label', true);
                            }
                        }
                    });

                    fetchAndPopulateItems({
                        project_id: projectId,
                        sub_project_id: ''
                    });
                });
            }

            /* Sub-Project Event Listener */
            if (subProjectSelect) {
                subProjectSelect.addEventListener('change', function() {
                    const selected = subProjectChoices ? subProjectChoices.getValue() : null;
                    if (selected && selected.customProperties) {
                        const props = selected.customProperties;
                        $('#program_id').val(props.program_id || '');
                        $('#program_sub_id').val(props.program_sub_id || '');
                        $('#cluster_id').val(props.cluster_id || '');
                        $('#account_sub_id').val(props.account_sub_id || '');
                    }

                    const subProjectId = this.value;
                    const projectId = cboProject ? cboProject.value : null;

                    fetchAndPopulateItems({
                        project_id: projectId,
                        sub_project_id: subProjectId
                    });
                });
            }

            /* Skip Sub-Project Switch Listener */
            const skipSubProjectCheckbox = document.getElementById('skipSubProject');
            if (skipSubProjectCheckbox) {
                skipSubProjectCheckbox.addEventListener('change', function() {
                    const projectId = cboProject ? cboProject.value : null;
                    const subProjectContainer = subProjectSelect.closest('.choices');

                    if (this.checked) {
                        subProjectSelect.value = '';
                        if (subProjectChoices) {
                            subProjectChoices.removeActiveItems();
                            subProjectChoices.disable();
                        }
                        subProjectSelect.removeAttribute('required');

                        subProjectSelect.classList.add('skip-active');
                        if (subProjectContainer) {
                            subProjectContainer.classList.add('choices-skip-active');
                        }

                        fetchAndPopulateItems({
                            project_id: projectId,
                            sub_project_id: ''
                        });
                    } else {
                        if (subProjectChoices) subProjectChoices.enable();
                        subProjectSelect.setAttribute('required', 'required');

                        subProjectSelect.classList.remove('skip-active');
                        if (subProjectContainer) {
                            subProjectContainer.classList.remove('choices-skip-active');
                        }
                    }
                });
            }

            /* Table Row Operations */
            if (tableBody) {
                tableBody.addEventListener('click', function(e) {
                    if (e.target.classList.contains('addRow')) {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="form-group">
                                <select class="form-control item-select" name="p_name[]" required>
                                    <option value="">ជ្រើសរើស</option>
                                </select>
                            </td>
                            <td class="form-group">
                                <select class="form-control unit-select" name="unit[]" required>
                                    <option value="">ជ្រើសរើស</option>
                                    @foreach ($unitType as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="form-group">
                                <input type="number" min="0" name="quantity[]" class="form-control" required>
                            </td>
                            <td class="form-group">
                                <input type="number" min="0" step="any" name="price[]" class="form-control" required>
                            </td>
                            <td class="form-group">
                                <input type="text" name="source[]" class="form-control source-input" placeholder="{{ __('forms.source') }}">
                            </td>
                            <td class="form-group">
                                <input type="text" name="p_year[]" class="form-control pro-year-input" placeholder="{{ __('forms.pro.year') }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger removeRow">-</button>
                            </td>
                        `;

                        tableBody.appendChild(newRow);
                        initRowChoices(newRow);

                        const projectId = cboProject ? cboProject.value : null;
                        const subProjectId = subProjectSelect ? subProjectSelect.value : null;

                        // Target ONLY the newly added row's item-select element
                        const newSelect = newRow.querySelector('.item-select');
                        fetchAndPopulateItems({
                            project_id: projectId,
                            sub_project_id: subProjectId
                        }, newSelect);
                    }

                    if (e.target.classList.contains('removeRow')) {
                        const row = e.target.closest('tr');
                        row.querySelectorAll('select').forEach(select => {
                            if (select.choicesInstance) {
                                select.choicesInstance.destroy();
                            }
                        });
                        row.remove();
                    }
                });
            }

            function initRowChoices(row) {
                const itemSelect = row.querySelector('.item-select');
                // const unitSelect = row.querySelector('.unit-select');

                if (itemSelect && !itemSelect.choicesInstance) {
                    itemSelect.choicesInstance = new Choices(itemSelect, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: 'ជ្រើសរើស',
                        searchPlaceholderValue: 'ស្វែងរក...',
                        shouldSort: false
                    });
                }

                // if (unitSelect && !unitSelect.choicesInstance) {
                //     unitSelect.choicesInstance = new Choices(unitSelect, {
                //         searchEnabled: true,
                //         itemSelectText: '',
                //         placeholder: true,
                //         placeholderValue: 'ជ្រើសរើស',
                //         searchPlaceholderValue: 'ស្វែងរក...',
                //         shouldSort: false
                //     });
                // }
            }
        });
    </script> --}}
    <script>
        window.materialReleaseConfig = {
            getProjectRoute: "{{ route('materialRelease.get.project') }}",
            getItemRoute: "{{ route('materialRelease.get.projectItem') }}",
            searchPlaceholder: "{{ __('forms.search...') }}"
        };

        let subProjectChoices = null;
        let agencyChoices = null;

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('materialReleaseForm');
            const submitBtn = document.getElementById('insertToTableBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    if (form.checkValidity()) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ${submitBtn.innerText}...`;
                    }
                });
            }

            // Initialize Flatpickr Datepicker
            const dateInput = document.getElementById('datepicker-basic');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: dateInput.value || null
                });
            }

            // Initialize Choices for Project Dropdown
            const cboProject = document.getElementById('cboProject');
            if (cboProject) {
                new Choices(cboProject, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Initialize Choices for Sub-Project Dropdown
            const subProjectSelect = document.getElementById('cboSubProject');
            if (subProjectSelect) {
                subProjectChoices = new Choices(subProjectSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Initialize Choices for Agency Dropdown
            const agencySelect = document.getElementById('agency');
            if (agencySelect) {
                agencyChoices = new Choices(agencySelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            // Initialize choices plugin on the first existing table row
            const tableBody = document.querySelector('#itemTable tbody');
            if (tableBody && tableBody.rows[0]) {
                initRowChoices(tableBody.rows[0]);
            }

            /**
             * Fetch items dynamically via AJAX and populate target select fields
             */
            function fetchAndPopulateItems(params, targetSelect = null) {
                $.ajax({
                    url: window.materialReleaseConfig.getItemRoute,
                    type: "GET",
                    data: params,
                    dataType: "json",
                    success: function(data) {
                        const $targets = targetSelect ? $(targetSelect) : $('.item-select');

                        $targets.each(function() {
                            const select = this;
                            if (!select.choicesInstance) return;

                            const currentValue = select.choicesInstance.getValue(true);
                            select.choicesInstance.clearStore();

                            if (!Array.isArray(data) || data.length === 0) {
                                select.choicesInstance.setChoices([{
                                    value: '',
                                    label: 'មិនមានទិន្នន័យ',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                                return;
                            }

                            const formattedChoices = data.map(function(item) {
                                const itemVal = String(item.id);
                                return {
                                    value: itemVal,
                                    label: item.p_name || 'Item #' + item.id,
                                    selected: itemVal === String(currentValue),
                                    disabled: false,
                                    customProperties: {
                                        unit: item.unit || '',
                                        price: item.price || ''
                                    }
                                };
                            });

                            formattedChoices.unshift({
                                value: '',
                                label: 'ជ្រើសរើស',
                                selected: !currentValue,
                                disabled: true
                            });

                            select.choicesInstance.setChoices(formattedChoices, 'value',
                                'label', true);

                            if (currentValue) {
                                select.choicesInstance.setChoiceByValue(String(currentValue));
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Fetch Error:", error);
                    }
                });
            }

            /**
             * Main Project Change Event Handler
             */
            if (cboProject) {
                cboProject.addEventListener('change', function() {
                    const projectId = this.value;

                    $('#program_id, #program_sub_id, #cluster_id, #account_sub_id').val('');

                    if (subProjectChoices) {
                        subProjectChoices.clearStore();
                        subProjectChoices.setChoices([{
                            value: '',
                            label: 'ជ្រើសរើស',
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }

                    if (!projectId) {
                        fetchAndPopulateItems({});
                        return;
                    }

                    $.ajax({
                        url: window.materialReleaseConfig.getProjectRoute,
                        type: "GET",
                        data: {
                            project_id: projectId
                        },
                        dataType: "json",
                        success: function(data) {
                            if (!subProjectChoices) return;
                            subProjectChoices.clearStore();

                            if (!Array.isArray(data) || data.length === 0) {
                                subProjectChoices.setChoices([{
                                    value: '',
                                    label: 'មិនមានទិន្នន័យ',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                            } else {
                                const choices = data.map(function(item) {
                                    return {
                                        value: String(item.value),
                                        label: item.label,
                                        customProperties: {
                                            program_id: item.program_id || '',
                                            program_sub_id: item.program_sub_id || '',
                                            cluster_id: item.cluster_id || '',
                                            account_sub_id: item.account_sub_id || ''
                                        }
                                    };
                                });
                                choices.unshift({
                                    value: '',
                                    label: 'ជ្រើសរើស',
                                    selected: true,
                                    disabled: true
                                });
                                subProjectChoices.setChoices(choices, 'value', 'label', true);
                            }
                        }
                    });

                    fetchAndPopulateItems({
                        project_id: projectId,
                        sub_project_id: ''
                    });
                });
            }

            /**
             * Sub-Project Change Event Handler
             */
            if (subProjectSelect) {
                subProjectSelect.addEventListener('change', function() {
                    const selected = subProjectChoices ? subProjectChoices.getValue() : null;
                    if (selected && selected.customProperties) {
                        const props = selected.customProperties;
                        $('#program_id').val(props.program_id || '');
                        $('#program_sub_id').val(props.program_sub_id || '');
                        $('#cluster_id').val(props.cluster_id || '');
                        $('#account_sub_id').val(props.account_sub_id || '');
                    }

                    const subProjectId = this.value;
                    const projectId = cboProject ? cboProject.value : null;

                    fetchAndPopulateItems({
                        project_id: projectId,
                        sub_project_id: subProjectId
                    });
                });
            }

            /**
             * Skip Sub-Project Checkbox Toggle Handler
             */
            const skipSubProjectCheckbox = document.getElementById('skipSubProject');
            if (skipSubProjectCheckbox) {
                skipSubProjectCheckbox.addEventListener('change', function() {
                    const projectId = cboProject ? cboProject.value : null;
                    const subProjectContainer = subProjectSelect.closest('.choices');

                    if (this.checked) {
                        subProjectSelect.value = '';
                        if (subProjectChoices) {
                            subProjectChoices.removeActiveItems();
                            subProjectChoices.disable();
                        }
                        subProjectSelect.removeAttribute('required');
                        subProjectSelect.classList.add('skip-active');
                        if (subProjectContainer) subProjectContainer.classList.add('choices-skip-active');

                        fetchAndPopulateItems({
                            project_id: projectId,
                            sub_project_id: ''
                        });
                    } else {
                        if (subProjectChoices) subProjectChoices.enable();
                        subProjectSelect.setAttribute('required', 'required');
                        subProjectSelect.classList.remove('skip-active');
                        if (subProjectContainer) subProjectContainer.classList.remove(
                            'choices-skip-active');
                    }
                });
            }

            /**
             * Dynamic Table Row Operations (Add/Remove Rows)
             */
            if (tableBody) {
                tableBody.addEventListener('click', function(e) {
                    if (e.target.classList.contains('addRow')) {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                    <td class="form-group">
                        <select class="form-control item-select" name="p_name[]" required>
                            <option value="">ជ្រើសរើស</option>
                        </select>
                    </td>
                    <td class="form-group">
                        <select class="form-control unit-select" name="unit[]" required>
                            <option value="">ជ្រើសរើស</option>
                            @foreach ($unitType as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="form-group">
                        <input type="number" min="0" name="quantity[]" class="form-control" required>
                    </td>
                    <td class="form-group">
                        <input type="number" min="0" step="any" name="price[]" class="form-control" required>
                    </td>
                    <td class="form-group">
                        <input type="text" name="source[]" class="form-control source-input" placeholder="{{ __('forms.source') }}">
                    </td>
                    <td class="form-group">
                        <input type="text" name="p_year[]" class="form-control pro-year-input" placeholder="{{ __('forms.pro.year') }}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger removeRow">-</button>
                    </td>
                `;

                        tableBody.appendChild(newRow);
                        initRowChoices(newRow);

                        const projectId = cboProject ? cboProject.value : null;
                        const subProjectId = subProjectSelect ? subProjectSelect.value : null;
                        const newSelect = newRow.querySelector('.item-select');

                        fetchAndPopulateItems({
                            project_id: projectId,
                            sub_project_id: subProjectId
                        }, newSelect);
                    }

                    if (e.target.classList.contains('removeRow')) {
                        const row = e.target.closest('tr');
                        row.querySelectorAll('select').forEach(select => {
                            if (select.choicesInstance) {
                                select.choicesInstance.destroy();
                            }
                        });
                        row.remove();
                    }
                });
            }

            /**
             * Helper Function to Initialize Choices on Item Dropdown Rows
             */
            function initRowChoices(row) {
                const itemSelect = row.querySelector('.item-select');
                if (itemSelect && !itemSelect.choicesInstance) {
                    itemSelect.choicesInstance = new Choices(itemSelect, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: 'ជ្រើសរើស',
                        searchPlaceholderValue: 'ស្វែងរក...',
                        shouldSort: false
                    });
                }
            }
        });
    </script>
@endsection
