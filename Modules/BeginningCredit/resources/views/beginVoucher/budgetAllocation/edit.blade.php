@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.budget.allocation') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ __('menus.budget.allocation') }}</a>
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
                        action="{{ route('budgetAllocation.update', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId, 'id' => $module->id]) }}"
                        autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.budget.allocation') }}</label>
                                    <!-- Updated name from 'budgetAllocation' to 'amount' -->
                                    <input type="number" step="any" min="0" class="form-control" name="amount"
                                        required value="{{ old('amount', $module->amount) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('amount')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.expense.type') }}</label>
                                    <!-- Updated name from 'cboExpenseType' to 'expense_type_id' -->
                                    <select class="form-control" data-trigger id="cboExpenseType" name="cboExpenseType"
                                        required data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($expenseTypes as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $module->budget_expense_type_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name_kh }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboExpenseType')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('budgetAllocation.index', ['params' => $params, 'budgetAllocationId' => $budgetAllocationId]) }}">{{ __('buttons.back') }}</a>

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
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropProvince = document.getElementById('province');
            const dropProvinceChoice = new Choices(dropProvince, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script> --}}
@endsection
