@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.notes') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.notes') }}</a></li>
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
                    <form class="needs-validation" novalidate method="POST" action="{{ route('notes.update', $params) }}"
                        autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="vTitle">{{ __('forms.notes.title') }}</label>
                                    <input type="text" class="form-control" id="vTitle" name="txtTitle" required
                                        tabindex="1" value="{{ $data->title }}" />
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="vDescription">{{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" name="txtDescription" required tabindex="2">{{ $data->description }}</textarea>
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.notes.task') }}</label>
                                    <select class="form-select" name="cboTaks" required tabindex="3">
                                        <option value="2" @if ($data->is_archived == 1) selected @endif>
                                            កំពុងធ្វើ
                                        </option>
                                        <option value="3" @if ($data->is_archived == 2) selected @endif>
                                            បានបញ្ចប់
                                        </option>
                                    </select>
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
    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
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
