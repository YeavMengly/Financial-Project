@extends('layouts.master')
@section("css")
    <link href="{{ asset("assets/libs/sweetalert2/sweetalert2.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css") }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.setting.log") }}</h4>

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
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-bordered dt-responsive  nowrap w-100']) !!}
                        @include("setting::system.detail")
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
    {!! $dataTable->scripts() !!}
    <script>
        $('body').on('click','.show_properties',function(){
            var log_id = $(this).attr('log_id');
            $.ajax({
                type: "get",
                url: '{{ route('system.detail') }}',
                data: {
                    log_id:log_id
                },
                dataType: "json",
                success: function (response) {
                    $('#modal_show_property').modal('show');
                    $('.log_lists').html(response.trs);
                    $('.activity_log').text(response.event);
                }
            });
        });
    </script>
@endsection
