@extends('layouts.master')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.sub.account') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.sub.account') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
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
                        action="{{ route('subAccount.update', $params) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" />
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.account') }}</label>
                                    <select id="cboAccountNumber" class="form-select" name="cboAccountNumber" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">ជ្រើសរើស</option>
                                        @foreach ($account as $acc)
                                            <option value="{{ $acc->accountNumber }}"
                                                @if (old('cboAccountNumber', isset($subAccount) ? $subAccount->accountNumber : '') == $acc->accountNumber) selected @endif>
                                                {{ $acc->accountNumber }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboAccountNumber')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.account') }}</label>
                                    <select id="cboAccountNumber" class="form-select" name="cboAccountNumber" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('buttons.select') }}</option>
                                        @foreach ($account as $acc)
                                            <option value="{{ $acc->accountNumber }}"
                                                @if (old('cboAccountNumber', isset($subAccount) ? $subAccount->accountNumber : '') == $acc->accountNumber) selected @endif>
                                                {{ $acc->accountNumber }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboAccountNumber')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}


                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.sub.account') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="subAccountNumber"
                                        value="{{ old('subAccountNumber', $subAccount->subAccountNumber) }}"
                                        tabindex="2" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.name') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="txtSubAccount"
                                        value="{{ old('txtSubAccount', $subAccount->txtSubAccount) }}" tabindex="3" />
                                    @error('txtSubAccount')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
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
        // $(document).ready(function () {
        //     $('#vDescription').summernote({
        //         backColor: 'red',
        //         height: 150,
        //         toolbar: [
        //             ['style', ['bold', 'italic', 'underline', 'clear']],
        //             ['para', ['ul', 'ol', 'paragraph']],
        //             ['color', ['color']],
        //         ]
        //     });
        // });
        $('#cboAccountNumber').change(function() {
            var cateId = $(this).val();
            $.ajax({
                url: '{!! route('document.by.category_id') !!}',
                type: 'get',
                global: false,
                data: {
                    cate_id: cateId
                },
                success: function(data) {
                    $('#cboCategorySub').html(data);
                }
            });
        });
    </script>
@endsection
