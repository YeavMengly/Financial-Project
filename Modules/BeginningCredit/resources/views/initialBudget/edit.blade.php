@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.initial.budget') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.initial.budget') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" novalidate method="POST"
                        action="{{ route('initialBudget.update', $params) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.initial.budget.year') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" min="1900" class="form-control" name="year"
                                        value="{{ old('year', $data->year) }}" tabindex="2" />

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="title"
                                        value="{{ old('title', $data->title) }}" tabindex="3" />
                                    @error('title')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.sub.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="sub_title"
                                        value="{{ old('sub_title', $data->sub_title) }}" tabindex="3" />
                                    @error('sub_title')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="description" required> {{ old('description', $data->description) }}</textarea>
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
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
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
        // $('#cboCategory').change(function () {
        //     var cateId = $(this).val();
        //     $.ajax({
        //         url: '{!! route('document.by.category_id') !!}',
        //         type: 'get',
        //         global: false,
        //         data: {cate_id: cateId},
        //         success: function (data) {
        //             $('#cboCategorySub').html(data);
        //         }
        //     });
        // });
    </script>
@endsection
