@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content.chapters') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);"><span>{{ __('menus.content') }}</span></a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);"><span>{{ $ministry->year }}</span></a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __('menus.content.chapters') }}</a>
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
                        action="{{ route('chapters.update', ['params' => $params, 'id' => $chapter->id]) }}"
                        autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.chapter') }}</label>
                                    <input type="text" class="form-control" name="no" required
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ $chapter->no }}" />
                                    @error('no')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name') }}</label>
                                    <input type="text" class="form-control" name="name" required
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ $chapter->name }}" />
                                    @error('name')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="cboType" class="form-label font-size-13 text-muted">
                                        {{ __('forms.type') }}
                                    </label>

                                    <select class="form-control" name="cboType" id="cboType" required
                                        data-pristine-required-message="{{ __('messages.required') }}">

                                        <option value="">{{ __('forms.search...') }}</option>

                                        @foreach ($type as $item)
                                            <option value="{{ $item->code }}"
                                                {{ old('cboType', $chapter->type_id) == $item->code ? 'selected' : '' }}>
                                                {{ $item->number_type }} - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('cboType')
                                        <div class="pristine-error text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('chapters.index', $params) }}">{{ __('buttons.back') }}</a>
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
@endsection
