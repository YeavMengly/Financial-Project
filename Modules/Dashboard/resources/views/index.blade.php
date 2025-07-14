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
            </div>
        </div>
    </div>
@endsection
