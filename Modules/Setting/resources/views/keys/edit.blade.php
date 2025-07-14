@extends('layouts.master')
@section("content")
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.api.key") }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __("menus.api.key") }}</a></li>
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
                          action="{{ route("keys.update", $keys->id) }}"
                          autocomplete="off">
                        @csrf
                        <input type="hidden"/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.name") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           type="text" class="form-control" name="txtTitle"
                                           value="{{ $keys->title }}"
                                           tabindex="1"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Key</label>
                                    <input required
                                           data-pristine-required-message="{{ __("messages.required") }}"
                                           type="text" class="form-control"
                                           value="{{ $keys->key }}"
                                           name="txtKey" tabindex="2"/>
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
