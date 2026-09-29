@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        @php
            $info = $mission->first();
        @endphp

        {{-- Mission Information --}}
        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    ព័ត៌មានបេសកកម្ម
                </h5>

                <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm">
                    <i class="fa fa-arrow-left me-1"></i>
                    {{ __('buttons.back') }}
                </a>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Legal Number --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.legal.id') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->legal_number ?? '-' }}
                        </div>
                    </div>

                    {{-- Legal Date --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.date.legal') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->legal_date ? \Carbon\Carbon::parse($info->legal_date)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    {{-- Province --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.province') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->province_name ?? '-' }}
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.start.date') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->start_date ? \Carbon\Carbon::parse($info->start_date)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.end.date') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->end_date ? \Carbon\Carbon::parse($info->end_date)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    {{-- Days --}}
                    <div class="col-md-2 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.days.count') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->days_count ?? 0 }}
                        </div>
                    </div>

                    {{-- Nights --}}
                    <div class="col-md-2 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.nights.count') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->nights_count ?? 0 }}
                        </div>
                    </div>

                    {{-- Mission Type --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.mission.type') }}
                        </label>

                        <div class="mt-1">
                            @if ($info->mission_type === 'local')
                                ក្នុងប្រទេស
                            @elseif ($info->mission_type === 'abroad')
                                ក្រៅប្រទេស
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-8 mb-3">
                        <label class="fw-bold">
                            {{ __('tables.th.mission.description') }}
                        </label>

                        <div class="mt-1">
                            {{ $info->description ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>
        </div>


        {{-- Mission Employees --}}
        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    {{ __('forms.mission.employee') }}
                </h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th class="text-center">ល.រ</th>
                                <th>ឈ្មោះខ្មែរ</th>
                                <th>ឈ្មោះឡាតាំង</th>
                                <th>តួនាទី</th>
                                <th>កម្រិត</th>
                                <th class="text-end">ប្រាក់ធ្វើដំណើរ</th>
                                <th class="text-end">ប្រាក់ហោប៉ៅ</th>
                                <th class="text-end">សរុបប្រាក់ហោប៉ៅ</th>
                                <th class="text-end">ប្រាក់ហូបចុក</th>
                                <th class="text-end">សរុបប្រាក់ហូបចុក</th>
                                <th class="text-end">ប្រាក់ស្នាក់នៅ</th>
                                <th class="text-end">សរុបប្រាក់ស្នាក់នៅ</th>
                                <th class="text-end">សរុប</th>
                                <th class="text-center"> {{ __('forms.assign') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($mission as $index => $item)
                                {{-- Skip row if there is no employee --}}
                                @if ($item->employee_id)
                                    <tr>

                                        <td class="text-center">
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            {{ $item->name_kh ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->name_latin ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->level_name ?? '-' }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->travel_allowance ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->pocket_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->total_pocket_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->meal_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->total_meal_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->accommodation_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->total_accommodation_money ?? 0) }} ៛
                                        </td>

                                        <td class="text-end fw-bold">
                                            {{ number_format($item->total ?? 0) }} ៛
                                        </td>

                                        <td class="text-center">

                                            @if ($item->assign_budget)
                                                <span class="badge bg-success">
                                                    អ្នកទទួល
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    ទេ
                                                </span>
                                            @endif

                                        </td>

                                    </tr>
                                @endif

                            @empty

                                <tr>
                                    <td colspan="14" class="text-center text-muted py-3">
                                        {{ __('forms.mission.employee') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                        <tfoot>

                            <tr class="table-light fw-bold">

                                <td colspan="12" class="text-end">
                                    សរុបរួម
                                </td>

                                <td class="text-end">
                                    {{ number_format($mission->sum('total')) }} ៛
                                </td>

                                <td></td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
