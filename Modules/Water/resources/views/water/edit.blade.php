@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.water') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('menus.water') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('buttons.update') }}</li>
                    </ol>
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
                        action="{{ route('water.update', ['params' => $params, 'id' => $module->id]) }}" autocomplete="off">
                        @csrf
                        <div class="row">

                            {{-- SELECT ENTITY --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title.entity') }}</label>

                                    <select id="dropEntity" name="title_entity" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($waterEntity as $item)
                                            <option value="{{ $item->id }}"
                                                data-location_number="{{ $item->location_number }}"
                                                {{ $module->title_entity == $item->id ? 'selected' : '' }}>
                                                {{ $item->location_number }} - {{ $item->title_entity }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('title_entity')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- LOCATION NUMBER --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.location.number') }}</label>
                                    <input type="text" id="location_number_use" name="location_number_use"
                                        class="form-control" required value="{{ $module->location_number_use }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('location_number_use')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- INVOICE --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.invoice') }}</label>
                                    <input type="text" name="invoice" class="form-control"
                                        value="{{ $module->invoice }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('invoice')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.date') }}</label>
                                    <input type="text" id="date" name="date" class="form-control" required
                                        value="{{ $module->date }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('date')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- USE START --}}
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.use.start') }}</label>
                                    <input type="text" id="use_start" name="use_start" class="form-control" required
                                        value="{{ $module->use_start }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('use_start')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- USE END --}}
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.use.end') }}</label>
                                    <input type="text" id="use_end" name="use_end" class="form-control" required
                                        value="{{ $module->use_end }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('use_end')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- KILO --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.kilo') }}</label>
                                    <input type="text" name="kilo" class="form-control" value="{{ $module->kilo }}"
                                        required data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('kilo')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- COST TOTAL --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.cost.total') }}</label>
                                    <input type="text" name="cost_total" class="form-control"
                                        value="{{ $module->cost_total }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                    @error('cost_total')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- BUTTONS --}}
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit">{{ __('buttons.save') }}</button>
                                <a href="{{ route('water.index', $params) }}" class="btn btn-dark">
                                    {{ __('buttons.back') }}
                                </a>
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========== Choices.js ==========
            const entitySelect = new Choices('#dropEntity', {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: "{{ __('forms.search...') }}",
                searchPlaceholderValue: "ស្វែងរក...",
                shouldSort: false,
            });

            // ========== Autofill Location Number ==========
            const selectEntity = document.getElementById('dropEntity');
            const locationInput = document.getElementById('location_number_use');

            selectEntity.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                locationInput.value = option.dataset.location_number ?? '';
            });

            // ========== Flatpickr ==========
            flatpickr('#date', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y'
            });
            flatpickr('#use_start', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y'
            });
            flatpickr('#use_end', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y'
            });

            // ========== Pristine Validation ==========
            const form = document.getElementById('pristine-valid-example');
            const pristine = new Pristine(form, {
                classTo: 'form-group',
                errorTextParent: 'form-group',
                errorTextClass: 'pristine-error text-help'
            });

            form.addEventListener('submit', function(e) {
                if (!pristine.validate()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    </script>
@endsection
