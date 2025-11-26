@extends('layouts.master')

@section('css')
    <style>
        /* Stylish table wrapper */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
            font-family: 'Khmer OS Battambang', sans-serif;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #343a40;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.dashboard') }}</h4>

                <div class="page-title-right">

                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>{{ __('tables.th.sub.account') }}</th>
                            <th>{{ __('tables.th.clusters') }}</th>
                            <th>{{ __('tables.th.fin_law') }}</th>
                            <th>{{ __('tables.th.current_loan') }}</th>
                            <th>{{ __('tables.th.new_credit_status') }}</th>
                            <th>{{ __('tables.th.early_balance') }}</th>
                            <th>{{ __('tables.th.apply') }}</th>
                            <th>{{ __('tables.th.deadline_balance') }}</th>
                            <th>{{ __('tables.th.credit') }}</th>
                            <th>{{ __('tables.th.law_average') }}</th>
                            <th>{{ __('tables.th.law_correction') }}</th>

                        </tr>
                    </thead>
                    {{-- <thead class="header-border">
                        <tr>
                        
                            <th rowspan="3">ជំពូក</th>
                            <th rowspan="3">គណនី</th>
                            <th rowspan="3">អនុគណនី</th>
                            <th rowspan="3">កូដកម្មវិធី</th>
                            <th rowspan="3" style="text-align: start; width: 350px;">ចំណាត់ថ្នាក់</th>
                            <th rowspan="3">ច្បាប់ហិ.វ</th>
                            <th rowspan="3">ឥណទានបច្ចុប្បន្ន</th>
                            <th colspan="5">ចលនាឥណទាន</th>
                            <th rowspan="3">វិចារណកម្ម</th>
                            <th rowspan="3">ស្ថានភាពឥណទានថ្មី</th>
                            <th rowspan="3">ស.ម.ដើមគ្រា</th>
                            <th rowspan="3">អនុវត្ត</th>
                            <th rowspan="3">ស.ម.ចុងគ្រា</th>
                            <th rowspan="3">ឥ.សល់</th>
                            <th colspan="2" rowspan="2">%ប្រៀបធៀប</th>
                        </tr>
                        <tr>
                            <th colspan="4">កើន</th>
                            <th rowspan="2">ថយ</th>
                        </tr>
                        <tr>
                            <th>កើនផ្ទៃក្នុង</th>
                            <th class="rotate-text">មិនបានគ្រោងទុក</th>
                            <th>បំពេញបន្ថែម</th>
                            <th>សរុប</th>
                            <th>%ច្បាប់</th>
                            <th>%ច្បាប់កែតម្រូវ</th>
                        </tr>
                    </thead> --}}

                    @php
                        $report = DB::table('begin_vouchers')->get();
                        $total_fin_law = $report->sum('fin_law');
                        $total_apply = $report->sum('apply');
                    @endphp

                    <tbody>
                        @foreach ($report as $row)
                            <tr>
                                <td>{{ $row->account_sub_id }}</td>
                                <td>{{ $row->no }}</td>
                                <td>{{ number_format($row->fin_law) }}</td>
                                <td>{{ number_format($row->current_loan) }}</td>
                                <td>{{ number_format($row->new_credit_status) }}</td>
                                <td>{{ number_format($row->early_balance) }}</td>
                                <td>{{ number_format($row->apply) }}</td>
                                <td>{{ number_format($row->deadline_balance) }}</td>
                                <td>{{ number_format($row->credit) }}</td>
                                <td>{{ number_format($row->law_average, 2) }}</td>
                                <td>{{ number_format($row->law_correction, 2) }}</td>
                            </tr>
                        @endforeach

                        {{-- Total Row --}}
                        <tr style="background:#d4edda; font-weight:bold; border-top:2px solid #28a745;">
                            <td colspan="2" class="text-end" style="padding-right:20px;">
                                សរុប (Total)
                            </td>

                            <td style="color:#155724;">
                                {{ number_format($total_fin_law) }}
                            </td>

                            <td style="color:#155724;">
                                {{ number_format($total_apply) }}
                            </td>

                            <td colspan="7"></td>
                        </tr>
                    </tbody>

                </table>



                {{-- <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('tables.th.no') }}</th>
                                    <th>{{ __('tables.th.order') }}</th>
                                    <th>{{ __('tables.th.category') }}</th>
                                    <th>{{ __('tables.th.document.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index=1; @endphp
                                @foreach ($report as $row)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $row->order }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            @php
                                                $total_doc = DB::table('documents')
                                                    ->where('cate_id', $row->id)
                                                    ->count();
                                                echo $total_doc;
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <table class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                             
                                    <th>{{ __('tables.th.financeLaw') }}</th>
                                    <th>{{ __('tables.th.currentCredit') }}</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @php $index=1; @endphp
                                @foreach ($chapter as $row)
                                    <tr>
                                   
                                        <td>{{ $row->total_fin_law }}</td>
                                        <td>{{ $row->total_current_loan }}</td>
                                        <td>
                                        
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> --}}

                {{-- <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.chapter') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('chapters')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.account') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('accounts')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.sub.account') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('account_subs')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.program') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('programs')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.sub.program') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('program_subs')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.clusters') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('agencies')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('tables.th.agency') }}</h5>
                                    <p class="card-text fs-4 fw-bold">
                                        {{ DB::table('agencies')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('tables.th.users') }}</h5>
                            <p class="card-text fs-4 fw-bold">
                                {{ DB::table('users')->where('id', '!=', 1)->count() }}
                            </p>

                            <hr>
                            <ul class="list-unstyled small">
                                @foreach (DB::table('users')->limit(5)->pluck('fullname') as $name)
                                    <li>{{ $name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
@endsection
