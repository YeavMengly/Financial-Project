@extends('layouts.master')
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

                <div class="card-body">

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
                    </div>
                </div>

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
