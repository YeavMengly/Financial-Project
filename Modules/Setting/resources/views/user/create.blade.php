@extends('layouts.master')
@section("content")
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.setting.member") }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __("menus.setting.member") }}</a></li>
                            <li class="breadcrumb-item active">{{ __("buttons.create") }}</li>
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
                    <form id="pristine-valid-example" novalidate method="POST" action="{{ route("user.store") }}"
                          autocomplete="off">
                        @csrf
                        <input type="hidden"/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.role") }}</label>
                                    <select class="form-select" name="cboRole" required tabindex="1">
                                        @foreach($role as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.name") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           type="text" class="form-control"
                                           name="txtName"
                                           tabindex="2"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.username") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           type="text"
                                           class="form-control"
                                           name="txtUsername"
                                           value="{{ old("txtUsername") }}"
                                           tabindex="3"/>
                                    @error('txtUsername')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.password") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           type="password"
                                           class="form-control"
                                           name="txtPassword"
                                           tabindex="4"/>
                                    @error('txtPassword')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                        value="save">{{ __("buttons.save") }}</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section("script")
    <script src="{{ asset("assets/libs/pristinejs/pristine.min.js") }}"></script>
    <script src="{{ asset("assets/js/pages/form-validations.init.js") }}"></script>
@endsection
