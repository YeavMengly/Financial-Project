@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        #itemTableWrapper,
        .table-responsive {
            width: 100% !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
            position: relative !important;
            display: block;
        }

        #itemTableWrapper {
            overflow-x: auto !important;
            padding-top: 300px !important;
            margin-top: -300px !important;
        }

        #itemTable {
            table-layout: fixed !important;
            width: 100% !important;
            min-width: 1300px !important;
        }

        #itemTable th {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        #itemTable td {
            vertical-align: middle !important;
            padding: 6px 8px !important;
            overflow: visible !important;
            position: relative !important;
        }

        .skip-label {
            color: #495057;
            transition: all 0.2s ease-in-out;
        }

        .form-check-input:checked~.skip-label {
            color: #223fe6 !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .choices {
            margin-bottom: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            position: relative !important;
            overflow: visible !important;
        }

        .choices__inner {
            min-height: 38px !important;
            height: 38px !important;
            padding: 2px 8px !important;
            font-size: 13px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            background-color: #fff !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        .choices__list--single {
            padding: 0 !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .choices__list--single .choices__item {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 34px !important;
            width: 100% !important;
            float: none !important;
        }

        .choices.is-open .choices__list--dropdown,
        .choices__list--dropdown {
            position: absolute !important;
            bottom: calc(100% + 6px) !important;
            top: auto !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            transform: none !important;
            z-index: 9999999 !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            box-shadow: 0 -15px 35px rgba(0, 0, 0, 0.3) !important;
            background-color: #ffffff !important;
            margin-bottom: 4px !important;
            box-sizing: border-box !important;
        }

        .choices__list--dropdown .choices__item--selectable {
            padding: 8px 10px !important;
            font-size: 13px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0b5ed7 !important;
            color: #ffffff !important;
        }

        .choices__list--dropdown .choices__input {
            font-size: 13px !important;
            padding: 6px 10px !important;
            margin-bottom: 6px !important;
            border-radius: 3px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #itemTableWrapper.table-skipped {
            opacity: 0.6;
        }

        #itemTableWrapper.table-skipped .choices {
            pointer-events: none;
            background-color: #e9ecef !important;
        }

        .form-check.form-switch {
            position: relative !important;
            z-index: 100 !important;
            pointer-events: auto !important;
        }

        .form-check-input {
            cursor: pointer !important;
            position: relative !important;
            z-index: 101 !important;
        }

        .skip-label {
            cursor: pointer !important;
            position: relative !important;
            z-index: 101 !important;
        }

        #itemTableWrapper {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.project') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.project') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.entry') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $ministry->year ?? '' }}</a></li>
                        <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example"
                            action="{{ route('project.update', ['params' => $params, 'id' => $id]) }}" method="POST"
                            novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                        <input type="text" name="stock_number"
                                            value="{{ old('stock_number', $module->stock_number ?? '') }}"
                                            class="form-control" tabindex="1" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                        <input type="text" name="stock_name"
                                            value="{{ old('stock_name', $module->stock_name ?? '') }}" class="form-control"
                                            tabindex="2" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="company_name">{{ __('forms.company.name') }}</label>
                                        <input type="text" name="company_name"
                                            value="{{ old('company_name', $module->company_name ?? '') }}"
                                            class="form-control" tabindex="3" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="warehouse_voucher">{{ __('forms.warehouse.voucher') }}</label>
                                        <input type="text" name="warehouse_voucher"
                                            value="{{ old('warehouse_voucher', $module->warehouse_voucher ?? '') }}"
                                            class="form-control" tabindex="4" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                        <input type="text" name="user_entry"
                                            value="{{ old('user_entry', $module->user_entry ?? '') }}" class="form-control"
                                            tabindex="5" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="user_receiver">{{ __('forms.receiver') }}</label>
                                        <input type="text" name="user_receiver"
                                            value="{{ old('user_receiver', $module->user_receiver ?? '') }}"
                                            class="form-control" tabindex="6" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date" class="form-label">{{ __('forms.date') }}</label>
                                        <input type="text" id="date" name="date" class="form-control"
                                            tabindex="7" placeholder="{{ __('forms.select_date') }}"
                                            value="{{ old('date', $module->date ?? '') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- PROJECT TABLE --}}
                            @php
                                $hasValidItems =
                                    isset($items) &&
                                    count($items) > 0 &&
                                    !empty($items[0]->sub_project ?? ($items[0]->sub_pro ?? null));
                                $isTableSkipped = old('skipItemTable', !$hasValidItems);
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">
                                    {{ __('forms.sub.project') }}
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="skipItemTable"
                                        name="skipItemTable" value="1" style="cursor:pointer;"
                                        {{ $isTableSkipped ? 'checked' : '' }}>
                                    <label class="form-check-label font-size-12 text-muted skip-label"
                                        for="skipItemTable">
                                        រំលង / មិនភ្ជាប់
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-12 col-md-12 mt-3" id="itemTableWrapper">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle text-nowrap" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th style="width:200px; min-width:200px;" class="align-middle">
                                                    {{ __('forms.project') }}
                                                </th>
                                                <th style="width:200px; min-width:200px;" class="align-middle">
                                                    ប្រភេទ (Category)
                                                </th>
                                                <th style="width:200px; min-width:200px;" class="align-middle">
                                                    ប្រភេទរង (Sub-category)
                                                </th>
                                                <th style="width:150px; min-width:200px;" class="align-middle">
                                                    {{ __('forms.sub.account') }}
                                                </th>
                                                <th style="width:150px; min-width:200px;" class="align-middle">
                                                    {{ __('forms.program') }}
                                                </th>
                                                <th style="width:150px; min-width:200px;" class="align-middle">
                                                    {{ __('forms.program.sub') }}
                                                </th>
                                                <th style="width:150px; min-width:200px;" class="align-middle">
                                                    {{ __('forms.cluster') }}
                                                </th>
                                                <th style="width:70px; min-width:70px;" class="text-center align-middle">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rowCounts = count($items ?? [1]);
                                            @endphp

                                            @for ($i = 0; $i < $rowCounts; $i++)
                                                @php
                                                    $item = $items[$i] ?? ($module ?? null);

                                                    $valSubPro = old(
                                                        "sub_pro.$i",
                                                        $item->sub_project ?? ($item->sub_pro ?? ''),
                                                    );
                                                    $valAccountSub = old("accountSub.$i", $item->account_sub_id ?? '');
                                                    $valProgram = old("program.$i", $item->program_id ?? '');
                                                    $valProgramSub = old(
                                                        "cboProgramSub.$i",
                                                        $item->program_sub_id ?? '',
                                                    );
                                                    $valCluster = old("cboCluster.$i", $item->cluster_id ?? '');

                                                    /* Universal Parser (Handles JSON, Comma Strings, & Raw Arrays) */
                                                    $parseArray = function ($source) {
                                                        if (is_array($source)) {
                                                            return array_values(array_filter($source, fn($v) => !is_null($v) && trim((string)$v) !== ''));
                                                        }
                                                        if (is_string($source) && !empty(trim($source))) {
                                                            $str = trim($source);
                                                            if (str_starts_with($str, '[') || str_starts_with($str, '"')) {
                                                                $decoded = json_decode($str, true);
                                                                if (is_string($decoded)) {
                                                                    $decoded = json_decode($decoded, true);
                                                                }
                                                                if (is_array($decoded)) {
                                                                    return array_values(array_filter($decoded, fn($v) => !is_null($v) && trim((string)$v) !== ''));
                                                                }
                                                            }
                                                            return array_values(array_filter(array_map('trim', explode(',', $str)), fn($v) => $v !== ''));
                                                        }
                                                        return [];
                                                    };

                                                    $rawCategories = old(
                                                        "category.$i",
                                                        $item->category ?? ($item->categories ?? null),
                                                    );
                                                    $valCategories = $parseArray($rawCategories);
                                                    if (empty($valCategories)) {
                                                        $valCategories = [''];
                                                    }

                                                    $rawSubCats = old(
                                                        "sub_category.$i",
                                                        $item->sub_category ?? ($item->sub_categories ?? null),
                                                    );
                                                    $valSubCategories = $parseArray($rawSubCats);
                                                    if (empty($valSubCategories)) {
                                                        $valSubCategories = [''];
                                                    }

                                                    $maxInputs = max(
                                                        count($valCategories),
                                                        count($valSubCategories),
                                                        1,
                                                    );
                                                @endphp

                                                <tr data-row-index="{{ $i }}">
                                                    {{-- Sub Project --}}
                                                    <td>
                                                        @if (!empty($item->id))
                                                            <input type="hidden" name="item_id[]"
                                                                value="{{ $item->id }}">
                                                        @endif
                                                        <input type="text" name="sub_pro[]"
                                                            value="{{ $valSubPro }}"
                                                            class="form-control table-required" required>
                                                    </td>

                                                    {{-- Category Inputs --}}
                                                    <td>
                                                        <div class="category-wrapper">
                                                            @for ($k = 0; $k < $maxInputs; $k++)
                                                                @php $catVal = $valCategories[$k] ?? ''; @endphp
                                                                <div class="d-flex mb-2 category-item">
                                                                    <input type="text" class="form-control me-1"
                                                                        name="category[{{ $i }}][]"
                                                                        value="{{ is_scalar($catVal) ? $catVal : '' }}"
                                                                        placeholder="បញ្ចូលប្រភេទ">
                                                                    @if ($k === 0)
                                                                        <button type="button"
                                                                            class="btn btn-outline-success btn-sm addCategoryBtn">+</button>
                                                                    @else
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-sm removeCategoryBtn">-</button>
                                                                    @endif
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </td>

                                                    {{-- Sub Category Inputs --}}
                                                    <td>
                                                        <div class="subcategory-wrapper">
                                                            @for ($k = 0; $k < $maxInputs; $k++)
                                                                @php $subCatVal = $valSubCategories[$k] ?? ''; @endphp
                                                                <div class="d-flex mb-2 subcategory-item">
                                                                    <input type="text" class="form-control me-1"
                                                                        name="sub_category[{{ $i }}][]"
                                                                        value="{{ is_scalar($subCatVal) ? $subCatVal : '' }}"
                                                                        placeholder="បញ្ចូលប្រភេទរង">
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </td>

                                                    {{-- Account Sub --}}
                                                    <td>
                                                        <select class="form-select cboSubAccount" name="accountSub[]">
                                                            <option value="">ជ្រើសរើស</option>
                                                            @foreach ($accountSub as $bv)
                                                                <option value="{{ $bv->no }}"
                                                                    {{ $valAccountSub == $bv->no ? 'selected' : '' }}>
                                                                    {{ $bv->no }}-{{ $bv->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    {{-- Program --}}
                                                    <td>
                                                        <select class="form-select cboProgram" name="program[]">
                                                            <option value="">ជ្រើសរើស</option>
                                                            @foreach ($program as $p)
                                                                <option value="{{ $p->id }}"
                                                                    {{ $valProgram == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->no }}-{{ $p->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    {{-- Program Sub --}}
                                                    <td>
                                                        <select class="form-select cboProgramSub" name="cboProgramSub[]"
                                                            data-program-id="{{ $valProgram }}"
                                                            data-selected="{{ $valProgramSub }}">
                                                            <option value="">{{ __('forms.search...') }}</option>
                                                        </select>
                                                    </td>

                                                    {{-- Cluster --}}
                                                    <td>
                                                        <select class="form-select cboCluster" name="cboCluster[]"
                                                            data-program-sub-id="{{ $valProgramSub }}"
                                                            data-selected="{{ $valCluster }}">
                                                            <option value="">{{ __('forms.search...') }}</option>
                                                        </select>
                                                    </td>

                                                    <td class="text-center">
                                                        @if ($i === 0)
                                                            <button type="button"
                                                                class="btn btn-success addRow">+</button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-danger removeRow">-</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vRefer" class="form-label">{{ __('forms.refer') }}</label>
                                        <textarea name="refer" id="vRefer" rows="5" class="form-control" tabindex="9"
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ old('refer', $module->refer ?? '') }}</textarea>
                                        @error('refer')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="vNote" class="form-label">{{ __('forms.note') }}</label>
                                        <textarea name="note" id="vNote" rows="5" class="form-control" tabindex="10"
                                            data-pristine-required-message="{{ __('messages.required') }}">{{ old('note', $module->note ?? '') }}</textarea>
                                        @error('note')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('project.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        const choicesMap = new WeakMap();

        function initChoicesInstance(element) {
            if (!element) return null;
            if (choicesMap.has(element)) return choicesMap.get(element);
            const instance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...',
                shouldSort: false,
                position: 'auto',
                removeItemButton: false
            });
            choicesMap.set(element, instance);
            element.classList.add('choices-initialized');
            return instance;
        }

        function updateRowIndices() {
            $('#itemTable tbody tr').each(function(newIndex) {
                $(this).attr('data-row-index', newIndex);
                $(this).find('.category-item input').each(function() {
                    $(this).attr('name', `category[${newIndex}][]`);
                });
                $(this).find('.subcategory-item input').each(function() {
                    $(this).attr('name', `sub_category[${newIndex}][]`);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            if ($('#vNote').length) {
                $('#vNote').summernote({
                    height: 120,
                    callbacks: {
                        onChange: function(contents) {
                            $('#vNote').val(contents);
                        }
                    }
                });
            }
            if ($('#vRefer').length) {
                $('#vRefer').summernote({
                    height: 120,
                    callbacks: {
                        onChange: function(contents) {
                            $('#vRefer').val(contents);
                        }
                    }
                });
            }

            document.querySelectorAll('.cboProgram, .cboSubAccount, .cboProgramSub, .cboCluster').forEach(function(select) {
                initChoicesInstance(select);
            });

            document.querySelectorAll('.cboProgram, .cboSubAccount').forEach(function(select) {
                const instance = choicesMap.get(select);
                if (select.value && instance) {
                    instance.setChoiceByValue(String(select.value));
                }
            });

            $('#itemTable tbody tr').each(function() {
                const row = $(this);
                const programSelect = row.find('.cboProgram')[0];
                const subSelect = row.find('.cboProgramSub')[0];
                const clusterSelect = row.find('.cboCluster')[0];

                const programVal = $(programSelect).val() || $(subSelect).attr('data-program-id');
                const selectedSubId = $(subSelect).attr('data-selected');
                const selectedClusterId = $(clusterSelect).attr('data-selected');

                const subChoices = choicesMap.get(subSelect);
                const clusterChoices = choicesMap.get(clusterSelect);

                if (programVal) {
                    $.ajax({
                        url: "{{ route('project.by.program_sub', $params) }}",
                        type: 'GET',
                        data: { program_id: programVal },
                        success: function(response) {
                            if (Array.isArray(response) && subChoices) {
                                const options = response.map(item => ({
                                    value: String(item.value),
                                    label: item.label,
                                    selected: String(item.value) === String(selectedSubId)
                                }));
                                options.unshift({
                                    value: '',
                                    label: '{{ __('forms.search...') }}',
                                    selected: !selectedSubId
                                });

                                subChoices.setChoices(options, 'value', 'label', true);
                                if (selectedSubId) {
                                    subChoices.setChoiceByValue(String(selectedSubId));
                                }

                                const currentSubVal = selectedSubId || $(subSelect).val();
                                if (currentSubVal) {
                                    $.ajax({
                                        url: "{{ route('project.by.cluster', $params) }}",
                                        type: 'GET',
                                        data: { program_sub_id: currentSubVal },
                                        success: function(clusterResponse) {
                                            if (Array.isArray(clusterResponse) && clusterChoices) {
                                                const clusterOptions = clusterResponse.map(item => ({
                                                    value: String(item.value),
                                                    label: item.label,
                                                    selected: String(item.value) === String(selectedClusterId)
                                                }));
                                                clusterOptions.unshift({
                                                    value: '',
                                                    label: '{{ __('forms.search...') }}',
                                                    selected: !selectedClusterId
                                                });

                                                clusterChoices.setChoices(clusterOptions, 'value', 'label', true);
                                                if (selectedClusterId) {
                                                    clusterChoices.setChoiceByValue(String(selectedClusterId));
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });

            const dateInput = document.getElementById('date');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true
                });
            }

            let pristine;
            const pristineConfig = {
                classTo: 'form-group',
                errorClass: 'has-danger',
                successClass: 'has-success',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'pristine-error text-help'
            };

            function reinitPristine() {
                if (pristine) pristine.destroy();
                if (form) {
                    pristine = new Pristine(form, pristineConfig, true);
                    setupSummernoteValidation();
                }
            }

            function setupSummernoteValidation() {
                const summernoteValidator = function(value, element) {
                    if (!element) return false;
                    const code = $(element).summernote('code');
                    const plainText = code.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                    return plainText.length > 0;
                };
                const noteElem = document.getElementById('vNote');
                const referElem = document.getElementById('vRefer');
                const defaultMsg = "{{ __('messages.required') }}";
                if (noteElem && noteElem.hasAttribute('required')) {
                    const msg = noteElem.getAttribute('data-pristine-required-message') || defaultMsg;
                    pristine.addValidator(noteElem, summernoteValidator, msg, 1, false);
                }
                if (referElem && referElem.hasAttribute('required')) {
                    const msg = referElem.getAttribute('data-pristine-required-message') || defaultMsg;
                    pristine.addValidator(referElem, summernoteValidator, msg, 1, false);
                }
            }

            function clearPristineError(input) {
                const parent = input.closest('.form-group');
                if (!parent) return;
                parent.classList.remove('has-danger', 'has-success');
                const error = parent.querySelector('.pristine-error');
                if (error) error.remove();
            }

            const skipItemTable = document.getElementById('skipItemTable');
            const itemTable = document.getElementById('itemTable');
            const itemTableWrapper = document.getElementById('itemTableWrapper');

            function updateItemTableState() {
                if (!skipItemTable || !itemTable) return;
                const skipped = skipItemTable.checked;

                if (itemTableWrapper) {
                    itemTableWrapper.classList.toggle('table-skipped', skipped);
                }

                itemTable.querySelectorAll('tbody tr').forEach(function(row) {
                    row.querySelectorAll('input').forEach(function(input) {
                        clearPristineError(input);
                        if (skipped) {
                            input.disabled = true;
                            input.removeAttribute('required');
                            input.classList.add('bg-light', 'text-muted');
                        } else {
                            input.disabled = false;
                            input.classList.remove('bg-light', 'text-muted');
                            if (input.classList.contains('table-required')) {
                                input.setAttribute('required', 'required');
                            }
                        }
                    });

                    row.querySelectorAll('select').forEach(function(select) {
                        const choicesInstance = choicesMap.get(select);
                        if (skipped) {
                            select.disabled = true;
                            if (choicesInstance) choicesInstance.disable();
                        } else {
                            select.disabled = false;
                            if (choicesInstance) choicesInstance.enable();
                        }
                    });

                    row.querySelectorAll('.addRow, .removeRow, .addCategoryBtn, .removeCategoryBtn')
                        .forEach(function(button) {
                            button.disabled = skipped;
                        });
                });

                reinitPristine();
            }

            if (skipItemTable) {
                skipItemTable.addEventListener('change', updateItemTableState);
                updateItemTableState();
            }

            reinitPristine();

            $(document).on('change', '.cboProgram', function() {
                const row = $(this).closest('tr');
                const programId = $(this).val();
                const subProgramElement = row.find('.cboProgramSub')[0];
                const clusterElement = row.find('.cboCluster')[0];

                const subProgramChoices = choicesMap.get(subProgramElement);
                const clusterChoices = choicesMap.get(clusterElement);

                if (subProgramChoices) {
                    subProgramChoices.clearChoices();
                    subProgramChoices.setChoices([{ value: '', label: '{{ __('forms.search...') }}', selected: true }], 'value', 'label', true);
                }

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{ value: '', label: '{{ __('forms.search...') }}', selected: true }], 'value', 'label', true);
                }

                if (!programId) return;

                $.ajax({
                    url: "{{ route('project.by.program_sub', $params) }}",
                    type: 'GET',
                    data: { program_id: programId },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 && subProgramChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({ value: '', label: '{{ __('forms.search...') }}', selected: true });
                            subProgramChoices.setChoices(formattedOptions, 'value', 'label', true);
                            subProgramChoices.showDropdown();
                        }
                    }
                });
            });

            $(document).on('change', '.cboProgramSub', function() {
                const row = $(this).closest('tr');
                const programSubId = $(this).val();
                const clusterElement = row.find('.cboCluster')[0];
                const clusterChoices = choicesMap.get(clusterElement);

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{ value: '', label: '{{ __('forms.search...') }}', selected: true }], 'value', 'label', true);
                }

                if (!programSubId) return;

                $.ajax({
                    url: "{{ route('project.by.cluster', $params) }}",
                    type: 'GET',
                    data: { program_sub_id: programSubId },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 && clusterChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({ value: '', label: '{{ __('forms.search...') }}', selected: true });
                            clusterChoices.setChoices(formattedOptions, 'value', 'label', true);
                            clusterChoices.showDropdown();
                        }
                    }
                });
            });

            $(document).on('click', '.addCategoryBtn', function() {
                const tr = $(this).closest('tr');
                const mainRowIndex = tr.attr('data-row-index') || tr.index();

                tr.find('.category-wrapper').append(`
                    <div class="d-flex mb-2 category-item">
                        <input type="text" class="form-control me-1" name="category[${mainRowIndex}][]" placeholder="បញ្ចូលប្រភេទ">
                        <button type="button" class="btn btn-outline-danger btn-sm removeCategoryBtn">-</button>
                    </div>
                `);

                tr.find('.subcategory-wrapper').append(`
                    <div class="d-flex mb-2 subcategory-item">
                        <input type="text" class="form-control me-1" name="sub_category[${mainRowIndex}][]" placeholder="បញ្ចូលប្រភេទរង">
                        <button type="button" class="btn btn-outline-danger btn-sm removeCategoryBtn" style="visibility: hidden;">-</button>
                    </div>
                `);
            });

            $(document).on('click', '.removeCategoryBtn', function() {
                const tr = $(this).closest('tr');
                const itemDiv = $(this).closest('.category-item, .subcategory-item');
                const index = itemDiv.index();

                tr.find('.category-wrapper .category-item').eq(index).remove();
                tr.find('.subcategory-wrapper .subcategory-item').eq(index).remove();
            });

            $(document).on('click', '.addRow', function() {
                const tableBody = $('#itemTable tbody');
                let rowIndex = tableBody.children('tr').length;

                const newRowHtml = `
                    <tr data-row-index="${rowIndex}">
                        <td>
                            <input type="text" name="sub_pro[]" class="form-control table-required" required>
                        </td>
                        <td>
                            <div class="category-wrapper">
                                <div class="d-flex mb-2 category-item">
                                    <input type="text" class="form-control me-1" name="category[${rowIndex}][]" placeholder="បញ្ចូលប្រភេទ">
                                    <button type="button" class="btn btn-outline-success btn-sm addCategoryBtn">+</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="subcategory-wrapper">
                                <div class="d-flex mb-2 subcategory-item">
                                    <input type="text" class="form-control me-1" name="sub_category[${rowIndex}][]" placeholder="បញ្ចូលប្រភេទរង">
                                    <button type="button" class="btn btn-outline-success btn-sm addSubCategoryBtn" style="visibility: hidden;">+</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <select class="form-select cboSubAccount" name="accountSub[]">
                                <option value="">ជ្រើសរើស</option>
                                @foreach ($accountSub as $bv)
                                    <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboProgram" name="program[]">
                                <option value="">ជ្រើសរើស</option>
                                @foreach ($program as $p)
                                    <option value="{{ $p->id }}">{{ $p->no }}-{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboProgramSub" name="cboProgramSub[]">
                                <option value="">{{ __('forms.search...') }}</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select cboCluster" name="cboCluster[]">
                                <option value="">{{ __('forms.search...') }}</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger removeRow">-</button>
                        </td>
                    </tr>`;

                tableBody.append(newRowHtml);
                const appendedRow = tableBody.children().last();
                appendedRow.find('select').each(function() {
                    initChoicesInstance(this);
                });
                updateRowIndices();
                updateItemTableState();
            });

            $(document).on('click', '.removeRow', function() {
                const row = $(this).closest('tr');
                row.find('select').each(function() {
                    const instance = choicesMap.get(this);
                    if (instance) instance.destroy();
                });
                row.remove();
                updateRowIndices();
                reinitPristine();
            });

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if ($('#vNote').length) $('#vNote').val($('#vNote').summernote('code'));
                    if ($('#vRefer').length) $('#vRefer').val($('#vRefer').summernote('code'));

                    const skipTableSwitch = document.getElementById('skipItemTable');
                    const isTableSkipped = skipTableSwitch && skipTableSwitch.checked;

                    if (isTableSkipped) {
                        $('#itemTable').find('input, select').prop('disabled', true).removeAttr('required');
                    }

                    let valid = false;
                    try {
                        valid = pristine ? pristine.validate() : true;
                    } catch (err) {
                        console.error("Pristine validation error:", err);
                        valid = true;
                    }

                    if (valid) {
                        const submitBtn = document.getElementById('insertToTableBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                កំពុងរក្សាទុក...
                            `;
                        }
                        form.submit();
                    } else {
                        const errorElement = form.querySelector('.has-danger, .pristine-error');
                        if (errorElement) {
                            errorElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });
            }
        });
    </script>
@endsection