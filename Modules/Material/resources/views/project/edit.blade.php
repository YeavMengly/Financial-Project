@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        #itemTableWrapper,
        /* .table-responsive {
                                width: 100% !important;
                                overflow-x: visible !important;
                                overflow-y: visible !important;
                                position: relative !important;
                                display: block;
                            } */

        /* #itemTableWrapper {
                                overflow-x: auto !important;
                                padding-top: 300px !important;
                                margin-top: -300px !important;
                            } */

        /* #itemTable {
                                table-layout: fixed !important;
                                width: 100% !important;
                                min-width: 1300px !important;
                            } */

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
                            enctype="multipart/form-data" novalidate>
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

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="titleInput" class="form-label mb-0">
                                                {{ __('forms.title') }}
                                            </label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipTitleInput" style="cursor:pointer;"
                                                    {{ empty($module->title) ? 'checked' : '' }}>
                                                <label class="form-check-label font-size-12 text-muted skip-label"
                                                    for="skipTitleInput" style="cursor:pointer;">
                                                    រំលង / មិនភ្ជាប់
                                                </label>
                                            </div>
                                        </div>
                                        <input type="text" id="titleInput" name="title" class="form-control"
                                            tabindex="8" value="{{ old('title', $module->title ?? '') }}"
                                            data-pristine-required-message="{{ __('messages.required') }}">

                                        @error('title')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="fileInput" class="form-label mb-0">
                                                {{ __('forms.file.type') }}
                                            </label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipFileInput" style="cursor:pointer;"
                                                    {{ empty($module->file) ? 'checked' : '' }}>
                                                <label class="form-check-label font-size-12 text-muted skip-label"
                                                    for="skipFileInput" style="cursor:pointer;">
                                                    រំលង / មិនភ្ជាប់
                                                </label>
                                            </div>
                                        </div>
                                        <input type="file" id="fileInput" name="file[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple data-max-size="5"
                                            data-allowed-extensions="pdf,doc,docx">

                                        <small class="form-text text-muted">
                                            Allowed types: PDF, DOC, DOCX (Max: 5MB per file)
                                        </small>

                                        @error('file.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- =====================================================
                                    SECTION 2: PROJECT TABLE (EDIT - 4 TDs + ACTION)
                                ====================================================== --}}
                                @php
                                    $isTableChecked = old('skipItemTable', $isTableSkipped ?? false);
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">
                                        {{ __('forms.sub.project') }}
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="skipItemTable" name="skipItemTable" value="1"
                                            style="cursor:pointer;" {{ $isTableChecked ? 'checked' : '' }}>
                                        <label class="form-check-label font-size-12 text-muted skip-label"
                                            for="skipItemTable">
                                            រំលង / មិនភ្ជាប់
                                        </label>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 mt-3" id="itemTableWrapper">
                                    <div class="table-responsive">
                                        <div class="card mb-3">
                                            <div class="card-body p-0">
                                                <table class="table table-bordered align-middle text-nowrap mb-0"
                                                    id="itemTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="align-middle">
                                                                {{ __('forms.project') }}
                                                            </th>
                                                            <th class="align-middle">
                                                                ប្រភេទ (Category)
                                                            </th>
                                                            <th class="align-middle">
                                                                ប្រភេទរង (Sub-category)
                                                            </th>
                                                            <th class="align-middle">
                                                                ព័ត៌មានលម្អិត (Detail)
                                                            </th>
                                                            {{-- <th style="width:150px; min-width:150px;" class="align-middle">
                                                                {{ __('forms.sub.account') }}
                                                            </th>
                                                            <th style="width:150px; min-width:150px;" class="align-middle">
                                                                {{ __('forms.program') }}
                                                            </th>
                                                            <th style="width:150px; min-width:150px;" class="align-middle">
                                                                {{ __('forms.program.sub') }}
                                                            </th>
                                                            <th style="width:150px; min-width:150px;" class="align-middle">
                                                                {{ __('forms.cluster') }}
                                                            </th> --}}
                                                            <th style="width:85px; min-width:85px;"
                                                                class="text-center align-middle">
                                                                Action
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $rowCollection =
                                                                isset($groupedRows) && $groupedRows->count() > 0
                                                                    ? $groupedRows->values()
                                                                    : (isset($items) && count($items) > 0
                                                                        ? collect([$items])
                                                                        : collect([[$module]]));
                                                            $rowCounts = $rowCollection->count();
                                                        @endphp

                                                        @for ($i = 0; $i < max(1, $rowCounts); $i++)
                                                            @php
                                                                $rowGroup = $rowCollection[$i] ?? [];
                                                                $firstItem =
                                                                    is_iterable($rowGroup) && count($rowGroup) > 0
                                                                        ? (is_array($rowGroup)
                                                                            ? reset($rowGroup)
                                                                            : $rowGroup->first())
                                                                        : $rowGroup;

                                                                $valSubPro = old(
                                                                    "sub_pro.$i",
                                                                    $firstItem->sub_project ??
                                                                        ($firstItem->sub_pro ?? ''),
                                                                );
                                                                $valAccountSub = old(
                                                                    "accountSub.$i",
                                                                    $firstItem->account_sub_id ?? '',
                                                                );
                                                                $valProgram = old(
                                                                    "program.$i",
                                                                    $firstItem->program_id ?? '',
                                                                );
                                                                $valProgramSub = old(
                                                                    "cboProgramSub.$i",
                                                                    $firstItem->program_sub_id ?? '',
                                                                );
                                                                $valCluster = old(
                                                                    "cboCluster.$i",
                                                                    $firstItem->cluster_id ?? '',
                                                                );

                                                                $structuredData = [];
                                                                if (is_iterable($rowGroup)) {
                                                                    foreach ($rowGroup as $rItem) {
                                                                        $cat = trim((string) ($rItem->category ?? ''));
                                                                        $subCat = trim(
                                                                            (string) ($rItem->sub_category ?? ''),
                                                                        );
                                                                        $detail = trim((string) ($rItem->detail ?? ''));

                                                                        if (
                                                                            $cat !== '' ||
                                                                            $subCat !== '' ||
                                                                            $detail !== ''
                                                                        ) {
                                                                            $catKey = $cat !== '' ? $cat : '';
                                                                            if (!isset($structuredData[$catKey])) {
                                                                                $structuredData[$catKey] = [];
                                                                            }
                                                                            $subKey = $subCat !== '' ? $subCat : '';
                                                                            if (
                                                                                !isset(
                                                                                    $structuredData[$catKey][$subKey],
                                                                                )
                                                                            ) {
                                                                                $structuredData[$catKey][$subKey] = [];
                                                                            }
                                                                            if ($detail !== '') {
                                                                                $structuredData[$catKey][
                                                                                    $subKey
                                                                                ][] = $detail;
                                                                            } elseif (
                                                                                empty($structuredData[$catKey][$subKey])
                                                                            ) {
                                                                                $structuredData[$catKey][$subKey] = [
                                                                                    '',
                                                                                ];
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                if (empty($structuredData)) {
                                                                    $structuredData = ['' => ['' => ['']]];
                                                                }

                                                                // Total leaf rows for this project group
                                                                $projectSpan = 0;
                                                                foreach ($structuredData as $subCats) {
                                                                    foreach ($subCats as $details) {
                                                                        $projectSpan += max(1, count($details));
                                                                    }
                                                                }

                                                                $catCount = count($structuredData);
                                                                $isFirstProjectRow = true;
                                                            @endphp

                                                            @foreach ($structuredData as $catName => $subCats)
                                                                @php
                                                                    $k = $loop->index;
                                                                    $catSpan = 0;
                                                                    foreach ($subCats as $details) {
                                                                        $catSpan += max(1, count($details));
                                                                    }
                                                                    $subCatCount = count($subCats);
                                                                    $isFirstCatRow = true;
                                                                @endphp

                                                                @foreach ($subCats as $subCatName => $details)
                                                                    @php
                                                                        $subIdx = $loop->index;
                                                                        $detailList = !empty($details)
                                                                            ? $details
                                                                            : [''];
                                                                        $subSpan = count($detailList);
                                                                        $isFirstSubRow = true;
                                                                    @endphp

                                                                    @foreach ($detailList as $dIdx => $detailVal)
                                                                        <tr data-row-index="{{ $i }}"
                                                                            data-cat-index="{{ $k }}"
                                                                            data-subcat-index="{{ $subIdx }}">

                                                                            {{-- TD 1: Project (once per project group) --}}
                                                                            @if ($isFirstProjectRow)
                                                                                <td data-col="project"
                                                                                    rowspan="{{ $projectSpan }}">
                                                                                    @if (!empty($firstItem->id))
                                                                                        <input type="hidden"
                                                                                            name="item_id[{{ $i }}][]"
                                                                                            value="{{ $firstItem->id }}">
                                                                                    @endif
                                                                                    <input type="text"
                                                                                        name="sub_pro[{{ $i }}]"
                                                                                        value="{{ $valSubPro }}"
                                                                                        class="form-control">
                                                                                </td>
                                                                            @endif

                                                                            {{-- TD 2: Category (once per category group) --}}
                                                                            @if ($isFirstCatRow)
                                                                                <td data-col="category"
                                                                                    rowspan="{{ $catSpan }}">
                                                                                    <div
                                                                                        class="d-flex align-items-center category-item">
                                                                                        <input type="text"
                                                                                            class="form-control me-1"
                                                                                            name="category[{{ $i }}][{{ $k }}]"
                                                                                            value="{{ $catName }}"
                                                                                            placeholder="បញ្ចូលប្រភេទ (Category)">
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-success btn-sm addCategoryBtn"
                                                                                            title="បន្ថែមប្រភេទ">+</button>
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-danger btn-sm removeCategoryBtn ms-1"
                                                                                            title="លុបប្រភេទ"
                                                                                            style="{{ $catCount <= 1 ? 'visibility: hidden;' : '' }}">-</button>
                                                                                    </div>
                                                                                </td>
                                                                            @endif

                                                                            {{-- TD 3: Sub-category (once per sub-category group) --}}
                                                                            @if ($isFirstSubRow)
                                                                                <td data-col="subcategory"
                                                                                    rowspan="{{ $subSpan }}">
                                                                                    <div
                                                                                        class="d-flex align-items-center subcategory-item">
                                                                                        <input type="text"
                                                                                            class="form-control me-1"
                                                                                            name="sub_category[{{ $i }}][{{ $k }}][{{ $subIdx }}]"
                                                                                            value="{{ $subCatName }}"
                                                                                            placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-success btn-sm addSubCategoryBtn"
                                                                                            title="បន្ថែមប្រភេទរង">+</button>
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-danger btn-sm removeSubCategoryBtn ms-1"
                                                                                            title="លុបប្រភេទរង"
                                                                                            style="{{ $subCatCount <= 1 ? 'visibility: hidden;' : '' }}">-</button>
                                                                                    </div>
                                                                                </td>
                                                                            @endif

                                                                            {{-- TD 4: Detail (every leaf row) --}}
                                                                            <td data-col="detail">
                                                                                <div
                                                                                    class="d-flex align-items-center detail-item">
                                                                                    <input type="text"
                                                                                        class="form-control me-1"
                                                                                        name="detail[{{ $i }}][{{ $k }}][{{ $subIdx }}][]"
                                                                                        value="{{ $detailVal }}"
                                                                                        placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-success btn-sm addDetailBtn"
                                                                                        title="បន្ថែមលម្អិត">+</button>
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-danger btn-sm removeDetailBtn ms-1"
                                                                                        title="លុបលម្អិត"
                                                                                        style="{{ $subSpan <= 1 ? 'visibility: hidden;' : '' }}">-</button>
                                                                                </div>
                                                                            </td>

                                                                            {{-- Hidden Choices.js selects on first project row --}}
                                                                            @if ($isFirstProjectRow)
                                                                                <td style="display: none;">
                                                                                    <select
                                                                                        class="form-select cboSubAccount"
                                                                                        name="accountSub[{{ $i }}]">
                                                                                        <option value="">ជ្រើសរើស
                                                                                        </option>
                                                                                        @foreach ($accountSub as $bv)
                                                                                            <option
                                                                                                value="{{ $bv->no }}"
                                                                                                {{ $valAccountSub == $bv->no ? 'selected' : '' }}>
                                                                                                {{ $bv->no }}-{{ $bv->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>

                                                                                <td style="display: none;">
                                                                                    <select class="form-select cboProgram"
                                                                                        name="program[{{ $i }}]">
                                                                                        <option value="">ជ្រើសរើស
                                                                                        </option>
                                                                                        @foreach ($program as $p)
                                                                                            <option
                                                                                                value="{{ $p->id }}"
                                                                                                {{ $valProgram == $p->id ? 'selected' : '' }}>
                                                                                                {{ $p->no }}-{{ $p->title }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>

                                                                                <td style="display: none;">
                                                                                    <select
                                                                                        class="form-select cboProgramSub"
                                                                                        name="cboProgramSub[{{ $i }}]"
                                                                                        data-program-id="{{ $valProgram }}"
                                                                                        data-selected="{{ $valProgramSub }}">
                                                                                        <option value="">
                                                                                            {{ __('forms.search...') }}
                                                                                        </option>
                                                                                    </select>
                                                                                </td>

                                                                                <td style="display: none;">
                                                                                    <select class="form-select cboCluster"
                                                                                        name="cboCluster[{{ $i }}]"
                                                                                        data-program-sub-id="{{ $valProgramSub }}"
                                                                                        data-selected="{{ $valCluster }}">
                                                                                        <option value="">
                                                                                            {{ __('forms.search...') }}
                                                                                        </option>
                                                                                    </select>
                                                                                </td>

                                                                                {{-- Action TD (once per project group) --}}
                                                                                <td data-col="action"
                                                                                    rowspan="{{ $projectSpan }}"
                                                                                    class="text-center align-middle">
                                                                                    @if ($i === 0)
                                                                                        <button type="button"
                                                                                            class="btn btn-success btn-sm addRow"
                                                                                            title="បន្ថែមជួរដេក">+</button>
                                                                                    @else
                                                                                        <button type="button"
                                                                                            class="btn btn-danger btn-sm removeRow"
                                                                                            title="លុបជួរដេក">-</button>
                                                                                    @endif
                                                                                </td>
                                                                            @endif
                                                                        </tr>

                                                                        @php
                                                                            $isFirstProjectRow = false;
                                                                            $isFirstCatRow = false;
                                                                            $isFirstSubRow = false;
                                                                        @endphp
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- =====================================================
                            SECTION 3: NOTE, REFER & ACTIONS
                        ====================================================== --}}
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

                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-primary"
                                            id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                        <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                            <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                        </a>
                                        <a class="btn btn-outline-secondary"
                                            href="{{ route('project.index', $params) }}">
                                            <i class="bx bx-arrow-back me-1"></i> {{ __('buttons.back') }}
                                        </a>
                                    </div>
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
            if (choicesMap.has(element)) {
                return choicesMap.get(element);
            }
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

        function destroyChoicesInstance(element) {
            if (choicesMap.has(element)) {
                const instance = choicesMap.get(element);
                if (instance && typeof instance.destroy === 'function') {
                    instance.destroy();
                }
                choicesMap.delete(element);
                element.classList.remove('choices-initialized');
            }
        }

        let groupUidCounter = 1;

        function nextUid(prefix) {
            return `${prefix}_${groupUidCounter++}`;
        }

        function setupSkipToggle(checkboxId, inputId) {
            const checkbox = document.getElementById(checkboxId);
            const input = document.getElementById(inputId);
            if (!checkbox || !input) return;

            const requiredMessage = input.getAttribute('data-pristine-required-message') || '';

            function updateState() {
                const isSkipped = checkbox.checked;

                if (isSkipped) {
                    input.value = '';
                    input.disabled = true;
                    input.setAttribute('disabled', 'disabled');
                    input.setAttribute('tabindex', '-1');
                    input.removeAttribute('required');
                    input.removeAttribute('data-pristine-required-message');
                    input.style.pointerEvents = 'none';
                    input.classList.add('bg-success-subtle', 'text-success-emphasis', 'border-success', 'opacity-75');
                } else {
                    input.disabled = false;
                    input.removeAttribute('disabled');
                    input.removeAttribute('tabindex');
                    input.style.pointerEvents = '';
                    if (requiredMessage) {
                        input.setAttribute('required', 'required');
                        input.setAttribute('data-pristine-required-message', requiredMessage);
                    }
                    input.classList.remove('bg-success-subtle', 'text-success-emphasis', 'border-success', 'opacity-75');
                }
            }

            checkbox.addEventListener('change', updateState);
            updateState();
        }

        setupSkipToggle('skipTitleInput', 'titleInput');
        setupSkipToggle('skipFileInput', 'fileInput');

        /* =========================================================
         * GROUP & ROWSPAN / INDEX SYNC FOR 4-TD EDIT + CREATE TABLE
         * ========================================================= */
        function ensureRowGroupIds() {
            const $rows = $('#itemTable tbody tr');
            let curProjectUid = null;
            let curCatUid = null;
            let curSubUid = null;

            $rows.each(function() {
                const $tr = $(this);

                if ($tr.find('td[data-col="project"]').length) {
                    curProjectUid = $tr.attr('data-puid') || nextUid('p');
                    $tr.attr('data-puid', curProjectUid);

                    if ($tr.find('td[data-col="category"]').length) {
                        curCatUid = $tr.attr('data-cuid') || nextUid('c');
                        $tr.attr('data-cuid', curCatUid);
                    }
                    if ($tr.find('td[data-col="subcategory"]').length) {
                        curSubUid = $tr.attr('data-suid') || nextUid('s');
                        $tr.attr('data-suid', curSubUid);
                    }
                } else if ($tr.find('td[data-col="category"]').length) {
                    curCatUid = $tr.attr('data-cuid') || nextUid('c');
                    $tr.attr('data-cuid', curCatUid);

                    if ($tr.find('td[data-col="subcategory"]').length) {
                        curSubUid = $tr.attr('data-suid') || nextUid('s');
                        $tr.attr('data-suid', curSubUid);
                    }
                } else if ($tr.find('td[data-col="subcategory"]').length) {
                    curSubUid = $tr.attr('data-suid') || nextUid('s');
                    $tr.attr('data-suid', curSubUid);
                }

                $tr.attr('data-puid', curProjectUid);
                $tr.attr('data-cuid', curCatUid);
                $tr.attr('data-suid', curSubUid);
            });
        }

        function updateRowIndices() {
            ensureRowGroupIds();

            const $rows = $('#itemTable tbody tr');
            $rows.find('td').removeAttr('rowspan');

            const projectOrder = [];
            const catOrderByProject = {};
            const subOrderByCat = {};

            $rows.each(function() {
                const $tr = $(this);
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const suid = $tr.attr('data-suid');

                if (!projectOrder.includes(puid)) {
                    projectOrder.push(puid);
                    catOrderByProject[puid] = [];
                }
                if (!catOrderByProject[puid].includes(cuid)) {
                    catOrderByProject[puid].push(cuid);
                    subOrderByCat[cuid] = [];
                }
                if (!subOrderByCat[cuid].includes(suid)) {
                    subOrderByCat[cuid].push(suid);
                }

                const rowIdx = projectOrder.indexOf(puid);
                const catIdx = catOrderByProject[puid].indexOf(cuid);
                const subIdx = subOrderByCat[cuid].indexOf(suid);

                $tr.attr('data-row-index', rowIdx);
                $tr.attr('data-cat-index', catIdx);
                $tr.attr('data-subcat-index', subIdx);

                $tr.find('input[name^="sub_pro"]').attr('name', `sub_pro[${rowIdx}]`);
                $tr.find('input[name^="item_id"]').attr('name', `item_id[${rowIdx}][]`);

                $tr.find('select.cboSubAccount').attr('name', `accountSub[${rowIdx}]`);
                $tr.find('select.cboProgram').attr('name', `program[${rowIdx}]`);
                $tr.find('select.cboProgramSub').attr('name', `cboProgramSub[${rowIdx}]`);
                $tr.find('select.cboCluster').attr('name', `cboCluster[${rowIdx}]`);

                $tr.find('.category-item input').attr('name', `category[${rowIdx}][${catIdx}]`);
                $tr.find('.subcategory-item input').attr('name', `sub_category[${rowIdx}][${catIdx}][${subIdx}]`);
                $tr.find('.detail-item input').attr('name', `detail[${rowIdx}][${catIdx}][${subIdx}][]`);
            });

            projectOrder.forEach(function(puid) {
                const $pRows = $rows.filter(`[data-puid="${puid}"]`);
                const $pFirst = $pRows.first();
                $pFirst.find('td[data-col="project"], td[data-col="action"]').attr('rowspan', $pRows.length);

                const catList = catOrderByProject[puid] || [];
                catList.forEach(function(cuid, cIdx) {
                    const $cRows = $pRows.filter(`[data-cuid="${cuid}"]`);
                    const $cFirst = $cRows.first();
                    $cFirst.find('td[data-col="category"]').attr('rowspan', $cRows.length);

                    if (cIdx === 0) {
                        $cFirst.find('.removeCategoryBtn').remove();
                    }

                    const subList = subOrderByCat[cuid] || [];
                    subList.forEach(function(suid, sIdx) {
                        const $sRows = $cRows.filter(`[data-suid="${suid}"]`);
                        const $sFirst = $sRows.first();
                        $sFirst.find('td[data-col="subcategory"]').attr('rowspan', $sRows.length);

                        if (sIdx === 0) {
                            $sFirst.find('.removeSubCategoryBtn').remove();
                        }
                        $sFirst.find('.removeDetailBtn').remove();
                    });
                });
            });
        }

        // Toggle Table Skip / Disabled State on Change
        $(document).on('change', '#skipItemTable', function() {
            const wrapper = $('#itemTableWrapper');
            const controls = $('#itemTable').find('input, select, button');

            if (this.checked) {
                wrapper.addClass('table-skipped');
                controls.each(function() {
                    $(this).prop('disabled', true).removeAttr('required');
                    if ($(this).hasClass('choices-input') || $(this).data('choice')) {
                        const choiceInstance = choicesMap.get(this);
                        if (choiceInstance && typeof choiceInstance.disable === 'function') {
                            choiceInstance.disable();
                        }
                    }
                });
            } else {
                wrapper.removeClass('table-skipped');
                controls.each(function() {
                    $(this).prop('disabled', false);
                    if ($(this).hasClass('choices-input') || $(this).data('choice')) {
                        const choiceInstance = choicesMap.get(this);
                        if (choiceInstance && typeof choiceInstance.enable === 'function') {
                            choiceInstance.enable();
                        }
                    }
                });
            }
        });

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

            document.querySelectorAll('.cboProgram, .cboSubAccount, .cboProgramSub, .cboCluster').forEach(function(
                select) {
                initChoicesInstance(select);
            });

            // Pre-load cascade dropdowns for edit views
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
                        data: {
                            program_id: programVal
                        },
                        success: function(response) {
                            if (Array.isArray(response) && subChoices) {
                                const options = response.map(item => ({
                                    value: String(item.value),
                                    label: item.label,
                                    selected: String(item.value) === String(
                                        selectedSubId)
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
                                        data: {
                                            program_sub_id: currentSubVal
                                        },
                                        success: function(clusterResponse) {
                                            if (Array.isArray(clusterResponse) &&
                                                clusterChoices) {
                                                const clusterOptions =
                                                    clusterResponse.map(item => ({
                                                        value: String(item
                                                            .value),
                                                        label: item.label,
                                                        selected: String(
                                                                item.value
                                                                ) ===
                                                            String(
                                                                selectedClusterId
                                                                )
                                                    }));
                                                clusterOptions.unshift({
                                                    value: '',
                                                    label: '{{ __('forms.search...') }}',
                                                    selected: !
                                                        selectedClusterId
                                                });

                                                clusterChoices.setChoices(
                                                    clusterOptions, 'value',
                                                    'label', true);
                                                if (selectedClusterId) {
                                                    clusterChoices.setChoiceByValue(
                                                        String(
                                                            selectedClusterId));
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

            // Program -> Sub Program Cascade
            $(document).on('change', '.cboProgram', function() {
                const row = $(this).closest('tr');
                const programId = $(this).val();
                const subProgramElement = row.find('.cboProgramSub')[0];
                const clusterElement = row.find('.cboCluster')[0];

                const subProgramChoices = choicesMap.get(subProgramElement);
                const clusterChoices = choicesMap.get(clusterElement);

                if (subProgramChoices) {
                    subProgramChoices.clearChoices();
                    subProgramChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }
                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }

                if (!programId) return;

                $.ajax({
                    url: "{{ route('project.by.program_sub', $params) }}",
                    type: 'GET',
                    data: {
                        program_id: programId
                    },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 &&
                            subProgramChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });
                            subProgramChoices.setChoices(formattedOptions, 'value', 'label',
                                true);
                        }
                    }
                });
            });

            // Sub Program -> Cluster Cascade
            $(document).on('change', '.cboProgramSub', function() {
                const row = $(this).closest('tr');
                const programSubId = $(this).val();
                const clusterElement = row.find('.cboCluster')[0];
                const clusterChoices = choicesMap.get(clusterElement);

                if (clusterChoices) {
                    clusterChoices.clearChoices();
                    clusterChoices.setChoices([{
                        value: '',
                        label: '{{ __('forms.search...') }}',
                        selected: true
                    }], 'value', 'label', true);
                }

                if (!programSubId) return;

                $.ajax({
                    url: "{{ route('project.by.cluster', $params) }}",
                    type: 'GET',
                    data: {
                        program_sub_id: programSubId
                    },
                    success: function(response) {
                        if (Array.isArray(response) && response.length > 0 && clusterChoices) {
                            const formattedOptions = response.map(item => ({
                                value: String(item.value),
                                label: item.label,
                                selected: false
                            }));
                            formattedOptions.unshift({
                                value: '',
                                label: '{{ __('forms.search...') }}',
                                selected: true
                            });
                            clusterChoices.setChoices(formattedOptions, 'value', 'label', true);
                        }
                    }
                });
            });

            // 1. Add Category Row inside project group (has removeCategoryBtn since it is newly added)
            $(document).on('click', '.addCategoryBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const $projectRows = $(`#itemTable tbody tr[data-puid="${puid}"]`);
                const $lastInProject = $projectRows.last();
                const newCuid = nextUid('c');
                const newSuid = nextUid('s');

                const newTrHtml = `
                <tr data-puid="${puid}" data-cuid="${newCuid}" data-suid="${newSuid}">
                    <td data-col="category">
                        <div class="d-flex align-items-center category-item">
                            <input type="text" class="form-control me-1" name="category[][]" placeholder="បញ្ចូលប្រភេទ (Category)">
                            <button type="button" class="btn btn-outline-success btn-sm addCategoryBtn" title="បន្ថែមប្រភេទ">+</button>
                            <button type="button" class="btn btn-outline-danger btn-sm removeCategoryBtn ms-1" title="លុបប្រភេទ">-</button>
                        </div>
                    </td>
                    <td data-col="subcategory">
                        <div class="d-flex align-items-center subcategory-item">
                            <input type="text" class="form-control me-1" name="sub_category[][][]" placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
                            <button type="button" class="btn btn-outline-success btn-sm addSubCategoryBtn" title="បន្ថែមប្រភេទរង">+</button>
                        </div>
                    </td>
                    <td data-col="detail">
                        <div class="d-flex align-items-center detail-item">
                            <input type="text" class="form-control me-1" name="detail[][][][]" placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                            <button type="button" class="btn btn-outline-success btn-sm addDetailBtn" title="បន្ថែមលម្អិត">+</button>
                        </div>
                    </td>
                </tr>`;

                $lastInProject.after(newTrHtml);
                updateRowIndices();
            });

            // 2. Remove Category Group
            $(document).on('click', '.removeCategoryBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const $projectRows = $(`#itemTable tbody tr[data-puid="${puid}"]`);
                const $catRows = $projectRows.filter(`[data-cuid="${cuid}"]`);

                const uniqueCats = new Set();
                $projectRows.each(function() {
                    uniqueCats.add($(this).attr('data-cuid'));
                });

                if (uniqueCats.size > 1) {
                    $catRows.remove();
                    updateRowIndices();
                }
            });

            // 3. Add Sub-Category Row inside category group (has removeSubCategoryBtn since it is newly added)
            $(document).on('click', '.addSubCategoryBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const $catRows = $(`#itemTable tbody tr[data-puid="${puid}"][data-cuid="${cuid}"]`);
                const $lastInCat = $catRows.last();
                const newSuid = nextUid('s');

                const newSubHtml = `
                <tr data-puid="${puid}" data-cuid="${cuid}" data-suid="${newSuid}">
                    <td data-col="subcategory">
                        <div class="d-flex align-items-center subcategory-item">
                            <input type="text" class="form-control me-1" name="sub_category[][][]" placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
                            <button type="button" class="btn btn-outline-success btn-sm addSubCategoryBtn" title="បន្ថែមប្រភេទរង">+</button>
                            <button type="button" class="btn btn-outline-danger btn-sm removeSubCategoryBtn ms-1" title="លុបប្រភេទរង">-</button>
                        </div>
                    </td>
                    <td data-col="detail">
                        <div class="d-flex align-items-center detail-item">
                            <input type="text" class="form-control me-1" name="detail[][][][]" placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                            <button type="button" class="btn btn-outline-success btn-sm addDetailBtn" title="បន្ថែមលម្អិត">+</button>
                        </div>
                    </td>
                </tr>`;

                $lastInCat.after(newSubHtml);
                updateRowIndices();
            });

            // 4. Remove Sub-Category Group
            $(document).on('click', '.removeSubCategoryBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const suid = $tr.attr('data-suid');
                const $catRows = $(`#itemTable tbody tr[data-puid="${puid}"][data-cuid="${cuid}"]`);
                const $subRows = $catRows.filter(`[data-suid="${suid}"]`);

                const uniqueSubs = new Set();
                $catRows.each(function() {
                    uniqueSubs.add($(this).attr('data-suid'));
                });

                if (uniqueSubs.size > 1) {
                    $subRows.remove();
                    updateRowIndices();
                }
            });

            // 5. Add Detail Row inside sub-category group (has removeDetailBtn since it is newly added)
            $(document).on('click', '.addDetailBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const suid = $tr.attr('data-suid');
                const $subRows = $(
                    `#itemTable tbody tr[data-puid="${puid}"][data-cuid="${cuid}"][data-suid="${suid}"]`
                );
                const $lastInSub = $subRows.last();

                const newDetailHtml = `
                <tr data-puid="${puid}" data-cuid="${cuid}" data-suid="${suid}">
                    <td data-col="detail">
                        <div class="d-flex align-items-center detail-item">
                            <input type="text" class="form-control me-1" name="detail[][][][]" placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                            <button type="button" class="btn btn-outline-success btn-sm addDetailBtn" title="បន្ថែមលម្អិត">+</button>
                            <button type="button" class="btn btn-outline-danger btn-sm removeDetailBtn ms-1" title="លុបលម្អិត">-</button>
                        </div>
                    </td>
                </tr>`;

                $lastInSub.after(newDetailHtml);
                updateRowIndices();
            });

            // 6. Remove Detail Row
            $(document).on('click', '.removeDetailBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $tr = $(this).closest('tr');
                const puid = $tr.attr('data-puid');
                const cuid = $tr.attr('data-cuid');
                const suid = $tr.attr('data-suid');
                const $subRows = $(
                    `#itemTable tbody tr[data-puid="${puid}"][data-cuid="${cuid}"][data-suid="${suid}"]`
                );

                if ($subRows.length > 1) {
                    $tr.remove();
                    updateRowIndices();
                }
            });

            // 7. Add Main Project Row
            $(document).on('click', '.addRow', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const tableBody = $('#itemTable tbody');
                const newPuid = nextUid('p');
                const newCuid = nextUid('c');
                const newSuid = nextUid('s');
                const rowIndex = tableBody.find('td[data-col="project"]').length;

                const newRowHtml = `
                <tr data-row-index="${rowIndex}" data-cat-index="0" data-subcat-index="0" data-puid="${newPuid}" data-cuid="${newCuid}" data-suid="${newSuid}">
                    <td data-col="project">
                        <input type="hidden" name="item_id[${rowIndex}][]" value="">
                        <input type="text" name="sub_pro[${rowIndex}]" class="form-control">
                    </td>
                    <td data-col="category">
                        <div class="d-flex align-items-center category-item">
                            <input type="text" class="form-control me-1" name="category[${rowIndex}][0]" placeholder="បញ្ចូលប្រភេទ (Category)">
                            <button type="button" class="btn btn-outline-success btn-sm addCategoryBtn" title="បន្ថែមប្រភេទ">+</button>
                        </div>
                    </td>
                    <td data-col="subcategory">
                        <div class="d-flex align-items-center subcategory-item">
                            <input type="text" class="form-control me-1" name="sub_category[${rowIndex}][0][0]" placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
                            <button type="button" class="btn btn-outline-success btn-sm addSubCategoryBtn" title="បន្ថែមប្រភេទរង">+</button>
                        </div>
                    </td>
                    <td data-col="detail">
                        <div class="d-flex align-items-center detail-item">
                            <input type="text" class="form-control me-1" name="detail[${rowIndex}][0][0][]" placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                            <button type="button" class="btn btn-outline-success btn-sm addDetailBtn" title="បន្ថែមលម្អិត">+</button>
                        </div>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboSubAccount" name="accountSub[${rowIndex}]">
                            <option value="">ជ្រើសរើស</option>
                            @foreach ($accountSub as $bv)
                                <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboProgram" name="program[${rowIndex}]">
                            <option value="">ជ្រើសរើស</option>
                            @foreach ($program as $p)
                                <option value="{{ $p->id }}">{{ $p->no }}-{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboProgramSub" name="cboProgramSub[${rowIndex}]">
                            <option value="">{{ __('forms.search...') }}</option>
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboCluster" name="cboCluster[${rowIndex}]">
                            <option value="">{{ __('forms.search...') }}</option>
                        </select>
                    </td>
                    <td data-col="action" class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm removeRow" title="លុបជួរដេក">-</button>
                    </td>
                </tr>`;

                tableBody.append(newRowHtml);
                const appendedRow = tableBody.children().last();

                appendedRow.find('select').each(function() {
                    initChoicesInstance(this);
                });

                updateRowIndices();
            });

            // 8. Remove Main Project Group
            $(document).on('click', '.removeRow', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const puid = $(this).closest('tr').attr('data-puid');
                const $groupRows = $(`#itemTable tbody tr[data-puid="${puid}"]`);

                if ($('#itemTable tbody tr[data-puid]').map(function() {
                        return $(this).attr('data-puid');
                    }).get().filter((v, i, a) => a.indexOf(v) === i).length > 1) {
                    $groupRows.find('select').each(function() {
                        destroyChoicesInstance(this);
                    });
                    $groupRows.remove();
                    updateRowIndices();
                } else {
                    alert('ត្រូវមានយ៉ាងហោចណាស់ ១ បន្ទាត់!');
                }
            });

            // Form Submit Handler
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if ($('#vNote').length) $('#vNote').val($('#vNote').summernote('code'));
                    if ($('#vRefer').length) $('#vRefer').val($('#vRefer').summernote('code'));

                    const skipTableSwitch = document.getElementById('skipItemTable');
                    if (skipTableSwitch && skipTableSwitch.checked) {
                        $('#itemTable').find('input, select').prop('disabled', true).removeAttr('required');
                    }

                    const submitBtn = document.getElementById('insertToTableBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> កំពុងរក្សាទុក...`;
                    }
                    form.submit();
                });
            }

            // Sync spans, group IDs, and skip toggle state on initial load
            updateRowIndices();
            if ($('#skipItemTable').is(':checked')) {
                $('#skipItemTable').trigger('change');
            }
        });
    </script>
@endsection
