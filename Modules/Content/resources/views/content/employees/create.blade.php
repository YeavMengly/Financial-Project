@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content.employee') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.content.employee') }}</a>
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
                    <form id="pristine-valid-example" novalidate method="POST" action="{{ route('employees.store') }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ{{ __('forms.id.number') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="number" class="form-control" name="id_number" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ{{ __('forms.account') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="number" class="form-control" name="account_number" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.kh') }}</label>
                                    <input type="text" class="form-control" name="name_kh" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.en') }}</label>
                                    <input type="text" class="form-control" name="name_latin" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" id="btnSave" name="submit" value="save" class="btn btn-primary">
                                    {{ __('buttons.save') }}
                                </button>
                                <a class="btn btn-dark" href="{{ route('employees.index') }}">{{ __('buttons.back') }}</a>

                            </div>
                        </div> --}}
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ{{ __('forms.id.number') }}</label>

                                    <input type="number" class="form-control" name="id_number" required
                                        data-pristine-required-message="សូមបញ្ចូលលេខអត្តសញ្ញាណ។" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>លេខ{{ __('forms.account') }}</label>

                                    <input type="number" class="form-control" name="account_number" required
                                        data-pristine-required-message="សូមបញ្ចូលលេខគណនី។" tabindex="2" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.kh') }}</label>

                                    <input type="text" class="form-control" name="name_kh" required
                                        data-pristine-required-message="សូមបញ្ចូលឈ្មោះជាភាសាខ្មែរ។" tabindex="3" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.en') }}</label>

                                    <input type="text" class="form-control" name="name_latin" required
                                        data-pristine-required-message="សូមបញ្ចូលឈ្មោះជាភាសាឡាតាំង។" tabindex="4" />
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
                                            <option value="{{ $p->id }}">
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboPosition')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" id="btnSave" name="submit" value="save" class="btn btn-primary">
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
        const form = document.getElementById('pristine-valid-example');

        if (form) {
            form.addEventListener('submit', function(e) {
                const num = this.querySelector('[name="id_number"]')?.value.trim();
                const acc = this.querySelector('[name="account_number"]')?.value.trim();
                const kh = this.querySelector('[name="name_kh"]')?.value.trim();
                const en = this.querySelector('[name="name_latin"]')?.value.trim();
                const file = this.querySelector('[name="file"]')?.value;

                // If required fields or file input are missing, don't lock the button
                if ((num !== undefined && (!num || !acc || !kh || !en)) || (file !== undefined && !file)) {
                    // Let browser validation catch it, or prevent default if manual check fails
                    return;
                }

                const btn = document.getElementById('btnSave');
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'កំពុងរក្សាទុក...';
                }
            });
        }

        // Re-enable the button if user navigates back or validation redirects back with errors
        window.addEventListener('pageshow', function(event) {
            const btn = document.getElementById('btnSave');
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'រក្សាទុក'; // Change back to your original button text
            }
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
