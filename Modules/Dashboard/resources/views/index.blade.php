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

        .table-scroll {
            max-height: 31vh;
            /* Set scroll height */
            overflow-y: auto;
        }

        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        #tableBody td {
            font-size: small;
        }

        .cardhover:hover {
            background-color: #fdf9f9;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.dashboard') }}</a></li>
                        {{-- <li class="breadcrumb-item active">{{ __('menus.dashboard') }}</li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Main Production --}}
    <div class="row">
        <div class="col-xl-2 col-md-6">
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

        <div class="col-xl-2 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                {{ __('tables.th.credit_movement') }}
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
        <div class="col-xl-2 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="d-flex flex-wrap align-items-center mb-4 w-100">
                            <span class="text-muted lh-4 d-block text-truncate">
                                {{ __('tables.th.new_credit_status') }}
                            </span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    {{ $loanCount }}
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $total_new_credit_status }}">
                                    {{ number_format($total_new_credit_status) }} <span>រៀល</span>
                                </span>
                            </span>
                        </div>

                        <div class="col-6">
                            <div id="mini-chart9" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
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

        <div class="col-xl-2 col-md-6">
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

        <div class="col-xl-2 col-md-6">
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
    <div class="row">
        {{-- bar chart --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div id="bar_chart" data-colors='["#2ab57d"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

    </div>
    {{-- chapter ,account --}}
    <div class=" ">
        <div class="card">
            <form id="chFilter" class="card-header align-items-center d-flex" method="GET"
                action="{{ url()->current() }}">
                <div class="flex-shrink-0">
                    <select class="form-select-sm" name="chapterLabels" id="chapterLabels">
                        <option selected="">ជំពូក</option>
                        @foreach ($chapterLabels as $ch)
                            <option value="{{ $ch }}" {{ request('chapterLabels') == $ch ? 'selected' : '' }}>
                                {{ $ch }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="card-body">
                <div class=" " data-simplebar style="max-height: 380px;">
                    <div class="table-responsive table-scroll">
                        <table class="table table-bordered table-striped table-hover align-middle  ">
                            <thead class=" text-center table-light sticky-header">
                                <tr>
                                    <th>{{ __('tables.th.account') }}</th>
                                    <th>{{ __('tables.th.fin_law') }}</th>
                                    <th>{{ __('tables.th.deadline_balance') }}</th>
                                    <th>{{ __('tables.th.remaining_credit') }}</th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @forelse ($accounts as $acc)
                                    <tr data-chapter="{{ $acc->no }}">
                                        <td class="text-center">{{ $acc->no }}</td>
                                        <td class="text-end">{{ number_format($acc->fin_law) }} ៛</td>

                                        <td class="text-end">{{ number_format($acc->deadline_balance) }} ៛</td>
                                        <td class="text-end">{{ number_format($acc->credit) }} ៛</td>
                                        <td>
                                            <div class="dropdown text-center account-card"
                                                data-account-id="{{ $acc->id }}"
                                                data-account-title="គណនី {{ $acc->no }}">
                                                <a class="text-muted dropdown-toggle font-size-15" role="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No accounts found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

    </div>
    <div class="row">
        <div class="col-xl-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    {{-- <div class="d-flex flex-wrap align-items-center mb-4">
                        <h5 class="card-title me-2">{{ __('labels.begin.budget') }}</h5>
                        <div class="ms-auto">
                            <div>
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    ALL
                                </button>
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    1M
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    6M
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-sm">
                                    1Y
                                </button>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row align-items-center">
                        <div class="col-sm">
                            <div id="wallet-balance" data-colors='["#d91b1b", "#52c41a", "#faad14"]' class="apex-charts">
                            </div>
                        </div>
                        <div class="col-sm align-self-center">
                            <div class="mt-4 mt-sm-0">
                                <div>
                                    <p class="mb-2">
                                        <i class="mdi mdi-circle align-middle font-size-10 me-2"
                                            style="color:#faad14"></i>
                                        {{ __('tables.th.financeLaw') }}
                                    </p>
                                    <h6>
                                        <span class="text-muted font-size-14 fw-normal">
                                            {{ number_format($total_fin_law) }} រៀល
                                        </span>
                                    </h6>
                                </div>
                                <div class="mt-4 pt-2">
                                    <p class="mb-2">
                                        <i class="mdi mdi-circle align-middle font-size-10 me-2"
                                            style="color:#52c41a"></i>
                                        បាន/កំពុង {{ __('tables.th.apply') }}
                                    </p>
                                    <h6>
                                        <span class="text-muted font-size-14 fw-normal">
                                            {{ number_format($total_deadline_balance) }} រៀល
                                        </span>
                                    </h6>
                                </div>
                                <div class="mt-4 pt-2">
                                    <p class="mb-2">
                                        <i class="mdi mdi-circle align-middle font-size-10 me-2"
                                            style="color:#d91b1b"></i>
                                        {{ __('tables.th.deadline_balance') }}
                                    </p>
                                    <h6>
                                        <span class="text-muted font-size-14 fw-normal">
                                            {{ number_format($total_credit) }} រៀល
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        {{-- Expense type --}}
        <div class="col-xl-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <div id="Expense-Type" data-colors='["#faad14","#2200ff","#e81a2c" ,"#fde50c" ]'
                                class="apex-charts">
                            </div>
                        </div>
                        <div class="col-sm align-self-center">
                            <div class="mt-4 mt-sm-0">
                                <div>
                                    <p class="mb-2">
                                        <i class="mdi mdi-circle align-middle font-size-10 me-2"
                                            style="color:#faad14"></i>
                                        <span class="me-3">{{ __('menus.check.control.guarantee') }}</span>
                                        <button type="button" class="btn btn-soft-primary btn-sm first-letter: mb-3">
                                            {{ $totalCountArch }}
                                        </button>
                                        <button type="button" class="btn btn-soft-danger btn-sm mb-3">
                                            {{ $totalCountDir }}
                                        </button>
                                        <button class="flex-shrink-0 text-end btn btn-soft-info btn-sm mb-3"
                                            type="button">
                                            <span class="dropdown">
                                                <a class="text-muted dropdown-toggle font-size-14" role="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true">
                                                    នៅសល់
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end cardhover mt-1 ml-4"
                                                    style="min-width:250px;">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center gap-2 py-2">
                                                        <i class="mdi mdi-circle"
                                                            style="color:#c0341e; font-size:10px;"></i>
                                                        <h6 class="mb-0 text-muted font-size-14 fw-normal">
                                                            {{ number_format($totalExpend) }} រៀល</h6>
                                                    </div>
                                                </div>
                                            </span>
                                        </button>
                                    </p>
                                    <h6>
                                        <span class="text-muted font-size-14 fw-normal">
                                            {{ number_format($expenditure_Guarantee) }} រៀល
                                        </span>
                                    </h6>
                                </div>
                                <div class="mt-10 pt-2">
                                    <p class="mb-2">
                                        <i class="mdi mdi-circle align-middle font-size-10 me-2"
                                            style="color:#2200ff"></i>
                                        <span class="me-3">ទូទាត់ </span>
                                        <button type="button" class="btn btn-soft-primary btn-sm mb-3">
                                            {{ $totalCountDir }}
                                        </button>
                                        <button class="flex-shrink-0 text-end btn btn-soft-info btn-sm mb-3"
                                            type="button">
                                            <span class="dropdown w-100">
                                                <a class="text-muted dropdown-toggle font-size-14" role="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true">
                                                    នៅសល់
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end cardhover mt-1"
                                                    style="min-width:250px;">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center gap-2 py-2">
                                                        <i class="mdi mdi-circle"
                                                            style="color:#c0341e; font-size:10px;"></i>
                                                        <h6 class="mb-0 text-muted font-size-14 fw-normal">
                                                            {{ number_format($totalDir) }} រៀល</h6>
                                                    </div>
                                                </div>
                                            </span>
                                        </button>
                                    </p>
                                    <h6>
                                        <span class="text-muted font-size-14 fw-normal">
                                            {{ number_format($direct_Payment) }} រៀល
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
    </div>

    {{-- Program Data Info --}}
    <div class="row">
        @foreach ($programs as $program)
            <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="col-xl-3 col-lg-4 col-md-6 program-card" style="cursor:pointer"
                data-program-id="{{ $program->id }}"
                data-program-title="{{ __('menus.program') }} {{ $program->no }}">
                <div class="card card-h-100 shadow-sm border-1 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="text-truncate">
                                <div class="text-muted small">{{ __('menus.program') }} <span>{{ $program->no }}</span>
                                </div>
                            </div>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-soft-info btn-sm js-count-btn">
                                    {{ $program->total_records }}
                                </button>
                                <button type="button" class="btn btn-soft-primary btn-sm js-count-btn">
                                    {{ $program->total_record_mandate }}
                                </button>
                                <button type="button" class="btn btn-soft-danger btn-sm">
                                    {{ $program->total_record_voucher }}
                                </button>
                            </div>
                        </div>
                        <div class="row text-center g-2">
                            <div class="col-4 border-end">
                                <span class="text-muted font-size-12 d-block">ច្បាប់ហិរញ្ញវត្ថុ</span>
                                <span
                                    class="counter-value mb-0 text-primary">{{ number_format($program->fin_law) }}</span>
                                <small class="text-muted">រៀល</small>
                            </div>
                            <div class="col-4 border-end">
                                <span class="text-muted font-size-12 d-block">អនុវត្ត</span>
                                <span class="counter-value mb-0 text-success">{{ number_format($program->apply) }}</span>
                                <small class="text-muted">រៀល</small>
                            </div>
                            <div class="col-4">
                                <span class="text-muted font-size-12 d-block">នៅសល់</span>
                                <span class="counter-value mb-0 text-danger">{{ number_format($program->credit) }}</span>
                                <small class="text-muted">រៀល</small>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <small class="text-muted d-block">
                                អនុវត្ត: <strong>{{ number_format($program->percent, 2) }}%</strong>
                            </small>
                        </div>
                        <span class="badge bg-success-subtle text-success program-card" role="button"
                            data-program-id="{{ $program->id }}"
                            data-program-title="{{ __('menus.program') }} {{ $program->no }}" style="cursor:pointer;">
                        <span class="badge bg-success-subtle text-success" role="button" style="cursor:pointer;">
                            Click to view details
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{-- chapter ,account --}}
    <div class="row">
        <div class="card">
            <form id="chFilter" class="card-header align-items-center d-flex" method="GET"
                action="{{ url()->current() }}">
                <div class="flex-shrink-0">
                    <select class="form-select-sm" name="chapterLabels" id="chapterLabels">
                        <option selected="">ជំពូក</option>
                        @foreach ($chapterLabels as $ch)
                            <option value="{{ $ch }}" {{ request('chapterLabels') == $ch ? 'selected' : '' }}>
                                {{ $ch }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="card-body">
                <div class=" " data-simplebar style="max-height: 380px;">
                    <div class="table-responsive table-scroll">
                        <table class="table table-bordered table-striped table-hover align-middle  ">
                            <thead class=" text-center table-light sticky-header">
                                <tr>
                                    <th>{{ __('tables.th.account') }}</th>
                                    <th>{{ __('tables.th.fin_law') }}</th>
                                    <th>{{ __('tables.th.deadline_balance') }}</th>
                                    <th>{{ __('tables.th.remaining_credit') }}</th>
                                    <th>បន្ថែម</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @forelse ($accounts as $acc)
                                    <tr data-chapter="{{ $acc->no }}">
                                        <td class="text-center">{{ $acc->no }}</td>
                                        <td class="text-end">{{ number_format($acc->fin_law) }} ៛</td>
                                        <td class="text-end">{{ number_format($acc->deadline_balance) }} ៛</td>
                                        <td class="text-end">{{ number_format($acc->credit) }} ៛</td>
                                        <td>
                                            <div class="dropdown text-center account-card"
                                                data-account-id="{{ $acc->id }}"
                                                data-account-title="គណនី {{ $acc->no }}">
                                                <a class="text-muted dropdown-toggle font-size-15" role="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No accounts found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>

    <div class="row">
        @php
            $qtyFuelRemain = max(($qtyFuel ?? 0) - ($qtyFuelRelease ?? 0), 0);
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="text-muted lh-4 d-block text-truncate">ប្រេងសាំង</span>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <form id="itemFilterForm" method="GET" action="{{ url()->current() }}">
                                @foreach (request()->except('item_name') as $k => $v)
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endforeach

                                <select name="item_name" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    @foreach ($itemOptions as $opt)
                                        <option value="{{ $opt }}"
                                            {{ ($itemName ?? '') === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-7">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-success-subtle">
                                        <small class="text-muted d-block">{{ __('menus.entry') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyFuel ?? 0, 2) }} <span class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="p-2 rounded bg-danger-subtle">
                                        <small class="text-muted d-block">{{ __('menus.release') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyFuelRelease ?? 0, 2) }} <span
                                                class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-2 rounded bg-primary-subtle">
                                        <small class="text-muted d-block">{{ __('menus.remain') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyFuelRemain, 2) }} <span class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div id="fuelDonutChart" class="apex-charts"></div>
                        </div>
                    </div>
                    <div class="text-nowrap mt-3">
                        <span class="badge bg-info-subtle text-info">Entry vs Release</span>
                        <span class="ms-1 text-muted font-size-13">{{ $year ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $qtyDieselRemain = max(($qtyDiesel ?? 0) - ($qtyDieselRelease ?? 0), 0);
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mb-3 w-100">
                        <span class="text-muted lh-4 d-block text-truncate">ប្រេងម៉ាស៊ូត</span>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-soft-primary btn-sm">
                                {{ $totalDiesel }}
                            </button>
                        </div>
                    </div>
                    <div class="row align-items-center g-2">
                        <div class="col-7">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-success-subtle">
                                        <small class="text-muted d-block">{{ __('menus.entry') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyDiesel ?? 0, 2) }} <span class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-danger-subtle">
                                        <small class="text-muted d-block">{{ __('menus.release') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyDieselRelease ?? 0, 2) }} <span
                                                class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 rounded bg-primary-subtle">
                                        <small class="text-muted d-block">{{ __('menus.remain') }}</small>
                                        <div class="fw-semibold">
                                            {{ number_format($qtyDieselRemain, 2) }} <span
                                                class="text-muted">លីត្រ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div id="dieselDonutChart" class="apex-charts"></div>
                        </div>
                    </div>
                    <div class="text-nowrap mt-2">
                        <span class="badge bg-info-subtle text-info">Entry vs Release</span>
                        <span class="ms-1 text-muted font-size-13">{{ $year ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-2 col-md-6">
            <div class="card card-h-100">
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

                        <div class="col-4">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $qtyOil }}">
                                    {{ number_format($qtyOil) }} <span>លីត្រ</span>
                                </span>
                            </span>
                        </div>

                        <div class="col-4">
                            <span class="mb-3">
                                <span class="counter-value" data-target="{{ $qtyOilRelease }}">
                                    {{ number_format($qtyOilRelease) }} <span>លីត្រ</span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">-29 Trades</span>
                        <span class="ms-1 text-muted font-size-13">Since last week</span>
                    </div>
                </div>
            </div>
        </div>

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
        </div>
    </div>

    {{-- Modal Program Sub --}}
    <div class="modal fade" id="programSubModal" tabindex="-1" aria-labelledby="programSubModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programSubModalLabel">Program Sub List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="programSubContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Cluster --}}
    <div class="modal fade" id="clusterModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('menus.program.sub') }} <span id="clusterModalTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="clusterContent" class="text-center text-muted">
                        កំពុងផ្ទុកទិន្នន័យ...
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Sub-Account --}}
    <div class="modal fade" id="accountSubModal" tabindex="-1" aria-labelledby="accountSubModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountSubModalLabel">Account Sub List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="accountSubContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
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

            var chartDataCreditStatus = @json($chartDataCreditStatus);

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
                colors: getChartColorsArray("mini-chart9"),
                series: [{
                    name: "ស្ថានភាពឥណទានថ្មី",
                    data: chartDataCreditStatus
                }],
            };

            var chart = new ApexCharts(document.querySelector("#mini-chart9"), options);
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
            const chapter = document.getElementById('chapterLabels');
            const form = document.getElementById('filter');
            const chapterForm = document.getElementById('chapterFilter');

            year.addEventListener('change', function() {
                form.submit();
            });

            chapter.addEventListener('change', function() {
                chapterForm.submit();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.program-card');

            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const programId = this.dataset.programId;
                    const programTitle = this.dataset.programTitle;

                    // Set modal title
                    document.getElementById('programSubModalLabel').innerText = programTitle;

                    // Show modal immediately with loading spinner
                    const modalElement = document.getElementById('programSubModal');
                    document.getElementById('programSubContent').innerHTML = `
                        <div class="d-flex justify-content-center align-items-center" style="height:150px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `;
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();

                    // Fetch programSubs via AJAX
                    fetch(`/dashboard/program/${programId}/subs`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                document.getElementById('programSubContent').innerHTML =
                                    '<p class="text-center">មិនមានអនុកម្មវិធីដែលអាចបង្ហាញបាន។</p>';
                                return;
                            }
                            // else {
                            //     console.log(data)
                            // }

                            // Build HTML grid
                            let html = '<div class="row g-2">';
                            data.forEach(sub => {
                                html += `
                           <div class="col-md-4 mb-2">
                                <div class="card shadow-sm program-sub-card"
                                    data-sub-id="${sub.id}"
                                    data-sub-no="${sub.no}"
                                    style="cursor:pointer">
                                    <div class="card-body">

                                    <!-- Header -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-truncate">
                                            <div class="text-muted small">
                                                អនុកម្មវិធី <span>${sub.no}</span>
                                            </div>
                                            <small class="text-muted">
                                                ${sub.description ?? '-'}
                                            </small>
                                        </div>

                                        <div class="ms-auto">
                                            <button type="button" class="btn btn-soft-info btn-sm">
                                                ${sub.total_records ?? 0}
                                            </button>
                                             <button type="button" class="btn btn-soft-primary btn-sm">
                                                ${sub.total_record_sub_mandate ?? 0}
                                            </button>
                                             <button type="button" class="btn btn-soft-danger btn-sm">
                                                ${sub.total_record_sub_voucher ?? 0}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Financial Row -->
                                    <div class="row text-center g-2">
                                        <div class="col-4 border-end">
                                            <span class="text-muted font-size-12 d-block">ច្បាប់ហិរញ្ញវត្ថុ</span>
                                            <span class="counter-value mb-0 text-primary">
                                                ${Number(sub.fin_law ?? 0).toLocaleString()}
                                            </span>
                                            <small class="text-muted">រៀល</small>
                                        </div>

                                        <div class="col-4 border-end">
                                            <span class="text-muted font-size-12 d-block">អនុវត្ត</span>
                                            <span class="counter-value mb-0 text-success">
                                                ${Number(sub.apply ?? 0).toLocaleString()}
                                            </span>
                                            <small class="text-muted">រៀល</small>
                                        </div>

                                        <div class="col-4">
                                            <span class="text-muted font-size-12 d-block">នៅសល់</span>
                                            <span class="counter-value mb-0 text-danger">
                                                ${Number(sub.credit ?? 0).toLocaleString()}
                                            </span>
                                            <small class="text-muted">រៀល</small>
                                        </div>
                                    </div>

                                    <!-- Percent -->
                                    <div class="mt-3 text-center">
                                        <small class="text-muted d-block">
                                            អនុវត្ត:
                                            <strong>${Number(sub.percent ?? 0).toFixed(2)}%</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                            });
                            html += '</div>';

                            document.getElementById('programSubContent').innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById('programSubContent').innerHTML =
                                '<p class="text-danger text-center">មិនមានទិន្នន័យដែលអាចបង្ហាញបាន។</p>';
                        });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {
            const card = e.target.closest('.program-sub-card');
            if (!card) return;

            const programSubId = card.dataset.subId;
            const subNo = card.dataset.subNo;

            document.getElementById('clusterModalTitle').innerText = subNo;
            document.getElementById('clusterContent').innerHTML =
                '<p class="text-center">កំពុងផ្ទុកទិន្នន័យ...</p>';

            const modal = new bootstrap.Modal(document.getElementById('clusterModal'));
            modal.show();

            fetch(`/dashboard/program-sub/${programSubId}/clusters`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        document.getElementById('clusterContent').innerHTML =
                            '<p class="text-center">មិនមានចង្កោម (Cluster)</p>';
                        return;
                    }

                    let html = '<div class="row g-2">';
                    data.forEach(cluster => {
                        html += `
                        <div class="col-md-4">
                            <div class="card shadow-sm cluster-card"
                                data-cluster-id="${cluster.id}"
                                style="cursor:pointer">
                                <div class="card-body">

                                    <div class="text-muted small mb-1">
                                        ចង្កោម ${cluster.no}
                                    </div>

                                    <div class="row text-center g-2">
                                        <div class="col-4 border-end">
                                            <small class="text-muted">ច្បាប់</small>
                                            <div class="counter-value text-primary">
                                                ${Number(cluster.fin_law ?? 0).toLocaleString()}
                                            </div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <small class="text-muted">អនុវត្ត</small>
                                            <div class="counter-valuetext-success ">
                                                ${Number(cluster.apply ?? 0).toLocaleString()}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">នៅសល់</small>
                                            <div class="counter-value text-danger">
                                                ${Number(cluster.credit ?? 0).toLocaleString()}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    `;
                    });
                    html += '</div>';

                    document.getElementById('clusterContent').innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('clusterContent').innerHTML =
                        '<p class="text-danger text-center">បរាជ័យក្នុងការផ្ទុកទិន្នន័យ</p>';
                });
        });

        document.addEventListener('click', e => {
            const cluster = e.target.closest('.cluster-card');
            if (!cluster) return;

            const clusterId = cluster.dataset.clusterId;
            console.log('Load vouchers for cluster:', clusterId);

            // fetch(`/dashboard/cluster/${clusterId}/vouchers`)
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const el = document.querySelector("#wallet-balance");
            const colors = JSON.parse(el.getAttribute("data-colors"));

            const options = {
                chart: {
                    type: "donut",
                    height: 260
                },
                series: [
                    {{ round($percent_credit, 2) }},
                    {{ round($percent_deadline_balance, 2) }},
                    {{ round($percent_fin_law, 2) }}
                ],
                labels: [
                    "{{ __('tables.th.deadline_balance') }}",
                    "បាន/កំពុង{{ __('tables.th.apply') }}",
                    "{{ __('tables.th.financeLaw') }}"
                ],
                colors: colors,
                plotOptions: {
                    pie: {
                        donut: {
                            size: "0%" // 👈 makes it look like your image
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: "13px",
                        fontWeight: "600",
                        colors: ["#fff"]
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                stroke: {
                    width: 0
                },
                legend: {
                    position: "bottom",
                    show: true // 👈 image has no legend under donut
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + "%";
                        }
                    }
                }
            };
            new ApexCharts(el, options).render();
        });
    </script>
    {{-- Expense type --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const el = document.querySelector("#Expense-Type");
            const colors = JSON.parse(el.getAttribute("data-colors"));

            const options = {
                chart: {
                    type: "donut",
                    height: 260
                },
                series: [
                    {{ round($percent_expenditure_Guarantee, 2) }},
                    {{ round($percent_direct_Payment, 2) }},
                ],
                labels: [
                    "ធានាចំណាយ",
                    // "បុរេប្រទាន",
                    "ទូទាត់",
                    // "នៅសល់",
                    // "បើកផ្ដល់មុន",
                ],
                colors: colors,
                plotOptions: {
                    pie: {
                        donut: {
                            size: "0%" // 👈 makes it look like your image
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: "13px",
                        fontWeight: "600",
                        colors: ["#fff"]
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                stroke: {
                    width: 0
                },
                legend: {
                    position: "bottom",
                    show: true // 👈 image has no legend under donut
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + "%";
                        }
                    }
                }
            };
            new ApexCharts(el, options).render();
        });
    </script>
    {{-- barchart --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function formatCurrency(value) {
                return new Intl.NumberFormat('en-US').format(value) + " ៛";
            }

            const el = document.querySelector("#bar_chart");
            const colors = JSON.parse(el.getAttribute("data-colors"));

            let chapterLabels = @json($chapterLabels);
            let finLawData = @json($finLawData);
            let remainData = @json($remainData);
            let deadlineData = @json($deadlineData);

            // combine data
            let combined = chapterLabels.map((label, index) => ({
                label: label,
                finLaw: finLawData[index],
                remain: remainData[index],
                deadline: deadlineData[index]
            }));

            // sort big → small by chapter label
            combined.sort((a, b) => b.label - a.label);

            // rebuild arrays after sorting
            const label = combined.map(item => 'ជំពូក' + item.label);
            const sortedFinLaw = combined.map(item => item.finLaw);
            const sortedRemain = combined.map(item => item.remain);
            const sortedDeadline = combined.map(item => item.deadline);

            const options = {
                chart: {
                    type: "bar",
                    height: 260,
                    toolbar: {
                        show: false
                    }
                },
                colors: colors,
                series: [{
                    name: "",
                    data: sortedFinLaw.map((value, index) => ({
                        x: label[index],
                        y: value,
                        remain: sortedRemain[index],
                        deadline: sortedDeadline[index]
                    }))
                }],
                xaxis: {
                    categories: label
                },
                plotOptions: {
                    bar: {
                        borderRadius: 2,
                        columnWidth: "60%"
                    }
                },
                dataLabels: {
                    enabled: false
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return formatCurrency(value);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value, opts) {
                            const remain = opts.w.config.series[0].data[opts.dataPointIndex].remain;
                            const deadline = opts.w.config.series[0].data[opts.dataPointIndex].deadline;

                            return "សរុប: " + formatCurrency(value) +
                                "<br>អនុវត្ដ: " + formatCurrency(remain) +
                                "<br>នៅសល់: " + formatCurrency(deadline);
                        }
                    }
                },
                // responsive for mobile
                responsive: [{
                    breakpoint: 992,
                    options: {
                        chart: {
                            height: 300
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "60%"
                            }
                        }
                    }
                }, {
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 280
                        },
                        xaxis: {
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: "10px"
                                }
                            }
                        }
                    }
                }, {
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 260
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "70%"
                            }
                        },
                        xaxis: {
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: "9px"
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: "9px"
                                }
                            }
                        }
                    }
                }]
            };

            new ApexCharts(el, options).render();
        });
    </script>
    {{-- search Chapter --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const chapterSelect = document.getElementById('chapterLabels');
            const tableRows = document.querySelectorAll('#tableBody tr');

            new Choices(chapterSelect, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                placeholderValue: 'ជ្រើសរើសជំពូក',
                searchPlaceholderValue: 'ស្វែងរក...'
            });

            chapterSelect.addEventListener('change', function() {

                const selectedChapter = this.value.trim().toLowerCase();

                tableRows.forEach(row => {
                    const rowChapter = (row.getAttribute('data-chapter') || '').trim()
                        .toLowerCase();
                    const selected = selectedChapter.trim().toLowerCase();

                    if (!selected || selected === 'ជំពូក'.toLowerCase()) {
                        row.style.display = '';
                    } else if (rowChapter.includes(selectedChapter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });


            });
        });
    </script>
    {{-- Modal Sub-Account --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.account-card');

            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const accountId = this.dataset.accountId;
                    const accountTitle = this.dataset.accountTitle;

                    // Set modal title
                    document.getElementById('accountSubModalLabel').innerText = accountTitle;

                    // Show modal immediately with loading spinner
                    const modalElement = document.getElementById('accountSubModal');
                    document.getElementById('accountSubContent').innerHTML = `
                        <div class="d-flex justify-content-center align-items-center" style="height:150px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `;
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();

                    // Fetch programSubs via AJAX
                    fetch(`/dashboard/account/${accountId}/subs`)
                        .then(res => res.json())
                        .then(data => {
                            // console.log(data);
                            if (data.length === 0) {
                                document.getElementById('accountSubContent').innerHTML =
                                    '<p class="text-center">មិនមានអនុគណនីដែលអាចបង្ហាញបាន។</p>';
                                return;
                            }

                            // Build HTML grid
                            let html = `
                                <div class="card-body">
                                    <div data-simplebar style="max-height: 380px;">
                                        <div class="table-responsive table-scroll">
                                            <table class="table table-bordered table-striped table-hover align-middle">
                                                <thead class="text-center table-light sticky-header">
                                                    <tr>
                                                        <th>អនុគណនី</th>
                                                        <th>ច្បាប់ហិរញ្ញវត្ថុ</th>
                                                        <th>អនុវត្ត</th>
                                                        <th>នៅសល់</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                            `;
                            data.forEach(subs => {
                                html += `
                                    <tr data-subs-id="${subs.id}" data-subs-no="${subs.no}" class="text-end font-size-14">
                                        <td class="text-center">${subs.no}</td>
                                        <td>${Number(subs.fin_law ?? 0).toLocaleString()}</td>
                                        <td>${Number(subs.apply ?? 0).toLocaleString()}</td>
                                        <td>${Number(subs.credit ?? 0).toLocaleString()}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;

                            document.getElementById('accountSubContent').innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById('accountSubContent').innerHTML =
                                '<p class="text-danger text-center">មិនមានទិន្នន័យដែលអាចបង្ហាញបាន។</p>';
                        });
                });
            });
        });
    </script>
