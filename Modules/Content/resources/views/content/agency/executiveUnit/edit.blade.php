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
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.content.agency.executive.unit') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);"><span>{{ __('menus.content') }}</span></a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);"><span>{{ $module->year }}</span></a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.content.agency') }}
                                    <span>{{ $agency->no }}</span></a>
                            </li>
                            <li class="breadcrumb-item">{{ __('menus.content.agency.executive.unit') }}
                            </li>
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
                        action="{{ route('executiveUnit.update', ['params' => $params, 'executiveId' => $executiveId, 'id' => encode_params($module->id)]) }}"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="title"
                                        value="{{ old('title', $executiveUnit->title) }}" tabindex="=1" />
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]) }}">{{ __('buttons.back') }}</a>
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
    <script src="{{ asset('assets/js/pages/summernote.init.js') }}"></script>
@endsection
