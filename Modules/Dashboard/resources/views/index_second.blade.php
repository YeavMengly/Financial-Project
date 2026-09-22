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
    
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.dashboard.second') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.dashboard.second') }}</a></li>
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
                        <div class="col-sm-3">
                            <label class="form-label font-size-13 text-muted" for="cboTodo">កំណត់ចំណាំ</label>
                            <select class="form-select" id="cboTodo" name="cboTodo" onchange="this.form.submit()">
                                <option value="2" {{ request('cboTodo') == 2 ? 'selected' : '' }}>កំពុងធ្វើ</option>
                                <option value="3" {{ request('cboTodo') == 3 ? 'selected' : '' }}>បានបញ្ចប់</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection