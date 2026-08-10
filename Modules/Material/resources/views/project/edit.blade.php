@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.project') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.project') }}</a></li>
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
                        <form id="pristine-valid-example"
                            action="{{ route('project.update', ['params' => $params, 'id' => $module->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                        <input type="text" name="stock_number" value="{{ $module->stock_number }}"
                                            class="form-control" tabindex="1"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                        <input type="text" name="stock_name" value="{{ $module->stock_name }}"
                                            class="form-control" tabindex="2"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="company_name">{{ __('forms.company.name') }}</label>
                                        <input type="text" name="company_name" value="{{ $module->company_name }}"
                                            class="form-control" tabindex="3"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="warehouse_voucher">{{ __('forms.warehouse.voucher') }}</label>
                                        <input type="text" name="warehouse_voucher"
                                            value="{{ $module->warehouse_voucher }}" class="form-control" tabindex="4"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                        <input type="text" name="user_entry" value="{{ $module->user_entry }}"
                                            class="form-control" tabindex="5"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_receiver">{{ __('forms.receiver') }}</label>
                                        <input type="text" name="user_receiver" value="{{ $module->user_receiver }}"
                                            class="form-control" tabindex="6"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date" class="form-label">{{ __('forms.date') }}</label>
                                        <input type="text" id="date" name="date" class="form-control"
                                            tabindex="7" placeholder="{{ __('forms.select_date') }}" value="{{ $module->date }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <!-- Title Section -->
                                {{-- <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="titleInput"
                                                class="form-label mb-0">{{ __('forms.title') }}</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipTitleInput" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipTitleInput" style="cursor: pointer;">
                                                    រំលង / មិនភ្ជាប់
                                                </label>
                                            </div>
                                        </div>

                                        <input type="text" id="titleInput" name="title" value="{{ $module->title }}" class="form-control"
                                            tabindex="7"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('title')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="fileInput"
                                                class="form-label mb-0">{{ __('forms.file.type') }}</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipFileInput" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipFileInput" style="cursor: pointer;">
                                                    រំលង / មិនភ្ជាប់
                                                </label>
                                            </div>
                                        </div>

                                        <input type="file" id="fileInput" name="file[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple data-max-size="5"
                                            data-allowed-extensions="pdf,doc,docx"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX (Max: 5MB per
                                            file)</small>

                                        @error('file.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
                            </div>

                            <div class="row">
                                <!-- Refer Field (Required, No Skip Option) -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vRefer" class="form-label">{{ __('forms.refer') }}</label>
                                        <textarea name="refer" id="vRefer" rows="5" class="form-control" required tabindex="9"
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ $module->refer }}</textarea>

                                        @error('refer')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Note Field (Required, No Skip Option) -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vNote" class="form-label">{{ __('forms.note') }}</label>
                                        <textarea name="note" id="vNote" rows="5" class="form-control" required tabindex="10"
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ $module->note }}</textarea>

                                        @error('note')
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
                                    href="{{ route('project.index', $params) }}">{{ __('buttons.back') }}</a>

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

        // ✅ Flatpickr "Basic"
        const dateInput = document.getElementById('date');
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
@endsection
