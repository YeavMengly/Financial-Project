@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="bx bx-check-circle"></i> Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.cluster') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.initial.budget') }}</a>
                            </li>
                            <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ __('menus.credit') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.cluster') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example" action="{{ route('beginCredit.update', $params) }}" novalidate
                            method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccountNumber" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboAgency" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($agency as $agc)
                                                <option value="{{ $agc->agencyNumber }}"
                                                    {{ $agc->agencyNumber == $beginCredit->agencyNumber ? 'selected' : '' }}>
                                                    {{ $agc->agencyTitle }}
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
                                        <label for="cboSubDepart" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.depart') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboSubDepart" name="cboSubDepart"
                                            required data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>

                                            @foreach ($subDepart as $depart)
                                                <option value="{{ $depart->subDepart }}"
                                                    {{ $depart->subDepart == $beginCredit->subDepart ? 'selected' : '' }}>
                                                    {{ $depart->subDepart }}
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
                                        <label for="cboSubAccountNumber" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboSubAccountNumber"
                                            name="cboSubAccountNumber" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($subAccount as $acount)
                                                <option value="{{ $acount->subAccountNumber }}"
                                                    {{ $acount->subAccountNumber == $beginCredit->subAccountNumber ? 'selected' : '' }}>
                                                    {{ $acount->subAccountNumber }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboSubAccountNumber')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.program.code') }}</label>
                                        <input type="text" name="program" class="form-control" required
                                            value="{{ old('program', $beginCredit->program) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />

                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.fin.law') }}</label>

                                        <input type="number" min="0" name="fin_law"
                                            value="{{ old('fin_law', $beginCredit->fin_law) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.current.loan') }}</label>
                                        <input type="number" min="0" name="current_loan"
                                            value="{{ old('current_loan', $beginCredit->current_loan) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="txtDescription" required>
                                        {{ $beginCredit->txtDescription }}
                                    </textarea>
                                    @error('txtDescription')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>

    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/pristinejs/dist/pristine.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                var valid = pristine.validate();
                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('choices-single-default');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'This is a placeholder',
                searchPlaceholderValue: 'This is a search placeholder',
                shouldSort: false
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const element = document.getElementById('cboSubAccountNumber');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboSubAccountNumber').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });

        $(document).ready(function() {
            const element = document.getElementById('cboAgency');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboAgency').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });

        $(document).ready(function() {
            const element = document.getElementById('cboSubDepart');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboSubDepart').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });
    </script>
@endsection
