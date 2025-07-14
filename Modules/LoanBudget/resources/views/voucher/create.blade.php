@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- preloader css -->
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.voucher') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.voucher') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="flashMessage"></div>

    <!-- end page title -->
    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example" action="{{ route('voucher.store', encode_params($params)) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccountNumber" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboAgency" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($beginCredit as $bc)
                                                <option value="{{ $bc->agencyNumber }}"
                                                    data-program="{{ $bc->agencyNumber }}">
                                                    {{ $bc->agencyNumber }} -
                                                    {{ optional($bc->agency)->agencyTitle ?? 'មិនមានទិន្ន័យ' }}
                                                </option>
                                            @endforeach

                                        </select>
                                        @error('cboAgency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubDepart" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.depart') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboSubDepart" name="cboSubDepart"
                                            required data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($beginCredit as $bc)
                                                <option value="{{ $bc->subDepart }}" data-program="{{ $bc->subDepart }}">
                                                    {{ $bc->subDepart }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboAgency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Sub Account Number --}}
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccountNumber" class="form-label text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" id="cboSubAccountNumber" name="subAccountNumber"
                                            required data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($beginCredit as $bc)
                                                <option value="{{ $bc->subAccountNumber }}"
                                                    data-program="{{ $bc->program }}">
                                                    {{ $bc->subAccount->subAccountNumber }} | {{ $bc->program }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subAccountNumber')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Program Code (auto-filled from JS) --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="programInput">{{ __('forms.program.code') }}</label>
                                        <input type="number" min="0" name="program" id="programInput" readonly
                                            required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('program')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- internal --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="internal">{{ __('forms.internal') }}</label>
                                        <input type="number" min="0" name="internal_increase" id="internal_increase"
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('internal_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- unexpected --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="unexpected">{{ __('forms.unexpected') }}</label>
                                        <input type="number" min="0" name="unexpected_increase"
                                            id="unexpected_increase" class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('unexpected_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- additional --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="additional">{{ __('forms.additional') }}</label>
                                        <input type="number" min="0" name="additional_increase"
                                            id="additional_increase" class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('additional_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- decrease --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="decrease">{{ __('forms.decrease') }}</label>
                                        <input type="number" min="0" name="decrease" id="decrease"
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('decrease')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- editorial --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="editorial">{{ __('forms.editorial') }}</label>
                                        <input type="number" min="0" name="editorial" id="editorial"
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('editorial')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="vDescription">{{ __('forms.document.description') }}</label>
                                        <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                            data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                        @error('txtDescription')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
                {{-- <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('tables.th.fin_law') }}</th>
                                <th>{{ __('tables.th.credit_movement') }}</th>
                                <th>{{ __('tables.th.new_credit_status') }}</th>
                                <th>{{ __('tables.th.credit') }}</th>
                                <th>{{ __('tables.th.deadline_balance') }}</th>
                                <th>{{ __('tables.th.applying') }}</th>
                                <th>{{ __('tables.th.remaining_credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span id="fin_law">0</span></td>
                                <td><span id="credit_movement">0</span></td>
                                <td><span id="new_credit_status">0</span></td>
                                <td><span id="credit">0</span></td>
                                <td><span id="deadline_balance">0</span></td>
                                <td><span id="applying">0</span></td>
                                <td><span id="remaining_credit">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- Bootstrap JS (needed for dismissible alert) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PristineJS (form validation) -->
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                if (!pristine.validate()) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <!-- Summernote -->
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
    </script>

    <!-- Choices.js (dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Bootstrap Bundle (optional for Bootstrap 5 components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dropzone.js (file upload) -->
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>

    <!-- Custom logic for BeginCredit loading -->

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subAccountSelect = document.getElementById('cboSubAccountNumber');
            const programInput = document.getElementById('programInput');
            const budgetInput = document.getElementById('budget');
            const programHidden = document.getElementById('programHiddenInput');

            // Initialize Choices.js (optional)
            if (typeof Choices !== 'undefined') {
                new Choices(subAccountSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholderValue: '',
                    searchPlaceholderValue: 'ជ្រើសរើស...'
                });
            }

            let credit = 0;

            subAccountSelect.addEventListener('change', function() {
                const selectedOption = subAccountSelect.options[subAccountSelect.selectedIndex];
                const subAccountId = this.value;
                const programCode = selectedOption.getAttribute('data-program');

                // Fill program inputs
                if (programInput) programInput.value = programCode;

            });


        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subAccountSelect = document.getElementById('cboSubAccountNumber');
            const programInput = document.getElementById('programInput');
            const budgetInput = document.getElementById('budget');
            const programHidden = document.getElementById('programHiddenInput');

            // ✅ Initialize Choices.js for better dropdown experience
            if (typeof Choices !== 'undefined') {
                new Choices(subAccountSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholderValue: '',
                    searchPlaceholderValue: 'ជ្រើសរើស...'
                });
            }

            // ✅ Handle SubAccount selection change
            subAccountSelect.addEventListener('change', function() {
                const selectedOption = subAccountSelect.options[subAccountSelect.selectedIndex];
                const subAccountId = this.value;
                const programCode = selectedOption.getAttribute('data-program');

                // Set program code in the input field
                if (programInput) {
                    programInput.value = programCode;
                }

                // If using a hidden input field to store program
                if (programHidden) {
                    programHidden.value = programCode;
                }

                // You may call a function here to fetch balance, etc.
                // fetchEarlyBalance(subAccountId, programCode);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const element = document.getElementById('cboAgency');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboAgency').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });

        $(document).ready(function() {
            const element = document.getElementById('cboSubDepart');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboSubDepart').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });
    </script>
@endsection
