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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
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
                        <form id="pristine-valid-example" action="{{ route('duelRelease.store', $params) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="stock_number" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.stock.number') }}
                                                </label>
                                                <select class="form-control" data-trigger id="dropStockNumber"
                                                    name="stock_number" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($duelEntry as $stock)
                                                        <option value="{{ $stock }}">{{ $stock }}</option>
                                                    @endforeach
                                                </select>
                                                </select>
                                                @error('stock_number')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="item_name" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.item.name') }}
                                                </label>
                                                <select id="cboDuel" class="form-select" name="item_name" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                </select>
                                                @error('item_name')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="quantity_request">{{ __('forms.quantity.request') }} /
                                                    លីត្រ</label>
                                                <input type="text" name="quantity_request" required class="form-control"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('quantity_request')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="agency" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.agency') }}
                                                </label>
                                                <select class="form-control" data-trigger id="dropAgency" name="agency"
                                                    required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($agency as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('agency')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="receipt_number">{{ __('forms.receipt.number') }}</label>
                                                <input type="text" id="receipt_number" name="receipt_number"
                                                    value="{{ old('receipt_number') }}" required maxlength="4"
                                                    inputmode="numeric" class="form-control"
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
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_request">{{ __('forms.user.request') }}</label>
                                                <input type="text" name="user_request" required class="form-control"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('user_request')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="receiver">{{ __('forms.receiver') }}</label>
                                                <input type="text" name="receiver" required class="form-control"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('receiver')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="date_release" class="form-label">កាលបរិច្ឆេទ</label>
                                                <input type="text" id="datepicker-basic" name="date_release"
                                                    class="form-control" placeholder="{{ __('forms.select_date') }}"
                                                    required
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('date_release')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">

                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label for="title">{{ __('forms.title') }}</label>

                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="skipTitle" style="cursor: pointer;">

                                                        <label class="form-check-label font-size-12 text-muted"
                                                            for="skipTitle" style="cursor: pointer;">
                                                            រំលង
                                                        </label>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="title" name="title" type="text"
                                                    placeholder="{{ __('forms.title') }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                @error('title')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
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
                                                            id="skipFileInput" style="cursor: pointer;">

                                                        <label class="form-check-label font-size-12 text-muted"
                                                            for="skipFileInput" style="cursor: pointer;">
                                                            រំលង
                                                        </label>
                                                    </div>
                                                </div>

                                                <input type="file" id="fileInput" name="file[]" class="form-control"
                                                    tabindex="15" accept=".pdf,.doc,.docx" multiple data-max-size="5"
                                                    data-allowed-extensions="pdf,doc,docx"
                                                    data-pristine-required-message="{{ __('messages.required') }}">

                                                <small class="form-text text-muted">
                                                    Allowed types: PDF, DOC, DOCX (Max: 5MB per file)
                                                </small>

                                                @error('file')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                                @error('file.*')
                                                    <div class="pristine-error text-help text-danger mt-1">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="vRefer">{{ __('forms.refer') }}</label>
                                                <textarea name="refer" id="vRefer" rows="5" class="form-control" required
                                                    data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                                @error('txtRefer')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="vNote">{{ __('forms.note') }}</label>
                                                <textarea name="note" id="vNote" rows="5" class="form-control" required
                                                    data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                                @error('txtNote')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const dropStockNumber = document.getElementById('dropStockNumber');
            const dropStockNumberChoice = new Choices(dropStockNumber, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const dropAgency = document.getElementById('dropAgency');
            const dropAgencyChoice = new Choices(dropAgency, {
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
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    <script>
        let programSubChoices = new Choices('#cboDuel', {
            searchEnabled: true,
            itemSelectText: '',
            placeholder: true,
            placeholderValue: "ស្វែងរក..."
        });

        $('#dropStockNumber').change(function() {
            var id = $(this).val();
            $.ajax({
                url: '{{ route('duelRelease.by.stock_number', ['params' => $params]) }}', // ✅ send params
                type: 'get',
                data: {
                    stock_number: id
                },
                success: function(data) {
                    if (programSubChoices) {
                        programSubChoices.destroy();
                    }
                    $('#cboDuel').html(data);
                    programSubChoices = new Choices('#cboDuel', {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "ស្វែងរក..."
                    });
                }
            });
        });
        //////
        document.addEventListener('DOMContentLoaded', function() {

            // IMPORTANT:
            // Change this to your real form ID
            const form = document.getElementById('pristine-valid-example');
            if (!form) {
                console.error('Form not found');
                return;
            }
            const pristine = new Pristine(form);
            const titleInput = document.getElementById('title');
            const skipTitleCheckbox = document.getElementById('skipTitle');
            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');
            // ==========================================
            // CLEAR FIELD ERRORS
            // ==========================================
            function clearFieldErrors(input) {

                if (!input) return;
                const parentGroup = input.closest('.form-group');
                if (parentGroup) {
                    parentGroup
                        .querySelectorAll('.pristine-error, .text-help')
                        .forEach(el => el.remove());

                    parentGroup.classList.remove(
                        'has-danger',
                        'has-error'
                    );
                }
                input.classList.remove(
                    'is-invalid',
                    'border-danger'
                );
            }
            // ==========================================
            // SETUP SKIP FIELD
            // ==========================================
            function setupSkipField(checkbox, input) {

                if (!checkbox || !input) return;
                // ------------------------------------------
                // Initial state
                // ------------------------------------------
                if (checkbox.checked) {

                    input.value = '';
                    input.disabled = true;

                } else {

                    input.disabled = false;
                    input.setAttribute('required', 'required');
                }
                // ------------------------------------------
                // Add validator
                // ------------------------------------------
                pristine.addValidator(
                    input,
                    function(value) {

                        // If skipped, always valid
                        if (checkbox.checked) {
                            return true;
                        }

                        // Otherwise field must contain data
                        return value.trim() !== '';

                    },
                    "{{ __('messages.required') }}",
                    1,
                    true
                );
                // ------------------------------------------
                // Skip checkbox change
                // ------------------------------------------
                checkbox.addEventListener('change', function() {

                    if (this.checked) {
                        // ==============================
                        // SKIP
                        // ==============================
                        input.value = '';
                        input.disabled = true;

                        input.removeAttribute('required');

                        clearFieldErrors(input);

                        pristine.reset(input);

                        input.classList.remove(
                            'is-invalid',
                            'border-danger'
                        );

                        input.classList.add(
                            'border-success',
                            'bg-success-subtle'
                        );

                    } else {
                        // ==============================
                        // REQUIRED AGAIN
                        // ==============================
                        input.disabled = false;

                        input.setAttribute('required', 'required');

                        input.classList.remove(
                            'border-success',
                            'bg-success-subtle'
                        );

                        clearFieldErrors(input);

                        pristine.reset(input);
                    }
                });
            }
            // ==========================================
            // INITIALIZE
            // ==========================================
            setupSkipField(
                skipTitleCheckbox,
                titleInput
            );
            setupSkipField(
                skipFileCheckbox,
                fileInput
            );
            // ==========================================
            // FORM SUBMIT
            // ==========================================

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // ------------------------------------------
                // Save which button was clicked
                // ------------------------------------------
                const submitter = e.submitter;
                if (submitter && submitter.name === 'action') {
                    let hidden = form.querySelector(
                        'input[name="action"]'
                    );
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'action';
                        form.appendChild(hidden);
                    }
                    hidden.value = submitter.value;
                }
                // ------------------------------------------
                // Validate
                // ------------------------------------------
                const isValid = pristine.validate();
                console.log('Form valid:', isValid);
                console.log('Errors:', pristine.getErrors());
                // If invalid, STOP
                if (!isValid) {
                    return;
                }
                HTMLFormElement.prototype.submit.call(form);
            });

        });
    </script>
@endsection
