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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.budget.control.mandate') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $data->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $data->name }}</li>
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
                    <form class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" id="filter" method="GET">
                        <!-- Sub Account Number -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="subAccountNumber">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="subAccountNumber" id="subAccountNumber">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($budgetMandate as $ts)
                                    <option value="{{ $ts->subAccountNumber }}"
                                        {{ request('subAccountNumber') == $ts->subAccountNumber ? 'selected' : '' }}>
                                        {{ $ts->subAccountNumber }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="program">{{ __('menus.sub.account') }}</label>
                            <select class="form-control" name="program" id="program">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($budgetMandate as $ts)
                                    <option value="{{ $ts->program }}"
                                        {{ request('program') == $ts->program ? 'selected' : '' }}>
                                        {{ $ts->program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Task Type -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="task_type">{{ __('menus.task') }}</label>
                            <select class="form-control" name="task_type" id="task_type">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($taskType as $ts)
                                    <option value="{{ $ts->task }}"
                                        {{ request('task_type') == $ts->task ? 'selected' : '' }}>
                                        {{ $ts->task }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="description">{{ __('menus.description') }}</label>
                            <input type="text" class="form-control" name="description"
                                value="{{ request('description') }}" placeholder="{{ __('menus.description') }}" />
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
                    @if (hasPermission('budgetMandate.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('budgetMandate.create', $params) }}"><i class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}</a>
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
            const taskTypeSelect = document.getElementById('agencyNumber');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសអង្គភាព', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('subAccountNumber');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសអនុគណនី', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('program');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសកម្មវិធី', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('task_type');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសប្រភេទ', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>
@endsection
