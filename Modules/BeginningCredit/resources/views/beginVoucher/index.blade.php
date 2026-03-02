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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" method="GET">
                        <div class="col-sm-3">
                            <select class="form-control" name="agency" id="agency">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($agency as $ag)
                                    <option value="{{ $ag->id }}"
                                        {{ request('agency') == $ag->id ? 'selected' : '' }}>
                                        {{ $ag->no }} - {{ $ag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select class="form-control" name="chapter" id="chapter">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($chapter as $ch)
                                    <option value="{{ $ch->no }}"
                                        {{ request('chapter') == $ch->no ? 'selected' : '' }}>
                                        {{ $ch->no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select class="form-control" name="account" id="account">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($account as $as)
                                    <option value="{{ $as->no }}"
                                        {{ request('account') == $as->no ? 'selected' : '' }}>
                                        {{ $as->no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select class="form-control" name="accountSub" id="accountSub">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($accountSub as $as)
                                    <option value="{{ $as->no }}"
                                        {{ request('accountSub') == $as->no ? 'selected' : '' }}>
                                        {{ $as->no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3 d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center px-3">
                                <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                            </button>

                            <a href="{{ url()->current() }}" class="btn btn-danger d-flex align-items-center px-3">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('buttons.delete') }}
                            </a>

                            <a href="{{ route(
                                'beginVoucher.export',
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

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const agency = document.getElementById('agency');
            const agencyChoices = new Choices(agency, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើសអង្គភាព',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const chapter = document.getElementById('chapter');
            const chapterChoices = new Choices(chapter, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើសជំពូក',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const account = document.getElementById('account');
            const accountChoices = new Choices(account, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើសគណនី',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const accountSub = document.getElementById('accountSub');
            const accountSubChoices = new Choices(accountSub, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើសអនុគណនី',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.getElementById('btnReset').addEventListener('click', function() {
            document.getElementById('filter').reset();
        });
    </script>
@endsection
