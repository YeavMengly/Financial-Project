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
                <h4 class="mb-sm-0 font-size-18"></h4>
                {{-- <h4 class="mb-sm-0 font-size-18">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">{{ __('menus.program') }} <span>{{ $module->no }}</span> </li>
                        <li class="breadcrumb-item active">{{ __('menus.program.sub') }}
                        </li>
                    </ol>
                </h4> --}}

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.program') }}
                                    <span>{{ $module->no }}</span></a>
                            </li>
                            <li class="breadcrumb-item">{{ __('menus.program.sub') }}
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
                        action="{{ route('program.sub.update', ['params' => $params, 'pId' => encode_params($pId), 'id' => encode_params($id)]) }}"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.program') }}</label>
                                    <input type="text" class="form-control"
                                        value="{{ $module->no }} . {{ $module->title }}" tabindex="1" disabled />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.program.sub') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="no"
                                        value="{{ old('no', substr($programSub->no, 1, 1)) }}" tabindex="1" />

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.title') }}</label>
                                    <textarea id="decription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="decription" required> {{ old('decription', $programSub->decription) }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('program.sub.index', ['params' => $params, 'pId' => encode_params($pId)]) }}">{{ __('buttons.back') }}</a>

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
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
@endsection
