@extends('layouts.master')
@section("content")
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.setting.roles") }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __("menus.setting.roles") }}</a></li>
                            <li class="breadcrumb-item active">{{ __("buttons.edit") }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" novalidate method="POST"
                          action="{{ route("role.update", $role->id) }}" class=".form" autocomplete="off">
                        @csrf
                        <input type="hidden"/>
                        <div class="row">
                            <div class="12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.role") }}</label>
                                    <input required data-pristine-required-message="{{ __("messages.required") }}"
                                           type="text"
                                           class="form-control"
                                           name="txtRole"
                                           tabindex="1" value="{{ $role->name }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <tbody>
                                    @foreach($pemissions as $permission)
                                        <tr>
                                            <td style="width: 50%"><span
                                                    class="text-capitalize">{{__("menus.".$permission->name)}}</span>
                                            </td>
                                            <td style="width: 50%">
                                                @foreach($permission->keywords as $key=>$keyword)
                                                    <div class="form-check mb-3">
                                                        @if(!empty($role->permissions))
                                                            <input class="form-check-input" type="checkbox"
                                                                   value="{{$keyword}}" id="{{$keyword}}"
                                                                   name="permissions[]" {{ in_array($keyword, $role->permissions)? 'checked':''}} />
                                                        @else
                                                            <input class="form-check-input" type="checkbox"
                                                                   value="{{$keyword}}" id="{{$keyword}}"
                                                                   name="permissions[]"/>
                                                        @endif
                                                        <label class="form-check-label" for="{{ $keyword }}">
                                                            {{ __('buttons.'.$key) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __("buttons.save") }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("script")
    <script src="{{ asset("assets/libs/pristinejs/pristine.min.js") }}"></script>
    <script src="{{ asset("assets/js/pages/form-validations.init.js") }}"></script>
@endsection

