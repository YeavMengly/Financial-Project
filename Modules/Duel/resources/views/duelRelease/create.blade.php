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
                                                <label for="title">{{ __('forms.title') }}</label>
                                                <input type="text" name="title" required class="form-control" data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('title')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                         <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="file">{{ __('forms.file') }}</label>
                                                <input type="file" id="fileInput" name="file[]" class="form-control"
                                                    accept=".pdf,.doc,.docx" multiple />
                                                <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX</small>
                                                @error('file')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
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

        // // ==========================================
        // //   SKIP LEGAL NUMBER LOGIC (With Visuals)
        // // ==========================================
        // const titleInput = document.getElementById('title');
        // const skipTitleCheckbox = document.getElementById('skipTitle');

        // const fileInput = document.getElementById('fileInput');
        // const skipFileCheckbox = document.getElementById('skipFileInput');
        // const setupSkipField = ({
        //     checkbox,
        //     input,
        //     defaultValue = '',
        //     restoreValidation = () => {}
        // }) => {

        //     if (!checkbox || !input) return;

        //     checkbox.addEventListener('change', function() {

        //         const parentGroup = input.closest('.form-group');

        //         if (this.checked) {
        //             input.value = '';
        //             input.disabled = true;

        //             input.classList.add('border-success', 'bg-success-subtle');
        //             parentGroup?.classList.add('has-success');

        //             input.removeAttribute('required');
        //             input.removeAttribute('min');
        //             input.removeAttribute('data-pristine-required-message');

        //             pristine.reset(input);
        //         } else {
        //             input.value = defaultValue;
        //             input.disabled = false;

        //             input.classList.remove('border-success', 'bg-success-subtle');
        //             parentGroup?.classList.remove('has-success');

        //             restoreValidation();
        //         }

        //         refreshPristine();
        //     });
        // };
        
        // setupSkipField({
        //     checkbox: skipTitleCheckbox,
        //     input: titleInput,
        //     defaultValue: '',
        //     restoreValidation: () => {
        //         titleInput.setAttribute('required', true);
        //         titleInput.setAttribute(
        //             'data-pristine-required-message',
        //             "{{ __('messages.required') }}"
        //         );
        //     }
        // });

        // setupSkipField({
        //     checkbox: skipFileCheckbox,
        //     input: fileInput,
        //     restoreValidation: () => {
        //         fileInput.setAttribute('required', true);
        //         fileInput.setAttribute(
        //             'data-pristine-required-message',
        //             "{{ __('messages.required') }}"
        //         );
        //     }
        // });
        // // ==========================================
        // //   MASTER FORM SUBMIT GATEKEEPER
        // // ==========================================
        // form.addEventListener('submit', function(e) {
        //     // 1. Stop the normal form submission
        //     e.preventDefault();

        //     // 2. Capture which button was clicked (Save vs Save & Create)
        //     const submitter = e.submitter;
        //     if (submitter && submitter.name === 'action') {
        //         let hidden = form.querySelector('input[name="action"]');

        //         // If the hidden input doesn't exist, create it
        //         if (!hidden) {
        //             hidden = document.createElement('input');
        //             hidden.type = 'hidden';
        //             hidden.name = 'action';
        //             form.appendChild(hidden);
        //         }
        //         hidden.value = submitter.value;
        //     }

        //     // 3. Handle the "skip" checkboxes to bypass Pristine validation
        //     [
        //         [skipTitleCheckbox, titleInput],
        //         [skipFileCheckbox, fileInput]
        //     ].forEach(([checkbox, input]) => {
        //         if (checkbox?.checked && input) {
        //             input.disabled = true; // Disable so Pristine ignores it
        //             input.removeAttribute('required');
        //             pristine.reset(input); // Clear any existing error messages for this field
        //         }
        //     });

        //     // 4. Run Pristine validation
        //     const isValid = pristine.validate();

        //     // 5. If validation fails, stop here (do not submit)
        //     if (!isValid) {
        //         return;
        //     }

        //     // 6. Re-enable the skipped inputs just before submitting!
        //     // We must do this so Laravel receives the empty/null values instead of missing keys.
        //     [titleInput, fileInput].forEach(input => {
        //         if (input) {
        //             input.disabled = false;
        //         }
        //     });

        //     // 7. Finally, submit the form natively to Laravel
        //     HTMLFormElement.prototype.submit.call(form);
        // });
    </script>
@endsection
