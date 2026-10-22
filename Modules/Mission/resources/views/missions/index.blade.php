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

                                    {{-- Payment Drafts --}}
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#paymentDraftModal">
                                        <i class="bx bx-file me-1"></i>
                                        Payment Drafts
                                        <span class="badge bg-light text-dark ms-1">
                                            {{ $paymentDrafts->count() ?? 0 }}
                                        </span>
                                    </button>

                                    {{-- Approval Records --}}
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#paymentApprovalModal">
                                        <i class="bx bx-check-shield me-1"></i>
                                        Approval Records
                                    </button>

                                    {{-- Export History --}}
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
                                <div class="d-flex flex-wrap align-items-end gap-2">

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

                                </div>
                            </div>
                        </div>

                    </div>


                    {{-- Payment Drafts Modal --}}
                    <div class="modal fade" id="paymentDraftModal" tabindex="-1"
                        aria-labelledby="paymentDraftModalLabel" aria-hidden="true">

                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header bg-warning-subtle">
                                    <h5 class="modal-title" id="paymentDraftModalLabel">
                                        <i class="bx bx-file me-1"></i>
                                        Payment Drafts
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <div
                                        class="d-flex flex-wrap justify-content-between
                            align-items-center gap-2 mb-3">

                                        <div>
                                            <h6 class="mb-1">Saved payment drafts</h6>
                                            <small class="text-muted">
                                                Continue editing a draft or add existing records.
                                            </small>
                                        </div>

                                        <button type="button" class="btn btn-primary" id="btnCreatePaymentDraft">
                                            <i class="bx bx-plus me-1"></i>
                                            Create Draft from Selected Records
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered table-hover
                                  align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="40">#</th>
                                                    <th>Mission ID</th>
                                                    <th>Mission Description</th>
                                                    <th>Draft Saved At</th>
                                                    <th>Status</th>
                                                    <th width="120">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($paymentDrafts as $draft)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $draft->id }}</td>
                                                        <td>{{ $draft->description ?? '-' }}</td>
                                                        <td>
                                                            {{ $draft->payment_draft_saved_at ? $draft->payment_draft_saved_at->format('d/m/Y H:i:s') : '-' }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning text-dark">
                                                                {{ $draft->payment_approval_status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('missions.edit', [
                                                                'params' => $params,
                                                                'id' => $draft->id,
                                                            ]) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="bx bx-edit"></i>
                                                                Open
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            No payment drafts found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr>

                                    <h6>Available records to add to a draft</h6>
                                    <p class="text-muted small">
                                        Select eligible mission records that have not already
                                        been paid. The server must validate each selected record.
                                    </p>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="40">
                                                        <input type="checkbox" id="selectAllDraftRecords">
                                                    </th>
                                                    <th>Mission ID</th>
                                                    <th>Description</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                            </thead>

                                            <tbody id="availableDraftRecords">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        Load eligible records from the server here.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- Payment Approval Modal --}}
                    <div class="modal fade" id="paymentApprovalModal" tabindex="-1" aria-hidden="true">

                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header bg-info-subtle">
                                    <h5 class="modal-title">
                                        <i class="bx bx-check-shield me-1"></i>
                                        Payment Approval Records
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Approval Status</label>
                                            <select class="form-select" id="filterApprovalStatus">
                                                <option value="">All statuses</option>
                                                <option value="draft">Draft</option>
                                                <option value="pending">Pending Approval</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                                <option value="paid">Paid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mission ID</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Submitted At</th>
                                                    <th>Approved At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($approvalRecords as $record)
                                                    <tr data-approval-status="{{ $record->payment_approval_status }}">
                                                        <td>{{ $record->id }}</td>
                                                        <td>{{ $record->description ?? '-' }}</td>
                                                        <td>
                                                            {{ ucfirst($record->payment_approval_status) }}
                                                        </td>
                                                        <td>
                                                            {{ $record->payment_submitted_at ? $record->payment_submitted_at->format('d/m/Y H:i') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $record->payment_approved_at ? $record->payment_approved_at->format('d/m/Y H:i') : '-' }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('missions.edit', [
                                                                'params' => $params,
                                                                'id' => $record->id,
                                                            ]) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            No approval records found.
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


                    {{-- Payment Export History Modal --}}
                    <div class="modal fade" id="paymentExportHistoryModal" tabindex="-1" aria-hidden="true">

                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">
                                        <i class="bx bx-history me-1"></i>
                                        Payment Export History
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <p class="text-muted">
                                        Review previous exports or generate a new copy.
                                        Each export should create a new history entry.
                                    </p>

                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered table-hover
                                  align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Mission ID</th>
                                                    <th>File Name</th>
                                                    <th>Exported By</th>
                                                    <th>Exported At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($exportLogs as $log)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $log->mission_id }}</td>
                                                        <td>{{ $log->file_name ?? '-' }}</td>
                                                        <td>
                                                            {{ $log->exporter?->name ?? 'Deleted User' }}
                                                        </td>
                                                        <td>
                                                            {{ $log->exported_at ? $log->exported_at->format('d/m/Y H:i:s') : '-' }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('missions.edit', [
                                                                'params' => $params,
                                                                'id' => $log->mission_id,
                                                            ]) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bx bx-show"></i>
                                                                Open
                                                            </a>

                                                            {{-- Connect this button to your existing export route. --}}
                                                            <button type="button"
                                                                class="btn btn-sm btn-success btn-export-again"
                                                                data-mission-id="{{ $log->mission_id }}">
                                                                <i class="bx bx-export"></i>
                                                                Export Again
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            No export history found.
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

    <script>
        /**
         * 11. Final export URL and save payment-status export history
         */

        const finalExportUrl =
            exportUrl + '?' + exportParams.toString();

        const selectedIds = Array.from(
            document.querySelectorAll('.mission-checkbox:checked')
        ).map(checkbox => Number(checkbox.value));

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;

        const startId = document.getElementById('start_index')?.value;
        const endId = document.getElementById('end_index')?.value;

        fetch(@json(route('missions.payment-status.export-history', ['params' => $params])), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    export_url: finalExportUrl,
                    file_name: 'payment_status_export.xlsx',
                    mission_ids: selectedIds,
                    start_id: startId || null,
                    end_id: endId || null,
                    filters: Object.fromEntries(exportParams.entries())
                })
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to save export history.');
                }

                // Download only after the history has been saved.
                window.location.href = finalExportUrl;
            })
            .catch(error => {
                console.error(error);
                alert(error.message || 'Unable to save export history.');
            });
    </script>
@endsection
