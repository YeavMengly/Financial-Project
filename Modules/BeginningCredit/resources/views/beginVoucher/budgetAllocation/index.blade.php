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
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"> {{ __('menus.credit') }}</li>
                        <li class="breadcrumb-item">{{ __('menus.budget.allocation') }}</li>
                    </ol>
                </h4>
                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $ministry->name }}</li>
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
                        <div class="col-sm-3">
                            <label for="companyName" class="form-label font-size-13 text-muted">
                                {{ __('forms.expense.type') }}
                            </label>
                            <select class="form-control" name="cboExpenseType" id="cboExpenseType">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($expenseTypes as $expenseType)
                                    <option value="{{ $expenseType->id }}"
                                        {{ request('cboExpenseType') == $expenseType->id ? 'selected' : '' }}>
                                        {{ $expenseType->name_kh }}
                                    </option>
                                @endforeach
                            </select>
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
                    {{-- @if (hasPermission('program.sub.create') && $module->is_archived != 2) --}}
                    <div class="col-sm">
                        <div class="mb-4 d-flex flex-wrap gap-2">
                            @if (hasPermission('budgetAllocation.create') && $ministry->is_archived != 2)
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('budgetAllocation.create', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId]) }}">
                                    <i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}
                                </a>
                            @endif

                            <a class="btn btn-dark"
                                href="{{ route('beginVoucher.index', ['params' => $params]) }}">{{ __('buttons.back') }}</a>
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
            const cboExpenseType = document.getElementById('cboExpenseType');
            const cboExpenseTypeChoices = new Choices(cboExpenseType, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    <script>
        $('#cboExpenseType').on('change keyup', function() {
            $('#budgetallocation-table').DataTable().ajax.reload();
        });
    </script>
@endsection
