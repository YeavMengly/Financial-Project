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
                    {{-- <form id="pristine-valid-example" novalidate method="POST" action="{{ route('publicServices.store') }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-lg-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboCate" class="form-label font-size-13 text-muted">
                                        {{ __('forms.public.service.agency') }}
                                    </label>
                                    <select class="form-select" id="cboCate" name="cboCate"
                                        required data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($publicServiceCategory as $psc)
                                            <option value="{{ $psc->id }}">
                                                {{ $psc->code }}-
                                                {{ $psc->service_cate_name }}</option>
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.service.name') }}</label>
                                    <input type="text" class="form-control" name="service_name" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
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
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.national') }}</label>
                                    <input type="text" class="form-control" name="percentage_national" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.mlvt') }}</label>
                                    <input type="text" class="form-control" name="percentage_mlvt" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.interior') }}</label>
                                    <input type="text" class="form-control" name="percentage_interior" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.percentage.mef') }}</label>
                                    <input type="text" class="form-control" name="percentage.mef" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                        

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" id="btnSave" name="submit" value="save" class="btn btn-primary">
                                    {{ __('buttons.save') }}
                                </button>
                                <a class="btn btn-dark"
                                    href="{{ route('publicServices.index') }}">{{ __('buttons.back') }}</a>

                            </div>
                        </div>
                    </form> --}}
                    <form id="pristine-valid-example" novalidate method="POST" action="{{ route('publicServices.store') }}"
                        autocomplete="off" enctype="multipart/form-data">

                        @csrf

                        <div class="row">

                            {{-- Service Category --}}
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label for="cboCate" class="form-label font-size-13 text-muted">
                                        {{ __('forms.public.service.agency') }}
                                    </label>

                                    <select class="form-select" id="cboCate" name="cboCate" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>

                                        @foreach ($publicServiceCategory as $psc)
                                            <option value="{{ $psc->id }}"
                                                {{ old('cboCate') == $psc->id ? 'selected' : '' }}>
                                                {{ $psc->code }} - {{ $psc->service_cate_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('cboCate')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Code --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        {{ __('forms.service.code') }}
                                    </label>

                                    <input type="text" class="form-control" name="service_code"
                                        value="{{ old('service_code') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('service_code')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Name --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label> {{ __('forms.service.name') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="service_name" required></textarea>
                                    @error('service_name')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Price --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        {{ __('forms.service.price') }}
                                    </label>

                                    <input type="number" class="form-control" name="service_price"
                                        value="{{ old('service_price') }}" min="0" step="1" required
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                    @error('service_price')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Max Service Time --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.max.service.time') }}</label>
                                    <input type="text" class="form-control" name="max_service_time" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            {{-- Expired --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.expired') }}</label>
                                    <input type="text" class="form-control" name="expired" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        {{ __('forms.expired') }}
                                    </label>

                                    <select class="form-select" name="expired" required>
                                        <option value="0" {{ old('expired', 0) == 0 ? 'selected' : '' }}>
                                            មិនផុតកំណត់
                                        </option>
                                        <option value="1" {{ old('expired') == 1 ? 'selected' : '' }}>
                                            ផុតកំណត់
                                        </option>
                                    </select>

                                    @error('expired')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- Revenue Distribution Title --}}
                            <div class="col-12 mt-2">
                                <div class="card border">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            ការបែងចែកចំណូលតាមភាគរយ (%)
                                        </h5>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">

                                            {{-- National --}}
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">
                                                        {{ __('forms.percentage.national') }}
                                                    </label>

                                                    <input type="number" class="form-control" name="percentage_national"
                                                        value="{{ old('percentage_national', 0) }}" min="0"
                                                        max="100" step="0.01">

                                                    @error('percentage_national')
                                                        <div class="pristine-error text-help">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- MLVT --}}
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">
                                                        {{ __('forms.percentage.mlvt') }}
                                                    </label>

                                                    <input type="number" class="form-control" name="percentage_mlvt"
                                                        value="{{ old('percentage_mlvt', 0) }}" min="0"
                                                        max="100" step="0.01">

                                                    @error('percentage_mlvt')
                                                        <div class="pristine-error text-help">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Interior --}}
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">
                                                        {{ __('forms.percentage.interior') }}
                                                    </label>

                                                    <input type="number" class="form-control" name="percentage_interior"
                                                        value="{{ old('percentage_interior', 0) }}" min="0"
                                                        max="100" step="0.01">

                                                    @error('percentage_interior')
                                                        <div class="pristine-error text-help">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- MEF --}}
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">
                                                        {{ __('forms.percentage.mef') }}
                                                    </label>

                                                    <input type="number" class="form-control" name="percentage_mef"
                                                        value="{{ old('percentage_mef', 0) }}" min="0"
                                                        max="100" step="0.01">

                                                    @error('percentage_mef')
                                                        <div class="pristine-error text-help">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="col-12 mt-3">
                                <div class="d-flex flex-wrap gap-2">

                                    <button type="submit" id="btnSave" name="submit" value="save"
                                        class="btn btn-primary">
                                        {{ __('buttons.save') }}
                                    </button>

                                    <a class="btn btn-dark" href="{{ route('publicServices.index') }}">
                                        {{ __('buttons.back') }}
                                    </a>

                                </div>
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
