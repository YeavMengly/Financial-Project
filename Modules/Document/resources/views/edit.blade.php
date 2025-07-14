@extends('layouts.master')
@section("css")
    <link href="{{ asset("assets/libs/summernote/summernote.min.css") }}" rel="stylesheet" type="text/css"/>
@endsection
@section("content")
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("menus.document") }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __("menus.document") }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __("buttons.edit") }}</li>
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
                          action="{{ route("document.update", $params) }}"
                          autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.category") }}</label>
                                    <select id="cboCategory" class="form-select" name="cboCategory" required
                                            data-pristine-required-message="{{ __("messages.required") }}">
                                        <option value="">ជ្រើសរើស</option>
                                        @foreach($category as $cate)
                                            <option value="{{ $cate->id }}"
                                                    @if($document->cate_id == $cate->id) selected @endif>{{ $cate->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboCategory')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.category.sub") }}</label>
                                    <select id="cboCategorySub" class="form-select" name="cboSub" required
                                            data-pristine-required-message="{{ __("messages.required") }}">
                                        <option value="">ជ្រើសរើស</option>
                                        @foreach($categorySub as $cateSub)
                                            <option value="{{ $cateSub->id }}"
                                                    @if($document->sub_id == $cateSub->id) selected @endif>{{ $cateSub->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboSub')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.document.title") }}</label>
                                    <input type="text" class="form-control" name="txtTitle" required
                                           data-pristine-required-message="{{ __("messages.required") }}"
                                           value="{{ $document->title }}"/>
                                    @error('txtTitle')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.document.year") }}</label>
                                    <input
                                        data-pristine-required-message="{{ __("messages.required") }}"
                                        data-pristine-min-message="ឆ្នាំ ត្រូវតែធំជាងសូន្យ"
                                        data-pristine-integer-message="ឆ្នាំ ត្រូវតែលេខ"
                                        min="1" type="integer"
                                        class="form-control" name="txtYear" required value="{{ $document->year }}"/>
                                    @error('txtYear')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __("forms.document.description") }}</label>
                                    <textarea id="vDescription"
                                              data-pristine-required-message="{{ __("messages.required") }}"
                                              rows="5" class="form-control"
                                              name="txtDescription" required>{{ $document->description }}</textarea>
                                    @error('txtDescription')
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
    <script src="{{ asset("assets/libs/summernote/summernote.min.js") }}"></script>
    <script src="{{ asset("assets/libs/pristinejs/pristine.min.js") }}"></script>
    <script src="{{ asset("assets/js/pages/form-validations.init.js") }}"></script>
    <script>
        $(document).ready(function () {
            $('#vDescription').summernote({
                backColor: 'red',
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
        $('#cboCategory').change(function () {
            var cateId = $(this).val();
            $.ajax({
                url: '{!! route("document.by.category_id") !!}',
                type: 'get',
                global: false,
                data: {cate_id: cateId},
                success: function (data) {
                    $('#cboCategorySub').html(data);
                }
            });
        });
    </script>

@endsection
