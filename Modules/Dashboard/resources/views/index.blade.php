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
                    <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" method="GET">
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
                                    {{ $totalBeginVoucher }} {{-- filtered by year --}}
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

    </div><!-- end row-->

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

    {{-- <div class="row">
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">My Wallet</span>
                            <h4 class="mb-3">
                                $<span class="counter-value" data-target="865.2">0</span>k
                            </h4>
                        </div>

                        <div class="col-6">
                            <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">+$20.9k</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Number of Trades</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="6258">0</span>
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->

        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Invested Amount</span>
                            <h4 class="mb-3">
                                $<span class="counter-value" data-target="4.32">0</span>M
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">+ $2.8k</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Profit Ration</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="12.57">0</span>%
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">+2.95%</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row--> --}}
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
@endsection
