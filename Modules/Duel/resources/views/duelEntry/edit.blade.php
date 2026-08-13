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
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
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
                        <form id="pristine-valid-example"
                            action="{{ route('duelEntry.update', ['params' => $params, 'id' => $module->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="project" class="form-label font-size-13 text-muted">
                                            {{ __('forms.project') }}
                                        </label>
                                        <select class="form-control" data-trigger id="dropProject" name="project" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($projects as $item)
                                                @php
                                                    // Match by project_id first, fallback to matching stock_number & stock_name
                                                    $isSelected =
                                                        (string) $item->id ===
                                                            (string) old('project', $module->project_id) ||
                                                        ($item->stock_number == $module->stock_number &&
                                                            $item->stock_name == $module->stock_name);
                                                @endphp
                                                <option value="{{ $item->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $item->stock_number }}-{{ $item->stock_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Source -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">

                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="source">
                                                {{ __('forms.source') }}
                                            </label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipSource" name="skipSource" value="1"
                                                    style="cursor: pointer;"
                                                    {{ old('skipSource', empty($module->source)) ? 'checked' : '' }}>

                                                <label class="form-check-label font-size-12 text-muted" for="skipSource">
                                                    រំលង
                                                </label>
                                            </div>
                                        </div>

                                        <input class="form-control" id="source" name="source" type="text"
                                            placeholder="{{ __('forms.source') }}"
                                            value="{{ old('source', $module->source) }}"
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
                                            <label for="pro_year">
                                                {{ __('forms.pro.year') }}
                                            </label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipProYear" name="skipProYear" value="1"
                                                    style="cursor: pointer;"
                                                    {{ old('skipProYear', empty($module->pro_year)) ? 'checked' : '' }}>

                                                <label class="form-check-label font-size-12 text-muted" for="skipProYear">
                                                    រំលង
                                                </label>
                                            </div>
                                        </div>

                                        <input class="form-control" id="pro_year" name="pro_year" type="text"
                                            placeholder="{{ __('forms.pro.year') }}"
                                            value="{{ old('pro_year', $module->pro_year) }}"
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
                                                            @foreach ($duelType as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ $type->id == $module->item_name ? 'selected' : '' }}>
                                                                    {{ $type->name_km }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <input type="number" min="0" name="quantity[]"
                                                            value="{{ $module->quantity }}" class="form-control"
                                                            required>
                                                    </td>

                                                    <td>
                                                        <input type="number" min="0" name="price[]"
                                                            value="{{ $module->price }}" class="form-control" required>
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
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
            new Choices(dropProject, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            // Define field mappings without title
            const fields = [{
                    input: document.getElementById('source'),
                    skip: document.getElementById('skipSource')
                },
                {
                    input: document.getElementById('pro_year'),
                    skip: document.getElementById('skipProYear')
                }
            ];

            function showError(input) {
                const group = input.closest('.form-group');
                group.querySelectorAll('.custom-required-error').forEach(el => el.remove());
                input.classList.add('is-invalid');
                const error = document.createElement('div');
                error.className = 'custom-required-error text-danger mt-1';
                error.innerText = "{{ __('messages.required') }}";
                group.appendChild(error);
            }

            function clearError(input) {
                const group = input.closest('.form-group');
                group.querySelectorAll('.custom-required-error').forEach(el => el.remove());
                input.classList.remove('is-invalid');
            }

            function updateFieldState(input, skipCheckbox) {
                if (!input || !skipCheckbox) return;

                if (skipCheckbox.checked) {
                    input.value = '';
                    input.disabled = true;
                    input.style.display = 'block';
                    clearError(input);
                } else {
                    input.disabled = false;
                    input.style.display = 'block';
                }
            }

            // Initialize state and listeners for source and pro_year
            fields.forEach(({
                input,
                skip
            }) => {
                if (input && skip) {
                    updateFieldState(input, skip);

                    skip.addEventListener('change', function() {
                        updateFieldState(input, skip);
                    });
                }
            });

            // Form Submit Validation
            form.addEventListener('submit', function(e) {
                let valid = true;

                fields.forEach(({
                    input,
                    skip
                }) => {
                    if (input && skip && !skip.checked) {
                        if (input.value.trim() === '') {
                            showError(input);
                            valid = false;
                        } else {
                            clearError(input);
                        }
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#itemTable tbody');
            tableBody.addEventListener('click', function(e) {
                // Handle Add Row Button
                if (e.target && e.target.classList.contains('addRow')) {
                    const firstRow = tableBody.querySelector('tr');
                    const newRow = firstRow.cloneNode(true);
                    // Clear values in the cloned row
                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                    // Change the action button on the new row to a Remove button
                    const actionTd = newRow.querySelector('td:last-child');
                    actionTd.innerHTML =
                    '<button type="button" class="btn btn-danger removeRow">-</button>';
                    // Append the new row to the table body
                    tableBody.appendChild(newRow);
                }
                // Handle Remove Row Button
                if (e.target && e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endsection
