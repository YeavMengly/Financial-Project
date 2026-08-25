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
                            <div class="col-lg-4 col-md-6">
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
                            <div class="col-lg-4 col-md-6">
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
                                                រំលង/ មិនភ្ជាប់
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
                                            <td class="form-group">
                                                <select class="form-control unit-select" name="unit[]" required>
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($unitType as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="form-group">
                                                <input type="number" min="0" name="quantity[]" class="form-control"
                                                    required>
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

        /* ==========================================================================
           1. INITIALIZATION ON DOM CONTENT LOADED
           ========================================================================== */
        document.addEventListener('DOMContentLoaded', function() {
            // Summernote
            if ($('#vNote, #vRefer').length) {
                $('#vNote, #vRefer').summernote({
                    backColor: 'red',
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['color', ['color']],
                    ]
                });
            }

            // Flatpickr
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

            const form = document.getElementById('pristine-valid-example');
            const skipSubProjectCheckbox = document.getElementById('skipSubProject');
            const submitBtn = document.getElementById('insertToTableBtn');

            // Skip logic handler
            function setupSkipField(checkbox, inputsSelector, isChoices = false) {
                if (!checkbox) return;

                const updateState = () => {
                    const elements = document.querySelectorAll(inputsSelector);
                    elements.forEach(input => {
                        const choicesWrapper = input.closest('.choices');

                        if (checkbox.checked) {
                            input.value = '';
                            if (isChoices && subProjectChoices) {
                                subProjectChoices.removeActiveItems();
                                subProjectChoices.disable();
                                if (choicesWrapper) choicesWrapper.classList.add('choices-skip-active');
                            } else {
                                input.disabled = true;
                                input.classList.add('skip-active');
                            }
                            input.removeAttribute('required');
                        } else {
                            if (isChoices && subProjectChoices) {
                                subProjectChoices.enable();
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

            setupSkipField(skipSubProjectCheckbox, '#cboSubProject', true);

            // Robust Form Submit Handler (Single-Click Prevention)
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (skipSubProjectCheckbox && skipSubProjectCheckbox.checked) {
                        if (subProjectSelect) {
                            subProjectSelect.removeAttribute('required');
                            subProjectSelect.disabled = false;
                            subProjectSelect.value = ''; // Submit empty value to Laravel
                        }
                    }

                    // Native HTML validation check
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        form.reportValidity();

                        // Re-disable choices if check failed
                        if (skipSubProjectCheckbox && skipSubProjectCheckbox.checked && subProjectChoices) {
                            subProjectChoices.disable();
                        }
                        return false;
                    }

                    // Disable button instantly upon valid submit
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> កំពុងរក្សាទុក...';
                    }
                });
            }

            /* ----------------------------------------------------------------------
               2. DYNAMIC TABLE ROWS (Add / Remove)
               ---------------------------------------------------------------------- */
            const tableBody = document.querySelector('#itemTable tbody');

            if (tableBody && tableBody.rows[0]) {
                initRowChoices(tableBody.rows[0]);
            }

            if (tableBody) {
                tableBody.addEventListener('click', function(e) {
                    if (e.target.classList.contains('addRow')) {
                        const firstRow = tableBody.rows[0];
                        const newRow = firstRow.cloneNode(true);

                        newRow.querySelectorAll('.choices').forEach(choicesWrapper => {
                            const originalSelect = choicesWrapper.querySelector('select');
                            if (originalSelect) {
                                originalSelect.classList.remove('choices-initialized');
                                originalSelect.removeAttribute('data-choice');
                                originalSelect.removeAttribute('tabindex');
                                originalSelect.removeAttribute('aria-hidden');
                                originalSelect.style.display = '';
                                originalSelect.selectedIndex = 0;

                                Array.from(originalSelect.options).forEach(opt => {
                                    opt.removeAttribute('selected');
                                });

                                choicesWrapper.parentNode.replaceChild(originalSelect,
                                    choicesWrapper);
                            }
                        });

                        newRow.querySelectorAll('input').forEach(input => {
                            if (input.type !== 'checkbox') input.value = '';
                        });

                        const btn = newRow.querySelector('button');
                        btn.classList.remove('btn-success', 'addRow');
                        btn.classList.add('btn-danger', 'removeRow');
                        btn.textContent = '-';

                        tableBody.appendChild(newRow);
                        initRowChoices(newRow);
                    }

                    if (e.target.classList.contains('removeRow')) {
                        e.target.closest('tr').remove();
                    }
                });
            }

            function initRowChoices(row) {
                const unitSelect = row.querySelector('.unit-select');
                if (unitSelect && !unitSelect.classList.contains('choices-initialized')) {
                    unitSelect.value = '';
                    new Choices(unitSelect, {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: 'ជ្រើសរើស',
                        searchPlaceholderValue: 'ស្វែងរក...',
                        shouldSort: false,
                        allowHTML: true
                    });
                    unitSelect.classList.add('choices-initialized');
                }
            }

            /* ----------------------------------------------------------------------
               3. AJAX SUB-PROJECT HANDLER
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
                                        account_sub_id: item.account_sub_id || ''
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

                subProjectSelect.addEventListener('change', function() {
                    if (!subProjectChoices) return;

                    const selected = subProjectChoices.getValue();
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
                });
            }
        });
    </script>
@endsection