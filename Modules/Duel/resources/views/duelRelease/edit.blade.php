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
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.duel.release') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.duel') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.release') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a></li>
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
                        {{-- ✅ UPDATE form --}}
                        <form id="pristine-valid-example"
                            action="{{ route('duelRelease.update', ['params' => $params, 'id' => $duelRelease->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            {{-- <div class="row"> --}}
                            <div class="row">
                                {{-- STOCK NUMBER --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_number" class="form-label font-size-13 text-muted">
                                            {{ __('forms.stock.number') }}
                                        </label>
                                        <select class="form-control" data-trigger id="dropStockNumber" name="stock_number"
                                            required>
                                            <option value="">{{ __('forms.search...') }}</option>

                                            @foreach ($duelEntry as $item)
                                                <option value="{{ $item->project_id }}"
                                                    {{ old('project_id', $duelRelease->project_id) == $item->project_id ? 'selected' : '' }}>
                                                    {{ $item->stock_number }} - {{ $item->stock_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('stock_number')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ITEM NAME --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="item_name" class="form-label font-size-13 text-muted">
                                            {{ __('forms.item.name') }}
                                        </label>
                                        <select id="cboDuel" class="form-select" name="item_name" required>
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($duelType as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ $type->id == $duelRelease->item_name ? 'selected' : '' }}>
                                                    {{ $type->name_km }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_name')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- QUANTITY REQUEST --}}
                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="quantity_request">{{ __('forms.quantity.request') }}</label>
                                        <input type="number" min="0" name="quantity_request" required
                                            class="form-control"
                                            value="{{ old('quantity_request', $duelRelease->quantity_request) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('quantity_request')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- RECEIPT NUMBER --}}
                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="receipt_number">{{ __('forms.receipt.number') }}</label>
                                        <input type="text" id="receipt_number" name="receipt_number"
                                            value="{{ old('receipt_number', $duelRelease->receipt_number ?? '') }}"
                                            required maxlength="4" inputmode="numeric" class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-pattern="/^\d{4}$/"
                                            data-pristine-pattern-message="សូមបញ្ចូលលេខ ៤ ខ្ទង់"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);" />
                                        @error('receipt_number')
                                            <div class="pristine-error text-help text-danger mt-1">{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- USER REQUEST --}}
                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_request">{{ __('forms.user.request') }}</label>
                                        <input type="text" name="user_request" required class="form-control"
                                            value="{{ old('user_request', $duelRelease->user_request) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('user_request')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Receiver --}}
                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="receiver">{{ __('forms.receiver') }}</label>
                                        <input type="text" name="receiver" required class="form-control"
                                            value="{{ old('receiver', $duelRelease->receiver) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('receiver')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- AGENCY --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="agency" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="dropAgency" name="agency"
                                            {{-- required --}}
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($agency as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('agency', $duelRelease->agency) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('agency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="cboExecutive"
                                            class="form-label font-size-13 text-muted">{{ __('forms.agency.executive.unit') }}</label>
                                        <select id="cboExecutive" class="form-select" name="cboExecutive" required
                                            data-trigger tabindex="7"
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- DATE RELEASE --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_release" class="form-label">កាលបរិច្ឆេទ</label>
                                        <input type="text" id="datepicker-basic" name="date_release"
                                            class="form-control" placeholder="{{ __('forms.select_date') }}" required
                                            value="{{ old('date_release', $duelRelease->date_release) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('date_release')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <!-- TITLE -->
                                {{-- <div class="col-xl-3 col-md-6">
                                        <div class="form-group mb-3">

                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="title">
                                                    {{ __('forms.title') }}
                                                </label>

                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="skipTitle" name="skipTitle" value="1"
                                                        style="cursor: pointer;"
                                                        {{ old('skipTitle', empty($duelRelease->title)) ? 'checked' : '' }}>

                                                    <label class="form-check-label font-size-12 text-muted"
                                                        for="skipTitle">
                                                        រំលង
                                                    </label>
                                                </div>
                                            </div>

                                            <input class="form-control" id="title" name="title" type="text"
                                                placeholder="{{ __('forms.title') }}"
                                                value="{{ old('title', $duelRelease->title) }}"
                                                data-pristine-required-message="{{ __('messages.required') }}">

                                            @error('title')
                                                <div class="pristine-error text-help text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div> --}}
                            </div>
                            <div class="row">
                                {{-- REFER --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vRefer">{{ __('forms.refer') }}</label>
                                        <textarea name="refer" id="vRefer" rows="5" class="form-control" required
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ old('refer', $duelRelease->refer) }}</textarea>
                                        @error('refer')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- NOTE --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vNote">{{ __('forms.note') }}</label>
                                        <textarea name="note" id="vNote" rows="5" class="form-control" required
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ old('note', $duelRelease->note) }}</textarea>
                                        @error('note')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- </div> --}}

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('duelRelease.index', $params) }}">{{ __('buttons.back') }}</a>
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
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

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
        // 1. Declare globally at the top of your scripts
        let programSubChoices = null;

        document.addEventListener('DOMContentLoaded', function() {
            const dropStockNumber = document.getElementById('dropStockNumber');
            new Choices(dropStockNumber, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            const dropAgency = document.getElementById('dropAgency');
            new Choices(dropAgency, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // 2. Assign to the global variable instead of a local const
            const cboDuel = document.getElementById('cboDuel');
            if (cboDuel) {
                programSubChoices = new Choices(cboDuel, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }
        });

        $('#dropStockNumber').change(function() {
            var id = $(this).val();
            $.ajax({
                url: '{{ route('duelRelease.by.stock_number', ['params' => $params]) }}',
                type: 'get',
                data: {
                    stock_number: id
                },
                success: function(data) {
                    // 3. Now safely destroy the previous Choices instance
                    if (programSubChoices) {
                        programSubChoices.destroy();
                    }
                    $('#cboDuel').html(data);
                    programSubChoices = new Choices('#cboDuel', {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholderValue: 'ជ្រើសរើស',
                        searchPlaceholderValue: "ស្វែងរក...",
                        shouldSort: false
                    });
                }
            });
        });

        // Auto trigger change once on edit
        document.addEventListener('DOMContentLoaded', function() {
            const stockSelect = document.getElementById('dropStockNumber');
            if (stockSelect && stockSelect.value) {
                $('#dropStockNumber').trigger('change');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const title = document.getElementById('title');
            const skipTitle = document.getElementById('skipTitle');

            function showError(input) {
                const group = input.closest('.form-group');
                group.querySelectorAll('.custom-required-error')
                    .forEach(el => el.remove());
                input.classList.add('is-invalid');
                const error = document.createElement('div');
                error.className =
                    'custom-required-error text-danger mt-1';
                error.innerText =
                    "{{ __('messages.required') }}";
                group.appendChild(error);
            }

            function clearError(input) {
                const group = input.closest('.form-group');
                group.querySelectorAll('.custom-required-error')
                    .forEach(el => el.remove());
                input.classList.remove('is-invalid');
            }
            // =====================================
            // TITLE STATE
            // =====================================
            function updateTitleState() {
                if (skipTitle.checked) {
                    // Disable input
                    title.disabled = true;
                    // KEEP INPUT VISIBLE
                    title.style.display = 'block';
                    // Clear error
                    clearError(title);
                } else {
                    // Enable input
                    title.disabled = false;

                    title.style.display = 'block';
                }
            }
            // Initial state
            updateTitleState();
            // When Skip changes
            skipTitle.addEventListener('change', function() {
                updateTitleState();
            });
            // =====================================
            // FORM SUBMIT
            // =====================================
            form.addEventListener('submit', function(e) {
                let valid = true;
                if (!skipTitle.checked) {
                    if (title.value.trim() === '') {
                        showError(title);
                        valid = false;
                    } else {
                        clearError(title);
                    }
                }
                if (!valid) {
                    e.preventDefault();
                    return false;
                }
            });

        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Grab the saved values from your database/old input
            // Replace `$duelRelease->executive_unit_id` with your actual variable
            const initialAgencyId = $('#cboAgency').val();
            const initialExecutiveId = "{{ old('cboExecutive', $duelRelease->executive_unit_id ?? '') }}";

            // ========= Choices Instances =========
            let executiveChoices = new Choices('#cboExecutive', {
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
                instanceRefSetter,
                selectedValue
            }) {
                $.ajax({
                    url,
                    type: "GET",
                    data,
                    success: function(html) {
                        // Populate the HTML
                        $(targetSelect).html(html);

                        // Re-initialize Choices.js
                        instanceRefSetter();

                        // 2. If an initial value was passed, set it in Choices.js
                        if (selectedValue) {
                            executiveChoices.setChoiceByValue(selectedValue.toString());
                        }
                    },
                    error: function() {
                        resetSelect(targetSelect);
                    }
                });
            }

            // ========= Script: Agency -> Executive Unit =========
            // Added selectedExecutiveId parameter with a default of null
            function handleAgencyChange(agencyId, selectedExecutiveId = null) {
                resetSelect('#cboExecutive');
                executiveChoices = resetChoices('#cboExecutive', executiveChoices);

                if (!agencyId) return;

                loadOptions({
                    url: "{{ route('duelRelease.by.executive') }}",
                    data: {
                        agency_id: agencyId
                    },
                    targetSelect: '#cboExecutive',
                    selectedValue: selectedExecutiveId, // Pass the value to the helper
                    instanceRefSetter: () => {
                        executiveChoices = resetChoices('#cboExecutive', executiveChoices);
                    }
                });
            }

            // ========= Events =========
            $('#cboAgency').on('change', function() {
                const agencyId = $(this).val();
                // On manual change by the user, don't pre-select an executive unit
                handleAgencyChange(agencyId, null);
            });

            // ========= Initialization for Edit View =========
            // 3. If there is a saved Agency ID on page load, trigger the change automatically
            if (initialAgencyId) {
                handleAgencyChange(initialAgencyId, initialExecutiveId);
            }

        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dropAgency = document.getElementById('dropAgency'); // Note the ID change here!
            const cboExecutive = document.getElementById('cboExecutive');

            // 1. Read the initial values
            // Because Blade adds 'selected' to dropAgency, it will automatically have a value on load
            const initialAgencyId = dropAgency.value;

            // Grab the saved Executive Unit ID from Blade
            const initialExecutiveId = "{{ old('cboExecutive', $duelRelease->executive_unit_id ?? '') }}";

            // ========= Choices Instances =========
            let agencyChoices = new Choices(dropAgency, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            let executiveChoices = new Choices(cboExecutive, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // ========= Helpers =========
            function resetSelect(selector) {
                $(selector).html(`<option value="">{{ __('forms.search...') }}</option>`);
            }

            function resetChoices(selector, instance) {
                if (instance) instance.destroy();
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholderValue: 'ជ្រើសរើស',
                    searchPlaceholderValue: 'ស្វែងរក...',
                    shouldSort: false
                });
            }

            function loadOptions({
                url,
                data,
                targetSelect,
                instanceRefSetter
            }) {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: data, // Passes both agency_id and selected_id
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter(); // Rebuild Choices.js to read the new 'selected' tag
                    },
                    error: function() {
                        resetSelect(targetSelect);
                        instanceRefSetter();
                    }
                });
            }

            // ========= Script: Agency -> Executive Unit =========
            function handleAgencyChange(agencyId, selectedExecutiveId = null) {
                resetSelect('#cboExecutive');
                executiveChoices = resetChoices('#cboExecutive', executiveChoices);

                if (!agencyId) return;

                loadOptions({
                    url: "{{ route('duelRelease.by.edit.executive') }}", // Ensure this route points to the controller method below
                    data: {
                        agency_id: agencyId,
                        selected_id: selectedExecutiveId
                    },
                    targetSelect: '#cboExecutive',
                    instanceRefSetter: () => {
                        executiveChoices = resetChoices('#cboExecutive', executiveChoices);
                    }
                });
            }

            // ========= Events =========
            $(dropAgency).on('change', function() {
                const agencyId = $(this).val();
                // User manually changed the agency, so pass null for the executive unit
                handleAgencyChange(agencyId, null);
            });

            // ========= Initialization for Edit View =========
            if (initialAgencyId) {
                // Trigger the AJAX call immediately on page load to fetch the saved executive units
                handleAgencyChange(initialAgencyId, initialExecutiveId);
            }

        });
    </script>
@endsection
