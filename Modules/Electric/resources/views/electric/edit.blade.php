@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.electric') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ __('menus.electric') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.update') }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate method="POST"
                        action="{{ route('electric.update', ['params' => $params, 'id' => $module->id]) }}"
                        autocomplete="off">
                        @csrf

                        <div class="row">
                            {{-- ENTITY --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label font-size-13 text-muted">
                                        {{ __('forms.title.entity') }}
                                    </label>
                                    <select class="form-control" data-trigger id="dropEntity" name="title_entity" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($electricEntity as $item)
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
                                <div class="mb-3">
                                    <label class="form-label" for="location_number_use">
                                        {{ __('forms.location.number') }}
                                    </label>
                                    <input type="text" class="form-control" id="location_number_use"
                                        name="location_number_use" list="location_number_list"
                                        value="{{ old('location_number_use', $module->location_number_use) }}" required />
                                    <datalist id="location_number_list">
                                        @foreach ($electricEntity as $item)
                                            <option value="{{ $item->location_number }}">
                                                {{ $item->location_number }} - {{ $item->title_entity }}
                                            </option>
                                        @endforeach
                                    </datalist>

                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">{{ __('forms.date') }}</label>
                                    <input type="text" id="date" name="date" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}"
                                        value="{{ old('date', $module->date) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('date')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- USE START --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="use_start" class="form-label">{{ __('forms.use.start') }}</label>
                                    <input type="text" id="use_start" name="use_start" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}"
                                        value="{{ old('use_start', $module->use_start) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('use_start')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- USE END --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="use_end" class="form-label">{{ __('forms.use.end') }}</label>
                                    <input type="text" id="use_end" name="use_end" class="form-control"
                                        placeholder="{{ __('forms.select_date') }}"
                                        value="{{ old('use_end', $module->use_end) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('use_end')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- KILO --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="kilo">{{ __('forms.kilo') }}</label>
                                    <input type="number" class="form-control" name="kilo"
                                        value="{{ old('kilo', $module->kilo) }}" required />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>

                            {{-- REACTIVE ENERGY --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="reactive_energy">
                                        {{ __('forms.reactive.energy') }}
                                    </label>
                                    <input type="number" class="form-control" name="reactive_energy"
                                        value="{{ old('reactive_energy', $module->reactive_energy) }}" />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>

                            {{-- COST TOTAL --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="cost_total">{{ __('forms.cost.total') }}</label>
                                    <input type="number" class="form-control" name="cost_total"
                                        value="{{ old('cost_total', $module->cost_total) }}" required />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">
                                {{ __('buttons.save') }}
                            </button>
                            <a href="{{ route('electric.index', $params) }}" class="btn btn-dark">
                                {{ __('buttons.back') }}
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        const dateInput = document.getElementById('date');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }

        const useDateInput = document.getElementById('use_start');
        if (useDateInput) {
            flatpickr(useDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: useDateInput.value || null
            });
        }

        const endDateInput = document.getElementById('use_end');
        if (endDateInput) {
            flatpickr(endDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: endDateInput.value || null
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEntity = document.getElementById('dropEntity');
            const locationInput = document.getElementById('location_number_use');

            const entityChoices = new Choices(selectEntity, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: '{{ __('forms.search...') }}',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // auto-fill location_number when entity is selected
            selectEntity.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option && option.dataset.location_number) {
                    locationInput.value = option.dataset.location_number;
                } else {
                    locationInput.value = '';
                }
            });
        });
    </script>
@endsection
