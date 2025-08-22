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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.sub.depart') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.sub.depart') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
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
                        action="{{ route('programSub.update', $params) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="cboNo" class="form-label font-size-13 text-muted">
                                        {{ __('forms.agency') }}
                                    </label>
                                    <select class="form-control" id="cboNo" name="cboNo" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($program as $dd)
                                            <option value="">
                                                {{ $data->no }} - {{ $data->decription }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboNo')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.sub.depart') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="no"
                                        value="{{ old('no', $data->no) }}" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <textarea id="decription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="decription" required> {{ old('decription', $data->decription) }}</textarea>
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
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#decription').summernote({
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
            const cboSubDepartSelect = document.getElementById('cboNo');
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
