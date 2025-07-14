@extends('layouts.master')
@section("content")
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.setting.category") }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __("menus.setting.category") }}</a></li>
                            <li class="breadcrumb-item active">{{ __("buttons.edit") }}</li>
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
                          action="{{ route("category.update", $module->id) }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.category") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           value="{{ $module->name }}"
                                           type="text" class="form-control"
                                           name="txtCategory" tabindex="1"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.order") }}</label>
                                    <input
                                        required
                                        data-pristine-required-message="{{ __("messages.required") }}"
                                        data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                        data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ"
                                        min="1"
                                        type="integer"
                                        value="{{ $module->order }}"
                                        class="form-control"
                                        name="txtOrder" tabindex="2"/>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit">{{ __("buttons.save") }}</button>
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
