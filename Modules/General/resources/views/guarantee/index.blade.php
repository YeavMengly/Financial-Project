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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.beginning.credit') }}</h4>

                <div class="page-title-right">

                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" id="filter">
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="year">{{ __('menus.account') }}</label>
                            <select class="form-control" name="year" id="year" required>
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($initialBudget as $ts)
                                    <option value="{{ $ts->year }}"
                                        {{ request('year') == $ts->year ? 'selected' : '' }}>{{ $ts->year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="subAccountNumber">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="subAccountNumber" id="subAccountNumber" required>
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($initialBudget as $ts)
                                    <option value="{{ $ts->subAccountNumber }}"
                                        {{ request('subAccountNumber') == $ts->subAccountNumber ? 'selected' : '' }}>
                                        {{ $ts->subAccountNumber }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-sm-3">
                            <label class="visually-hidden" for="program">{{ __('menus.program') }}</label>
                            <select class="form-control" name="program" id="program" required>
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($initialBudget as $ts)
                                    <option value="{{ $ts->program }}"
                                        {{ request('program') == $ts->program ? 'selected' : '' }}>{{ $ts->program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboTxtChapter">{{ __('menus.account') }}</label>
                            <input type="text" class="form-control" name="txtChapter" required
                                data-pristine-required-message="{{ __('messages.required') }}"
                                placeholder="{{ __('menus.description') }}" />
                        </div>

                        <!-- Start Date -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="start_date">{{ __('menus.start_date') }}</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ request('start_date') }}" />
                        </div>

                        <!-- End Date -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="end_date">{{ __('menus.end_date') }}</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" />
                        </div>

                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger ms-2" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>
                            @if (hasPermission('beginCredit.create'))
                                {{-- <div class="col-sm-3"> --}}
                                {{-- <div class="mb-4"> --}}
                                {{-- <a class="btn btn-light waves-effect waves-light ms-2" href=""><i
                                        class="bx bx-download me-1"></i>
                                    {{ __('buttons.download') }}</a> --}}
                                {{-- </div> --}}
                                {{-- </div> --}}


                                <a class="btn btn-light ms-2" href="" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Download PDF">
                                    <i class="fas fa-print" style="color: red"></i>
                                </a>

                                <a class="btn btn-light ms-2" href="" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Download Excel">
                                    <i class="fas fa-file-excel fa-1x" style="color: green"></i>
                                </a>
                            @endif
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
                    {{-- @if (hasPermission('beginCredit.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('beginCredit.create') }}"><i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}</a>
                            </div>
                        </div>
                    @endif --}}

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


    <script>
        document.getElementById('btnReset').addEventListener('click', function() {
            document.getElementById('filter').reset();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('year');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('subAccountNumber');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('program');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
