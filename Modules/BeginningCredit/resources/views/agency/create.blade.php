@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.agency') }}</h4>
                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.agency') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
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
                    <form id="pristine-valid-example" novalidate method="POST"
                        action="{{ route('agency.store', $params) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="cboSubAccountNumber" class="form-label font-size-13 text-muted">
                                        {{ __('forms.depart') }}
                                    </label>
                                    <select class="form-control" data-trigger id="cboProgram" name="cboProgram" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($data as $agc)
                                            <option value="{{ $agc->id }}">
                                                {{ $agc->no }}-
                                                {{ $agc->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboProgram')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.number') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="numeric" class="form-control" name="no" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="name" tabindex="2" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.nick_name') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="nick_name" tabindex="2" />
                                </div>
                            </div>


                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cboSubDepartSelect = document.getElementById('cboProgram');
            const cboSubDepartChoice = new Choices(cboSubDepartSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>
@endsection
