@extends('layouts.master')
@section("css")
    <link href="{{ asset("assets/libs/sweetalert2/sweetalert2.min.css") }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css") }}"
          rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.document") }}</h4>

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
                            <label class="visually-hidden" for="cboCategory">ជ្រើសរើស ប្រភេទ</label>
                            <select class="form-select select2" id="cboCategory" name="cboCategory">
                                <option value="">ជ្រើសរើស ប្រភេទ</option>
                                @foreach($category as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboCategorySub">ជ្រើសរើស អនុប្រភេទ</label>
                            <select class="form-select select2" id="cboCategorySub" name="cboCategorySub">
                                <option value="">ជ្រើសរើស អនុប្រភេទ</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="visually-hidden" for="cboStatus">ជ្រើសរើស ស្ថានភាព</label>
                            <select class="form-select" id="cboStatus" name="cboStatus">
                                <option value="">ជ្រើសរើស ស្ថានភាព</option>
                                <option value="1">សកម្ម</option>
                                <option value="2">លុប</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">{{ __("buttons.search") }}</button>
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
                    @if(hasPermission('document.create'))
                        <div class="col-sm">
                            <div class="mb-4">
                                <a class="btn btn-light waves-effect waves-light" href="{{ route("document.create") }}"><i
                                        class="bx bx-plus me-1"></i> {{ __("buttons.create") }}</a>
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
@section("script")
    <script src="{{ asset("assets/libs/sweetalert2/sweetalert2.min.js") }}"></script>
    <script src="{{ asset("assets/libs/datatables.net/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js") }}"></script>
    <script src="{{ asset("assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js") }}"></script>
    <script src="{{ asset("assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js") }}"></script>
    <script>
        function confirm(url, condi) {
            if (condi == 1) {
                Swal.fire({
                    title: '{{ __("messages.confirm.delete") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e7515a',
                    cancelButtonColor: '#e2a03f',
                    confirmButtonText: '{{ __("buttons.delete") }}!',
                    cancelButtonText: '{{ __("buttons.back") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = url;
                    }
                });
            } else {
                Swal.fire({
                    title: '{{ __("messages.confirm.back") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2ab57d',
                    cancelButtonColor: '#e2a03f',
                    confirmButtonText: '{{ __("buttons.get.back") }}!',
                    cancelButtonText: '{{ __("buttons.back") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = url;
                    }
                });
            }
        }
    </script>
    {!! $dataTable->scripts() !!}
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
@endsection
