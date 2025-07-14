@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- preloader css -->+
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.voucher') }}
                    @foreach ($initialVoucher as $item)
                        {{ $item->year }}
                    @endforeach
                </h4>

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
                        <form id="pristine-valid-example"
                            action="{{ route('budget-voucher.store', encode_params($params)) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
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

                                {{-- Budget --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="budget">{{ __('forms.budget') }}</label>
                                        <input type="number" min="0" name="budget" id="budget" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('budget')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Task Type --}}
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="task_type"
                                            class="form-label text-muted">{{ __('forms.voucher.type') }}</label>
                                        <select class="form-control" name="task_type" id="task_type" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($taskType as $ts)
                                                <option value="{{ $ts->task }}">{{ $ts->task }}</option>
                                            @endforeach
                                        </select>
                                        @error('task_type')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Date --}}
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="example-datetime-local-input">{{ __('forms.select_date') }}</label>
                                        <input type="date" name="date" id="example-datetime-local-input" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('date')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Attachments --}}
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="fileInput">{{ __('forms.file.type') }}</label>
                                        <input type="file" id="fileInput" name="attachments[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX</small>
                                        @error('attachments.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
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

                            {{-- Submit Button --}}
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ឥណទានអនុម័ត</th>
                                <th>ចលនាឥណទាន</th>
                                <th>ស្ថានភាពឥណទានថ្មី</th>
                                <th>ឥណទានទំនេរ</th>
                                <th>ធានាចំណាយពីមុន</th>
                                <th>ស្នើរសុំលើកនេះ</th>
                                <th>ឥណទាននៅសល់</th>
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
                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('task_type');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
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
    <script>
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
                const baseUrl = "{{ url('budgetplan/voucher/create') }}";

                // Fill program inputs
                if (programInput) programInput.value = programCode;
                if (programHidden) programHidden.value = programCode;

                if (subAccountId && programCode) {
                    const url = `${baseUrl}/${subAccountId}/${programCode}/early-balance`;

                    console.log("Fetching from:", url);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            console.log("✅ Raw Data Fetched:", data);

                            credit = data.credit;

                            document.getElementById('fin_law').textContent = formatNumber(data.fin_law);
                            document.getElementById('credit_movement').textContent = formatNumber(data
                                .credit_movement);
                            document.getElementById('new_credit_status').textContent = formatNumber(data
                                .new_credit_status);
                            document.getElementById('credit').textContent = formatNumber(data.credit);
                            document.getElementById('deadline_balance').textContent = formatNumber(data
                                .deadline_balance);

                            const apply = parseFloat(budgetInput.value) || 0;
                            updateRemainingCredit(apply);
                        })

                        .catch(error => console.error('❌ Error fetching early balance:', error));
                }
            });

            if (budgetInput) {
                budgetInput.addEventListener('input', updateApplyValue);
            }

            function updateApplyValue() {
                const apply = parseFloat(budgetInput.value) || 0;
                document.getElementById('applying').textContent = formatNumber(apply);
                updateRemainingCredit(apply);
            }

            function updateRemainingCredit(apply) {
                const credit = parseFloat(document.getElementById('credit').textContent.replace(/,/g, '')) || 0;
                const display = document.getElementById('remaining_credit');
                const flashMessage = document.getElementById('flashMessage'); // Ensure this exists in your HTML

                const remaining = credit - apply;

                if (remaining < 0) {
                    display.textContent = "0";

                    // Optional: Clear the input if over-limit
                    document.getElementById('budget').value = ''; // Clear the input field

                    // Show flash message
                    flashMessage.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>ជូនដំណឹង:</strong> ឥណទាននៅសល់មិនគ្រប់ចំនួន!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
                    return false;
                }

                display.textContent = formatNumber(remaining);
                // flashMessage.innerHTML = ''; // Clear flash message if input is valid
                return true;
            }



            function formatNumber(num) {
                const parsed = parseFloat(num);
                if (isNaN(parsed)) return "0";
                return parsed.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }
        });
    </script>
@endsection
