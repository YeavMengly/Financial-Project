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
                    {{ __('menus.beginning.credit') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.ministries') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.credit') }}</li>
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

                    {{-- <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0">
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="agencyNumber">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="agencyNumber" id="agencyNumber">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($data as $agc)
                                    <option
                                        value="{{ $agc->agencyNumber }}"{{ request('agencyNumber') == $agc->agencyNumber ? 'selected' : '' }}>
                                        {{ $agc->agency->agencyNumber }}-{{ $agc->agency->agencyTitle }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="subAccountNumber">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="subAccountNumber" id="subAccountNumber">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($data as $ts)
                                    <option value="{{ $ts->subAccountNumber }}"
                                        {{ request('subAccountNumber') == $ts->subAccountNumber ? 'selected' : '' }}>
                                        {{ $ts->subAccountNumber }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="program">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="program" id="program">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($data as $ts)
                                    <option value="{{ $ts->program }}"
                                        {{ request('program') == $ts->program ? 'selected' : '' }}>
                                        {{ $ts->program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="description">{{ __('menus.description') }}</label>
                            <input type="text" class="form-control" name="txtDescription"
                                value="{{ request('txtDescription') }}" placeholder="{{ __('menus.description') }}" />
                        </div>

                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger ms-2" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>

                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (hasPermission('beginVoucher.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('beginVoucher.create', $params) }}">
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
            const agencyNumber = document.getElementById('agencyNumber');
            const agencyNumberChoices = new Choices(agencyNumber, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const subAccountNumber = document.getElementById('subAccountNumber');
            const subAccountNumberChoices = new Choices(subAccountNumber, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const program = document.getElementById('program');
            const programChoices = new Choices(program, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.getElementById('btnReset').addEventListener('click', function() {
            document.getElementById('filter').reset();
        });

         <script>
        $('#cboCategory').change(function () {
            var cateId = $(this).val();
            $.ajax({
                url: '{!! route("document.by.category_id") !!}',
                type: 'get',
                global: false,
                data: {cate_id: cateId},
                success: function (data) {
                    $('#cboCategorySub').html(data);
                }
            });
        });
    </script>
    </script>
@endsection
