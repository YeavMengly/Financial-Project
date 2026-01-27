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
                        <li class="breadcrumb-item">{{ __('menus.program') }} <span>{{ $program->no }}</span> </li>
                        <li class="breadcrumb-item active">{{ __('menus.program.sub') }}<span>{{ $programSub->no }}</span>
                        </li>
                        <li class="breadcrumb-item active">{{ __('menus.cluster') }}</li>
                    </ol>
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"><span>{{ $data->year }}</span></a>
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
                {{-- <div class="card-body">
                    <form class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0" id="filter" method="GET">

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="no">{{ __('menus.program.sub') }}</label>
                            <select class="form-control" name="no" id="no">
                                <option value="">{{ __('forms.search...') }}</option>

                                @foreach ($programSub as $dd)
                                    <option value="{{ $dd->id }}" {{ request('no') == $dd->no ? 'selected' : '' }}>
                                        {{ $dd->no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="decription">{{ __('menus.title') }}</label>
                            <select class="form-control" name="decription" id="decription">
                                <option value="">{{ __('forms.search...') }}</option>
                                @foreach ($programSub as $dp)
                                    <option value="{{ $dp->id }}"
                                        {{ request('decription') == $dp->decription ? 'selected' : '' }}>
                                        {{ $dp->decription }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger ms-2" style="width: 80px;">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                            </a>
                        </div>
                    </form>

                </div> --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (hasPermission('cluster.create'))
                        <div class="col-sm">
                            <div class="mb-4 d-flex flex-wrap gap-2">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('cluster.create', ['params' => $params, 'pId' => $pId, 'pSubId' => $pSubId]) }}"><i
                                        class="bx bx-plus me-1"></i>
                                    {{ __('buttons.create') }}</a>

                                <a class="btn btn-dark"
                                    href="{{ route('program.sub.index', ['params' => $params, 'pId' => $pId]) }}">{{ __('buttons.back') }}</a>
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
@endsection
