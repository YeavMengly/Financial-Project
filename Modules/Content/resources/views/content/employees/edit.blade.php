@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.content.employee') }}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">
                                {{ __('menus.content.employee') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('buttons.edit') }}
                        </li>
                    </ol>
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
                        action="{{ route('employees.update', $params) }}" autocomplete="off">

                        @csrf


                        <div class="row">

                            {{-- ID Number --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ
                                        {{ __('forms.id.number') }}
                                    </label>

                                    <input type="number" class="form-control" name="id_number"
                                        value="{{ old('id_number', $module->id_number) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" tabindex="1">

                                    @error('id_number')
                                        <div class="pristine-error text-help">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Account Number --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ
                                        {{ __('forms.account') }}
                                    </label>

                                    <input type="number" class="form-control" name="account_number"
                                        value="{{ old('account_number', $module->account_number) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" tabindex="2">

                                    @error('account_number')
                                        <div class="pristine-error text-help">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Khmer Name --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>
                                        {{ __('forms.name.kh') }}
                                    </label>

                                    <input type="text" class="form-control" name="name_kh"
                                        value="{{ old('name_kh', $module->name_kh) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" tabindex="3">

                                    @error('name_kh')
                                        <div class="pristine-error text-help">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Latin Name --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>
                                        {{ __('forms.name.en') }}
                                    </label>

                                    <input type="text" class="form-control" name="name_latin"
                                        value="{{ old('name_latin', $module->name_latin) }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" tabindex="4">

                                    @error('name_latin')
                                        <div class="pristine-error text-help">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Position --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="cboPosition" class="form-label font-size-13 text-muted">
                                        {{ __('forms.position') }}
                                    </label>
                                    <select class="form-select" id="cboPosition" name="cboPosition" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($position as $p)
                                            <option value="{{ $p->id }}" {{ $module->position_id == $p->id }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboPosition')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            {{-- Buttons --}}
                            <div class="d-flex flex-wrap gap-2">

                                <button type="submit" id="btnSave" class="btn btn-primary">
                                    {{ __('buttons.save') }}
                                </button>

                                <a class="btn btn-dark" href="{{ route('employees.index') }}">
                                    {{ __('buttons.back') }}
                                </a>

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
        document.getElementById('pristine-valid-example')
            .addEventListener('submit', function(e) {

                const num = this.querySelector('[name="id_number"]').value.trim();
                const acc = this.querySelector('[name="account_number"]').value.trim();
                const kh = this.querySelector('[name="name_kh"]').value.trim();
                const en = this.querySelector('[name="name_latin"]').value.trim();

                if (!num || !acc || !kh || !en) {
                    e.preventDefault();
                    return false;
                }

                const btn = document.getElementById('btnSave');

                btn.disabled = true;
                btn.innerText = 'កំពុងរក្សាទុក...';
            });
    </script>

    {{-- UI Searching Dropdown  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboPosition');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false,

            });
        });
    </script>
@endsection
