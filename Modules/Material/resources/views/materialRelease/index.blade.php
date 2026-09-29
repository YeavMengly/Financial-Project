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
                <h4 class="mb-sm-0 font-size-18">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"> {{ __('menus.material') }}</li>
                        <li class="breadcrumb-item">{{ __('menus.release') }}</li>
                    </ol>
                </h4>
                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.material') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.release') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $ministry->year }}
                            </li>
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
                    <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" method="GET">

                        <div class="row">
                            <!-- Project Dropdown -->
                            <div class="col-sm-2">
                                <label for="project"
                                    class="form-label font-size-13 text-muted">{{ __('forms.project') }}</label>
                                <select class="form-control" name="project" id="project">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($project as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('project') == $item->id ? 'selected' : '' }}>
                                            {{ $item->stock_number }}-{{ $item->stock_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Company Name Dropdown -->
                            <div class="col-sm-2">
                                <label for="companyName"
                                    class="form-label font-size-13 text-muted">{{ __('forms.company.name') }}</label>
                                <select class="form-control" name="company_name" id="companyName">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($companies as $item)
                                        <option value="{{ $item->company_name }}"
                                            {{ request('company_name') == $item->company_name ? 'selected' : '' }}>
                                            {{ $item->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- User Entry Dropdown -->
                            <div class="col-sm-2">
                                <label for="userEntry"
                                    class="form-label font-size-13 text-muted">{{ __('forms.user.entry') }}</label>
                                <select class="form-control" name="user_entry" id="userEntry">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($userEntries as $item)
                                        <option value="{{ $item->user_entry }}"
                                            {{ request('user_entry') == $item->user_entry ? 'selected' : '' }}>
                                            {{ $item->user_entry }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Source Dropdown -->
                            <div class="col-sm-2">
                                <label for="source"
                                    class="form-label font-size-13 text-muted">{{ __('forms.source') }}</label>
                                <select class="form-control" name="source" id="source">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($sources as $item)
                                        <option value="{{ $item->source }}"
                                            {{ request('source') == $item->source ? 'selected' : '' }}>
                                            {{ $item->source }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <label for="item_name" class="form-label font-size-13 text-muted">
                                    {{ __('forms.pro.name') }}
                                </label>
                                <select class="form-control" name="p_name" id="Pname">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($materialRelease as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('p_name') == $item->p_name ? 'selected' : '' }}>
                                            {{ $item->p_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="col-sm-2">
                                <label for="unit" class="form-label font-size-13 text-muted">
                                    {{ __('forms.unit') }}
                                </label>
                                <select class="form-control" name="unit" id="unit">
                                    <option value="">{{ __('forms.search...') }}</option>
                                    @foreach ($unitType as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('unit') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                        <div class="col-sm-3 d-flex align-items-center gap-2" style="margin-top: 34px;">

                            {{-- Search --}}
                            <button type="submit" class="btn btn-primary d-flex align-items-center px-3">
                                <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                            </button>

                            {{-- Reset --}}
                            <a id="btnReset" class="btn btn-danger d-flex align-items-center px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('buttons.delete') }}
                            </a>

                            {{-- Export --}}
                            <a href="{{ route('materialRelease.export', array_merge(['params' => $params])) }}"
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
                    @if (hasPermission('materialRelease.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('materialRelease.create', $params) }}">
                                    <i class="bx bx-plus me-1"></i> {{ __('buttons.create') }}
                                </a>
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

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const project = document.getElementById('project');
            const projectChoices = new Choices(project, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false,
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const companyName = document.getElementById('companyName');
            const companyNameChoices = new Choices(companyName, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const userEntry = document.getElementById('userEntry');
            const userEntryChoices = new Choices(userEntry, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const source = document.getElementById('source');
            const sourceChoices = new Choices(source, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const Pname = document.getElementById('Pname');
            const PnameChoices = new Choices(Pname, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const unit = document.getElementById('unit');
            const unitChoices = new Choices(unit, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
        $('#btnReset').on('click', function() {
            $('#filter')[0].reset();

            $('#materialrelease-table').DataTable().ajax.reload();
        });


        $('#cboCategory').change(function() {
            var cateId = $(this).val();
            $.ajax({
                url: '{!! route('document.by.category_id') !!}',
                type: 'get',
                global: false,
                data: {
                    cate_id: cateId
                },
                success: function(data) {
                    $('#cboCategorySub').html(data);
                }
            });
        });
    </script>
    <script>
        let fpStart = null;
        let fpEnd = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Function to extract URL parameters (keeps date values after page refresh)
            function getUrlParam(param) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            const initialStartDate = getUrlParam('start_date') || document.getElementById('start_date')?.value ||
                null;
            const initialEndDate = getUrlParam('end_date') || document.getElementById('end_date')?.value || null;

            // Common config with auto-reload on selection
            const dateConfig = {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                onChange: function(selectedDates, dateStr) {
                    // Instantly refresh DataTables when date changes
                    if ($.fn.DataTable.isDataTable('#materialrelease-table')) {
                        $('#materialrelease-table').DataTable().ajax.reload();
                    }
                }
            };
            const startDateEl = document.getElementById('start_date');
            const endDateEl = document.getElementById('end_date');
            // Initialize Start Date Flatpickr
            if (startDateEl) {
                fpStart = flatpickr(startDateEl, {
                    ...dateConfig,
                    defaultDate: initialStartDate
                });
            }
            // Initialize End Date Flatpickr
            if (endDateEl) {
                fpEnd = flatpickr(endDateEl, {
                    ...dateConfig,
                    defaultDate: initialEndDate
                });
            }
        });
    </script>

    <script>
        $('#project, #companyName, #userEntry, #source, #Pname,#stockNum')
            .on('change keyup',
                function() {
                    $('#materialrelease-table').DataTable().ajax.reload();
                });
    </script>
@endsection
