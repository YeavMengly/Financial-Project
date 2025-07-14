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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.initial.budget') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.account') }}</a></li>
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
                    <form id="pristine-valid-example" novalidate method="POST" action="{{ route('initialBudget.store') }}"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.initial.budget.year') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" min="1900" class="form-control" name="year" tabindex="2" />

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="title" tabindex="3" />
                                    @error('title')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.sub.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="sub_title" tabindex="3" />
                                    @error('sub_title')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="description" required></textarea>
                                    @error('description')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script> --}}

    {{-- <script src="https://cdn.jsdelivr.net/npm/pristinejs/dist/pristine.min.js"></script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                var valid = pristine.validate();
                if (!valid) {
                    e.preventDefault(); // Prevent submission if form is invalid
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
@endsection
