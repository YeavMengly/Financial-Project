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
                            {{-- category with Skip Toggle --}}
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboCategory" class="form-label font-size-13 text-muted mb-0">
                                            ប្រភេទ(Category)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipCategory" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted mb-0" for="skipCategory"
                                                style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboCategory" class="form-select" name="cboCategory" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboCategory')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- sub category with Skip Toggle --}}
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboSubCategory" class="form-label font-size-13 text-muted mb-0">
                                            ប្រភេទរង(Sub Category)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipSubCategory" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted mb-0"
                                                for="skipSubCategory" style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboSubCategory" class="form-select" name="cboSubCategory" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboSubCategory')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Detail with Skip Toggle --}}
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboDetail" class="form-label font-size-13 text-muted mb-0">
                                            លម្អិត(Detail)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipDetail" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted mb-0" for="skipDetail"
                                                style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboDetail" class="form-select" name="cboDetail" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboDetail')
                                        <div class="pristine-error text-help">{{ $message }}</div>
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
                                                        <option value="{{ $item->name }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="form-group">
                                                <input type="number" min="0" name="quantity[]" step="0.01"
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
            getCategoryRoute: "{{ route('materialRelease.get.category') }}",
            getSubCategoryRoute: "{{ route('materialRelease.get.subCategory') }}",
            getDetailRoute: "{{ route('materialRelease.get.detail') }}",
            searchPlaceholder: "{{ __('forms.search...') }}"
        };

        let subProjectChoices = null;
        let agencyChoices = null;
        let categoryChoices = null;
        let subCategoryChoices = null;
        let detailChoices = null;

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

            const defaultChoicesConfig = {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            };

            // Initialize Choices for Project, Sub-Project, Agency
            const cboProject = document.getElementById('cboProject');
            if (cboProject) {
                new Choices(cboProject, defaultChoicesConfig);
            }

            const subProjectSelect = document.getElementById('cboSubProject');
            if (subProjectSelect) {
                subProjectChoices = new Choices(subProjectSelect, defaultChoicesConfig);
            }

            const agencySelect = document.getElementById('agency');
            if (agencySelect) {
                agencyChoices = new Choices(agencySelect, defaultChoicesConfig);
            }

            // Initialize Choices for Category, Sub Category, Detail
            const categorySelect = document.getElementById('cboCategory');
            const subCategorySelect = document.getElementById('cboSubCategory');
            const detailSelect = document.getElementById('cboDetail');

            if (categorySelect) {
                categoryChoices = new Choices(categorySelect, defaultChoicesConfig);
            }
            if (subCategorySelect) {
                subCategoryChoices = new Choices(subCategorySelect, defaultChoicesConfig);
            }
            if (detailSelect) {
                detailChoices = new Choices(detailSelect, defaultChoicesConfig);
            }

            // Initialize choices plugin on the first existing table row
            const tableBody = document.querySelector('#itemTable tbody');
            if (tableBody && tableBody.rows[0]) {
                initRowChoices(tableBody.rows[0]);
            }

            function isSkipped(checkboxId) {
                return Boolean(document.getElementById(checkboxId)?.checked);
            }

            function getChoiceVal(selectEl, choiceInst) {
                if (choiceInst) {
                    const val = choiceInst.getValue(true);
                    if (Array.isArray(val)) return String(val[0] ?? '');
                    if (val !== undefined && val !== null) return String(val);
                }
                return selectEl ? String(selectEl.value || '') : '';
            }

            function setChoiceVal(choiceInst, selectEl, value) {
                if (!choiceInst || !selectEl) return;
                const val = value !== undefined && value !== null ? String(value) : '';
                choiceInst.removeActiveItems();
                if (val) {
                    choiceInst.setChoiceByValue(val);
                }
                selectEl.value = val;
            }

            /**
             * Helper: Get active sub-project ID (empty string if skipped)
             */
            function getActiveSubProjectId() {
                if (isSkipped('skipSubProject') || !subProjectSelect) return '';
                return getChoiceVal(subProjectSelect, subProjectChoices);
            }

            function getActiveCategoryId() {
                if (isSkipped('skipCategory') || !categorySelect) return '';
                return getChoiceVal(categorySelect, categoryChoices);
            }

            function getActiveSubCategoryId() {
                if (isSkipped('skipSubCategory') || !subCategorySelect) return '';
                return getChoiceVal(subCategorySelect, subCategoryChoices);
            }

            function getActiveDetailId() {
                if (isSkipped('skipDetail') || !detailSelect) return '';
                return getChoiceVal(detailSelect, detailChoices);
            }

            function getCurrentItemFilters() {
                return {
                    project_id: cboProject ? String(cboProject.value || '') : '',
                    sub_project_id: getActiveSubProjectId(),
                    category_id: getActiveCategoryId(),
                    sub_category_id: getActiveSubCategoryId(),
                    detail_id: getActiveDetailId()
                };
            }

            /**
             * Helper: Clear auto-filled fields on a table row
             */
            function clearRowAutoFields(row) {
                if (!row) return;

                const priceInput = row.querySelector('input[name="price[]"]');
                if (priceInput) {
                    priceInput.value = '';
                    priceInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }

                const sourceInput = row.querySelector('input[name="source[]"]');
                if (sourceInput) {
                    sourceInput.value = '';
                }

                const proYearInput = row.querySelector('input[name="p_year[]"]');
                if (proYearInput) {
                    proYearInput.value = '';
                }

                const unitSelect = row.querySelector('.unit-select');
                if (unitSelect) {
                    if (unitSelect.choicesInstance) {
                        unitSelect.choicesInstance.removeActiveItems();
                    } else {
                        $(unitSelect).val('').trigger('change');
                    }
                }
            }

            /**
             * Helper: Reset Choices dropdown options
             */
            function resetChoicesDropdown(instance, emptyLabel = 'ជ្រើសរើស') {
                if (!instance) return;
                instance.removeActiveItems();
                instance.clearStore();
                instance.setChoices([{
                    value: '',
                    label: emptyLabel,
                    selected: true,
                    disabled: true
                }], 'value', 'label', true);
            }

            /**
             * Helper: Populate Choices dropdown from AJAX JSON array
             */
            function populateChoicesDropdown(instance, data) {
                if (!instance) return;
                instance.removeActiveItems();
                instance.clearStore();

                if (!Array.isArray(data) || data.length === 0) {
                    instance.setChoices([{
                        value: '',
                        label: 'មិនមានទិន្នន័យ',
                        selected: true,
                        disabled: true
                    }], 'value', 'label', true);
                    return;
                }

                const choices = data.map(item => ({
                    value: String(item.value ?? item.id ?? ''),
                    label: item.label ?? item.name ?? ('#' + item.id),
                    disabled: Boolean(item.no_data)
                }));

                choices.unshift({
                    value: '',
                    label: 'ជ្រើសរើស',
                    selected: true,
                    disabled: true
                });

                instance.setChoices(choices, 'value', 'label', true);
            }

            /**
             * Reusable Skip Toggle Binder
             */
            function bindSkipToggle({
                checkboxId,
                selectEl,
                getInstance,
                onSkipChange
            }) {
                const checkbox = document.getElementById(checkboxId);
                if (!checkbox || !selectEl) return;

                checkbox.addEventListener('change', function() {
                    const instance = getInstance();
                    const container = selectEl.closest('.choices');

                    if (this.checked) {
                        selectEl.value = '';
                        if (instance) {
                            instance.removeActiveItems();
                            instance.disable();
                        }
                        selectEl.removeAttribute('required');
                        selectEl.classList.add('skip-active');
                        if (container) container.classList.add('choices-skip-active');
                    } else {
                        if (instance) instance.enable();
                        selectEl.setAttribute('required', 'required');
                        selectEl.classList.remove('skip-active');
                        if (container) container.classList.remove('choices-skip-active');
                    }

                    if (typeof onSkipChange === 'function') {
                        onSkipChange(this.checked);
                    }
                });
            }

            /**
             * Fetch items dynamically via AJAX
             */
            function fetchAndPopulateItems(params = null, targetSelect = null) {
                const queryParams = params || getCurrentItemFilters();

                $.ajax({
                    url: window.materialReleaseConfig.getItemRoute,
                    type: "GET",
                    data: queryParams,
                    dataType: "json",
                    success: function(data) {
                        const $targets = targetSelect ? $(targetSelect) : $('.item-select');

                        $targets.each(function() {
                            const select = this;
                            const inst = select.choicesInstance;
                            if (!inst) return;

                            const currentValue = String(inst.getValue(true) || '');
                            inst.removeActiveItems();
                            inst.clearStore();

                            if (!Array.isArray(data) || data.length === 0) {
                                inst.setChoices([{
                                    value: '',
                                    label: 'មិនមានទិន្នន័យ',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                                clearRowAutoFields(select.closest('tr'));
                                return;
                            }

                            const formattedChoices = data.map(function(item) {
                                const itemVal = String(item.id);
                                return {
                                    value: itemVal,
                                    label: item.p_name || 'Item #' + item.id,
                                    selected: false,
                                    disabled: false,
                                    customProperties: {
                                        unit: item.unit ?? item.unit_id ?? '',
                                        price: item.price ?? '',
                                        source: item.source ?? '',
                                        p_year: item.p_year ?? item.pro_year ?? item
                                            .project_year ?? ''
                                    }
                                };
                            });

                            formattedChoices.unshift({
                                value: '',
                                label: 'ជ្រើសរើស',
                                selected: true,
                                disabled: true
                            });

                            inst.setChoices(formattedChoices, 'value', 'label', true);

                            const matched = currentValue ?
                                formattedChoices.find(c => !c.disabled && c.value ===
                                    currentValue) :
                                null;

                            if (matched) {
                                inst.setChoiceByValue(currentValue);
                            } else {
                                clearRowAutoFields(select.closest('tr'));
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Fetch Error:", error);
                    }
                });
            }

            /**
             * Auto-fill Unit, Price, Source, and Project Year when an item is selected
             */
            $(document).on('change', '.item-select', function() {
                const row = this.closest('tr');
                if (!row) return;

                const selectInstance = this.choicesInstance;
                const selectedValue = selectInstance ?
                    selectInstance.getValue(true) :
                    $(this).val();

                if (!selectedValue) {
                    clearRowAutoFields(row);
                    return;
                }

                let props = null;
                if (selectInstance && selectInstance._store && Array.isArray(selectInstance._store
                        .choices)) {
                    const matched = selectInstance._store.choices.find(
                        c => String(c.value) === String(selectedValue)
                    );
                    props = matched ? matched.customProperties : null;
                }

                if (!props) {
                    const optionEl = this.querySelector(
                        `option[value="${CSS.escape(String(selectedValue))}"]`);
                    if (optionEl && optionEl.dataset.props) {
                        try {
                            props = JSON.parse(optionEl.dataset.props);
                        } catch (_) {
                            props = null;
                        }
                    }
                }

                if (!props) return;

                const priceInput = row.querySelector('input[name="price[]"]');
                if (priceInput) {
                    priceInput.value = props.price ?? '';
                    priceInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }

                const unitSelect = row.querySelector('.unit-select');
                if (unitSelect && props.unit !== undefined && props.unit !== null && props.unit !== '') {
                    const unitVal = String(props.unit).trim();
                    if (unitSelect.choicesInstance) {
                        unitSelect.choicesInstance.setChoiceByValue(unitVal);
                    } else {
                        $(unitSelect).val(unitVal).trigger('change');
                    }
                }

                const sourceInput = row.querySelector('input[name="source[]"]');
                if (sourceInput) {
                    sourceInput.value = props.source ?? '';
                }

                const proYearInput = row.querySelector('input[name="p_year[]"]');
                if (proYearInput) {
                    proYearInput.value = props.p_year ?? '';
                }
            });

            /**
             * Cascade Loaders for Category -> Sub Category -> Detail
             */
            function loadCategories(projectId, subProjectId = '', preselectCategory = '') {
                resetChoicesDropdown(categoryChoices);
                resetChoicesDropdown(subCategoryChoices);
                resetChoicesDropdown(detailChoices);

                if (!projectId) return $.Deferred().resolve('').promise();

                return $.ajax({
                    url: window.materialReleaseConfig.getCategoryRoute,
                    type: "GET",
                    data: {
                        project_id: projectId,
                        sub_project_id: subProjectId
                    },
                    dataType: "json"
                }).then(function(data) {
                    populateChoicesDropdown(categoryChoices, data);
                    const autoVal = preselectCategory || (Array.isArray(data) && data.length === 1 ? String(
                        data[0].value) : '');
                    if (autoVal && categoryChoices && categorySelect && !isSkipped('skipCategory')) {
                        setChoiceVal(categoryChoices, categorySelect, autoVal);
                        return autoVal;
                    }
                    return '';
                });
            }

            function loadSubCategories(categoryId = '', preselectSubCategory = '') {
                resetChoicesDropdown(subCategoryChoices);
                resetChoicesDropdown(detailChoices);

                const projectId = cboProject ? cboProject.value : '';
                const subProjectId = getActiveSubProjectId();

                if (!projectId) return $.Deferred().resolve('').promise();

                return $.ajax({
                    url: window.materialReleaseConfig.getSubCategoryRoute,
                    type: "GET",
                    data: {
                        project_id: projectId,
                        sub_project_id: subProjectId,
                        category_id: categoryId
                    },
                    dataType: "json"
                }).then(function(data) {
                    populateChoicesDropdown(subCategoryChoices, data);
                    const autoVal = preselectSubCategory || (Array.isArray(data) && data.length === 1 ?
                        String(data[0].value) : '');
                    if (autoVal && subCategoryChoices && subCategorySelect && !isSkipped(
                        'skipSubCategory')) {
                        setChoiceVal(subCategoryChoices, subCategorySelect, autoVal);
                        return autoVal;
                    }
                    return '';
                });
            }

            function loadDetails(subCategoryId = '', preselectDetail = '') {
                resetChoicesDropdown(detailChoices);

                const projectId = cboProject ? cboProject.value : '';
                const subProjectId = getActiveSubProjectId();
                const categoryId = getActiveCategoryId();

                if (!projectId) return $.Deferred().resolve('').promise();

                return $.ajax({
                    url: window.materialReleaseConfig.getDetailRoute,
                    type: "GET",
                    data: {
                        project_id: projectId,
                        sub_project_id: subProjectId,
                        category_id: categoryId,
                        sub_category_id: subCategoryId
                    },
                    dataType: "json"
                }).then(function(data) {
                    populateChoicesDropdown(detailChoices, data);
                    const autoVal = preselectDetail || (Array.isArray(data) && data.length === 1 ? String(
                        data[0].value) : '');
                    if (autoVal && detailChoices && detailSelect && !isSkipped('skipDetail')) {
                        setChoiceVal(detailChoices, detailSelect, autoVal);
                        return autoVal;
                    }
                    return '';
                });
            }

            function runCascadeAndRefreshItems(preselect = {}) {
                const projectId = cboProject ? (cboProject.value || '') : '';
                const subProjectId = getActiveSubProjectId();

                if (!projectId) {
                    fetchAndPopulateItems({
                        project_id: '',
                        sub_project_id: '',
                        category_id: '',
                        sub_category_id: '',
                        detail_id: ''
                    });
                    return;
                }

                loadCategories(projectId, subProjectId, preselect.category || '')
                    .then(function(activeCat) {
                        const catVal = activeCat || getActiveCategoryId();
                        return loadSubCategories(catVal, preselect.sub_category || '');
                    })
                    .then(function(activeSubCat) {
                        const subCatVal = activeSubCat || getActiveSubCategoryId();
                        return loadDetails(subCatVal, preselect.detail || '');
                    })
                    .then(function() {
                        fetchAndPopulateItems();
                    });
            }

            /**
             * Main Project Change Event Handler
             */
            if (cboProject) {
                cboProject.addEventListener('change', function() {
                    const projectId = this.value;

                    $('#program_id, #program_sub_id, #cluster_id, #account_sub_id').val('');
                    resetChoicesDropdown(subProjectChoices);
                    resetChoicesDropdown(categoryChoices);
                    resetChoicesDropdown(subCategoryChoices);
                    resetChoicesDropdown(detailChoices);

                    if (!projectId) {
                        runCascadeAndRefreshItems();
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
                            populateChoicesDropdown(subProjectChoices, data);
                            runCascadeAndRefreshItems();
                        }
                    });
                });
            }

            /**
             * Sub-Project Change Event Handler
             */
            if (subProjectSelect) {
                subProjectSelect.addEventListener('change', function() {
                    runCascadeAndRefreshItems();
                });
            }

            /**
             * Category, Sub-Category, and Detail Change Event Handlers
             */
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const catVal = getActiveCategoryId();
                    loadSubCategories(catVal)
                        .then(function(activeSubCat) {
                            const subCatVal = activeSubCat || getActiveSubCategoryId();
                            return loadDetails(subCatVal);
                        })
                        .then(function() {
                            fetchAndPopulateItems();
                        });
                });
            }

            if (subCategorySelect) {
                subCategorySelect.addEventListener('change', function() {
                    const subCatVal = getActiveSubCategoryId();
                    loadDetails(subCatVal)
                        .then(function() {
                            fetchAndPopulateItems();
                        });
                });
            }

            if (detailSelect) {
                detailSelect.addEventListener('change', function() {
                    fetchAndPopulateItems();
                });
            }

            /**
             * Bind All 4 Skip Checkboxes (Sub-Project, Category, Sub-Category, Detail)
             */
            bindSkipToggle({
                checkboxId: 'skipSubProject',
                selectEl: subProjectSelect,
                getInstance: () => subProjectChoices,
                onSkipChange: () => {
                    runCascadeAndRefreshItems();
                }
            });

            bindSkipToggle({
                checkboxId: 'skipCategory',
                selectEl: categorySelect,
                getInstance: () => categoryChoices,
                onSkipChange: () => {
                    runCascadeAndRefreshItems();
                }
            });

            bindSkipToggle({
                checkboxId: 'skipSubCategory',
                selectEl: subCategorySelect,
                getInstance: () => subCategoryChoices,
                onSkipChange: () => {
                    const catVal = getActiveCategoryId();
                    loadSubCategories(catVal)
                        .then(function(activeSubCat) {
                            const subCatVal = activeSubCat || getActiveSubCategoryId();
                            return loadDetails(subCatVal);
                        })
                        .then(function() {
                            fetchAndPopulateItems();
                        });
                }
            });

            bindSkipToggle({
                checkboxId: 'skipDetail',
                selectEl: detailSelect,
                getInstance: () => detailChoices,
                onSkipChange: () => {
                    fetchAndPopulateItems();
                }
            });

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
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
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

                        const newSelect = newRow.querySelector('.item-select');
                        fetchAndPopulateItems(getCurrentItemFilters(), newSelect);
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
                if (itemSelect && !itemSelect.choicesInstance) {
                    itemSelect.choicesInstance = new Choices(itemSelect, defaultChoicesConfig);
                }

                const unitSelect = row.querySelector('.unit-select');
                if (unitSelect && !unitSelect.choicesInstance) {
                    unitSelect.choicesInstance = new Choices(unitSelect, defaultChoicesConfig);
                }
            }
        });
    </script>
@endsection
