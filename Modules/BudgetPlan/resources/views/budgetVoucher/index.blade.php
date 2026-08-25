@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.voucher') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $data->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $data->name }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" id="filter" method="GET">
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboTodo" class="form-label font-size-13 text-muted">ជ្រើសរើស
                                    កំណត់ចំណាំ</label>

                                {{-- <label class="visually-hidden" for="cboTodo">ជ្រើសរើស កំណត់ចំណាំ</label> --}}
                                <select class="form-select" id="cboTodo" name="cboTodo">
                                    <option value="1">ជ្រើសរើស កំណត់ចំណាំ</option>
                                    <option value="2" selected>កំពុងធ្វើ</option>
                                    <option value="3">បានបញ្ចប់</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">

                            <div class="form-group mb-3">
                                <label for="cboStatus" class="form-label font-size-13 text-muted">ជ្រើសរើស ស្ថានភាព</label>
                                {{-- <label class="visually-hidden" for="cboStatus">ជ្រើសរើស ស្ថានភាព</label> --}}
                                <select class="form-select" id="cboStatus" name="cboStatus">
                                    <option value="1">ជ្រើសរើស ស្ថានភាព</option>
                                    <option value="2" selected>សកម្ម</option>
                                    <option value="3">លុប</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboProgram"
                                    class="form-label font-size-13 text-muted">{{ __('menus.content.program') }}</label>
                                <select class="form-control" name="cboProgram" id="cboProgram">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($program as $p)
                                        <option value="{{ $p->id }}"
                                            {{ request('cboProgram') == $p->id ? 'selected' : '' }}>
                                            {{ __('menus.content.program') }} {{ $p->no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- Sub Account Number -->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboAccountSub" class="form-label font-size-13 text-muted">
                                    {{ __('menus.content.sub.accounts') }}
                                </label>

                                <select class="form-control" name="cboAccountSub" id="cboAccountSub">
                                    <option value="">{{ __('forms.search...') }}</option>

                                    @foreach ($accountSub as $as)
                                        <option value="{{ $as->no }}" @selected(old('cboAccountSub', $budgetVoucher) == $as->account_sub_id)>
                                            {{ $as->no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboAgency"
                                    class="form-label font-size-13 text-muted">{{ __('menus.content.agency') }}</label>
                                <select class="form-control" name="cboAgency" id="cboAgency">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($agency as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('cboAgency') == $item->id ? 'selected' : '' }}>
                                            {{ $item->no }} - {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboExpenseType"
                                    class="form-label font-size-13 text-muted">{{ __('forms.expense.type') }}</label>
                                <select class="form-select" id="cboExpenseType" name="cboExpenseType" required
                                    tabindex="13" data-pristine-required-message="{{ __('messages.required') }}">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($expenseType as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name_kh }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="CboPaymentVoucherNumber"
                                    class="form-label font-size-13 text-muted">{{ __('menus.number.voucher') }}</label>
                                {{-- <label class="visually-hidden" for="CboPaymentVoucherNumber">{{ __('menus.voucher') }}</label> --}}
                                <input type="text" id="CboPaymentVoucherNumber" name="CboPaymentVoucherNumber"
                                    class="form-control" placeholder="{{ __('menus.number.voucher') }}"
                                    value="{{ request('CboPaymentVoucherNumber') }}"
                                    data-pristine-required-message="{{ __('messages.required') }}" />
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="start_date"
                                    class="form-label font-size-13 text-muted">{{ __('menus.start_date') }}</label>
                                {{-- <label class="visually-hidden" for="start_date">{{ __('menus.start_date') }}</label> --}}
                                <input type="text" id="start_date" name="start_date" class="form-control"
                                    placeholder="{{ __('forms.select_date') }}" value="{{ request('start_date') }}"
                                    data-pristine-required-message="{{ __('messages.required') }}" />
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="end_date"
                                    class="form-label font-size-13 text-muted">{{ __('menus.end_date') }}</label>
                                {{-- <label class="visually-hidden" for="end_date">{{ __('menus.end_date') }}</label> --}}
                                <input type="text" id="end_date" name="end_date" class="form-control"
                                    placeholder="{{ __('forms.select_date') }}" value="{{ request('end_date') }}"
                                    data-pristine-required-message="{{ __('messages.required') }}" />
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label for="button"
                                class="form-label font-size-13 text-muted">{{ __('buttons.search') }}</label>
                            <div class="form-group mb-3 d-flex align-items-center gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                {{-- Export --}}
                                <a id="btnExport"
                                    href="{{ route(
                                        'budgetVoucher.export',
                                        array_merge(
                                            ['params' => $params],
                                            request()->only(['cboTodo', 'cboStatus', 'cboExpenseType', 'cboAccountSub', 'start_date', 'end_date']),
                                        ),
                                    ) }}"
                                    class="btn btn-success d-flex align-items-center px-3">
                                    <i class="bx bx-download me-1"></i> {{ __('buttons.download') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (hasPermission('budgetVoucher.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('budgetVoucher.create', $params) }}"><i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}</a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-bordered dt-responsive  nowrap w-100']) !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        if (startDateInput) {
            flatpickr(startDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: startDateInput.value || null
            });
        }

        if (endDateInput) {
            flatpickr(endDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: endDateInput.value || null
            });
        }
    </script>
    <script>
        function confirm(url, condi) {
            if (condi == 1) {
                Swal.fire({
                    title: '{{ __('messages.confirm.delete') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e7515a',
                    cancelButtonColor: '#e2a03f',
                    confirmButtonText: '{{ __('buttons.delete') }}!',
                    cancelButtonText: '{{ __('buttons.back') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = url;
                    }
                });
            } else {
                Swal.fire({
                    title: '{{ __('messages.confirm.back') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2ab57d',
                    cancelButtonColor: '#e2a03f',
                    confirmButtonText: '{{ __('buttons.get.back') }}!',
                    cancelButtonText: '{{ __('buttons.back') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = url;
                    }
                });
            }
        }
    </script>
    {!! $dataTable->scripts() !!}

    <!-- Choices.js (dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Custom logic for BeginCredit loading -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cboAccountSub = document.getElementById('cboAccountSub');
            const cboAccountSubChoices = new Choices(cboAccountSub, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសអនុគណនី', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('CboPaymentVoucherNumber');

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Submit the form
                    this.form.submit();
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('CboMandate');

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Submit the form
                    this.form.submit();
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('cboExpenseType');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសប្រភេទចំណាយ', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboProgramSelect = document.getElementById('cboProgram');
            const cboProgramChoices = new Choices(cboProgramSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសកម្មវិធី', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboAgencySelect = document.getElementById('cboAgency');
            const cboAgencyChoices = new Choices(cboAgencySelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសអង្គភាព', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cboTodo = document.getElementById('cboTodo');
            const cboTodoChoices = new Choices(cboTodo, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: '',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboStatus = document.getElementById('cboStatus');
            const cboStatusChoices = new Choices(cboStatus, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: '',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
    <script>
        $('#btnExport').on('click', function(e) {
            e.preventDefault();

            let baseUrl = "{{ route('budgetVoucher.export', ['params' => $params]) }}";

            let params = new URLSearchParams({
                cboExpenseType: $('#cboExpenseType').val(),
                cboAccountSub: $('#cboAccountSub').val(),
                CboPaymentVoucherNumber: $('#CboPaymentVoucherNumber').val(),
                cboAgency: $('#cboAgency').val(),
                cboProgram: $('#cboProgram').val(),
                no: $('#no').val(),
                cboTodo: $('#cboTodo').val(),
                cboStatus: $('#cboStatus').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
            });

            // ✅ Redirect with correct query string
            window.location.href = baseUrl + '?' + params.toString();
        });
    </script>
    <script>
        $('#cboTodo, #cboStatus, #cboProgram, #cboAccountSub, #cboAgency, #cboExpenseType, #CboPaymentVoucherNumber,  #start_date, #end_date')
            .on('change keyup',
                function() {
                    $('#budgetvoucher-table').DataTable().ajax.reload();
                });
    </script>
    {{-- <script>
        window.startPicker = null;
        window.endPicker = null;

        $(document).ready(function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Initialize Flatpickr
            if (startDateInput) {
                window.startPicker = flatpickr(startDateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: startDateInput.value || null
                });
            }

            if (endDateInput) {
                window.endPicker = flatpickr(endDateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: endDateInput.value || null
                });
            }

            // Lock/Unlock Function
            function checkAndLockStartDate() {
                const voucherNo = $('#CboPaymentVoucherNumber').val() ? $('#CboPaymentVoucherNumber').val().trim() :
                    '';
                const $startDateInput = $('#start_date');

                if (voucherNo === '0001') {
                    $.ajax({
                        url: "{{ route('budgetVoucher.getLegalDate') }}",
                        type: "GET",
                        data: {
                            voucher_number: '0001'
                        },
                        success: function(res) {
                            if (res.legal_date && window.startPicker) {
                                // 1. Set the fixed legal_date value
                                window.startPicker.setDate(res.legal_date, true);
                                window.startPicker.set('minDate', res.legal_date);

                                // 2. Lock / Disable the UI
                                $startDateInput.prop('disabled', true);
                                $(window.startPicker.altInput).prop('disabled', true).addClass(
                                    'bg-light pe-none');
                            }
                        }
                    });
                } else {
                    // Unlock / Enable for all other voucher numbers
                    $startDateInput.prop('disabled', false);
                    if (window.startPicker) {
                        window.startPicker.set('minDate', null);
                        if (window.startPicker.altInput) {
                            $(window.startPicker.altInput).prop('disabled', false).removeClass('bg-light pe-none');
                        }
                    }
                }
            }

            // Run check on page load
            checkAndLockStartDate();

            // Run check whenever CboPaymentVoucherNumber changes
            $('#CboPaymentVoucherNumber').on('change keyup', function() {
                checkAndLockStartDate();
            });
        });
    </script> --}}

    <script>
        const startDatePicker = flatpickr('#start_date', {
            dateFormat: 'Y-m-d',

            onChange: function(selectedDates) {

                if (selectedDates.length > 0) {

                    endDatePicker.set('minDate', selectedDates[0]);

                    // If current end date is before start date, clear it
                    if (
                        endDatePicker.selectedDates.length > 0 &&
                        endDatePicker.selectedDates[0] < selectedDates[0]
                    ) {
                        endDatePicker.clear();
                    }
                } else {

                    endDatePicker.set('minDate', null);
                }
            }
        });


        const endDatePicker = flatpickr('#end_date', {
            dateFormat: 'Y-m-d'
        });
    </script>
@endsection
