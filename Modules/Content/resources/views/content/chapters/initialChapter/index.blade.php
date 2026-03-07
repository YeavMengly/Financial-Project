@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);"><span>{{ __('menus.content') }}</span></a>
                            </li>
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
                    <form id="filter" method="GET" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0">
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboTodo">ជ្រើសរើស កំណត់ចំណាំ</label>
                            <select class="form-control" id="cboTodo" name="cboTodo">
                                <option value="1">ជ្រើសរើស កំណត់ចំណាំ</option>
                                <option value="2" selected>កំពុងធ្វើ</option>
                                <option value="3">បានបញ្ចប់</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboStatus">ជ្រើសរើស ស្ថានភាព</label>
                            <select class="form-select" id="cboStatus" name="cboStatus">
                                <option value="1">ជ្រើសរើស ស្ថានភាព</option>
                                <option value="2" selected>សកម្ម</option>
                                <option value="3">លុប</option>
                            </select>
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
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-bordered dt-responsive  nowrap w-100']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    {!! $dataTable->scripts() !!}
@endsection
