@extends('layouts.master')

@section('css')
    {{-- Plugin CSS (only if used on this page) --}}
    <link href="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet"
        href="{{ asset('https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css') }}" />
    <style>
        /* Stylish table wrapper */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
            font-family: 'Khmer OS Battambang', sans-serif;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #343a40;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        #programSubModal .card {
            transition: all 0.25s ease;
            cursor: pointer;
        }

        #programSubModal .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
        }
    </style>

    </style>

    </style>
@endsection


@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.dashboard') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">  {{ $item->year }}</a>
                        </li> --}}
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('menus.dashboard') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" method="GET">
                        <div class="col-sm-3">
                            <label for="item_name" class="form-label font-size-13 text-muted">
                                {{ __('forms.year') }}
                            </label>

                            <select class="form-control" name="year" id="year">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($ministries as $item)
                                    <option value="{{ $item->year }}"
                                        {{ $selectedYear == $item->year ? 'selected' : '' }}>
                                        {{ $item->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3 d-flex align-items-center gap-2" style="margin-top: 36px;">
                            <button type="submit" class="btn btn-primary d-flex align-items-center px-3">
                                <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                            </button>

                            <a href="{{ url()->current() }}" class="btn btn-danger d-flex align-items-center px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('buttons.delete') }}
                            </a>
                        </div>
                    </form> --}}
                    <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" method="GET"
                        action="{{ url()->current() }}">
                        <div class="col-sm-3">
                            <label for="year" class="form-label font-size-13 text-muted">
                                {{ __('forms.year') }}
                            </label>

                            <select class="form-control" name="year" id="year">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($ministries as $item)
                                    <option value="{{ $item->year }}"
                                        {{ (string) $selectedYear === (string) $item->year ? 'selected' : '' }}>
                                        {{ $item->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-sm-3 d-flex align-items-center gap-2" style="margin-top: 36px;">
                            <button type="submit" class="btn btn-primary d-flex align-items-center px-3">
                                <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                            </button>

                            <a href="{{ url()->current() }}" class="btn btn-danger d-flex align-items-center px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('buttons.delete') }}
                            </a>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-left">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">{{ __('menus.initial.voucher') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                {{ __('tables.th.financeLaw') }}
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $totalBeginVoucher }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $total_fin_law }}">
                                    {{ number_format($total_fin_law) }} <span>រៀល</span>
                                </span>
                            </span>
                        </div>

                        <div class="col-6">
                            <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>

                    <div class="text-nowrap mt-2">
                        <span class="badge bg-success-subtle text-success">
                            {{ number_format($total_fin_law) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                {{ __('tables.th.total.increase') }}
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $loanCount }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $total_total_increase }}">
                                    {{ number_format($total_total_increase) }} <span>រៀល</span>
                                </span>
                            </span>
                        </div>

                        <div class="col-6">
                            <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>

                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">
                            {{ number_format($total_total_increase) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                {{ __('tables.th.deadline_balance') }}
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $totalBeginVoucher }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $total_deadline_balance }}">
                                    {{ number_format($total_deadline_balance) }} <span>រៀល</span>
                                </span>
                            </span>
                        </div>

                        <div class="col-6">
                            <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>

                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">
                            {{ number_format($total_deadline_balance) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6 d-flex">
                            <div class="col-12">
                                <span class="text-muted lh-4 d-block text-truncate">
                                    {{ __('tables.th.law_average') }}
                                </span>
                                <span class="mb-3">
                                    <span class="counter-value" data-target="{{ $law_average_percent }}">
                                        {{ number_format($law_average_percent, 2) }} %
                                    </span>
                                </span>
                                <div class="card-body">
                                    <span class="mb-3">
                                        <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12">
                                <span class="text-muted lh-4 d-block text-truncate">
                                    {{ __('tables.th.law_correction') }}
                                </span>
                                <span class="mb-3">
                                    <span class="counter-value" data-target="{{ $law_correction_percent }}">
                                        {{ number_format($law_correction_percent, 2) }} %
                                    </span>
                                </span>
                                <div class="card-body">
                                    <span class="mb-3">
                                        <div id="mini-chart5" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 text-nowrap d-flex">
                        <div class="col-12">
                            <span class="badge bg-success-subtle text-success">
                                {{ number_format($law_average_percent, 2) }} %
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="badge bg-success-subtle text-success">
                                {{ number_format($law_correction_percent, 2) }} %
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3">
        @foreach ($programs as $program)
            @php
                $t = $programTotals[$program->no] ?? null;
                // Temporary demo numbers (replace with real totals later)
                $finLaw = $t->fin_law ?? 0; // if not in programs table, keep 0
                $apply = $t->apply ?? 0; // if not in programs table, keep 0
                $remain = max($finLaw - $apply, 0);
            @endphp

            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100 cursor-pointer program-card" role="button" data-bs-toggle="modal"
                    data-bs-target="#programSubModal" data-program-id="{{ $program->id }}"
                    data-program-title="កម្មវិធី {{ $program->no ?? '' }}">

                    <div class="card-body">

                        {{-- Title --}}
                        <div class="mb-3">
                            <span class="text-muted d-block text-truncate">
                                កម្មវិធី {{ $program->no ?? '' }} - {{ $program->name_kh ?? ($program->name ?? '') }}
                            </span>
                        </div>

                        {{-- 3 Columns --}}
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <span class="text-muted font-size-12 d-block">ច្បាប់ហរិញ្ញវត្ថុ</span>
                                <h6 class="mb-0 text-primary">{{ number_format($finLaw) }}</h6>
                                <small class="text-muted">រៀល</small>
                            </div>

                            <div class="col-4 border-end">
                                <span class="text-muted font-size-12 d-block">អនុវត្ត</span>
                                <h6 class="mb-0 text-success">{{ number_format($apply) }}</h6>
                                <small class="text-muted">រៀល</small>
                            </div>

                            <div class="col-4">
                                <span class="text-muted font-size-12 d-block">នៅសល់</span>
                                <h6 class="mb-0 text-danger">{{ number_format($remain) }}</h6>
                                <small class="text-muted">រៀល</small>
                            </div>
                        </div>

                        <div class="text-nowrap mt-3 text-center">
                            <span class="badge bg-info-subtle text-info">
                                {{ $program->name_en ?? 'Program Summary' }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-left">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">{{ __('menus.initial.mandate') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                ប្រេងសាំង
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $totalFuel }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $qtyFuel }}">
                                    {{ number_format($qtyFuel) }} <span>លីត្រ</span>
                                </span>
                            </span>
                        </div>
                        {{-- <div class="col-6">
                            <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div> --}}
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->

        <div class="col-xl-2 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                ប្រេងម៉ាស៊ូត
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $totalDiesel }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $qtyDiesel }}">
                                    {{ number_format($qtyDiesel) }} <span>លីត្រ</span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->

        <div class="col-xl-2 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                ប្រេងម៉ាស៊ីន
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $totalOil }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $qtyOil }}">
                                    {{ number_format($qtyOil) }} <span>លីត្រ</span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->

        <div class="col-xl-2 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                សម្ភារ
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $materialCount }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $total_quantity }}">
                                    {{ number_format($total_quantity) }} <span>{{ __('menus.type') }}</span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->
    </div><!-- end row-->

    <div class="modal fade" id="programSubModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="programSubTitle">Program Sub</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        @php
                            $subs = [
                                [
                                    'id' => 1,
                                    'no' => '01',
                                    'kh' => 'អនុកម្មវិធី ១',
                                    'en' => 'Sub Program 1',
                                    'badge' => 'primary',
                                ],
                                [
                                    'id' => 2,
                                    'no' => '02',
                                    'kh' => 'អនុកម្មវិធី ២',
                                    'en' => 'Sub Program 2',
                                    'badge' => 'success',
                                ],
                                [
                                    'id' => 3,
                                    'no' => '03',
                                    'kh' => 'អនុកម្មវិធី ៣',
                                    'en' => 'Sub Program 3',
                                    'badge' => 'warning',
                                ],
                                [
                                    'id' => 4,
                                    'no' => '04',
                                    'kh' => 'អនុកម្មវិធី ៤',
                                    'en' => 'Sub Program 4',
                                    'badge' => 'danger',
                                ],
                                [
                                    'id' => 5,
                                    'no' => '05',
                                    'kh' => 'អនុកម្មវិធី ៥',
                                    'en' => 'Sub Program 5',
                                    'badge' => 'info',
                                ],
                                [
                                    'id' => 6,
                                    'no' => '06',
                                    'kh' => 'អនុកម្មវិធី ៦',
                                    'en' => 'Sub Program 6',
                                    'badge' => 'secondary',
                                ],
                            ];
                        @endphp

                        @foreach ($subs as $sub)
                            <div class="col-xl-4 col-md-6">
                                <div class="card h-100 shadow-sm border-0 text-center program-sub-card" role="button"
                                    data-sub-id="{{ $sub['id'] }}" data-sub-title="{{ $sub['kh'] }}"
                                    data-bs-toggle="modal" data-bs-target="#clusterModal">
                                    <div class="card-body">
                                        <span class="badge bg-{{ $sub['badge'] }} mb-2">{{ $sub['no'] }}</span>
                                        <h6 class="mt-2">{{ $sub['kh'] }}</h6>
                                        <small class="text-muted">{{ $sub['en'] }}</small>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="clusterModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="clusterModalTitle">Cluster</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>លេខចង្កោម</th>
                                    <th>Name KH</th>
                                    <th>Name EN</th>
                                    <th>ហិរញ្ញវត្ថុ</th>

                                    <th>អនុវត្ត</th>

                                    <th>នៅសល់</th>


                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="clusterRows">

                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- Plugin JS just for this page --}}
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    {{-- DataTables --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('year');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var chartDataFinLaw = @json($chartDataFinLaw);

            function getChartColorsArray(id) {
                var colors = document.getElementById(id).getAttribute("data-colors");
                return JSON.parse(colors);
            }

            var options = {
                chart: {
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: getChartColorsArray("mini-chart1"),
                series: [{
                    name: "ច្បាប់ហរិញ្ញវត្ថុ",
                    data: chartDataFinLaw
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart1"), options);
            chart.render();

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var chartTotalIncrease = @json($chartTotalIncrease);

            function getChartColorsArray(id) {
                var colors = document.getElementById(id).getAttribute("data-colors");
                return JSON.parse(colors);
            }

            var options = {
                chart: {
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: getChartColorsArray("mini-chart2"),
                series: [{
                    name: "ចលនាកើន",
                    data: chartTotalIncrease
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart2"), options);
            chart.render();

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var chartDataDeadLine = @json($chartDataDeadLine);

            function getChartColorsArray(id) {
                var colors = document.getElementById(id).getAttribute("data-colors");
                return JSON.parse(colors);
            }

            var options = {
                chart: {
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: getChartColorsArray("mini-chart3"),
                series: [{
                    name: "សមតុល្យចុងគ្រា",
                    data: chartDataDeadLine
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart3"), options);
            chart.render();

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var chartAvg = @json($chartAvg);

            function getChartColorsArray(id) {
                var colors = document.getElementById(id).getAttribute("data-colors");
                return JSON.parse(colors);
            }

            var options = {
                chart: {
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: getChartColorsArray("mini-chart4"),
                series: [{
                    name: "ច្បាប់មធ្យម",
                    data: chartAvg
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart4"), options);
            chart.render();

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var chartAvgCorrect = @json($chartAvgCorrect);

            function getChartColorsArray(id) {
                var colors = document.getElementById(id).getAttribute("data-colors");
                return JSON.parse(colors);
            }

            var options = {
                chart: {
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: getChartColorsArray("mini-chart5"),
                series: [{
                    name: "កែសម្រួលច្បាប់",
                    data: chartAvgCorrect
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart5"), options);
            chart.render();

        });
    </script>

    {{-- Search Auth Sticky --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const year = document.getElementById('year');
            const form = document.getElementById('filter');

            year.addEventListener('change', function() {
                form.submit();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalTitle = document.getElementById('programSubTitle');
            const loading = document.getElementById('programSubLoading');
            const content = document.getElementById('programSubContent');
            const empty = document.getElementById('programSubEmpty');
            const rows = document.getElementById('programSubRows');

            document.querySelectorAll('.program-card').forEach(card => {
                card.addEventListener('click', async () => {
                    const programId = card.dataset.programId;
                    const programTitle = card.dataset.programTitle || 'Program';

                    modalTitle.textContent = programTitle + ' - Program Sub';

                    // reset UI
                    rows.innerHTML = '';
                    loading.classList.remove('d-none');
                    content.classList.add('d-none');
                    empty.classList.add('d-none');

                    try {
                        const url = `{{ url('/dashboard/programs') }}/${programId}/subs`;
                        const res = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });


                        const json = await res.json();
                        const data = json.data || [];

                        loading.classList.add('d-none');

                        if (data.length === 0) {
                            empty.classList.remove('d-none');
                            return;
                        }

                        data.forEach((item, i) => {
                            rows.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${item.no ?? ''}</td>
                            <td>${item.name_kh ?? ''}</td>
                            <td>${item.name_en ?? ''}</td>
                        </tr>
                    `);
                        });

                        content.classList.remove('d-none');
                    } catch (e) {
                        loading.classList.add('d-none');
                        empty.classList.remove('d-none');
                        empty.textContent = 'Error loading program_sub.';
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.program-card').forEach(card => {
                card.addEventListener('click', () => {
                    const title = card.dataset.programTitle || 'Program';
                    document.getElementById('programSubTitle').textContent = title +
                        ' - Program Sub';
                });
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            const clusterTitle = document.getElementById('clusterModalTitle');
            const clusterRows = document.getElementById('clusterRows');

            document.querySelectorAll('.program-sub-card').forEach(card => {
                card.addEventListener('click', () => {
                    const subTitle = card.dataset.subTitle;

                    clusterTitle.textContent = subTitle + ' - Clusters';
                    clusterRows.innerHTML = '';

                    // Static example (replace with dynamic later)
                    const clusters = [{
                            code: '01',
                            kh: 'ក្លាស្ទ័រ ១',
                            en: 'Cluster 1',
                            financial: '5000000',
                            status: 'Active'
                        },
                        {
                            code: '02',
                            kh: 'ក្លាស្ទ័រ ២',
                            en: 'Cluster 2',
                            financial: '3000000',
                            status: 'Active'
                        },
                        {
                            code: '03',
                            kh: 'ក្លាស្ទ័រ ៣',
                            en: 'Cluster 3',
                            financial: '2000000',
                            status: 'Inactive'
                        },
                        {
                            code: '04',
                            kh: 'ក្លាស្ទ័រ ៤',
                            en: 'Cluster 4',
                            financial: '0',
                            status: 'Inactive'
                        },
                        {
                            code: '05',
                            kh: 'ក្លាស្ទ័រ ៥',
                            en: 'Cluster 5',
                            financial: '1500000',
                            status: 'Active'
                        },
                        {
                            code: '06',
                            kh: 'ក្លាស្ទ័រ ៦',
                            en: 'Cluster 6',
                            financial: '1500000',
                            status: 'Active'
                        },
                        {
                            code: '07',
                            kh: 'ក្លាស្ទ័រ ៧',
                            en: 'Cluster 7',
                            financial: '1300000',
                            status: 'Inactive'
                        }
                    ];

                    clusters.forEach((item, i) => {
                        clusterRows.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="text-center">${i + 1}</td>
                        <td>${item.code}</td>
                        <td>${item.kh}</td>
                        <td>${item.en}</td>
                        <td>${item.financial}</td>
                     
                        <td>
                            <span class="badge ${item.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>
                `);
                    });
                });
            });

        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const programSubModalEl = document.getElementById('programSubModal'); // first modal
            const clusterModalEl = document.getElementById('clusterModal'); // second modal

            const clusterTitle = document.getElementById('clusterModalTitle');
            const clusterRows = document.getElementById('clusterRows');

            document.querySelectorAll('.program-sub-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    // prevent bootstrap auto open (we will open after hiding first modal)
                    e.preventDefault();

                    const subTitle = card.dataset.subTitle || 'Program Sub';

                    clusterTitle.textContent = subTitle + ' - Clusters';
                    clusterRows.innerHTML = '';

                    // Static example (replace with dynamic later)
                    const clusters = [{
                            code: '01',
                            kh: 'ក្លាស្ទ័រ ១',
                            en: 'Cluster 1',
                            fin: 5000000,
                            apply: 3000000,
                            remain: 2000000,
                            status: 'Active'
                        },
                        {
                            code: '02',
                            kh: 'ក្លាស្ទ័រ ២',
                            en: 'Cluster 2',
                            fin: 3000000,
                            apply: 1000000,
                            remain: 2000000,
                            status: 'Active'
                        },
                        {
                            code: '03',
                            kh: 'ក្លាស្ទ័រ ៣',
                            en: 'Cluster 3',
                            fin: 2000000,
                            apply: 0,
                            remain: 2000000,
                            status: 'Inactive'
                        },
                    ];

                    clusters.forEach((item, i) => {
                        clusterRows.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="text-center">${i + 1}</td>
                        <td>${item.code}</td>
                        <td>${item.kh}</td>
                        <td>${item.en}</td>
                        <td class="text-end">${Number(item.fin).toLocaleString()}</td>
                        <td class="text-end">${Number(item.apply).toLocaleString()}</td>
                        <td class="text-end">${Number(item.remain).toLocaleString()}</td>
                        <td class="text-center">
                            <span class="badge ${item.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>
                `);
                    });

                    // ✅ Hide first modal, then show second modal (no conflict)
                    const programSubModal = bootstrap.Modal.getInstance(programSubModalEl);
                    if (programSubModal) programSubModal.hide();

                    programSubModalEl.addEventListener('hidden.bs.modal',
                        function openClusterOnce() {
                            programSubModalEl.removeEventListener('hidden.bs.modal',
                                openClusterOnce);
                            new bootstrap.Modal(clusterModalEl).show();
                        });
                });
            });
        });
    </script>
@endsection
