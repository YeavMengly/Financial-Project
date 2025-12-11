@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.electric.entity') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.electric.entity') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" novalidate method="POST"
                        action="{{ route('electricEntity.update', ['params' => $params, 'id' => $module->id]) }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title.entity') }}</label>
                                    <input type="text" class="form-control" name="txtTitleEntity"
                                        value="{{ $module->title_entity }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('txtTitleEntity')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.location.number') }}</label>
                                    <input type="text" class="form-control" name="txtLocationNmber"
                                        value="{{ $module->location_number }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('txtLocationNmber')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>
                                        {{ __('forms.province') }}
                                    </label>
                                    <select class="form-control" data-trigger id="province" name="province" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($provinces as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $module->province_id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('electricEntity.index', $params) }}">{{ __('buttons.back') }}</a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropProvince = document.getElementById('province');
            const dropProvinceChoice = new Choices(dropProvince, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script> --}}
@endsection
