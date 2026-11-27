@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        /* =========================================================
                                                                                                                                                                 * TABLE WRAPPER & SCROLL FIXES
                                                                                                                                                                 * ========================================================= */
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
                                    table-layout: auto !important;
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
            pointer-events: auto !important;
        }

        #itemTable input.form-control,
        .form-group input,
        .form-group textarea {
            pointer-events: auto !important;
            position: relative;
            z-index: 5;
        }

        /* Base style when switch is unchecked */
        .skip-label {
            color: #495057;
            transition: all 0.2s ease-in-out;
        }

        /* Bold blue highlight when switch is checked */
        .form-check-input:checked~.skip-label {
            color: #223fe6 !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        /* =========================================================
                                                                                                                                                                 * CHOICES.JS STYLING & STRICT INPUT-WIDTH CONTROL
                                                                                                                                                                 * ========================================================= */
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

        /* =========================================================
                                                                                                                                                 * FIX SWITCH CLICKABILITY & OVERLAPS
                                                                                                                                                 * ========================================================= */
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

        /* Fix negative margin container overlap */
        #itemTableWrapper {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
@endsection

@section('content')
    <!-- PAGE TITLE -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.project') }}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ __('menus.project') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ __('menus.entry') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $ministry->year ?? '' }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('buttons.create') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM CARD SECTION -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="pristine-valid-example" action="{{ route('project.store', $params) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- =====================================================
                            SECTION 1: MAIN INFORMATION
                        ====================================================== --}}
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_number">{{ __('forms.stock.number') }}</label>
                                    <input type="text" id="stock_number" name="stock_number" required
                                        class="form-control" tabindex="1"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('stock_number') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_name">{{ __('forms.stock.name') }}</label>
                                    <input type="text" id="stock_name" name="stock_name" required class="form-control"
                                        tabindex="2" data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('stock_name') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="company_name">{{ __('forms.company.name') }}</label>
                                    <input type="text" id="company_name" name="company_name" required
                                        class="form-control" tabindex="3"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('company_name') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_voucher">
                                        កាលបរិច្ឆេទ{{ __('forms.warehouse.voucher') }}ក្រុមហ៊ុន
                                    </label>
                                    <input type="text" id="warehouse_voucher" name="warehouse_voucher" required
                                        class="form-control" tabindex="4"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('warehouse_voucher') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_entry">{{ __('forms.user.entry') }}</label>
                                    <input type="text" id="user_entry" name="user_entry" required class="form-control"
                                        tabindex="5" data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('user_entry') }}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_receiver">{{ __('forms.receiver') }}</label>
                                    <input type="text" id="user_receiver" name="user_receiver" required
                                        class="form-control" tabindex="6"
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('user_receiver') }}">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date" class="form-label">
                                        {{ __('forms.date') }}បញ្ចូលឃ្លាំង
                                    </label>
                                    <input type="text" id="date" name="date" class="form-control" tabindex="7"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}"
                                        value="{{ old('date') }}">
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
                                                id="skipTitleInput" style="cursor:pointer;">
                                            <label class="form-check-label font-size-12 text-muted skip-label"
                                                for="skipTitleInput" style="cursor:pointer;">
                                                រំលង / មិនភ្ជាប់
                                            </label>
                                        </div>
                                    </div>
                                    <input type="text" id="titleInput" name="title" class="form-control"
                                        tabindex="8" data-pristine-required-message="{{ __('messages.required') }}">

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
                                                id="skipFileInput" style="cursor:pointer;">
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
                        </div>
                        {{-- =====================================================
    SECTION 2: PROJECT TABLE
====================================================== --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">
                                {{ __('forms.sub.project') }}
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="skipItemTable"
                                    name="skipItemTable" value="1" style="cursor:pointer;"
                                    {{ old('skipItemTable') ? 'checked' : '' }}>
                                <label class="form-check-label font-size-12 text-muted skip-label" for="skipItemTable">
                                    រំលង / មិនភ្ជាប់
                                </label>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-12 mt-3" id="itemTableWrapper">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-nowrap" id="itemTable">
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
                                            <th style="width:85px; min-width:85px;" class="text-center align-middle">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-row-index="0" data-cat-index="0" data-subcat-index="0">
                                            {{-- TD 1: Project --}}
                                            <td data-col="project" class="td-project">
                                                <input type="text" name="sub_pro[]" class="form-control">
                                            </td>

                                            {{-- TD 2: Category (first/header item has only +) --}}
                                            <td data-col="category" class="td-category">
                                                <div class="d-flex align-items-center category-item">
                                                    <input type="text" class="form-control me-1" name="category[0][]"
                                                        placeholder="បញ្ចូលប្រភេទ (Category)">
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm addCategoryBtn"
                                                        title="បន្ថែមប្រភេទ">+</button>
                                                </div>
                                            </td>

                                            {{-- TD 3: Sub-category (first/header item has only +) --}}
                                            <td data-col="subcategory" class="td-subcategory">
                                                <div class="d-flex align-items-center subcategory-item">
                                                    <input type="text" class="form-control me-1"
                                                        name="sub_category[0][0][]"
                                                        placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm addSubCategoryBtn"
                                                        title="បន្ថែមប្រភេទរង">+</button>
                                                </div>
                                            </td>

                                            {{-- TD 4: Detail (first/header item has only +) --}}
                                            <td data-col="detail" class="td-detail">
                                                <div class="d-flex align-items-center detail-item">
                                                    <input type="text" class="form-control me-1"
                                                        name="detail[0][0][0][]"
                                                        placeholder="បញ្ចូលព័ត៌មានលម្អិត (Detail)">
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm addDetailBtn"
                                                        title="បន្ថែមលម្អិត">+</button>
                                                </div>
                                            </td>

                                            {{-- Hidden Choices.js selects preserved --}}
                                            <td style="display: none;">
                                                <select class="form-select cboSubAccount" name="accountSub[]">
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($accountSub as $bv)
                                                        <option value="{{ $bv->no }}">
                                                            {{ $bv->no }}-{{ $bv->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="display: none;">
                                                <select class="form-select cboProgram" name="program[]">
                                                    <option value="">ជ្រើសរើស</option>
                                                    @foreach ($program as $p)
                                                        <option value="{{ $p->id }}">
                                                            {{ $p->no }}-{{ $p->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="display: none;">
                                                <select class="form-select cboProgramSub" name="cboProgramSub[]">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                </select>
                                            </td>
                                            <td style="display: none;">
                                                <select class="form-select cboCluster" name="cboCluster[]">
                                                    <option value="">{{ __('forms.search...') }}</option>
                                                </select>
                                            </td>

                                            {{-- Action TD --}}
                                            <td data-col="action" class="td-action text-center">
                                                <button type="button" class="btn btn-success btn-sm addRow"
                                                    title="បន្ថែមជួរ">+</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- =====================================================
                            SECTION 3: NOTE, REFER & ACTIONS
                        ====================================================== --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vRefer" class="form-label">{{ __('forms.refer') }}</label>
                                    <textarea name="refer" id="vRefer" rows="5" class="form-control" required tabindex="9"
                                        data-pristine-required-message="{{ __('messages.required') }}">{{ old('refer') }}</textarea>
                                    @error('refer')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vNote" class="form-label">{{ __('forms.note') }}</label>
                                    <textarea name="note" id="vNote" rows="5" class="form-control" required tabindex="10"
                                        data-pristine-required-message="{{ __('messages.required') }}">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">
                                    {{ __('buttons.save') }}
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width:80px;">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-outline-secondary" href="{{ route('project.index', $params) }}">
                                    <i class="bx bx-arrow-back me-1"></i>
                                    {{ __('buttons.back') }}
                                </a>
                            </div>
                        </div>
                    </form>
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
        /* =========================================================
         * 1. CHOICES.JS INSTANCE MANAGER (WEAKMAP)
         * ========================================================= */
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

        /* =========================================================
         * 2. GROUP HELPERS & INDEX / ROWSPAN SYNC (4-TD + ACTION)
         * ========================================================= */
        let groupUidCounter = 1;

        function nextUid(prefix) {
            return `${prefix}_${groupUidCounter++}`;
        }

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

                $tr.find('.category-item input').attr('name', `category[${rowIdx}][]`);
                $tr.find('.subcategory-item input').attr('name', `sub_category[${rowIdx}][${catIdx}][]`);
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            /* =========================================================
             * 3. PLUGINS INITIALIZATION (SELECTS, SUMMERNOTE, FLATPICKR)
             * ========================================================= */
            document.querySelectorAll('.cboProgram, .cboSubAccount, .cboProgramSub, .cboCluster').forEach(function(
                select) {
                initChoicesInstance(select);
            });

            if ($('#vNote').length) $('#vNote').summernote({
                height: 120
            });
            if ($('#vRefer').length) $('#vRefer').summernote({
                height: 120
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

            /* =========================================================
             * 4. PRISTINE VALIDATION SETUP
             * ========================================================= */
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
                if (form) pristine = new Pristine(form, pristineConfig, true);
            }

            function clearPristineError(input) {
                const parent = input.closest('.form-group');
                if (!parent) return;
                parent.classList.remove('has-danger', 'has-success');
                const error = parent.querySelector('.pristine-error');
                if (error) error.remove();
            }

            /* =========================================================
             * 5. FIELD SKIP TOGGLES (TITLE & FILE)
             * ========================================================= */
            function setupSkipToggle(checkboxId, inputId) {
                const checkbox = document.getElementById(checkboxId);
                const input = document.getElementById(inputId);
                if (!checkbox || !input) return;

                const requiredMessage = input.getAttribute('data-pristine-required-message') ||
                    "{{ __('messages.required') }}";

                function updateState() {
                    clearPristineError(input);

                    if (checkbox.checked) {
                        input.value = '';
                        input.disabled = true;
                        input.setAttribute('disabled', 'disabled');
                        input.setAttribute('tabindex', '-1');
                        input.removeAttribute('required');
                        input.removeAttribute('data-pristine-required-message');
                        input.style.pointerEvents = 'none';
                        input.classList.add('bg-success-subtle', 'text-success-emphasis', 'border-success',
                            'opacity-75');
                    } else {
                        input.disabled = false;
                        input.removeAttribute('disabled');
                        input.removeAttribute('tabindex');
                        input.style.pointerEvents = '';
                        input.setAttribute('required', 'required');
                        input.setAttribute('data-pristine-required-message', requiredMessage);
                        input.classList.remove('bg-success-subtle', 'text-success-emphasis', 'border-success',
                            'opacity-75');
                    }
                    reinitPristine();
                }

                checkbox.addEventListener('change', updateState);
                updateState();
            }

            setupSkipToggle('skipTitleInput', 'titleInput');
            setupSkipToggle('skipFileInput', 'fileInput');

            /* =========================================================
             * 6. TABLE SKIP TOGGLE
             * ========================================================= */
            const itemTableWrapper = document.getElementById('itemTableWrapper');
            const itemTable = document.getElementById('itemTable');

            function applyTableSkipState(isSkipped) {
                if (!itemTableWrapper || !itemTable) return;
                itemTableWrapper.classList.toggle('table-skipped', isSkipped);
                itemTable.querySelectorAll('input, select, button').forEach(el => {
                    el.disabled = isSkipped;
                    if (isSkipped) {
                        el.removeAttribute('required');
                    } else if (el.classList.contains('table-required')) {
                        el.setAttribute('required', 'required');
                    }
                });
                if (typeof reinitPristine === 'function') reinitPristine();
            }

            $(document).on('change', '#skipItemTable', function() {
                applyTableSkipState(this.checked);
            });

            const initialSkipSwitch = document.getElementById('skipItemTable');
            if (initialSkipSwitch) applyTableSkipState(initialSkipSwitch.checked);

            /* =========================================================
             * 7. DYNAMIC DROPDOWNS: PROGRAM -> SUB PROGRAM -> CLUSTER
             * ========================================================= */
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
                            clusterChoices.showDropdown();
                        }
                    }
                });
            });

            /* =========================================================
             * 8. 4-TD CATEGORY, SUB-CATEGORY & DETAIL MANAGEMENT
             * ========================================================= */

            // 1. Add Category Row inside project group
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

            // 3. Add Sub-Category Row inside category group
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

            // 5. Add Detail Row inside sub-category group
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
                        <input type="text" name="sub_pro[]" class="form-control table-required">
                    </td>
                    <td data-col="category">
                        <div class="d-flex align-items-center category-item">
                            <input type="text" class="form-control me-1" name="category[${rowIndex}][]" placeholder="បញ្ចូលប្រភេទ (Category)">
                            <button type="button" class="btn btn-outline-success btn-sm addCategoryBtn" title="បន្ថែមប្រភេទ">+</button>
                        </div>
                    </td>
                    <td data-col="subcategory">
                        <div class="d-flex align-items-center subcategory-item">
                            <input type="text" class="form-control me-1" name="sub_category[${rowIndex}][0][]" placeholder="បញ្ចូលប្រភេទរង (Sub-category)">
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
                        <select class="form-select cboSubAccount" name="accountSub[]">
                            <option value="">ជ្រើសរើស</option>
                            @foreach ($accountSub as $bv)
                                <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboProgram" name="program[]">
                            <option value="">ជ្រើសរើស</option>
                            @foreach ($program as $p)
                                <option value="{{ $p->id }}">{{ $p->no }}-{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboProgramSub" name="cboProgramSub[]">
                            <option value="">{{ __('forms.search...') }}</option>
                        </select>
                    </td>
                    <td style="display: none;">
                        <select class="form-select cboCluster" name="cboCluster[]">
                            <option value="">{{ __('forms.search...') }}</option>
                        </select>
                    </td>
                    <td data-col="action" class="text-center">
                        <button type="button" class="btn btn-danger btn-sm removeRow" title="លុបជួរ">-</button>
                    </td>
                </tr>`;

                tableBody.append(newRowHtml);
                const appendedRow = tableBody.children().last();
                appendedRow.find('select').each(function() {
                    if (typeof initChoicesInstance === 'function') {
                        initChoicesInstance(this);
                    }
                });

                updateRowIndices();
            });

            // 8. Remove Main Project Group
            $(document).on('click', '.removeRow', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const tableBody = $('#itemTable tbody');
                const puid = $(this).closest('tr').attr('data-puid');
                const uniqueProjects = new Set();
                tableBody.find('tr[data-puid]').each(function() {
                    uniqueProjects.add($(this).attr('data-puid'));
                });

                if (uniqueProjects.size > 1) {
                    const $groupRows = $(`#itemTable tbody tr[data-puid="${puid}"]`);
                    $groupRows.find('select').each(function() {
                        destroyChoicesInstance(this);
                    });
                    $groupRows.remove();
                    updateRowIndices();
                } else {
                    alert('ត្រូវមានយ៉ាងហោចណាស់ ១ បន្ទាត់!');
                }
            });

            /* =========================================================
             * 9. FORM SUBMISSION & VALIDATION HANDLING
             * ========================================================= */
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

            // Initialize group IDs & spans on initial load
            updateRowIndices();
        });
    </script>
@endsection
