@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.public.service') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.public.service') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
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
                    <form id="pristine-valid-example" novalidate method="POST" action="{{ route('publicServices.store') }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-lg-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboCate" class="form-label font-size-13 text-muted">
                                        {{ __('forms.public.service.agency') }}
                                    </label>
                                    <select class="form-select" id="cboCate" name="cboCate" required
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                        <option value="">{{ __('forms.search...') }}</option>

                                        @foreach ($publicServiceCategory as $psc)
                                            <option value="{{ $psc['id'] }}"
                                                {{ old('cboCate') == $psc['id'] ? 'selected' : '' }}>

                                                {{ str_repeat('— ', $psc['level']) }}
                                                {{ $psc['code'] }} - {{ $psc['name'] }}

                                            </option>
                                        @endforeach

                                    </select>
                                    @error('cboCate')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.service.code') }}</label>
                                    <input type="text" class="form-control" name="service_code" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.service.name') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="service_name" required></textarea>
                                    @error('service_name')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.service.price') }}</label>
                                    <input type="text" class="form-control" name="service_price" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.max.service.time') }}</label>
                                    <input type="text" class="form-control" name="max_service_time" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.expired') }}</label>
                                    <input type="text" class="form-control" name="expired" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="mb-0">
                                                ការបែងចែកចំណូលតាមភាគរយ (%)
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.national') }}</label>
                                    <input type="text" class="form-control" name="percentage_national" min="0"
                                        required data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.mlvt') }}</label>
                                    <input type="text" class="form-control" name="percentage_mlvt" min="0"
                                        required data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.interior') }}</label>
                                    <input type="text" class="form-control" name="percentage_interior" min="0"
                                        required data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.mef') }}</label>
                                    <input type="text" class="form-control" name="percentage.mef" min="0" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" id="btnSave" name="submit" value="save"
                                    class="btn btn-primary">
                                    {{ __('buttons.save') }}
                                </button>
                                <a class="btn btn-dark"
                                    href="{{ route('publicServices.index') }}">{{ __('buttons.back') }}</a>

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
    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                height: 150,
                styleWithCSS: true,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']]
                ],
                callbacks: {
                    onPaste: function(e) {
                        // Preserve pasted text spacing and line breaks
                        const clipboardData = (e.originalEvent || e).clipboardData;
                        if (!clipboardData) return;

                        const text = clipboardData.getData('text/plain');

                        if (text) {
                            e.preventDefault();

                            document.execCommand(
                                'insertHTML',
                                false,
                                $('<div>').text(text).html()
                                .replace(/\r\n|\r|\n/g, '<br>')
                                .replace(/ {2,}/g, function(spaces) {
                                    return '&nbsp;'.repeat(spaces.length);
                                })
                            );
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboCate');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>
@endsection
