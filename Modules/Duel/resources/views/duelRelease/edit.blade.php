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
                    {{ __('menus.duel.release') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.duel') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.release') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
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
                        {{-- ✅ UPDATE form --}}
                        <form id="pristine-valid-example"
                            action="{{ route('duelRelease.update', ['params' => $params, 'id' => $duelRelease->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="row">

                                        {{-- STOCK NUMBER --}}
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="stock_number" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.stock.number') }}
                                                </label>
                                                <select class="form-control" data-trigger id="dropStockNumber"
                                                    name="stock_number" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($duelEntry as $stock)
                                                        <option value="{{ $stock }}"
                                                            {{ old('stock_number', $duelRelease->stock_number) == $stock ? 'selected' : '' }}>
                                                            {{ $stock }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('stock_number')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- ITEM NAME --}}
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="item_name" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.item.name') }}
                                                </label>
                                                <select id="cboDuel" class="form-select" name="item_name" required>
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($duelType as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ $type->id == $duelRelease->item_name ? 'selected' : '' }}>
                                                            {{ $type->name_km }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('item_name')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- QUANTITY REQUEST --}}
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="quantity_request">{{ __('forms.quantity.request') }}</label>
                                                <input type="number" min="0" name="quantity_request" required
                                                    class="form-control"
                                                    value="{{ old('quantity_request', $duelRelease->quantity_request) }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('quantity_request')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- AGENCY --}}
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="agency" class="form-label font-size-13 text-muted">
                                                    {{ __('forms.agency') }}
                                                </label>
                                                <select class="form-control" data-trigger id="dropAgency" name="agency"
                                                    required
                                                    data-pristine-required-message="{{ __('messages.required') }}">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                    @foreach ($agency as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ old('agency', $duelRelease->agency) == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('agency')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- RECEIPT NUMBER --}}
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="receipt_number">{{ __('forms.receipt.number') }}</label>
                                                <input type="text" name="receipt_number" required class="form-control"
                                                    value="{{ old('receipt_number', $duelRelease->receipt_number) }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('receipt_number')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- USER REQUEST --}}
                                        <div class="col-xl-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="user_request">{{ __('forms.user.request') }}</label>
                                                <input type="text" name="user_request" required class="form-control"
                                                    value="{{ old('user_request', $duelRelease->user_request) }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('user_request')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- DATE RELEASE --}}
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="date_release" class="form-label">កាលបរិច្ឆេទ</label>
                                                <input type="text" id="datepicker-basic" name="date_release"
                                                    class="form-control" placeholder="{{ __('forms.select_date') }}"
                                                    required value="{{ old('date_release', $duelRelease->date_release) }}"
                                                    data-pristine-required-message="{{ __('messages.required') }}" />
                                                @error('date_release')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="row">

                                        {{-- REFER --}}
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="vRefer">{{ __('forms.refer') }}</label>
                                                <textarea name="refer" id="vRefer" rows="5" class="form-control" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">{{ old('refer', $duelRelease->refer) }}</textarea>
                                                @error('refer')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- NOTE --}}
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="vNote">{{ __('forms.note') }}</label>
                                                <textarea name="note" id="vNote" rows="5" class="form-control" required
                                                    data-pristine-required-message="{{ __('messages.required') }}">{{ old('note', $duelRelease->note) }}</textarea>
                                                @error('note')
                                                    <div class="pristine-error text-help">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('duelRelease.index', $params) }}">{{ __('buttons.back') }}</a>
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
            const dropStockNumber = document.getElementById('dropStockNumber');
            const dropStockNumberChoice = new Choices(dropStockNumber, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const dropAgency = document.getElementById('dropAgency');
            const dropAgencyChoice = new Choices(dropAgency, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cboDuel = document.getElementById('cboDuel');
            const cboDuelChoice = new Choices(cboDuel, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
    <script>
        $('#dropStockNumber').change(function() {
            var id = $(this).val();
            $.ajax({
                url: '{{ route('duelRelease.by.stock_number', ['params' => $params]) }}',
                type: 'get',
                data: {
                    stock_number: id
                },
                success: function(data) {
                    if (programSubChoices) {
                        programSubChoices.destroy();
                    }
                    $('#cboDuel').html(data);
                    programSubChoices = new Choices('#cboDuel', {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "ស្វែងរក..."
                    });
                }
            });
        });

        // ✅ On edit: auto trigger change once if value exists
        document.addEventListener('DOMContentLoaded', function() {
            const stockSelect = document.getElementById('dropStockNumber');
            if (stockSelect && stockSelect.value) {
                $('#dropStockNumber').trigger('change');
            }
        });
    </script>
@endsection
