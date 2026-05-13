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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.cost.implement.program') }}</h4>

                <div class="page-title-right">

                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">

        <div class="col-12">

            <div class="card shadow-sm border-0">

                <!-- HEADER -->
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <!-- TITLE -->
                        <div>
                        </div>
                        <!-- RIGHT ACTION -->
                        <div class="d-flex align-items-center gap-2">

                            <!-- Filter Year -->
                            <select id="ministryFilter" class="form-select">
                                @foreach ($ministries as $ministry)
                                    <option value="{{ $ministry->id }}">
                                        {{ __('menus.annual.data') }} {{ $ministry->year }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- SEARCH -->
                            {{-- <div class="position-relative">

                                <input type="text" id="customSearch-program" class="form-control ps-5"
                                    placeholder="{{ __('forms.search...') }}" style="min-width:250px;">

                                <i class="bx bx-search position-absolute" style="top:10px; left:15px;"></i>

                            </div> --}}

                            @include('report::report.cost_implement.program.dropdown')

                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="card-body">
                    <!-- TABLE -->
                    <div class="table-responsive">

                        {!! $dataTable->table([
                            'class' => 'table table-bordered table-hover align-middle nowrap w-100',
                        ]) !!}

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
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {

            let table = $('#costimplementprogram-table').DataTable();

            /**
             * GLOBAL SEARCH (optional if you have input)
             */
            $('#customSearch-program').on('keyup', function() {
                table.search(this.value).draw();
            });

            /**
             * TOGGLE COLUMN (PROGRAM TABLE ONLY)
             */
            $('.toggle-column-program').on('change', function() {

                let columnIndex = $(this).data('column');
                let column = table.column(columnIndex);

                let isVisible = column.visible();

                column.visible(!isVisible);

                /**
                 * UNIQUE STORAGE KEY
                 */
                let key = 'costimplementprogram_dt_col_' + columnIndex;
                localStorage.setItem(key, !isVisible);
            });

            /**
             * RESTORE STATE
             */
            $('.toggle-column-program').each(function() {

                let columnIndex = $(this).data('column');

                let key = 'costimplementprogram_dt_col_' + columnIndex;

                let saved = localStorage.getItem(key);

                if (saved !== null) {

                    let isVisible = (saved === 'true');

                    table.column(columnIndex).visible(isVisible);

                    $(this).prop('checked', isVisible);
                }
            });

            /**
             * RESET BUTTON
             */
            $('#resetProgramColumns').on('click', function() {

                $('.toggle-column-program').each(function() {

                    let columnIndex = $(this).data('column');

                    table.column(columnIndex).visible(true);

                    $(this).prop('checked', true);

                    localStorage.removeItem(
                        'costimplementprogram_dt_col_' + columnIndex
                    );
                });

            });

        });
    </script>

    <script>
        $('#yearFilter, #ministryFilter').on('change keyup', function() {
            $('#costimplementprogram-table').DataTable().ajax.reload();
        });
    </script>
@endsection
