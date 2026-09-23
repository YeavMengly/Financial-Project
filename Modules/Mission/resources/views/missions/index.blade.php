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
                                    <option value="3">2. បានទូទាត់រួចរាល់ - Paid</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboStatus" class="form-label font-size-13 text-muted">ជ្រើសរើស ស្ថានភាព</label>
                               
                                <select class="form-select" id="cboStatus" name="cboStatus">
                                    <option value="1">ជ្រើសរើស ស្ថានភាព</option>
                                    <option value="2" selected>សកម្ម</option>
                                    <option value="3">លុប</option>
                                </select>
                            </div>
                        </div> --}}

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

                        {{-- <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboPosition"
                                    class="form-label font-size-13 text-muted">{{ __('menus.content.position') }}</label>
                                <select class="form-control" name="cboPosition" id="cboPosition">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($position as $p)
                                        <option value="{{ $p->id }}"
                                            {{ request('cboPosition') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        {{-- <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="cboLevel"
                                    class="form-label font-size-13 text-muted">{{ __('menus.content.level') }}</label>
                                <select class="form-control" name="cboLevel" id="cboLevel">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($level as $l)
                                        <option value="{{ $l->id }}"
                                            {{ request('cboLevel') == $l->id ? 'selected' : '' }}>
                                            {{ $l->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

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
                                {{-- <a id="btnExport"
                                    href="{{ route(
                                        'budgetVoucher.export',
                                        array_merge(
                                            ['params' => $params],
                                            request()->only(['cboTodo', 'cboStatus', 'cboExpenseType', 'cboAccountSub', 'start_date', 'end_date']),
                                        ),
                                    ) }}"
                                    class="btn btn-success d-flex align-items-center px-3">
                                    <i class="bx bx-download me-1"></i> {{ __('buttons.download') }}
                                </a> --}}
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
                        <div class="mb-4 d-flex flex-wrap gap-2">

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

                            <a class="btn btn-dark" href="{{ route('initialMissions.index') }}">
                                {{ __('buttons.back') }}
                            </a>

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
        $('#cboTodo, #cboStatus, #cboMissionType, #cboName ,#cboPosition, #cboLevel, #cboProvince, #start_date, #end_date')
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

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        $(document).on('click', '#btnPaymentStatus', function() {

            let cboId = $(".mission-checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            if (cboId.length === 0) {
                toastr.warning('សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។');
                return;
            }

            // Show loading while calculating total
            Swal.fire({
                title: 'កំពុងប្រតិបត្តិការ...',
                text: 'សូមរង់ចាំបន្តិច',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('missions.paymentTotal', $params) }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    cboId: cboId
                },

                success: function(response) {

                    if (!response.success) {
                        Swal.close();
                        toastr.warning('មិនអាចគណនាចំនួនទឹកប្រាក់បានទេ។');
                        return;
                    }

                    let totalAmount = parseFloat(response.total_amount || 0);

                    // Format number: 1,234,567.00
                    let formattedTotal = totalAmount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    // Now show confirmation
                    Swal.fire({
                        title: 'បញ្ជាក់ការទូទាត់',
                        html: `
                        <div class="text-start">
                            <p>
                                តើអ្នកចង់ប្តូរស្ថានភាពបេសកកម្ម
                                <strong>${cboId.length}</strong>
                                ដែលបានជ្រើសទៅជា
                                <strong>ទូទាត់</strong> <i class="bx bx-money me-1"></i> មែនទេ?
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
                        </div>
                    `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'បាទ/ចាស ទូទាត់',
                        cancelButtonText: 'បោះបង់',
                        reverseButtons: true
                    }).then(function(result) {

                        if (!result.isConfirmed) {
                            return;
                        }

                        // Disable button
                        $("#btnPaymentStatus")
                            .prop("disabled", true)
                            .html(`
                            <span class="spinner-border spinner-border-sm me-1"
                                  role="status"></span>
                            កំពុងដំណើរការ...
                        `);

                        $.ajax({
                            url: "{{ route('missions.updatePaymentStatus', $params) }}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                cboId: cboId
                            },

                            success: function(response) {

                                if (response.success) {

                                    toastr.success(response.message);

                                    $("#mission-table")
                                        .DataTable()
                                        .ajax.reload(null, false);

                                    $("#checkAllMissions")
                                        .prop("checked", false);

                                    $(".mission-checkbox")
                                        .prop("checked", false);
                                }
                            },

                            error: function(xhr) {

                                let errorMsg = xhr.responseJSON?.message ||
                                    'មានបញ្ហាក្នុងការប្តូរស្ថានភាពបង់ប្រាក់។';

                                toastr.warning(errorMsg);
                            },

                            complete: function() {

                                $("#btnPaymentStatus").html(`
                                <i class="bx bx-money me-1"></i>
                                ទូទាត់
                            `);

                                let checked =
                                    $(".mission-checkbox:checked").length;

                                $("#btnPaymentStatus")
                                    .prop("disabled", checked === 0);
                            }
                        });
                    });
                },

                error: function(xhr) {

                    Swal.close();

                    let errorMsg = xhr.responseJSON?.message ||
                        'មិនអាចគណនាចំនួនទឹកប្រាក់បានទេ។';

                    toastr.warning(errorMsg);
                }
            });
        });
    </script>
    {{-- <script>
        $(document).on('click', '#btnPaymentStatus', function() {
            let cboId = $(".mission-checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            if (cboId.length === 0) {
                toastr.warning('សូមជ្រើសរើសបេសកកម្មយ៉ាងហោចណាស់មួយ។');
                return;
            }

            Swal.fire({
                title: 'បញ្ជាក់ការបង់ប្រាក់',
                text: 'តើអ្នកចង់ប្តូរស្ថានភាពបេសកកម្មដែលបានជ្រើសទៅជា បានបង់ មែនទេ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'បាទ/ចាស បង់ប្រាក់',
                cancelButtonText: 'បោះបង់',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: "{{ route('missions.updatePaymentStatus', $params) }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        cboId: cboId
                    },
                    beforeSend: function() {
                        $("#btnPaymentStatus")
                            .prop("disabled", true)
                            .html(`
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Processing...
                    `);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Display success message returned from controller
                            toastr.success(response.message);

                            // Reload DataTable and clear selection state
                            $("#mission-table").DataTable().ajax.reload(null, false);
                            $("#checkAllMissions").prop("checked", false);
                            $(".mission-checkbox").prop("checked", false);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message ||
                            'មានបញ្ហាក្នុងការប្តូរស្ថានភាពបង់ប្រាក់។';
                        toastr.warning(errorMsg);
                    },
                    complete: function() {
                        // Restore button appearance and re-evaluate state
                        $("#btnPaymentStatus").html(`
                    <i class="bx bx-money me-1"></i>
                    បង់ប្រាក់
                `);

                        let checked = $(".mission-checkbox:checked").length;
                        $("#btnPaymentStatus").prop("disabled", checked === 0);
                    }
                });
            });
        });
    </script> --}}
@endsection
