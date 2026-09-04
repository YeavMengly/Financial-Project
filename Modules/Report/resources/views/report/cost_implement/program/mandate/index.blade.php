@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e9ecef;
        }

        #costimplementprogramMandate-table {
            width: 100% !important;
            margin: 0 !important;
            table-layout: auto !important;
            border-collapse: collapse !important;
        }

        #costimplementprogramMandate-table th,
        #costimplementprogramMandate-table td {
            white-space: nowrap !important;
            vertical-align: middle !important;
            padding: 8px 12px !important;
            font-size: 15px;
        }

        .total-summary-row th {
            font-weight: bold !important;
        }

        .dataTables_wrapper .row {
            margin: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.cost.implement.program') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div></div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Filter Year -->
                            <select id="yearFilter" name="yearFilter" class="form-select">
                                @foreach ($ministries->unique('year') as $ministry)
                                    <option value="{{ $ministry->year }}" data-ministry-id="{{ $ministry->id }}"
                                        {{ $ministry->year == $selectedYear ? 'selected' : '' }}>
                                        {{ __('menus.annual.data') }} {{ $ministry->year }}
                                    </option>
                                @endforeach
                            </select>

                            @include('report::report.cost_implement.program.mandate.dropdown')
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive-wrapper">
                        <table id="costimplementprogramMandate-table" class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">កម្មវិធី</th>
                                    <th rowspan="2" class="text-center align-middle">ច្បាប់ហិរញ្ញវត្ថុ</th>
                                    <th rowspan="2" class="text-center align-middle">ឥណទានថ្មី</th>
                                    <th colspan="7" class="text-center">ទូទាត់ចំណាយ</th>
                                </tr>
                                <tr>
                                    <th class="text-center">ដើមគ្រា</th>
                                    <th class="text-center">អនុវត្ត</th>
                                    <th class="text-center">ភាគរយ</th>
                                    <th class="text-center">បូកយោង</th>
                                    <th class="text-center">ភាគរយ</th>
                                    <th class="text-center">នៅសល់</th>
                                    <th class="text-center">ភាគរយ</th>
                                </tr>
                                <tr>
                                    <th class="text-center">១</th>
                                    <th class="text-center">២</th>
                                    <th class="text-center">៣</th>
                                    <th class="text-center">៤</th>
                                    <th class="text-center">៥</th>
                                    <th class="text-center">៦=៥/២</th>
                                    <th class="text-center">៧=៤+៥</th>
                                    <th class="text-center">៨=៧/២</th>
                                    <th class="text-center">៩=២-៧</th>
                                    <th class="text-center">១០=៩/២</th>
                                </tr>

                                <tr class="total-summary-row">
                                    <th class="text-center" id="total_label">សរុប</th>
                                    <th class="text-end" id="total_fin_law">0,00</th>
                                    <th class="text-end" id="total_new_credit">0,00</th>
                                    <th class="text-end" id="total_early_balance">0,00</th>
                                    <th class="text-end" id="total_apply">0,00</th>
                                    <th class="text-end" id="total_apply_percent">0,00%</th>
                                    <th class="text-end" id="total_credit">0,00</th>
                                    <th class="text-end" id="total_credit_percent">0,00%</th>
                                    <th class="text-end" id="total_deadline_balance">0,00</th>
                                    <th class="text-end" id="total_remaining_percent">0,00%</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
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

    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {

            function formatMoney(value) {
                return Number(value || 0).toLocaleString('en-US', {
                    // minimumFractionDigits: 3,
                    // maximumFractionDigits: 3
                }) + ' ៛';
            }

            function formatPercent(value) {
                return Number(value || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '%';
            }

            // Update Summary Header on XHR load
            $('#costimplementprogramMandate-table').on('xhr.dt', function(e, settings, json) {
                $('#total_label').text('សរុប');
                if (!json || !json.totals) return;

                const t = json.totals;

                $('#total_fin_law').text(formatMoney(t.fin_law));
                $('#total_new_credit').text(formatMoney(t.new_credit_status));
                $('#total_early_balance').text(formatMoney(t.early_balance));
                $('#total_apply').text(formatMoney(t.apply));
                $('#total_apply_percent').text(formatPercent(t.apply_percent));
                $('#total_credit').text(formatMoney(t.credit));
                $('#total_credit_percent').text(formatPercent(t.credit_percent));
                $('#total_deadline_balance').text(formatMoney(t.deadline_balance));
                $('#total_remaining_percent').text(formatPercent(t.remaining_percent));

                // Recalculate columns width after data loads
                setTimeout(function() {
                    $('#costimplementprogramMandate-table').DataTable().columns.adjust();
                }, 50);
            });

            // Trigger table reload on year/ministry filter change
            $(document).on('change', '#yearFilter, #ministryFilter, select[name="ministry_id"], #ministry_id',
                function() {
                    $('#costimplementprogramMandate-table').DataTable().ajax.reload();
                });

            // Column visibility toggles
            $(document).on('click', '.dropdown-menu', function(e) {
                e.stopPropagation();
            });

            $(document).on('change', '.toggle-column-program', function() {
                const table = $('#costimplementprogramMandate-table').DataTable();
                const columnIndex = $(this).data('column');
                table.column(columnIndex).visible($(this).is(':checked'));
                table.columns.adjust();
            });

            $(document).on('click', '#resetProgramColumns', function() {
                const table = $('#costimplementprogramMandate-table').DataTable();
                $('.toggle-column-program').prop('checked', true);
                table.columns().visible(true);
                table.columns.adjust();
            });

        });
    </script>
@endsection