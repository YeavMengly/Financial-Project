@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <style>
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
                        <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" method="POST"
                        action="{{ route('materialRelease.update', ['params' => $params, 'id' => $releaseId]) }}"
                        autocomplete="off" id="materialReleaseForm">
                        @csrf
                        <input type="hidden" name="release_id" value="{{ $releaseId }}">

                        <div class="row">
                            {{-- Main Project --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboProject" class="form-label font-size-13 text-muted">
                                        {{ __('forms.project') }}
                                    </label>
                                    <select class="form-control" id="cboProject" name="cboProject" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($project as $item)
                                            <option value="{{ $item->project_id ?? $item->id }}"
                                                {{ $release->project_id == ($item->project_id ?? $item->id) ? 'selected' : '' }}>
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
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipSubProject" style="cursor: pointer;"
                                                {{ empty($release->project_sub_id) || $release->project_sub_id == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label font-size-12 text-muted mb-0"
                                                for="skipSubProject" style="cursor: pointer;">
                                                រំលង/ មិនភ្ជាប់
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboSubProject" class="form-select" name="cboSubProject">
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboSubProject')
                                        <div class="text-danger font-size-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Category with Skip Toggle --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboCategory" class="form-label font-size-13 text-muted mb-0">
                                            ប្រភេទ (Category)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            @php
                                                $hasCategory = filled(
                                                    old(
                                                        'cboCategory',
                                                        $release->category_id ?? ($module->category_id ?? null),
                                                    ),
                                                );
                                            @endphp
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipCategory" style="cursor: pointer;"
                                                {{ $hasCategory ? '' : 'checked' }}>
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

                            {{-- Sub-Category with Skip Toggle --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboSubCategory" class="form-label font-size-13 text-muted mb-0">
                                            ប្រភេទរង (Sub-category)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            @php
                                                $hasSubCategory = filled(
                                                    old(
                                                        'cboSubCategory',
                                                        $release->category_sub_id ?? ($module->category_sub_id ?? null),
                                                    ),
                                                );
                                            @endphp
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipSubCategory" style="cursor: pointer;"
                                                {{ $hasSubCategory ? '' : 'checked' }}>
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
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboDetail" class="form-label font-size-13 text-muted mb-0">
                                            ព័ត៌មានលម្អិត (Detail)
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            @php
                                                $hasDetail = filled(
                                                    old('cboDetail', $release->detail ?? ($module->detail ?? null)),
                                                );
                                            @endphp
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipDetail" style="cursor: pointer;"
                                                {{ $hasDetail ? '' : 'checked' }}>
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

                            <input type="hidden" id="program_id" name="program_id">
                            <input type="hidden" id="program_sub_id" name="program_sub_id">
                            <input type="hidden" id="cluster_id" name="cluster_id">
                            <input type="hidden" id="account_sub_id" name="account_sub_id">

                            {{-- Agency --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agency"
                                        class="form-label font-size-13 text-muted">{{ __('forms.agency') }}</label>
                                    <select class="form-control" id="agency" name="agency"
                                        data-selected="{{ $release->agency_id ?? '' }}" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($Agency as $item)
                                            <option value="{{ $item->id }}"
                                                {{ isset($release->agency_id) && $release->agency_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
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
                                        value="{{ $release->date_release }}" placeholder="{{ __('forms.select_date') }}"
                                        required />
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
                                        @foreach ($items as $index => $row)
                                            <tr>
                                                <td class="form-group">
                                                    <select class="form-control item-select" name="p_name[]"
                                                        data-selected="{{ $row->p_name }}" required>
                                                        <option value="">ជ្រើសរើស</option>
                                                    </select>
                                                </td>
                                                <td class="form-group">
                                                    <select class="form-control unit-select" name="unit[]" required>
                                                        <option value="">ជ្រើសរើស</option>
                                                        @foreach ($unitType as $item)
                                                            <option value="{{ $item->name }}"
                                                                {{ $row->unit == $item->name || $row->unit == $item->id ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="form-group">
                                                    <input type="number" min="0" name="quantity[]"
                                                        step="0.01" value="{{ $row->quantity_request }}"
                                                        class="form-control" required>
                                                </td>
                                                <td class="form-group">
                                                    <input type="number" min="0" step="any" name="price[]"
                                                        value="{{ $row->price }}" class="form-control" required>
                                                </td>
                                                <td class="form-group">
                                                    <input type="text" name="source[]" value="{{ $row->source }}"
                                                        class="form-control source-input"
                                                        placeholder="{{ __('forms.source') }}">
                                                </td>
                                                <td class="form-group">
                                                    <input type="text" name="p_year[]" value="{{ $row->p_year }}"
                                                        class="form-control pro-year-input"
                                                        placeholder="{{ __('forms.pro.year') }}">
                                                </td>
                                                <td class="text-center">
                                                    @if ($index === 0)
                                                        <button type="button" class="btn btn-success addRow">+</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger removeRow">-</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button class="btn btn-primary" type="submit" name="submit" value="save"
                                    id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
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

    <script>
        window.materialReleaseConfig = {
            getProjectRoute: "{{ route('materialRelease.get.project') }}",
            getItemRoute: "{{ route('materialRelease.get.projectItem') }}",
            getCategoryRoute: "{{ route('materialRelease.get.category') }}",
            getSubCategoryRoute: "{{ route('materialRelease.get.subCategory') }}",
            getDetailRoute: "{{ route('materialRelease.get.detail') }}",
            searchPlaceholder: "{{ __('forms.search...') }}",
            initialSubProjectId: "{{ old('cboSubProject', $release->project_sub_id ?? ($module->project_sub_id ?? '')) }}",
            initialAgencyId: "{{ old('agency', $release->agency_id ?? ($module->agency_id ?? '')) }}",
            initialCategoryId: "{{ old('cboCategory', $release->category_id ?? ($module->category_id ?? '')) }}",
            initialSubCategoryId: "{{ old('cboSubCategory', $release->category_sub_id ?? ($module->category_sub_id ?? '')) }}",
            initialDetailId: "{{ old('cboDetail', $release->detail ?? ($module->detail ?? '')) }}",
            releaseId: "{{ $release->id ?? '' }}"
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

                const initialAgency = window.materialReleaseConfig.initialAgencyId || agencySelect.getAttribute(
                    'data-selected');
                if (initialAgency) {
                    setTimeout(() => {
                        agencyChoices.setChoiceByValue(String(initialAgency));
                    }, 100);
                }
            }

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

            const tableBody = document.querySelector('#itemTable tbody');
            if (tableBody) {
                Array.from(tableBody.rows).forEach(row => initRowChoices(row));
            }

            function resetChoicesDropdown(instance, emptyLabel = 'ជ្រើសរើស') {
                if (!instance) return;
                instance.clearStore();
                instance.setChoices([{
                    value: '',
                    label: emptyLabel,
                    selected: true,
                    disabled: true
                }], 'value', 'label', true);
            }

            function populateChoicesDropdown(instance, data, selectedValue = '') {
                if (!instance) return;
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

                const targetVal = selectedValue || (data.length === 1 ? String(data[0].value ?? data[0].id) : '');

                const choices = data.map(item => {
                    const val = String(item.value ?? item.id);
                    return {
                        value: val,
                        label: item.label ?? item.name ?? ('#' + item.id),
                        selected: targetVal !== '' && val === String(targetVal)
                    };
                });

                choices.unshift({
                    value: '',
                    label: 'ជ្រើសរើស',
                    selected: targetVal === '',
                    disabled: true
                });

                instance.setChoices(choices, 'value', 'label', true);

                if (targetVal !== '') {
                    instance.setChoiceByValue(String(targetVal));
                }
            }

            function isSkipped(checkboxId) {
                return Boolean(document.getElementById(checkboxId)?.checked);
            }

            function getActiveSubProjectId() {
                if (isSkipped('skipSubProject') || !subProjectSelect) return '';
                return subProjectSelect.value || '';
            }

            function getActiveCategoryId() {
                if (isSkipped('skipCategory') || !categorySelect) return '';
                return categorySelect.value || '';
            }

            function getActiveSubCategoryId() {
                if (isSkipped('skipSubCategory') || !subCategorySelect) return '';
                return subCategorySelect.value || '';
            }

            function getActiveDetailId() {
                if (isSkipped('skipDetail') || !detailSelect) return '';
                return detailSelect.value || '';
            }

            /**
             * Reusable Skip Toggle Helper (syncs initial state + change listener)
             */
            function bindSkipToggle({
                checkboxId,
                selectEl,
                getInstance,
                onSkipChange
            }) {
                const checkbox = document.getElementById(checkboxId);
                if (!checkbox || !selectEl) return;

                function applyState(skipped, shouldNotify = false) {
                    const instance = typeof getInstance === 'function' ? getInstance() : null;
                    const container = selectEl.closest('.choices');

                    if (skipped) {
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

                    if (shouldNotify && typeof onSkipChange === 'function') {
                        onSkipChange(skipped);
                    }
                }

                applyState(checkbox.checked, false);

                checkbox.addEventListener('change', function() {
                    applyState(this.checked, true);
                });
            }

            /**
             * Load Sub-Projects for a Project
             */
            function loadSubProjects(projectId, preselectSubProject = '') {
                resetChoicesDropdown(subProjectChoices);

                if (!projectId) {
                    return $.Deferred().resolve([]).promise();
                }

                return $.ajax({
                    url: window.materialReleaseConfig.getProjectRoute,
                    type: "GET",
                    data: {
                        project_id: projectId
                    },
                    dataType: "json"
                }).done(function(data) {
                    populateChoicesDropdown(subProjectChoices, data, preselectSubProject);
                });
            }

            /**
             * Cascade Loaders for Category -> Sub Category -> Detail
             */
            function loadCategories(projectId, subProjectId = '', preselectCategory = '') {
                resetChoicesDropdown(categoryChoices);
                resetChoicesDropdown(subCategoryChoices);
                resetChoicesDropdown(detailChoices);

                if (!projectId) return $.Deferred().resolve([]).promise();

                return $.ajax({
                    url: window.materialReleaseConfig.getCategoryRoute,
                    type: "GET",
                    data: {
                        project_id: projectId,
                        sub_project_id: subProjectId
                    },
                    dataType: "json"
                }).done(function(data) {
                    populateChoicesDropdown(categoryChoices, data, isSkipped('skipCategory') ? '' :
                        preselectCategory);
                });
            }

            function loadSubCategories(categoryId = '', preselectSubCategory = '') {
                resetChoicesDropdown(subCategoryChoices);
                resetChoicesDropdown(detailChoices);

                const projectId = cboProject ? cboProject.value : '';
                const subProjectId = getActiveSubProjectId();

                if (!projectId) return $.Deferred().resolve([]).promise();

                return $.ajax({
                    url: window.materialReleaseConfig.getSubCategoryRoute,
                    type: "GET",
                    data: {
                        project_id: projectId,
                        sub_project_id: subProjectId,
                        category_id: categoryId
                    },
                    dataType: "json"
                }).done(function(data) {
                    populateChoicesDropdown(subCategoryChoices, data, isSkipped('skipSubCategory') ? '' :
                        preselectSubCategory);
                });
            }

            function loadDetails(subCategoryId = '', preselectDetail = '') {
                resetChoicesDropdown(detailChoices);

                const projectId = cboProject ? cboProject.value : '';
                const subProjectId = getActiveSubProjectId();
                const categoryId = getActiveCategoryId();

                if (!projectId) return $.Deferred().resolve([]).promise();

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
                }).done(function(data) {
                    populateChoicesDropdown(detailChoices, data, isSkipped('skipDetail') ? '' :
                        preselectDetail);
                });
            }

            function refreshCascade(opts = {}) {
                const projectId = cboProject ? cboProject.value : '';
                const subProjectId = getActiveSubProjectId();
                const preCat = opts.preselectCategory ?? '';
                const preSubCat = opts.preselectSubCategory ?? '';
                const preDetail = opts.preselectDetail ?? '';

                if (!projectId) {
                    resetChoicesDropdown(categoryChoices);
                    resetChoicesDropdown(subCategoryChoices);
                    resetChoicesDropdown(detailChoices);
                    return $.Deferred().resolve().promise();
                }

                return loadCategories(projectId, subProjectId, preCat)
                    .then(() => {
                        const activeCat = getActiveCategoryId() || preCat;
                        return loadSubCategories(activeCat, preSubCat);
                    })
                    .then(() => {
                        const activeSubCat = getActiveSubCategoryId() || preSubCat;
                        return loadDetails(activeSubCat, preDetail);
                    });
            }

            function fetchAndPopulateItems(extraParams = {}) {
                const projectId = cboProject ? cboProject.value : '';
                const params = Object.assign({
                    project_id: projectId,
                    sub_project_id: getActiveSubProjectId(),
                    category_id: getActiveCategoryId(),
                    sub_category_id: getActiveSubCategoryId(),
                    detail_id: getActiveDetailId(),
                    release_id: window.materialReleaseConfig.releaseId
                }, extraParams);

                if (!projectId) {
                    $('.item-select').each(function() {
                        if (this.choicesInstance) {
                            resetChoicesDropdown(this.choicesInstance, 'មិនមានទិន្នន័យ');
                        }
                    });
                    return;
                }

                $.ajax({
                    url: window.materialReleaseConfig.getItemRoute,
                    type: "GET",
                    data: params,
                    dataType: "json",
                    success: function(data) {
                        $('.item-select').each(function() {
                            const select = this;
                            if (!select.choicesInstance) return;

                            const selectedVal = $(select).attr('data-selected') || select
                                .value || '';
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
                                const val = item.p_name || String(item.id);
                                return {
                                    value: val,
                                    label: item.p_name || 'Item #' + item.id,
                                    selected: String(val) === String(selectedVal),
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
                                selected: !selectedVal,
                                disabled: true
                            });

                            select.choicesInstance.setChoices(formattedChoices, 'value',
                                'label', true);

                            if (selectedVal) {
                                select.choicesInstance.setChoiceByValue(String(selectedVal));
                                $(select).trigger('change');
                            }
                        });
                    }
                });
            }

            /* Project Change Listener: Reload Sub-Projects, Cascade, and Items */
            if (cboProject) {
                cboProject.addEventListener('change', function() {
                    const newProjectId = this.value;

                    // Clear initial edit preselections once user switches project
                    window.materialReleaseConfig.initialSubProjectId = '';
                    window.materialReleaseConfig.initialCategoryId = '';
                    window.materialReleaseConfig.initialSubCategoryId = '';
                    window.materialReleaseConfig.initialDetailId = '';

                    // Clear old data-selected item values on project change so items drop cleanly
                    $('.item-select').removeAttr('data-selected');

                    loadSubProjects(newProjectId, '').then(() => {
                        return refreshCascade();
                    }).then(() => {
                        fetchAndPopulateItems();
                    });
                });
            }

            /* Sub-Project Change Listener */
            if (subProjectSelect) {
                subProjectSelect.addEventListener('change', function() {
                    if (isSkipped('skipSubProject')) return;
                    refreshCascade().then(() => fetchAndPopulateItems());
                });
            }

            /* Initial Edit-Mode Auto-Drop */
            if (cboProject && cboProject.value) {
                const initialProjectId = cboProject.value;
                const initialSubProjectId = isSkipped('skipSubProject') ? '' : window.materialReleaseConfig
                    .initialSubProjectId;
                const initialCategoryId = window.materialReleaseConfig.initialCategoryId;
                const initialSubCategoryId = window.materialReleaseConfig.initialSubCategoryId;
                const initialDetailId = window.materialReleaseConfig.initialDetailId;

                loadSubProjects(initialProjectId, initialSubProjectId).then(() => {
                    return refreshCascade({
                        preselectCategory: initialCategoryId,
                        preselectSubCategory: initialSubCategoryId,
                        preselectDetail: initialDetailId
                    });
                }).then(() => {
                    fetchAndPopulateItems();
                });
            }

            /* Category, Sub-Category & Detail Change Listeners */
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    if (isSkipped('skipCategory')) return;
                    loadSubCategories(this.value)
                        .then(() => loadDetails(getActiveSubCategoryId()))
                        .then(() => fetchAndPopulateItems());
                });
            }

            if (subCategorySelect) {
                subCategorySelect.addEventListener('change', function() {
                    if (isSkipped('skipSubCategory')) return;
                    loadDetails(this.value)
                        .then(() => fetchAndPopulateItems());
                });
            }

            if (detailSelect) {
                detailSelect.addEventListener('change', function() {
                    if (isSkipped('skipDetail')) return;
                    fetchAndPopulateItems();
                });
            }

            /* Auto-fill Unit, Price, Source, and Project Year on Item Selection */
            $(document).on('change', '.item-select', function() {
                const row = this.closest('tr');
                if (!row) return;

                const selectInstance = this.choicesInstance;
                if (!selectInstance) return;

                const selectedValue = selectInstance.getValue(true);
                if (!selectedValue) return;

                const activeChoice = selectInstance._store.choices.find(choice =>
                    String(choice.value) === String(selectedValue) && choice.active
                );

                if (activeChoice && activeChoice.customProperties) {
                    const props = activeChoice.customProperties;

                    const priceInput = row.querySelector('input[name="price[]"]');
                    if (priceInput && props.price !== undefined) {
                        priceInput.value = props.price;
                    }

                    const unitSelect = row.querySelector('.unit-select');
                    if (unitSelect && props.unit) {
                        if (unitSelect.choicesInstance) {
                            unitSelect.choicesInstance.setChoiceByValue(String(props.unit));
                        } else {
                            $(unitSelect).val(props.unit).trigger('change');
                        }
                    }

                    const sourceInput = row.querySelector('input[name="source[]"]');
                    if (sourceInput && props.source !== undefined) {
                        sourceInput.value = props.source;
                    }

                    const proYearInput = row.querySelector('input[name="p_year[]"]');
                    if (proYearInput && props.p_year !== undefined) {
                        proYearInput.value = props.p_year;
                    }
                }
            });

            /* Bind Skip Toggles for Sub-Project, Category, Sub-Category, and Detail */
            bindSkipToggle({
                checkboxId: 'skipSubProject',
                selectEl: subProjectSelect,
                getInstance: () => subProjectChoices,
                onSkipChange: (skipped) => {
                    const preSub = skipped ? '' : window.materialReleaseConfig.initialSubProjectId;
                    loadSubProjects(cboProject?.value || '', preSub)
                        .then(() => refreshCascade())
                        .then(() => fetchAndPopulateItems());
                }
            });

            bindSkipToggle({
                checkboxId: 'skipCategory',
                selectEl: categorySelect,
                getInstance: () => categoryChoices,
                onSkipChange: (skipped) => {
                    if (skipped) {
                        loadSubCategories('')
                            .then(() => loadDetails(getActiveSubCategoryId()))
                            .then(() => fetchAndPopulateItems());
                    } else {
                        const projectId = cboProject ? cboProject.value : '';
                        loadCategories(projectId, getActiveSubProjectId(), window.materialReleaseConfig
                                .initialCategoryId)
                            .then(() => loadSubCategories(getActiveCategoryId(), window
                                .materialReleaseConfig.initialSubCategoryId))
                            .then(() => loadDetails(getActiveSubCategoryId(), window
                                .materialReleaseConfig.initialDetailId))
                            .then(() => fetchAndPopulateItems());
                    }
                }
            });

            bindSkipToggle({
                checkboxId: 'skipSubCategory',
                selectEl: subCategorySelect,
                getInstance: () => subCategoryChoices,
                onSkipChange: (skipped) => {
                    if (skipped) {
                        loadDetails('')
                            .then(() => fetchAndPopulateItems());
                    } else {
                        loadSubCategories(getActiveCategoryId(), window.materialReleaseConfig
                                .initialSubCategoryId)
                            .then(() => loadDetails(getActiveSubCategoryId(), window
                                .materialReleaseConfig.initialDetailId))
                            .then(() => fetchAndPopulateItems());
                    }
                }
            });

            bindSkipToggle({
                checkboxId: 'skipDetail',
                selectEl: detailSelect,
                getInstance: () => detailChoices,
                onSkipChange: (skipped) => {
                    if (!skipped) {
                        loadDetails(getActiveSubCategoryId(), window.materialReleaseConfig
                                .initialDetailId)
                            .then(() => fetchAndPopulateItems());
                    } else {
                        fetchAndPopulateItems();
                    }
                }
            });

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
                        fetchAndPopulateItems();
                    }

                    if (e.target.classList.contains('removeRow')) {
                        const row = e.target.closest('tr');
                        row.querySelectorAll('select').forEach(select => {
                            if (select.choicesInstance) select.choicesInstance.destroy();
                        });
                        row.remove();
                    }
                });
            }

            function initRowChoices(row) {
                const itemSelect = row.querySelector('.item-select');
                const unitSelect = row.querySelector('.unit-select');

                if (itemSelect && !itemSelect.choicesInstance) {
                    itemSelect.choicesInstance = new Choices(itemSelect, defaultChoicesConfig);
                }

                if (unitSelect && !unitSelect.choicesInstance) {
                    unitSelect.choicesInstance = new Choices(unitSelect, defaultChoicesConfig);
                }
            }
        });
    </script>
@endsection
