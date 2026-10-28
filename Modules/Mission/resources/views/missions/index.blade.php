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

    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content.missions') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            {{-- <li class="breadcrumb-item"><a
                                    href="javascript: void(0);"><span>{{ __('menus.missions') }}</span></a>
                            </li> --}}
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);"><span>{{ $ministry->year }}</span></a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('menus.content.missions') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" id="filter" method="GET">
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboTodo" class="form-label font-size-13 text-muted">ជ្រើសរើស
                                    ស្ថានភាពបង់ប្រាក់</label>

                                {{-- <label class="visually-hidden" for="cboTodo">ជ្រើសរើស កំណត់ចំណាំ</label> --}}
                                <select class="form-select" id="cboTodo" name="cboTodo">
                                    <option value="1">ជ្រើសរើស កំណត់ចំណាំ</option>
                                    <option value="2" selected>1. មិនទាន់ទូទាត់ - Unpaid</option>
                                    <option value="3">2. រក្សាទុកទូទាត់ - Pending</option>
                                    <option value="4">3. បានទូទាត់រួចរាល់ - Paid</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboMissionType" class="form-label font-size-13 text-muted">ជ្រើសរើស
                                    ប្រភេទបេសកកម្ម</label>
                                {{-- <label class="visually-hidden" for="cboStatus">ជ្រើសរើស ស្ថានភាព</label> --}}
                                <select class="form-select" id="cboMissionType" name="cboMissionType">
                                    <option value="1">ជ្រើសរើស ប្រភេទបេសកកម្ម</option>
                                    <option value="2" selected>1. ក្នុងប្រទេស - local</option>
                                    <option value="3">2. ក្រៅប្រទេស - abroad</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboName"
                                    class="form-label font-size-13 text-muted">{{ __('menus.employees') }}</label>
                                <select class="form-control" name="cboName" id="cboName">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ request('cboName') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name_kh }} - {{ $emp->name_latin }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboProvince"
                                    class="form-label font-size-13 text-muted">{{ __('menus.content.province') }}</label>
                                <select class="form-control" name="cboProvince" id="cboProvince">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}"
                                            {{ request('cboProvince') == $province->id ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <a id="btnExport" href="{{ route('missions.export', ['params' => $params]) }}"
                                    class="btn btn-success d-flex align-items-center px-3">

                                    <i class="bx bx-download me-1"></i>
                                    {{ __('buttons.download') }}

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
                    {{-- @if (hasPermission('missions.create') && $module->is_archived != 2) --}}
                    <div class="col-sm">
                        {{-- <div class="mb-4 d-flex flex-wrap gap-2">
                            @if (hasPermission('missions.create') && $ministry->is_archived != 2)
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('missions.create', $params) }}"><i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}</a>
                            @endif
                            <a class="btn btn-dark"
                                href="{{ route('initialMissions.index') }}">{{ __('buttons.back') }}</a>
                        </div> --}}
                        {{-- <div class="mb-4 d-flex flex-wrap gap-2">

                            @if (hasPermission('missions.create') && $ministry->is_archived != 2)
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('missions.create', $params) }}">
                                    <i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}
                                </a>
                            @endif

                            <button type="button" class="btn btn-success" id="btnPaymentStatus" disabled>
                                <i class="bx bx-money me-1"></i>
                                ទូទាត់
                            </button>

                            <a class="btn btn-dark" href="{{ route('initialMission.index') }}">
                                {{ __('buttons.back') }}
                            </a>
                        </div> --}}

                        {{-- <div class="mb-4 d-flex flex-wrap align-items-end justify-content-between gap-2">

                            <div class="d-flex flex-wrap align-items-end gap-2">

                                @if (hasPermission('missions.create') && $ministry->is_archived != 2)
                                    <a class="btn btn-light waves-effect waves-light"
                                        href="{{ route('missions.create', $params) }}">
                                        <i class="bx bx-plus me-1"></i>
                                        {{ __('buttons.create') }}
                                    </a>
                                @endif

                                <button type="button" class="btn btn-success" id="btnPaymentStatus" disabled>
                                    <i class="bx bx-money me-1"></i>
                                    ទូទាត់
                                </button>
                                <a class="btn btn-dark" href="{{ route('initialMission.index') }}">
                                    {{ __('buttons.back') }}
                                </a>
                            </div>

                            <div class="ms-auto d-flex flex-wrap align-items-end gap-2">
                                <div style="width: 120px;">
                                    <label for="start_id" class="form-label mb-1">ID ចាប់ពី</label>
                                    <input type="number" id="start_index" class="form-control" min="1"
                                        placeholder="Start ID">
                                </div>

                                <div style="width: 120px;">
                                    <label for="end_id" class="form-label mb-1">ID ដល់</label>
                                    <input type="number" id="end_index" class="form-control" min="1"
                                        placeholder="End ID">
                                </div>

                                <button type="button" class="btn btn-primary" id="btnSearchIdRange">
                                    <i class="bx bx-search me-1"></i> ស្វែងរក
                                </button>

                                <button type="button" class="btn btn-secondary" id="btnResetIdRange">
                                    <i class="bx bx-reset"></i>
                                </button>
                            </div>

                        </div> --}}

                        <div class="mb-4 d-flex flex-column gap-3">

                            {{-- Top toolbar --}}
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                                {{-- Left: Main actions --}}
                                <div class="d-flex flex-wrap align-items-center gap-2">

                                    @if (hasPermission('missions.create') && $ministry->is_archived != 2)
                                        <a class="btn btn-light waves-effect waves-light"
                                            href="{{ route('missions.create', $params) }}">
                                            <i class="bx bx-plus me-1"></i>
                                            {{ __('buttons.create') }}
                                        </a>
                                    @endif

                                    {{-- Payment --}}
                                    <button type="button" class="btn btn-success" id="btnPaymentStatus" disabled>
                                        <i class="bx bx-money me-1"></i>
                                        ទូទាត់
                                    </button>

                                    {{-- Save Payment Draft --}}
                                    <button type="button" class="btn btn-warning" id="btnSavePaymentDraft">
                                        <i class="bx bx-save me-1"></i>
                                        រក្សាទុកទូទាត់
                                        <span class="badge bg-danger ms-1" id="pendingMissionCount">
                                            {{ $pendingMissionCount }}
                                        </span>
                                    </button>

                                    {{-- Export History --}}
                                    {{-- <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                        data-bs-target="#paymentExportHistoryModal">
                                        <i class="bx bx-history me-1"></i>
                                        Export History
                                    </button> --}}
                                    <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                        data-bs-target="#paymentExportHistoryModal">
                                        <i class="bx bx-history me-1"></i>
                                        Export History
                                    </button>

                                    <a class="btn btn-outline-secondary" href="{{ route('initialMission.index') }}">
                                        <i class="bx bx-arrow-back me-1"></i>
                                        {{ __('buttons.back') }}
                                    </a>

                                </div>

                                {{-- Right: Search by Mission ID --}}
                                {{-- <div class="d-flex flex-wrap align-items-end gap-2">

                                    <div style="width: 120px;">
                                        <label for="start_index" class="form-label mb-1">
                                            ID ចាប់ពី
                                        </label>
                                        <input type="number" id="start_index" class="form-control" min="1"
                                            placeholder="Start ID">
                                    </div>

                                    <div style="width: 120px;">
                                        <label for="end_index" class="form-label mb-1">
                                            ID ដល់
                                        </label>
                                        <input type="number" id="end_index" class="form-control" min="1"
                                            placeholder="End ID">
                                    </div>

                                    <button type="button" class="btn btn-primary" id="btnSearchIdRange">
                                        <i class="bx bx-search me-1"></i>
                                        ស្វែងរក
                                    </button>

                                    <button type="button" class="btn btn-secondary" id="btnResetIdRange"
                                        title="Reset ID search">
                                        <i class="bx bx-reset"></i>
                                    </button>

                                </div> --}}
                            </div>
                        </div>
                    </div>

                    {{-- Payment Export History Modal --}}

                    {{-- Payment Export History Modal --}}
                    <div class="modal fade" id="paymentExportHistoryModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">
                                        <i class="bx bx-history me-1"></i>
                                        Payment Export History
                                    </h5>

                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <p class="text-muted">
                                        List of paid missions. Each mission appears only once.
                                    </p>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Payment Status</th>
                                                    <th>Paid At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($exportLogs as $mission)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>

                                                        <td>
                                                            <span class="badge bg-success">
                                                                {{ ucfirst($mission->payment_status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $mission->paid_at ? \Carbon\Carbon::parse($mission->paid_at)->format('d/m/Y H:i:s') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{-- <a href="{{ route('missions.edit', [
                                                                'params' => $params,
                                                                'id' => $mission->id,
                                                            ]) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bx bx-show"></i>
                                                                Open
                                                            </a> --}}

                                                            {{-- <button type="button"
                                                                class="btn btn-sm btn-success btn-export-again"
                                                                data-mission-id="{{ $mission->id }}">
                                                                <i class="bx bx-export"></i>
                                                                Export Again
                                                            </button> --}}
                                                            <button type="button"
                                                                class="btn btn-sm btn-success btn-export-again"
                                                                data-paid-at="{{ $mission->paid_at }}">
                                                                <i class="bx bx-export"></i>
                                                                Export Again
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            No paid missions found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- @endif --}}
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cboTodoSelect = document.getElementById('cboTodo');
            const cboTodoChoices = new Choices(cboTodoSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboStatusSelect = document.getElementById('cboStatus');
            const cboStatusChoices = new Choices(cboStatusSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboMissionTypeSelect = document.getElementById('cboMissionType');
            const cboMissionTypeChoices = new Choices(cboMissionTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboNameSelect = document.getElementById('cboName');
            const cboNameChoices = new Choices(cboNameSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboPositionSelect = document.getElementById('cboPosition');
            const cboPositionChoices = new Choices(cboPositionSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboLevelSelect = document.getElementById('cboLevel');
            const cboLevelChoices = new Choices(cboLevelSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboProvinceSelect = document.getElementById('cboProvince');
            const cboProvinceChoices = new Choices(cboProvinceSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>
    <script>
        $('#cboTodo, #cboStatus, #cboMissionType, #cboName ,#cboPosition, #cboLevel, #cboProvince, #start_date, #end_date,#start_index, #end_index')
            .on(
                'change keyup',
                function() {
                    $('#mission-table').DataTable().ajax.reload();
                });
    </script>
    {!! $dataTable->scripts() !!}

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

    <script>
        $('#btnExport').on('click', function(e) {
            e.preventDefault();

            let url = $(this).attr('href');

            let selectedIds = [];

            $('.mission-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            let params = new URLSearchParams();

            // Selected missions
            selectedIds.forEach(function(id) {
                params.append('cboId[]', id);
            });

            // Existing filters
            let cboTodo = $('#cboTodo').val();
            let cboMissionType = $('#cboMissionType').val();
            let cboName = $('#cboName').val();
            let cboProvince = $('#cboProvince').val();
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();

            if (cboTodo) {
                params.append('cboTodo', cboTodo);
            }

            if (cboMissionType) {
                params.append('cboMissionType', cboMissionType);
            }

            if (cboName) {
                params.append('cboName', cboName);
            }

            if (cboProvince) {
                params.append('cboProvince', cboProvince);
            }

            if (startDate) {
                params.append('start_date', startDate);
            }

            if (endDate) {
                params.append('end_date', endDate);
            }

            let queryString = params.toString();

            if (queryString) {
                url += '?' + queryString;
            }

            window.location.href = url;
        });
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        $(document).on('click', '#btnPaymentStatus', function() {

            const $button = $('#btnPaymentStatus');

            // ==========================================================
            // 1. Get selected mission IDs
            // ==========================================================

            // let cboId = $('.mission-checkbox:checked')
            //     .map(function() {
            //         return $(this).val();
            //     })
            //     .get();
            let cboId = getSelectedMissionIds();

            // Remove duplicate IDs
            cboId = [...new Set(cboId)];

            // ==========================================================
            // 2. Check selection
            // ==========================================================

            if (cboId.length === 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'បញ្ជាក់',
                    text: 'សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។'
                });

                return;
            }

            // ==========================================================
            // 3. CSRF
            // ==========================================================

            const csrfToken =
                $('meta[name="csrf-token"]').attr('content');

            // ==========================================================
            // 4. Disable button
            // ==========================================================

            $button
                .prop('disabled', true)
                .html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    កំពុងគណនា...
                `);

            // ==========================================================
            // 5. Loading
            // ==========================================================

            Swal.fire({
                title: 'កំពុងប្រតិបត្តិការ...',
                text: 'កំពុងគណនាចំនួនប្រាក់ សូមរង់ចាំបន្តិច',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            // ==========================================================
            // 6. Calculate payment total
            // ==========================================================

            $.ajax({

                url: "{{ route('missions.paymentTotal', $params) }}",

                type: 'POST',

                data: {
                    _token: csrfToken,
                    cboId: cboId
                },

                success: function(response) {

                    if (!response.success) {

                        Swal.fire({
                            icon: 'warning',
                            title: 'បញ្ហា',
                            text: response.message ||
                                'មិនអាចគណនាចំនួនទឹកប្រាក់បានទេ។'
                        });

                        return;
                    }

                    // ==================================================
                    // Format total
                    // ==================================================

                    const totalAmount =
                        parseFloat(response.total_amount || 0);

                    const formattedTotal =
                        totalAmount.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                    // ==================================================
                    // Confirmation
                    // ==================================================

                    Swal.fire({

                        title: 'បញ្ជាក់ការទូទាត់',

                        html: `
                                    <div class="text-start">

                                        <p>
                                            តើអ្នកចង់ប្តូរស្ថានភាព
                                            <strong>${cboId.length}</strong>
                                            បេសកកម្មដែលបានជ្រើសរើស
                                            ទៅជា
                                            <strong class="text-success">
                                                ទូទាត់
                                            </strong>
                                            មែនទេ?
                                        </p>

                                        <hr>

                                        <div class="d-flex justify-content-between">
                                            <strong>ចំនួនបេសកកម្ម:</strong>
                                            <strong>${cboId.length}</strong>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2">
                                            <strong>សរុបប្រាក់:</strong>

                                            <strong class="text-primary fs-5">
                                                ${formattedTotal}
                                            </strong>
                                        </div>

                                        <div class="mt-3 text-muted small">
                                            បន្ទាប់ពីទូទាត់រួច
                                            ប្រព័ន្ធនឹងទាញយក Excel ដោយស្វ័យប្រវត្តិ។
                                        </div>

                                    </div>
                                `,

                        icon: 'question',

                        showCancelButton: true,

                        confirmButtonText: 'បាទ/ចាស ទូទាត់ និងទាញយក Excel',

                        cancelButtonText: 'បោះបង់',

                        reverseButtons: true,

                        focusCancel: true

                    }).then(function(result) {

                        // ==================================================
                        // Cancel
                        // ==================================================

                        if (!result.isConfirmed) {

                            $button
                                .prop('disabled', false)
                                .html(`
                            <i class="bx bx-money me-1"></i>
                            ទូទាត់
                        `);

                            return;
                        }

                        // ==================================================
                        // Update button
                        // ==================================================

                        $button
                            .prop('disabled', true)
                            .html(`
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        កំពុងទូទាត់...
                    `);

                        // ==================================================
                        // 7. Update payment status
                        // ==================================================

                        $.ajax({

                            url: "{{ route('missions.updatePaymentStatus', $params) }}",

                            type: 'POST',

                            data: {
                                _token: csrfToken,
                                cboId: cboId
                            },

                            success: function(response) {

                                if (!response.success) {

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'បញ្ហា',
                                        text: response.message ||
                                            'មិនអាចប្តូរស្ថានភាពការទូទាត់បានទេ។'
                                    });

                                    return;
                                }

                                // ==================================================
                                // 8. Get ONLY successfully paid mission IDs
                                // ==================================================

                                const paidMissionIds =
                                    response.mission_ids || [];

                                if (paidMissionIds.length === 0) {

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'បញ្ហា',
                                        text: 'មិនមានបេសកកម្មដែលបានទូទាត់ទេ។'
                                    });

                                    return;
                                }

                                console.log(
                                    'Paid Mission IDs:',
                                    paidMissionIds
                                );

                                // ==================================================
                                // 9. Build export URL
                                // ==================================================

                                const exportUrl =
                                    "{{ route('missions.export', ['params' => $params]) }}";

                                const exportParams =
                                    new URLSearchParams();

                                // IMPORTANT:
                                // Add IDs ONLY ONCE
                                paidMissionIds.forEach(function(id) {

                                    exportParams.append(
                                        'cboId[]',
                                        id
                                    );

                                });

                                // ==================================================
                                // 10. Add current filters
                                // ==================================================

                                const cboTodo =
                                    $('#cboTodo').val();

                                const cboMissionType =
                                    $('#cboMissionType').val();

                                const cboName =
                                    $('#cboName').val();

                                const cboProvince =
                                    $('#cboProvince').val();

                                const startDate =
                                    $('#start_date').val();

                                const endDate =
                                    $('#end_date').val();

                                if (cboTodo) {

                                    exportParams.append(
                                        'cboTodo',
                                        cboTodo
                                    );

                                }

                                if (cboMissionType) {

                                    exportParams.append(
                                        'cboMissionType',
                                        cboMissionType
                                    );

                                }

                                if (cboName) {

                                    exportParams.append(
                                        'cboName',
                                        cboName
                                    );

                                }

                                if (cboProvince) {

                                    exportParams.append(
                                        'cboProvince',
                                        cboProvince
                                    );

                                }

                                if (startDate) {

                                    exportParams.append(
                                        'start_date',
                                        startDate
                                    );

                                }

                                if (endDate) {

                                    exportParams.append(
                                        'end_date',
                                        endDate
                                    );

                                }

                                // ==================================================
                                // 11. Final export URL
                                // ==================================================

                                const finalExportUrl =
                                    exportUrl +
                                    '?' +
                                    exportParams.toString();

                                console.log(
                                    'Export URL:',
                                    finalExportUrl
                                );

                                // ==================================================
                                // 12. Success message
                                // ==================================================

                                Swal.fire({

                                    icon: 'success',

                                    title: 'ទូទាត់ជោគជ័យ',

                                    html: `
                                <p>
                                    បានទូទាត់
                                    <strong>${paidMissionIds.length}</strong>
                                    បេសកកម្មជោគជ័យ។
                                </p>

                                <p class="text-muted mb-0">
                                    កំពុងទាញយក Excel...
                                </p>
                            `,

                                    timer: 1500,

                                    showConfirmButton: false,

                                    allowOutsideClick: false

                                });

                                // ==================================================
                                // 13. Clear selection
                                // ==================================================

                                $('#checkAllMissions')
                                    .prop('checked', false);

                                $('.mission-checkbox')
                                    .prop('checked', false);

                                // ==================================================
                                // 14. Reload DataTable
                                // ==================================================

                                $('#mission-table')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);

                                // ==================================================
                                // 15. Download Excel ONCE
                                // ==================================================

                                setTimeout(function() {

                                    window.location.href =
                                        finalExportUrl;

                                }, 700);

                            },

                            error: function(xhr) {

                                console.error(
                                    'Payment Update Error:',
                                    xhr.responseText
                                );

                                const errorMsg =
                                    xhr.responseJSON?.message ||
                                    'មានបញ្ហាក្នុងការប្តូរស្ថានភាពបង់ប្រាក់។';

                                Swal.fire({
                                    icon: 'error',
                                    title: 'បញ្ហា',
                                    text: errorMsg
                                });

                            },

                            complete: function() {

                                $button
                                    .prop('disabled', true)
                                    .html(`
                                <i class="bx bx-money me-1"></i>
                                ទូទាត់
                            `);

                            }

                        });

                    });

                },

                error: function(xhr) {

                    console.error(
                        'Payment Total Error:',
                        xhr.responseText
                    );

                    const errorMsg =
                        xhr.responseJSON?.message ||
                        'មិនអាចគណនាចំនួនទឹកប្រាក់បានទេ។';

                    Swal.fire({
                        icon: 'error',
                        title: 'បញ្ហា',
                        text: errorMsg
                    });

                    $button
                        .prop('disabled', false)
                        .html(`
                    <i class="bx bx-money me-1"></i>
                    ទូទាត់
                `);
                }

            });

        });
    </script>

    <script>
        // =====================================================
        // 1. Persistent selection storage
        // =====================================================
        window.selectedMissionIds = window.selectedMissionIds || new Set();

        // =====================================================
        // 2. Save selection immediately when clicked
        // =====================================================
        $(document).on('change', '#mission-table .mission-checkbox', function() {
            const id = String($(this).val());

            if (this.checked) {
                window.selectedMissionIds.add(id);
            } else {
                window.selectedMissionIds.delete(id);
            }

            console.log(
                'Saved Mission IDs:',
                Array.from(window.selectedMissionIds)
            );

            updateCheckAllState();
        });

        // =====================================================
        // 3. Select all rows on the current page
        // =====================================================
        $(document).on('change', '#checkAllMissions', function() {
            const checked = this.checked;

            $('#mission-table .mission-checkbox').each(function() {
                const id = String($(this).val());

                $(this).prop('checked', checked);

                if (checked) {
                    window.selectedMissionIds.add(id);
                } else {
                    window.selectedMissionIds.delete(id);
                }
            });

            updateCheckAllState();

            console.log(
                'Saved Mission IDs:',
                Array.from(window.selectedMissionIds)
            );
        });

        // =====================================================
        // 4. Restore selection after DataTable pagination
        // =====================================================
        $('#mission-table').on('draw.dt', function() {
            $('#mission-table .mission-checkbox').each(function() {
                const id = String($(this).val());

                $(this).prop(
                    'checked',
                    window.selectedMissionIds.has(id)
                );
            });

            updateCheckAllState();

            console.log(
                'Restored Mission IDs:',
                Array.from(window.selectedMissionIds)
            );
        });

        // =====================================================
        // 5. Update header checkbox
        // =====================================================
        function updateCheckAllState() {
            const $checkboxes = $('#mission-table .mission-checkbox');
            const total = $checkboxes.length;
            const checked = $checkboxes.filter(':checked').length;

            $('#checkAllMissions')
                .prop('checked', total > 0 && checked === total)
                .prop(
                    'indeterminate',
                    checked > 0 && checked < total
                );
        }

        // =====================================================
        // 6. Get ALL selected IDs, including other pages
        // =====================================================
        function getSelectedMissionIds() {
            return Array.from(window.selectedMissionIds);
        }
    </script>

    {{-- <script>
        $('#btnSearchIdRange').on('click', function() {
            const start = $('#start_id').val();
            const end = $('#end_id').val();

            if (start && end && Number(start) > Number(end)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid ID range',
                    text: 'Start ID must be less than or equal to End ID.'
                });
                return;
            }

            $('#mission-table').DataTable().ajax.reload();
        });

        $('#btnResetIdRange').on('click', function() {
            $('#start_id').val('');
            $('#end_id').val('');

            $('#mission-table').DataTable().ajax.reload();
        });
    </script> --}}
    {{-- <script>
        $('#btnSearchIdRange').on('click', function() {
            const start = Number($('#start_index').val());
            const end = Number($('#end_index').val());

            if (start && end && start > end) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid index range',
                    text: 'Start index must be less than or equal to End index.'
                });
                return;
            }

            $('#mission-table').DataTable().ajax.reload();
        });

        $('#btnResetIdRange').on('click', function() {
            $('#start_index, #end_index').val('');
            $('#mission-table').DataTable().ajax.reload();
        });
    </script> --}}

    <script>
        // Search by Mission ID range and calculate payment
        $('#btnSearchIdRange').on('click', function() {
            const start = $('#start_index').val();
            const end = $('#end_index').val();

            if (start && end && Number(start) > Number(end)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid index range',
                    text: 'Start index must be less than or equal to End index.'
                });
                return;
            }

            // Reload the DataTable with the ID range
            $('#mission-table').DataTable().ajax.reload();

            // Calculate payment total for the ID range
            calculatePaymentByIdRange();
        });

        // Reset ID range and reload table
        $('#btnResetIdRange').on('click', function() {
            $('#start_index').val('');
            $('#end_index').val('');

            $('#mission-table').DataTable().ajax.reload();

            // Return to calculation using the selected checkboxes
            calculatePaymentBySelectedIds();
        });

        // Calculate payment using the ID range
        function calculatePaymentByIdRange() {
            const start = $('#start_id').val();
            const end = $('#end_id').val();

            $.ajax({
                url: "{{ route('missions.paymentTotal', $params) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    start_id: start,
                    end_id: end,
                    cboId: []
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Payment Total',
                        html: `
                        <p>Mission ID: ${start || 'Minimum'} - ${end || 'Maximum'}</p>
                        <h3>${Number(response.total || 0).toLocaleString()} ៛</h3>
                    `,
                        confirmButtonText: 'OK'
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to calculate payment total.'
                    });
                }
            });
        }

        // Calculate payment using selected checkboxes after reset
        function calculatePaymentBySelectedIds() {
            const ids = window.selectedMissionIds ?
                Array.from(window.selectedMissionIds) : [];

            if (ids.length === 0) {
                return;
            }

            $.ajax({
                url: "{{ route('missions.paymentTotal', $params) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    cboId: ids
                },
                success: function(response) {
                    console.log('Selected payment total:', response.total);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>

    <script>
        $(document).on('change', '#filterApprovalStatus', function() {
            const status = $(this).val();

            $('#paymentApprovalModal tbody tr[data-approval-status]').each(function() {
                const rowStatus = $(this).data('approval-status');

                $(this).toggle(!status || rowStatus === status);
            });
        });
    </script>

    {{-- Save Payment Draft --}}
    {{-- Save Payment Draft --}}
    {{-- <script>
        $(document).on('click', '#btnSavePaymentDraft', function() {
            const $button = $(this);

            // Get selected IDs and remove duplicates.
            const cboId = [...new Set(
                getSelectedMissionIds().map(Number)
            )].filter(id => Number.isInteger(id) && id > 0);

            if (cboId.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'បញ្ជាក់',
                    text: 'សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។'
                });

                return;
            }

            Swal.fire({
                title: 'Save Draft Payment?',
                text: `Save ${cboId.length} selected missions for payment later?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Save Draft',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $button.prop('disabled', true);

                $.ajax({
                    url: @json(route('missions.savePaymentDraft', [
                            'params' => $params,
                        ])),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        cboId: cboId
                    },

                    success: function(response) {
                        if (!response.success) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Unable to save draft',
                                text: response.message || 'Please try again.'
                            });

                            return;
                        }

                        const amount = Number(response.total_amount || 0)
                            .toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });

                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Saved Successfully',
                            html: `
                            <div class="text-start">
                                <p>
                                    Draft ID:
                                    <strong>${response.draft_id}</strong>
                                </p>

                                <p>
                                    Number of missions:
                                    <strong>${response.mission_count}</strong>
                                </p>

                                <p>
                                    Total payment:
                                    <strong>${amount}</strong>
                                </p>

                                <p>
                                    Saved date:
                                    <strong>${response.created_at || ''}</strong>
                                </p>

                                <p class="text-success mb-0">
                                    <i class="bx bx-check-circle"></i>
                                    No missions have been marked as paid.
                                </p>
                            </div>
                        `,
                            confirmButtonText: 'OK'
                        }).then(function() {
                            // Optional: reload the page if it displays a
                            // server-rendered list of saved drafts.
                            // Remove this line if you do not want a reload.
                            // location.reload();
                        });

                        // If you have a saved-drafts table on this page,
                        // refresh that table here to show the new draft.
                    },

                    error: function(xhr) {
                        let message = 'Failed to save payment draft.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors)
                                .flat()
                                .join('\n');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    },

                    complete: function() {
                        $button.prop('disabled', false);
                    }
                });
            });
        });
    </script> --}}
    {{-- <script>
        $(document).on('click', '#btnSavePaymentDraft', function() {
            const selectedIds = [];

            $('.mission-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'មិនទាន់ជ្រើសរើសបេសកកម្ម',
                    text: 'សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។',
                    confirmButtonText: 'យល់ព្រម'
                });
                return;
            }

            const $button = $(this);
            $button.prop('disabled', true);

            $.ajax({
                url: "{{ route('missions.paymentDraft.calculateTotal', ['params' => $params]) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    cboId: selectedIds
                },
                success: function(response) {
                    if (!response.success) {
                        Swal.fire(
                            'មានបញ្ហា',
                            'មិនអាចគណនាចំនួនទឹកប្រាក់ទូទាត់បានទេ។',
                            'error'
                        );
                        return;
                    }

                    const total = Number(response.total_amount || 0);

                    Swal.fire({
                        icon: 'question',
                        title: 'បញ្ជាក់ការរក្សាទុកសេចក្តីព្រាងទូទាត់',
                        html: `
                        <p>ចំនួនបេសកកម្មដែលបានជ្រើសរើស៖
                            <strong>${response.mission_ids.length}</strong>
                        </p>

                        <p>ទឹកប្រាក់បេសកកម្មដែលមានស្រាប់៖
                            <strong>${Number(response.existing_total).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}</strong>
                        </p>

                        <p>ទឹកប្រាក់បេសកកម្មថ្មី៖
                            <strong>${Number(response.new_total).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}</strong>
                        </p>

                        <hr>

                        <p>ទឹកប្រាក់សរុប៖
                            <strong>${total.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}</strong>
                        </p>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'បញ្ជាក់ និងរក្សាទុក',
                        cancelButtonText: 'បោះបង់'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            // ហៅមុខងារ savePaymentDraft ដើម្បីរក្សាទុកសេចក្តីព្រាងទូទាត់។
                            Swal.fire({
                                icon: 'info',
                                title: 'ការគណនាបានបញ្ចប់',
                                text: 'សូមភ្ជាប់មុខងាររក្សាទុកសេចក្តីព្រាងទូទាត់។',
                                confirmButtonText: 'យល់ព្រម'
                            });
                        }
                    });
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message ||
                        'Failed to calculate payment total.';

                    Swal.fire('Error', message, 'error');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });
    </script> --}}

    <script>
        $(document).on('click', '#btnSavePaymentDraft', function() {

            const $button = $(this);

            // 1. Get selected IDs, including selections from other pages.
            let selectedIds = getSelectedMissionIds();

            selectedIds = [...new Set(selectedIds.map(String))];

            // 2. Validate selection.
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'មិនទាន់ជ្រើសរើសបេសកកម្ម',
                    text: 'សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។',
                    confirmButtonText: 'យល់ព្រម'
                });
                return;
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // 3. Disable button.
            $button.prop('disabled', true);

            // 4. Calculate total.
            $.ajax({
                url: "{{ route('missions.paymentDraft.calculateTotal', ['params' => $params]) }}",
                type: 'POST',
                data: {
                    _token: csrfToken,
                    cboId: selectedIds
                },

                success: function(response) {

                    if (!response.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'មានបញ្ហា',
                            text: response.message ||
                                'មិនអាចគណនាចំនួនទឹកប្រាក់ទូទាត់បានទេ។',
                            confirmButtonText: 'យល់ព្រម'
                        });
                        return;
                    }

                    const money = value =>
                        Number(value || 0).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                    const total = Number(response.total_amount || 0);

                    // 5. Confirm payment draft.
                    Swal.fire({
                        icon: 'question',
                        title: 'បញ្ជាក់ការរក្សាទុកសេចក្តីព្រាងទូទាត់',

                        html: `
                        <div class="text-start">
                            <p>
                                ចំនួនបេសកកម្មដែលបានជ្រើសរើស៖
                                <strong>${selectedIds.length}</strong>
                            </p>

                            <p>
                                ទឹកប្រាក់បេសកកម្មដែលមានស្រាប់៖
                                <strong>${money(response.existing_total)}</strong>
                            </p>

                            <p>
                                ទឹកប្រាក់បេសកកម្មថ្មី៖
                                <strong>${money(response.new_total)}</strong>
                            </p>

                            <hr>

                            <p>
                                ទឹកប្រាក់សរុប៖
                                <strong class="text-primary fs-5">
                                    ${money(total)}
                                </strong>
                            </p>
                        </div>
                    `,

                        showCancelButton: true,
                        confirmButtonText: 'បញ្ជាក់ និងរក្សាទុក',
                        cancelButtonText: 'បោះបង់',
                        reverseButtons: true
                    }).then(function(result) {

                        if (!result.isConfirmed) {
                            return;
                        }

                        // 6. Save the selected missions.
                        $.ajax({
                            url: "{{ route('missions.paymentDraft.save', ['params' => $params]) }}",
                            type: 'POST',
                            data: {
                                _token: csrfToken,
                                cboId: selectedIds
                            },

                            success: function(saveResponse) {

                                if (!saveResponse.success) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'មានបញ្ហា',
                                        text: saveResponse.message ||
                                            'មិនអាចរក្សាទុកសេចក្តីព្រាងទូទាត់បានទេ។',
                                        confirmButtonText: 'យល់ព្រម'
                                    });
                                    return;
                                }

                                // 7. Clear persistent selection.
                                window.selectedMissionIds.clear();

                                $('#checkAllMissions')
                                    .prop('checked', false)
                                    .prop('indeterminate', false);

                                // 8. Show success and refresh DataTable.
                                Swal.fire({
                                    icon: 'success',
                                    title: 'រក្សាទុកបានជោគជ័យ',
                                    text: saveResponse.message ||
                                        'បានរក្សាទុកសេចក្តីព្រាងទូទាត់ដោយជោគជ័យ។',
                                    confirmButtonText: 'យល់ព្រម'
                                }).then(function() {
                                    $('#mission-table')
                                        .DataTable()
                                        .ajax.reload(null, false);
                                });
                            },

                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'មានបញ្ហា',
                                    text: xhr.responseJSON?.message ||
                                        'មានបញ្ហាក្នុងការរក្សាទុកសេចក្តីព្រាងទូទាត់។',
                                    confirmButtonText: 'យល់ព្រម'
                                });
                            },

                            complete: function() {
                                $button.prop('disabled', false);
                            }
                        });
                    });
                },

                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'មានបញ្ហា',
                        text: xhr.responseJSON?.message ||
                            'មិនអាចគណនាចំនួនទឹកប្រាក់ទូទាត់បានទេ។',
                        confirmButtonText: 'យល់ព្រម'
                    });
                },

                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.btn-export-again', function () {
    const paidAt = $(this).attr('data-paid-at');
    const $button = $(this);

    $button.prop('disabled', true);

    $.ajax({
        url: @json(route('missions.exportIds', ['params' => $params])),
        type: 'GET',
        dataType: 'json',
        data: {
            paid_at: paidAt
        },

        success: function (response) {
            if (!response.success || !response.mission_ids.length) {
                Swal.fire(
                    'មិនមានទិន្នន័យ',
                    response.message || 'រកមិនឃើញបេសកកម្មដែលបានទូទាត់ទេ។',
                    'warning'
                );
                return;
            }

            // Use the existing GET export route.
            const exportUrl = new URL(
                @json(route('missions.export', ['params' => $params])),
                window.location.origin
            );

            response.mission_ids.forEach(function (id) {
                exportUrl.searchParams.append('cboId[]', id);
            });

            window.location.href = exportUrl.toString();
        },

        error: function (xhr) {
            Swal.fire(
                'មានបញ្ហា',
                xhr.responseJSON?.message || 'មិនអាចទាញយកទិន្នន័យបានទេ។',
                'error'
            );
        },

        complete: function () {
            $button.prop('disabled', false);
        }
    });
});
    </script>
@endsection
