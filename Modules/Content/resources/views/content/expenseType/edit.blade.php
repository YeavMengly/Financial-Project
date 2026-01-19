@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.expense.type') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.expense.type') }}</a>
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
                        action="{{ route('expenseType.update', $params) }}" autocomplete="off"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.kh') }}</label>
                                    <input type="text" class="form-control" name="name_kh" value="{{ $module->name_kh }}"
                                        required data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('name_kh')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name.en') }}</label>
                                    <input type="text" class="form-control" name="name_en" value="{{ $module->name_en }}"
                                        required data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('name_en')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="status" value="1" class="form-check-input"
                                            {{ old('status', $module->status) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">

                                <button type="submit" id="btnSave" name="submit" value="save" class="btn btn-primary">
                                    {{ __('buttons.save') }}
                                </button>
                                <a class="btn btn-dark"
                                    href="{{ route('expenseType.index') }}">{{ __('buttons.back') }}</a>

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

                const kh = this.querySelector('[name="name_kh"]').value.trim();
                const en = this.querySelector('[name="name_en"]').value.trim();

                if (!kh || !en) {
                    e.preventDefault();
                    return false;
                }

                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                btn.innerText = 'កំពុងរក្សាទុក...';
            });
    </script>
@endsection
