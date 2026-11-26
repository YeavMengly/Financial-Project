@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.material.entry') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.material') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.entry') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a></li>
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
                    <form id="pristine-valid-example" action="{{ route('materialEntry.store', $params) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row">
                            {{-- Project Selection --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboProject" class="form-label font-size-13 text-muted">
                                        ជ្រើសរើស{{ __('forms.project') }}
                                    </label>
                                    <select class="form-select" id="cboProject" name="cboProject" required tabindex="1">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($project as $p)
                                            <option value="{{ $p->id }}">{{ $p->stock_number }} -
                                                {{ $p->stock_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboProject')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Sub Project with Skip Toggle --}}
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label for="cboSubProject" class="form-label font-size-13 text-muted mb-0">
                                            {{ __('forms.sub.project') }}
                                        </label>
                                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch"
                                                id="skipSubProject" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted mb-0"
                                                for="skipSubProject" style="cursor: pointer;">
                                                រំលង
                                            </label>
                                        </div>
                                    </div>
                                    <select id="cboSubProject" class="form-select" name="cboSubProject" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>
                                    @error('cboSubProject')
                                        <div class="pristine-error text-help">{{ $message }}</div>
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
                            <!-- Hidden inputs -->
                            <input type="hidden" id="program_id" name="program_id">
                            <input type="hidden" id="program_sub_id" name="program_sub_id">
                            <input type="hidden" id="cluster_id" name="cluster_id">
                            <input type="hidden" id="account_sub_id" name="account_sub_id">
                            {{-- Product Table --}}
                            <div class="col-xl-12 col-md-12 mt-3">
                                <table class="table table-bordered" id="itemTable">
                                    <thead>
                                        <tr>
                                            <th width="300">{{ __('forms.item.name') }}</th>
                                            <th width="150">{{ __('forms.unit') }}</th>
                                            <th width="150">{{ __('forms.quantity') }}</th>
                                            <th width="150">{{ __('forms.price') }}ឯកតា</th>
                                            <th width="200" class="align-middle">{{ __('forms.source') }}</th>
                                            <th width="200" class="align-middle">{{ __('forms.pro.year') }}</th>
                                            <th width="60">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="form-group">
                                                <input type="text" name="p_name[]" class="form-control" required>
                                            </td>
                                            {{-- <td class="form-group">
                                                <select class="form-control unit-select" name="unit[]" required>
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($unitType as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td> --}}

                                            <td class="form-group">
                                                <select class="form-control unit-select" name="unit[]" required>
                                                    <option value="">ជ្រើសរើស</option>

                                                    @foreach ($unitType as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach

                                                    <option value="__add_new__">➕ បន្ថែម​ {{ __('menus.unit') }} ថ្មី
                                                    </option>
                                                </select>
                                            </td>
                                            {{-- Modal Unit --}}
                                            <div class="modal fade" id="addUnitModal" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">បន្ថែម {{ __('menus.unit') }} ថ្មី
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <label class="form-label">{{ __('menus.unit') }}</label>

                                                            <input type="text" id="newUnitName" class="form-control"
                                                                placeholder="បញ្ចូល {{ __('menus.unit') }} ថ្មី">

                                                            <div id="unitError" class="text-danger mt-2"
                                                                style="display:none;">
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">
                                                                បោះបង់
                                                            </button>

                                                            <button type="button" class="btn btn-primary"
                                                                id="saveNewUnit">
                                                                រក្សាទុក
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <td class="form-group">
                                                <input type="number" min="0" name="quantity[]" step="0.01"
                                                    class="form-control" required>
                                            </td>
                                            <td class="form-group">
                                                <input type="number" min="0" step="any" name="price[]"
                                                    class="form-control" required>
                                            </td>
                                            <td class="form-group">
                                                {{-- Optional field (required removed) --}}
                                                <input type="text" name="source[]" class="form-control source-input"
                                                    placeholder="{{ __('forms.source') }}">
                                            </td>
                                            <td class="form-group">
                                                {{-- Optional field (required removed) --}}
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

                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="submit" class="btn btn-primary"
                                id="insertToTableBtn">{{ __('buttons.save') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>
                            <a class="btn btn-dark"
                                href="{{ route('materialEntry.index', $params) }}">{{ __('buttons.back') }}</a>
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
        window.materialEntryConfig = {
            getProjectRoute: "{{ route('materialEntry.get.project') }}",
            searchPlaceholder: "{{ __('forms.search...') }}"
        };

        let subProjectChoices = null;
        let categoryChoices = null;
        let subCategoryChoices = null;
        let detailChoices = null;
        const choicesMap = new WeakMap();

        function initChoicesInstance(element) {
            if (!element) return null;
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

        function destroyChoicesInstance(element) {
            if (choicesMap.has(element)) {
                const instance = choicesMap.get(element);
                if (instance && typeof instance.destroy === 'function') {
                    instance.destroy();
                }
                choicesMap.delete(element);
                element.classList.remove('choices-initialized');
            }
        }

        // Toggle Table Skip / Disabled State on Change
        $(document).on('change', '#skipItemTable', function() {
            const wrapper = $('#itemTableWrapper');
            const inputs = $('#itemTable').find('input, select');

            if (this.checked) {
                wrapper.addClass('table-skipped');
                inputs.each(function() {
                    $(this).prop('disabled', true).removeAttr('required');
                    if ($(this).hasClass('choices-input') || $(this).data('choice')) {
                        const choiceInstance = choicesMap.get(this);
                        if (choiceInstance && typeof choiceInstance.disable === 'function') {
                            choiceInstance.disable();
                        }
                    }
                });
            } else {
                wrapper.removeClass('table-skipped');
                inputs.each(function() {
                    $(this).prop('disabled', false);
                    if ($(this).hasClass('choices-input') || $(this).data('choice')) {
                        const choiceInstance = choicesMap.get(this);
                        if (choiceInstance && typeof choiceInstance.enable === 'function') {
                            choiceInstance.enable();
                        }
                    }
                });
            }
        });

        if ($('#skipItemTable').is(':checked')) {
            $('#skipItemTable').trigger('change');
        }

        function updateRowIndices() {
            $('#itemTable tbody tr').each(function(newIndex) {
                $(this).attr('data-row-index', newIndex);

                $(this).find('input[name^="sub_pro"]').attr('name', `sub_pro[${newIndex}]`);
                $(this).find('input[name^="item_id"]').attr('name', `item_id[${newIndex}][]`);

                $(this).find('select.cboSubAccount').attr('name', `accountSub[${newIndex}]`);
                $(this).find('select.cboProgram').attr('name', `program[${newIndex}]`);
                $(this).find('select.cboProgramSub').attr('name', `cboProgramSub[${newIndex}]`);
                $(this).find('select.cboCluster').attr('name', `cboCluster[${newIndex}]`);

                $(this).find('.category-block').each(function(catIndex) {
                    $(this).attr('data-cat-index', catIndex);
                    $(this).find('.category-item input').attr('name', `category[${newIndex}][${catIndex}]`);

                    $(this).find('.subcategory-item').each(function(subCatIndex) {
                        $(this).attr('data-subcat-index', subCatIndex);
                        $(this).find('> div > input').attr('name',
                            `sub_category[${newIndex}][${catIndex}][${subCatIndex}]`);

                        $(this).find('.detail-item input').each(function() {
                            $(this).attr('name',
                                `detail[${newIndex}][${catIndex}][${subCatIndex}][]`);
                        });
                    });
                });
            });
        }

        /* ==========================================================================
           INITIALIZATION ON DOM CONTENT LOADED
           ========================================================================== */
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            // Summernote
            if ($('#vNote').length) {
                $('#vNote').summernote({
                    height: 120,
                    callbacks: {
                        onChange: function(contents) {
                            $('#vNote').val(contents);
                        }
                    }
                });
            }
            if ($('#vRefer').length) {
                $('#vRefer').summernote({
                    height: 120,
                    callbacks: {
                        onChange: function(contents) {
                            $('#vRefer').val(contents);
                        }
                    }
                });
            }

            // Flatpickr
            const dateInput = document.getElementById('date');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true
                });
            }

            // Main Project Dropdown
            const cboProject = document.getElementById('cboProject');
            if (cboProject) {
                new Choices(cboProject, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false,
                    allowHTML: true
                });
            }

            // Sub Project Dropdown
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

            // Category Dropdown
            const categorySelect = document.getElementById('cboCategory');
            if (categorySelect) {
                categoryChoices = new Choices(categorySelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false,
                    allowHTML: true
                });
            }

            // Sub Category Dropdown
            const subCategorySelect = document.getElementById('cboSubCategory');
            if (subCategorySelect) {
                subCategoryChoices = new Choices(subCategorySelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false,
                    allowHTML: true
                });
            }

            // Detail Dropdown
            const detailSelect = document.getElementById('cboDetail');
            if (detailSelect) {
                detailChoices = new Choices(detailSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false,
                    allowHTML: true
                });
            }

            document.querySelectorAll('.cboProgram, .cboSubAccount, .cboProgramSub, .cboCluster').forEach(function(
                select) {
                initChoicesInstance(select);
            });

            const skipSubProjectCheckbox = document.getElementById('skipSubProject');
            const skipCategoryCheckbox = document.getElementById('skipCategory');
            const skipSubCategoryCheckbox = document.getElementById('skipSubCategory');
            const skipDetailCheckbox = document.getElementById('skipDetail');
            const submitBtn = document.getElementById('insertToTableBtn');

            // Skip logic handler
            function setupSkipField(checkbox, inputsSelector, isChoices = false, choicesInstance = null) {
                if (!checkbox) return;

                const updateState = () => {
                    const elements = document.querySelectorAll(inputsSelector);
                    elements.forEach(input => {
                        const choicesWrapper = input.closest('.choices');

                        if (checkbox.checked) {
                            input.value = '';
                            if (isChoices && choicesInstance) {
                                choicesInstance.removeActiveItems();
                                choicesInstance.disable();
                                if (choicesWrapper) choicesWrapper.classList.add('choices-skip-active');
                            } else {
                                input.disabled = true;
                                input.classList.add('skip-active');
                            }
                            input.removeAttribute('required');
                        } else {
                            if (isChoices && choicesInstance) {
                                choicesInstance.enable();
                                if (choicesWrapper) choicesWrapper.classList.remove(
                                    'choices-skip-active');
                            } else {
                                input.disabled = false;
                                input.classList.remove('skip-active');
                            }
                            input.setAttribute('required', 'required');
                        }
                    });
                };

                checkbox.addEventListener('change', updateState);
            }

            setupSkipField(skipSubProjectCheckbox, '#cboSubProject', true, subProjectChoices);
            setupSkipField(skipCategoryCheckbox, '#cboCategory', true, categoryChoices);
            setupSkipField(skipSubCategoryCheckbox, '#cboSubCategory', true, subCategoryChoices);
            setupSkipField(skipDetailCheckbox, '#cboDetail', true, detailChoices);

            // Pre-load cascade dropdowns for edit views
            $('#itemTable tbody tr').each(function() {
                const row = $(this);
                const programSelect = row.find('.cboProgram')[0];
                const subSelect = row.find('.cboProgramSub')[0];
                const clusterSelect = row.find('.cboCluster')[0];

                const programVal = $(programSelect).val() || $(subSelect).attr('data-program-id');
                const selectedSubId = $(subSelect).attr('data-selected');
                const selectedClusterId = $(clusterSelect).attr('data-selected');

                const subChoices = choicesMap.get(subSelect);
                const clusterChoices = choicesMap.get(clusterSelect);

                if (programVal) {
                    $.ajax({
                        url: "{{ route('project.by.program_sub', $params) }}",
                        type: 'GET',
                        data: {
                            program_id: programVal
                        },
                        success: function(response) {
                            if (Array.isArray(response) && subChoices) {
                                const options = response.map(item => ({
                                    value: String(item.value),
                                    label: item.label,
                                    selected: String(item.value) === String(
                                        selectedSubId)
                                }));
                                options.unshift({
                                    value: '',
                                    label: '{{ __('forms.search...') }}',
                                    selected: !selectedSubId
                                });

                                subChoices.setChoices(options, 'value', 'label', true);
                                if (selectedSubId) {
                                    subChoices.setChoiceByValue(String(selectedSubId));
                                }

                                const currentSubVal = selectedSubId || $(subSelect).val();
                                if (currentSubVal) {
                                    $.ajax({
                                        url: "{{ route('project.by.cluster', $params) }}",
                                        type: 'GET',
                                        data: {
                                            program_sub_id: currentSubVal
                                        },
                                        success: function(clusterResponse) {
                                            if (Array.isArray(clusterResponse) &&
                                                clusterChoices) {
                                                const clusterOptions =
                                                    clusterResponse.map(item => ({
                                                        value: String(item
                                                            .value),
                                                        label: item.label,
                                                        selected: String(
                                                                item.value
                                                            ) ===
                                                            String(
                                                                selectedClusterId
                                                            )
                                                    }));
                                                clusterOptions.unshift({
                                                    value: '',
                                                    label: '{{ __('forms.search...') }}',
                                                    selected: !
                                                        selectedClusterId
                                                });

                                                clusterChoices.setChoices(
                                                    clusterOptions, 'value',
                                                    'label', true);
                                                if (selectedClusterId) {
                                                    clusterChoices.setChoiceByValue(
                                                        String(
                                                            selectedClusterId));
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });

            // Program -> Sub Program Cascade
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
                        }
                    }
                });
            });

            // Sub Program -> Cluster Cascade
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
                        }
                    }
                });
            });

            // Add Main Table Row
            $(document).on('click', '.addRow', function(e) {
                e.preventDefault();
                const tableBody = $('#itemTable tbody');
                let rowIndex = tableBody.children('tr').length;

                const newRowHtml = `
            <tr data-row-index="${rowIndex}">
                <td>
                    <input type="hidden" name="item_id[${rowIndex}][]" value="">
                    <input type="text" name="p_name[]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <select class="form-control form-control-sm unit-select" name="unit[]" required>
                        <option value="">ជ្រើសរើស</option>
                        @foreach ($unitType as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                        <option value="__add_new__">➕ បន្ថែម {{ __('menus.unit') }} ថ្មី</option>
                    </select>
                </td>
                <td>
                    <input type="number" min="0" name="quantity[]" step="0.01" class="form-control form-control-sm" required>
                </td>
                <td>
                    <input type="number" min="0" step="any" name="price[]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <input type="text" name="source[]" class="form-control form-control-sm source-input" placeholder="{{ __('forms.source') }}">
                </td>
                <td>
                    <input type="text" name="p_year[]" class="form-control form-control-sm pro-year-input" placeholder="{{ __('forms.pro.year') }}">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-danger btn-sm removeRow" title="លុបជួរដេក">-</button>
                </td>
            </tr>`;

                tableBody.append(newRowHtml);
                const appendedRow = tableBody.children().last();

                appendedRow.find('select').each(function() {
                    initChoicesInstance(this);
                });

                updateRowIndices();
            });

            // Remove Main Table Row
            $(document).on('click', '.removeRow', function(e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                row.find('select').each(function() {
                    destroyChoicesInstance(this);
                });
                row.remove();
                updateRowIndices();
            });

            /* ----------------------------------------------------------------------
               AJAX CASCADING DROPDOWNS HANDLER
               ---------------------------------------------------------------------- */
            const projectSelect = document.getElementById('cboProject');

            if (projectSelect && subProjectSelect) {
                projectSelect.addEventListener('change', function() {
                    const projectId = this.value;

                    $('#program_id').val('');
                    $('#program_sub_id').val('');
                    $('#cluster_id').val('');
                    $('#account_sub_id').val('');

                    if (subProjectChoices) subProjectChoices.clearStore();
                    if (categoryChoices) {
                        categoryChoices.clearStore();
                        categoryChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }
                    if (subCategoryChoices) {
                        subCategoryChoices.clearStore();
                        subCategoryChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }
                    if (detailChoices) {
                        detailChoices.clearStore();
                        detailChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }

                    if (!projectId) {
                        if (subProjectChoices) {
                            subProjectChoices.setChoices([{
                                value: '',
                                label: window.materialEntryConfig.searchPlaceholder,
                                selected: true,
                                disabled: true
                            }], 'value', 'label', true);
                        }
                        return;
                    }

                    $.ajax({
                        url: window.materialEntryConfig.getProjectRoute,
                        type: "GET",
                        data: {
                            project_id: projectId
                        },
                        dataType: "json",
                        beforeSend: function() {
                            if (subProjectChoices) {
                                subProjectChoices.clearStore();
                                subProjectChoices.setChoices([{
                                    value: '',
                                    label: 'កំពុងផ្ទុក...',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                            }
                        },
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
                                return;
                            }

                            const choices = data.map(function(item) {
                                return {
                                    value: String(item.value),
                                    label: item.label,
                                    customProperties: {
                                        program_id: item.program_id || '',
                                        program_sub_id: item.program_sub_id || '',
                                        cluster_id: item.cluster_id || '',
                                        account_sub_id: item.account_sub_id || '',
                                        hierarchy: item.hierarchy || {}
                                    }
                                };
                            });

                            subProjectChoices.setChoices(choices, 'value', 'label', true);

                            if (skipSubProjectCheckbox && skipSubProjectCheckbox.checked) {
                                subProjectChoices.disable();
                            }
                        },
                        error: function() {
                            if (subProjectChoices) {
                                subProjectChoices.clearStore();
                                subProjectChoices.setChoices([{
                                    value: '',
                                    label: 'មិនអាចទិន្នន័យផ្ទុកបាន',
                                    selected: true,
                                    disabled: true
                                }], 'value', 'label', true);
                            }
                        }
                    });
                });

                // 1. Sub-Project Change -> Populates Categories, Sub-Categories, and Details instantly
                subProjectSelect.addEventListener('change', function() {
                    if (!subProjectChoices) return;

                    const selected = subProjectChoices.getValue();

                    if (categoryChoices) categoryChoices.clearStore();
                    if (subCategoryChoices) {
                        subCategoryChoices.clearStore();
                        subCategoryChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }
                    if (detailChoices) {
                        detailChoices.clearStore();
                        detailChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);
                    }

                    if (!selected || !selected.value) {
                        $('#program_id').val('');
                        $('#program_sub_id').val('');
                        $('#cluster_id').val('');
                        $('#account_sub_id').val('');
                        return;
                    }

                    const props = selected.customProperties || {};
                    $('#program_id').val(props.program_id || '');
                    $('#program_sub_id').val(props.program_sub_id || '');
                    $('#cluster_id').val(props.cluster_id || '');
                    $('#account_sub_id').val(props.account_sub_id || '');

                    const hierarchy = props.hierarchy || {};
                    const catKeys = Object.keys(hierarchy);

                    if (catKeys.length > 0 && categoryChoices && (!skipCategoryCheckbox || !
                            skipCategoryCheckbox.checked)) {
                        const shouldAutoSelect = (catKeys.length === 1);
                        const catOpts = catKeys.map((val, idx) => ({
                            value: String(val),
                            label: String(val),
                            selected: shouldAutoSelect && idx === 0
                        }));
                        categoryChoices.setChoices(catOpts, 'value', 'label', true);

                        if (shouldAutoSelect) {
                            $('#cboCategory').trigger('change');
                        }
                    }

                    let allSubCats = [];
                    let allDetails = [];

                    Object.values(hierarchy).forEach(subObj => {
                        Object.keys(subObj).forEach(subCatKey => {
                            if (subCatKey && !allSubCats.includes(subCatKey)) allSubCats
                                .push(subCatKey);

                            const dets = subObj[subCatKey] || [];
                            dets.forEach(d => {
                                if (d && d !== '-' && !allDetails.includes(d))
                                    allDetails.push(d);
                            });
                        });
                    });

                    if (allSubCats.length > 0 && subCategoryChoices) {
                        const subCatOpts = allSubCats.map((val, idx) => ({
                            value: String(val),
                            label: String(val),
                            selected: allSubCats.length === 1 && idx === 0
                        }));
                        subCategoryChoices.setChoices(subCatOpts, 'value', 'label', true);
                    }

                    if (allDetails.length > 0 && detailChoices) {
                        const detailOpts = allDetails.map((val, idx) => ({
                            value: String(val),
                            label: String(val),
                            selected: allDetails.length === 1 && idx === 0
                        }));
                        detailChoices.setChoices(detailOpts, 'value', 'label', true);
                    }
                });

                // 2. Category Change -> Filters Sub-Category & Detail Dropdowns dynamically
                const categorySelectElement = document.getElementById('cboCategory');
                if (categorySelectElement) {
                    categorySelectElement.addEventListener('change', function() {
                        if (!subProjectChoices || !subCategoryChoices || !detailChoices) return;

                        const selectedSubProj = subProjectChoices.getValue();
                        const selectedCat = this.value;

                        subCategoryChoices.clearStore();
                        subCategoryChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);

                        detailChoices.clearStore();
                        detailChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);

                        if (!selectedSubProj || !selectedSubProj.customProperties || !selectedCat) return;

                        const hierarchy = selectedSubProj.customProperties.hierarchy || {};
                        const subCatsObj = hierarchy[selectedCat] || {};
                        const subCatKeys = Object.keys(subCatsObj);

                        if (subCatKeys.length > 0 && (!skipSubCategoryCheckbox || !skipSubCategoryCheckbox
                                .checked)) {
                            const shouldAutoSelectSub = (subCatKeys.length === 1);
                            const subCatOpts = subCatKeys.map((val, idx) => ({
                                value: String(val),
                                label: String(val),
                                selected: shouldAutoSelectSub && idx === 0
                            }));
                            subCategoryChoices.setChoices(subCatOpts, 'value', 'label', true);

                            if (shouldAutoSelectSub) {
                                $('#cboSubCategory').trigger('change');
                            }
                        }

                        let categoryDetails = [];
                        Object.values(subCatsObj).forEach(detailsArr => {
                            if (Array.isArray(detailsArr)) {
                                detailsArr.forEach(d => {
                                    if (d && d !== '-' && !categoryDetails.includes(d)) {
                                        categoryDetails.push(d);
                                    }
                                });
                            }
                        });

                        const filteredDetails = categoryDetails.filter(v => v !== null && v !== undefined &&
                            String(v).trim() !== '');

                        if (filteredDetails.length > 0 && (!skipDetailCheckbox || !skipDetailCheckbox
                                .checked)) {
                            const shouldAutoSelectDetail = (filteredDetails.length === 1);
                            const detailOpts = filteredDetails.map((val, idx) => ({
                                value: String(val),
                                label: String(val),
                                selected: shouldAutoSelectDetail && idx === 0
                            }));
                            detailChoices.setChoices(detailOpts, 'value', 'label', true);
                        }
                    });
                }

                // 3. Sub-Category Change -> Filters Detail Dropdown specifically
                const subCategorySelectElement = document.getElementById('cboSubCategory');
                if (subCategorySelectElement) {
                    subCategorySelectElement.addEventListener('change', function() {
                        if (!subProjectChoices || !detailChoices) return;

                        const selectedSubProj = subProjectChoices.getValue();
                        const selectedCat = categoryChoices ? categoryChoices.getValue(true) : '';
                        const selectedSubCat = this.value;

                        detailChoices.clearStore();
                        detailChoices.setChoices([{
                            value: '',
                            label: window.materialEntryConfig.searchPlaceholder,
                            selected: true,
                            disabled: true
                        }], 'value', 'label', true);

                        if (!selectedSubProj || !selectedSubProj.customProperties || !selectedCat || !
                            selectedSubCat) return;

                        const hierarchy = selectedSubProj.customProperties.hierarchy || {};
                        let detailsArr = hierarchy[selectedCat]?.[selectedSubCat] || [];

                        if (detailsArr.length === 0 && Array.isArray(selectedSubProj.customProperties
                                .details)) {
                            detailsArr = selectedSubProj.customProperties.details;
                        }

                        const filteredDetails = detailsArr.filter(v => v !== null && v !== undefined &&
                            String(v).trim() !== '');

                        if (filteredDetails.length > 0 && (!skipDetailCheckbox || !skipDetailCheckbox
                                .checked)) {
                            const shouldAutoSelect = (filteredDetails.length === 1);
                            const detailOpts = filteredDetails.map((val, idx) => ({
                                value: String(val),
                                label: String(val),
                                selected: shouldAutoSelect && idx === 0
                            }));

                            detailChoices.setChoices(detailOpts, 'value', 'label', true);
                        }
                    });
                }
            }

            // Form Submit Handler
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (skipSubProjectCheckbox && skipSubProjectCheckbox.checked && subProjectSelect) {
                        subProjectSelect.removeAttribute('required');
                        subProjectSelect.disabled = false;
                        subProjectSelect.value = '';
                    }
                    if (skipCategoryCheckbox && skipCategoryCheckbox.checked && categorySelect) {
                        categorySelect.removeAttribute('required');
                        categorySelect.disabled = false;
                        categorySelect.value = '';
                    }
                    if (skipSubCategoryCheckbox && skipSubCategoryCheckbox.checked && subCategorySelect) {
                        subCategorySelect.removeAttribute('required');
                        subCategorySelect.disabled = false;
                        subCategorySelect.value = '';
                    }
                    if (skipDetailCheckbox && skipDetailCheckbox.checked && detailSelect) {
                        detailSelect.removeAttribute('required');
                        detailSelect.disabled = false;
                        detailSelect.value = '';
                    }

                    const skipTableSwitch = document.getElementById('skipItemTable');
                    if (skipTableSwitch && skipTableSwitch.checked) {
                        $('#itemTable').find('input, select').prop('disabled', true).removeAttr('required');
                    }

                    if ($('#vNote').length) $('#vNote').val($('#vNote').summernote('code'));
                    if ($('#vRefer').length) $('#vRefer').val($('#vRefer').summernote('code'));

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> កំពុងរក្សាទុក...`;
                    }
                    form.submit();
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let currentSelect = null;

            // When user selects "Add New Unit"
            $(document).on('change', '.unit-select', function() {

                if ($(this).val() === '__add_new__') {

                    currentSelect = $(this);

                    // Reset input
                    $('#newUnitName').val('');
                    $('#unitError').hide().text('');

                    // Open modal
                    $('#addUnitModal').modal('show');

                    // Reset select
                    $(this).val('');
                }
            });


            // Save new unit
            $('#saveNewUnit').on('click', function() {

                const unitName = $('#newUnitName').val().trim();

                if (!unitName) {
                    $('#unitError')
                        .text('សូមបញ្ចូលឈ្មោះ ឯកតា')
                        .show();

                    return;
                }

                const button = $(this);

                button.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('unit-types.store') }}",
                    type: "POST",

                    data: {
                        _token: "{{ csrf_token() }}",
                        name: unitName
                    },

                    success: function(response) {

                        if (response.success) {

                            // Add new option to current select
                            const option = new Option(
                                response.data.name,
                                response.data.id,
                                true,
                                true
                            );

                            currentSelect.append(option);

                            // Select new unit
                            currentSelect.val(response.data.id);

                            // Close modal
                            $('#addUnitModal').modal('hide');

                            // Reset
                            $('#newUnitName').val('');
                            $('#unitError').hide();
                        }
                    },

                    error: function(xhr) {

                        let message = 'មិនអាចបន្ថែម ឯកតា បានទេ។';

                        if (xhr.responseJSON?.errors?.name) {
                            message = xhr.responseJSON.errors.name[0];
                        }

                        $('#unitError')
                            .text(message)
                            .show();
                    },

                    complete: function() {

                        button.prop('disabled', false)
                            .html('រក្សាទុក');
                    }
                });
            });

        });
    </script>
@endsection
