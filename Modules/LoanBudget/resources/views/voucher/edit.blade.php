@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- preloader css -->
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.voucher') }}</h4>
                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.voucher') }}</a></li>
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
                            action="{{ route('voucher.update', ['params' => $params, 'id' => $module->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboAgency" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboAgency" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($agency as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('cboAgency', $module->agency_id == $item->id ? 'selected' : '') }}>
                                                    {{ $item->no }} -
                                                    {{ $item->name ?? 'មិនមានទិន្ន័យ' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboAgency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" id="cboSubAccount" name="cboSubAccount" required>
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $as)
                                                <option value="{{ $as->no }}" {{-- ✅ use `id` not `subAccountNumber` --}}
                                                    {{ old('cboSubAccount', $module->account_sub_id) == $as->no ? 'selected' : '' }}>
                                                    {{ $as->no }} - {{ $as->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subAccountNumber')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="programInput">{{ __('forms.program.code') }}</label>
                                        <input type="number" min="0" name="no" placeholder="xxxxxxx" required
                                            class="form-control" value="{{ old('no', $module->no) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('program')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="internal">{{ __('forms.internal') }}</label>
                                        <input type="number" min="0" name="internal_increase" id="internal_increase"
                                            class="form-control"
                                            value="{{ old('internal_increase', $module->internal_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('internal_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="unexpected">{{ __('forms.unexpected') }}</label>
                                        <input type="number" min="0" name="unexpected_increase"
                                            id="unexpected_increase" class="form-control"
                                            value="{{ old('unexpected_increase', $module->unexpected_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('unexpected_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="additional">{{ __('forms.additional') }}</label>
                                        <input type="number" min="0" name="additional_increase"
                                            id="additional_increase" class="form-control"
                                            value="{{ old('additional_increase', $module->additional_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('additional_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="decrease">{{ __('forms.decrease') }}</label>
                                        <input type="number" min="0" name="decrease" id="decrease"
                                            class="form-control" value="{{ old('decrease', $module->decrease) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('decrease')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="editorial">{{ __('forms.editorial') }}</label>
                                        <input type="number" min="0" name="editorial" id="editorial"
                                            class="form-control" value="{{ old('editorial', $module->editorial) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('editorial')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="vDescription">{{ __('forms.document.description') }}</label>
                                        <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                       {{ old('txtDescription', $module->txtDescription) }}</textarea>
                                        @error('txtDescription')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('voucher.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                if (!pristine.validate()) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subAccountSelect = document.getElementById('cboSubAccount');
            const programInput = document.getElementById('programInput');
            const budgetInput = document.getElementById('budget');
            const programHidden = document.getElementById('programHiddenInput');

            // ✅ Initialize Choices.js for better dropdown experience
            if (typeof Choices !== 'undefined') {
                new Choices(subAccountSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholderValue: '',
                    searchPlaceholderValue: 'ជ្រើសរើស...'
                });
            }

            subAccountSelect.addEventListener('change', function() {
                const selectedOption = subAccountSelect.options[subAccountSelect.selectedIndex];
                const subAccountId = this.value;
                const programCode = selectedOption.getAttribute('data-program');

                if (programInput) {
                    programInput.value = programCode;
                }

                if (programHidden) {
                    programHidden.value = programCode;
                }

            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cboSubDepartSelect = document.getElementById('cboAgency');
            const cboSubDepartChoice = new Choices(cboSubDepartSelect, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
@endsection
