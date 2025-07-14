@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
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
                            <label class="visually-hidden" for="cbochapterNum">{{ __('menus.account') }}</label>
                            <input type="text" class="form-control" name="chapterNum" required
                                data-pristine-required-message="{{ __('messages.required') }}"
                                placeholder="{{ __('menus.sub.account') }}" />
                        </div>
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboTxtChapter">{{ __('menus.account') }}</label>
                            <input type="text" class="form-control" name="txtChapter" required
                                data-pristine-required-message="{{ __('messages.required') }}"
                                placeholder="{{ __('menus.program') }}" />
                        </div>
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboTxtChapter">{{ __('menus.account') }}</label>
                            <input type="text" class="form-control" name="txtChapter" required
                                data-pristine-required-message="{{ __('messages.required') }}"
                                placeholder="{{ __('menus.description') }}" />
                        </div>

                        <div class="col-sm-3 d-flex">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>

                        </div>
                        @if (hasPermission('beginCredit.create'))
                            <div class="col-sm-3">
                                {{-- <div class="mb-4"> --}}
                                <a class="btn btn-light waves-effect waves-light" href=""><i
                                        class="bx bx-download me-1"></i>
                                    {{ __('buttons.download') }}</a>
                                {{-- </div> --}}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (hasPermission('beginCredit.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light"
                                    href="{{ route('beginCredit.create') }}"><i class="bx bx-plus me-1"></i>
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
@endsection
