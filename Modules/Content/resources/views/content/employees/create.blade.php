@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.employees') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.employees') }}</a>
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
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.id.number') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="id_number" tabindex="1" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.account') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="account_number" tabindex="1" />
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
@endsection
