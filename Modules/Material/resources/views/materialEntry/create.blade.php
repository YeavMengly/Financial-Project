@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.material') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.entry') }}</a></li>
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

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="company_name">{{ __('forms.company.name') }}</label>
                                        <input type="text" name="company_name" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('company_name')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                 <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                        <input type="text" name="stock_number" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('stock_number')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                        <input type="text" name="stock_name" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('stock_name')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                        <input type="text" name="user_entry" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('user_entry')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="p_code">{{ __('forms.pro.code') }}</label>
                                        <input type="text" name="p_code" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('p_code')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="p_name">{{ __('forms.pro.name') }}</label>
                                        <input type="text" name="p_name" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('p_name')
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

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="title">{{ __('forms.title') }}</label>
                                        <input type="text" name="title" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('title')
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
                                        <input type="number" min="0" name="quantity" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('quantity')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="price">{{ __('forms.price') }}</label>
                                        <input type="number" min="0" name="price" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('price')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="source">{{ __('forms.source') }}</label>
                                        <input type="text" name="source" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('source')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_entry" class="form-label">កាលបរិច្ឆេទ</label>
                                        <input type="text" id="datepicker-basic" name="date_entry"
                                            class="form-control" placeholder="{{ __('forms.select_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('date_entry')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="file">{{ __('forms.file') }}</label>
                                        <input type="file" id="fileInput" name="file[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX</small>
                                        @error('file')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="vRefer">{{ __('forms.refer') }}</label>
                                    <textarea name="refer" id="vRefer" rows="5" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('txtRefer')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="vNote">{{ __('forms.note') }}</label>
                                    <textarea name="note" id="vNote" rows="5" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('txtNote')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('duelEntry.index', $params) }}">{{ __('buttons.back') }}</a>

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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
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
    </script>
@endsection
