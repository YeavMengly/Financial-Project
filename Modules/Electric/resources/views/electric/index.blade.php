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
                        <li class="breadcrumb-item"> {{ __('menus.electric') }}</li>
                        <li class="breadcrumb-item">{{ __('menus.entity') }}</li>
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

                            {{-- <label for="item_name" class="form-label font-size-13 text-muted">
                                {{ __('forms.title.entity') }}
                            </label> --}}
                            <select class="form-control" data-trigger id="dropEntity" name="title_entity" required
                                data-pristine-required-message="{{ __('messages.required') }}">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($electricEntity as $item)
                                    <option value="{{ $item->id }}"
                                        data-location_number="{{ $item->location_number }}">
                                        {{ $item->location_number }} - {{ $item->title_entity }}
                                    </option>
                                @endforeach
                            </select>
                            @error('title_entity')
                                <div class="pristine-error text-help">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-3">
                            <select class="form-control" name="invoice" id="invoice">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($electric as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->invoice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            {{-- <div class="form-group mb-3"> --}}
                            {{-- <label for="date_entry" class="form-label">កាលបរិច្ឆេទ</label> --}}
                            <input type="text" id="use_start" name="use_start" class="form-control"
                                placeholder="{{ __('forms.select_date') }}" required
                                data-pristine-required-message="{{ __('messages.required') }}" />
                            @error('use_start')
                                <div class="pristine-error text-help">{{ $message }}</div>
                            @enderror
                            {{-- </div> --}}
                        </div>

                        <div class="col-sm-3">
                            {{-- <div class="form-group mb-3"> --}}
                            {{-- <label for="date_entry" class="form-label">កាលបរិច្ឆេទ</label> --}}
                            <input type="text" id="use_end" name="use_end" class="form-control"
                                placeholder="{{ __('forms.select_date') }}" required
                                data-pristine-required-message="{{ __('messages.required') }}" />
                            @error('use_end')
                                <div class="pristine-error text-help">{{ $message }}</div>
                            @enderror
                            {{-- </div> --}}
                        </div>

                        <div class="col-sm-3 d-flex align-items-center gap-2">

                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>

                            <a href="{{ route('electric.export', array_merge(['params' => $params])) }}"
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
                    @if (hasPermission('electric.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('electric.create', $params) }}">
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
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
            const dropEntity = document.getElementById('dropEntity');
            const dropEntityChoices = new Choices(dropEntity, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const invoice = document.getElementById('invoice');
            const invoiceChoices = new Choices(invoice, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    <script>
        // ✅ Flatpickr "Basic"
        const useStartDateInput = document.getElementById('use_start');
        if (useStartDateInput) {
            flatpickr(useStartDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: useStartDateInput.value || null
            });
        }

        const useEndDateInput = document.getElementById('use_end');
        if (useEndDateInput) {
            flatpickr(useEndDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: useEndDateInput.value || null
            });
        }
    </script>
@endsection
