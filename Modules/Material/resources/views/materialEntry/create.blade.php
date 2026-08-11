@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.material.entry') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.material') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.entry') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example" action="{{ route('materialEntry.store', $params) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProject" class="form-label font-size-13 text-muted">
                                            ជ្រើសរើស{{ __('forms.project') }}
                                        </label>
                                        <select class="form-select" id="cboProject" name="cboProject" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($project as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->stock_number }}-
                                                    {{ $p->stock_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('cboProject')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="p_name">{{ __('forms.item.name') }}</label>
                                        <input type="text" name="p_name" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('p_name')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="unit" class="form-label font-size-13 text-muted">
                                            {{ __('forms.unit') }}
                                        </label>
                                        <select class="form-control" data-trigger id="dropUnit" name="unit" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($unitType as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="quantity">{{ __('forms.quantity') }}</label>
                                        <input type="number" min="0" name="qty" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('qty')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="price">{{ __('forms.price') }}ឯកតា</label>
                                        <input type="number" min="0" name="price" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('price')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="source">{{ __('forms.source') }}</label>
                                        <input type="text" name="source" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('source')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="p_year">{{ __('forms.pro.year') }}</label>
                                        <input type="text" name="p_year" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('p_year')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('materialEntry.index', $params) }}">{{ __('buttons.back') }}</a>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#vNote').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });

        $(document).ready(function() {
            $('#vRefer').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboSubDepartSelect = document.getElementById('cboAgency');
            const cboSubDepartChoice = new Choices(cboSubDepartSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        // ✅ Flatpickr "Basic"
        const dateInput = document.getElementById('datepicker-basic');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropDuel = document.getElementById('dropDuel');
            const dropDuelChoice = new Choices(dropDuel, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const dropUnit = document.getElementById('dropUnit');
            const dropUnitChoice = new Choices(dropUnit, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProject');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
@endsection
