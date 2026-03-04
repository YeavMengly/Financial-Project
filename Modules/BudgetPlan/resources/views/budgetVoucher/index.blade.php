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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.budget.control.voucher') }}
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
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboTodo">ជ្រើសរើស កំណត់ចំណាំ</label>
                            <select class="form-control" id="cboTodo" name="cboTodo">
                                <option value="1">ជ្រើសរើស កំណត់ចំណាំ</option>
                                <option value="2">កំពុងធ្វើ</option>
                                <option value="3" selected>បានបញ្ចប់</option>
                            </select>
                        </div>

                        <!-- Sub Account Number -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="subAccountNumber">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="subAccountNumber" id="subAccountNumber">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($budgetVoucher as $ts)
                                    <option value="{{ $ts->account_sub_id }}"
                                        {{ request('subAccountNumber') == $ts->account_sub_id ? 'selected' : '' }}>
                                        {{ $ts->account_sub_id }} -
                                        {{ $ts->no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Task Type -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboExpenseType">{{ __('menus.task') }}</label>
                            <select class="form-control" name="cboExpenseType" id="cboExpenseType">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($expenseType as $ts)
                                    <option value="{{ $ts->id }}"
                                        {{ request('cboExpenseType') == $ts->name_kh ? 'selected' : '' }}>
                                        {{ $ts->name_kh }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="start_date">{{ __('menus.start_date') }}</label>
                            <input type="text" id="start_date" name="date" class="form-control"
                                placeholder="{{ __('forms.select_date') }}" name="start_date"
                                value="{{ request('start_date') }}"
                                data-pristine-required-message="{{ __('messages.required') }}" />
                        </div>

                        <!-- End Date -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="end_date">{{ __('menus.end_date') }}</label>
                            <input type="text" id="end_date" name="date" class="form-control"
                                placeholder="{{ __('forms.select_date') }}" name="end_date"
                                value="{{ request('end_date') }}"
                                data-pristine-required-message="{{ __('messages.required') }}" />
                        </div>

                        <div class="col-sm-3 d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>
                            {{-- Export --}}

                            <a href="{{ route(
                                'budgetVoucher.export',
                                array_merge(['params' => $params], request()->only(['agency', 'account', 'accountSub', 'no', 'txtDescription'])),
                            ) }}"
                                class="btn btn-success d-flex align-items-center px-3">
                                <i class="bx bx-download me-1"></i> {{ __('buttons.download') }}
                            </a>

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
            const taskTypeSelect = document.getElementById('agencyNumber');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('subAccountNumber');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសអនុគណនី', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('program');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('cboExpenseType');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសប្រភេទ', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>
@endsection
