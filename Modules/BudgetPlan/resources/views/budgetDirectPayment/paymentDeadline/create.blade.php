@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <style>
        /* Turn border green when Pristine marks the form-group as valid / optional */
        .has-success .form-control,
        .has-success .form-select,
        .has-success .choices__inner {
            border-color: #198754 !important;
            /* Bootstrap 5 Success Green */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.4-1.4 1-1l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        /* Adjust padding for standard inputs so typed text doesn't overlap the green checkmark icon */
        .has-success .form-control {
            padding-right: calc(1.5em + 0.75rem);
        }

        /* Remove checkmark icon from Choices.js boxes to prevent overlapping the dropdown arrow */
        .has-success .choices__inner {
            background-image: none !important;
        }

        /* Prevent green border and checkmark icon on disabled or readonly inputs (when skipped) */
        .has-success .form-control:disabled,
        .has-success .form-control[readonly],
        .form-control:disabled,
        .form-control[readonly] {
            border-color: #ced4da !important;
            background-image: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.payment.deadline') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.payment.deadline') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div id="flashMessage"></div>

    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example"
                            action="{{ route('budgetDirectPayment.paymentDeadline.store', $params) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboExpenseType"
                                            class="form-label text-muted">{{ __('forms.expense.type') }}</label>
                                        <select id="cboExpenseType" class="form-select" name="cboExpenseType" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($expenseType as $item)
                                                <option value="{{ $item->id }}">{{ $item->name_kh }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboLegalId" class="form-label font-size-13 text-muted">
                                            {{ __('forms.legal.id') }}
                                        </label>
                                        <select id="cboLegalId" class="form-select" name="cboLegalId" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                        @error('cboLegalId')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.legal.name') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            type="text" class="form-control" name="legalName" tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="cbotemporaryId"
                                                class="form-label mb-0">{{ __('forms.temporary.id') }}</label>
                                            <!-- Modern Skip Switch -->
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipCboTemporaryId" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipCboTemporaryId" style="cursor: pointer;">
                                                    រំលង / មិនបញ្ចូល
                                                </label>
                                            </div>
                                        </div>
                                        <input required id="cbotemporaryId" name="cbotemporaryId" type="number"
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            placeholder="{{ __('forms.temporary.id') }}" tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.day.number') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            type="text" class="form-control" placeholder="{{ __('forms.day.number') }}"
                                            name="cbodayOfNumber" tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProgram" class="form-label font-size-13 text-muted">
                                            {{ __('forms.program') }}
                                        </label>
                                        <select class="form-select" id="cboProgram" name="cboProgram" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($program as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->no }}-{{ $p->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('cboProgram')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProgramSub" class="form-label font-size-13 text-muted">
                                            {{ __('forms.program.sub') }}
                                        </label>
                                        <select id="cboProgramSub" class="form-select" name="cboProgramSub" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                        @error('cboProgramSub')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboCluster" class="form-label font-size-13 text-muted">
                                            {{ __('forms.cluster') }}
                                        </label>
                                        <select id="cboCluster" class="form-select" name="cboCluster" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                        @error('cboCluster')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboAgency" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select id="cboAgency" class="form-select" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                        @error('cboAgency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" id="cboSubAccount" name="cboSubAccount" required>
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $bv)
                                                <option value="{{ $bv->no }}">
                                                    {{ $bv->no }}-{{ $bv->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="budget">{{ __('forms.budget') }}</label>
                                        <input type="number" min="0" name="budget" id="budget" required
                                            class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('budget')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="transactionDate"
                                            class="form-label">{{ __('forms.select_date') }}</label>
                                        <input type="text" id="transactionDate" name="transactionDate"
                                            class="form-control" placeholder="{{ __('forms.select_transaction_date') }}"
                                            required data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="requestDate" class="form-label">{{ __('forms.select_date') }}</label>
                                        <input type="text" id="requestDate" name="requestDate" class="form-control"
                                            placeholder="{{ __('forms.select_request_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="fileInput">{{ __('forms.file.type') }}</label>
                                        <input type="file" id="fileInput" name="attachments[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX</small>
                                        @error('attachments.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="vDescription">{{ __('forms.document.description') }}</label>
                                    <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('txtDescription')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('budgetDirectPayment.paymentDeadline.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ឥណទានអនុម័ត</th>
                                <th>ចលនាឥណទាន</th>
                                <th>ស្ថានភាពឥណទានថ្មី</th>
                                <th>ឥណទានទំនេរ</th>
                                <th>ធានាចំណាយពីមុន</th>
                                <th>ស្នើរសុំលើកនេះ</th>
                                <th>ឥណទាននៅសល់</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span id="fin_law">0</span></td>
                                <td><span id="credit_movement">0</span></td>
                                <td><span id="new_credit_status">0</span></td>
                                <td><span id="credit">0</span></td>
                                <td><span id="deadline_balance">0</span></td>
                                <td><span id="applying">0</span></td>
                                <td><span id="remaining_credit">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Consolidated Single Imports (No Duplicates) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            if (!form) return;

            // ==========================================
            // 1. MASTER PRISTINE INITIALIZATION
            // ==========================================
            const createPristineInstance = () => {
                return new Pristine(form, {
                    classTo: 'form-group',
                    errorClass: 'has-danger',
                    successClass: 'has-success',
                    errorTextParent: 'form-group',
                    errorTextTag: 'div',
                    errorTextClass: 'pristine-error text-help text-danger font-size-14 mt-1'
                });
            };

            let pristine = createPristineInstance();

            const refreshPristine = () => {
                pristine.destroy();
                pristine = createPristineInstance();
            };

            // ==========================================
            // 2. FLATPICKR (With Auto-Validation Sync)
            // ==========================================
            const initFlatpickr = (id) => {
                const el = document.getElementById(id);
                if (!el) return;
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: el.value || null,
                    onChange: () => pristine.validate(el),
                    onClose: () => pristine.validate(el)
                });
            };
            initFlatpickr('transactionDate');
            initFlatpickr('requestDate');

            // ==========================================
            // 3. SUMMERNOTE (With Auto-Validation Sync)
            // ==========================================
            if (window.jQuery) {
                $('#vDescription').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['color', ['color']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            const clean = contents.replace(/<\/?[^>]+(>|$)/g, "").trim();
                            document.getElementById('vDescription').value = clean === '' ? '' :
                                contents;
                            pristine.validate(document.getElementById('vDescription'));
                        }
                    }
                });
            }

            // ==========================================
            // 4. CHOICES.JS INITIALIZATION
            // ==========================================
            const defaultChoicesOpts = {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            };

            let cboExpenseChoices = new Choices('#cboExpenseType', defaultChoicesOpts);
            let cboLegalChoices = new Choices('#cboLegalId', defaultChoicesOpts);
            let programChoices = new Choices('#cboProgram', defaultChoicesOpts);
            let programSubChoices = new Choices('#cboProgramSub', defaultChoicesOpts);
            let clusterChoices = new Choices('#cboCluster', defaultChoicesOpts);
            let agencyChoices = new Choices('#cboAgency', defaultChoicesOpts);
            let subAccountChoices = new Choices('#cboSubAccount', defaultChoicesOpts);

            const resetSelect = (selector) => $(selector).html(
                `<option value="">{{ __('forms.search...') }}</option>`);
            const resetChoices = (selector, instance) => {
                instance.destroy();
                return new Choices(selector, defaultChoicesOpts);
            };

            const loadOptions = ({
                url,
                data,
                targetSelect,
                instanceRefSetter
            }) => {
                $.ajax({
                    url,
                    type: "GET",
                    data,
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter();
                    },
                    error: () => resetSelect(targetSelect)
                });
            };

            // ==========================================
            // 5. AJAX CASCADING DROPDOWNS
            // ==========================================
            $('#cboExpenseType').on('change', function() {
                const expenseTypeId = $(this).val();
                cboLegalChoices.clearStore();
                cboLegalChoices.clearChoices();
                if (!expenseTypeId) return;

                $.ajax({
                    url: "{{ route('budgetDirectPayment.paymentDeadline.get.expense_type_id') }}",
                    type: 'GET',
                    data: {
                        expense_type_id: expenseTypeId
                    },
                    success: function(data) {
                        let options = [];
                        $(data).filter('option').each(function() {
                            options.push({
                                value: $(this).val(),
                                label: $(this).text()
                            });
                        });
                        cboLegalChoices.clearChoices();
                        cboLegalChoices.setChoices(options, 'value', 'label', true);
                    }
                });
            });

            $('#cboProgram').on('change', function() {
                const programId = $(this).val();
                resetSelect('#cboProgramSub');
                programSubChoices = resetChoices('#cboProgramSub', programSubChoices);
                resetSelect('#cboAgency');
                agencyChoices = resetChoices('#cboAgency', agencyChoices);
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('budgetDirectPayment.paymentDeadline.by.program_sub') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => programSubChoices = resetChoices('#cboProgramSub',
                        programSubChoices)
                });

                loadOptions({
                    url: "{{ route('budgetDirectPayment.paymentDeadline.by.agency') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboAgency',
                    instanceRefSetter: () => agencyChoices = resetChoices('#cboAgency',
                        agencyChoices)
                });
            });

            $('#cboProgramSub').on('change', function() {
                const programSubId = $(this).val();
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);
                if (!programSubId) return;

                loadOptions({
                    url: "{{ route('budgetDirectPayment.paymentDeadline.by.cluster') }}",
                    data: {
                        program_sub_id: programSubId
                    },
                    targetSelect: '#cboCluster',
                    instanceRefSetter: () => clusterChoices = resetChoices('#cboCluster',
                        clusterChoices)
                });
            });

            // ==========================================
            // 6. EARLY BALANCE & CREDIT CALCULATIONS
            // ==========================================
            const n = v => (isNaN(+v) ? 0 : +v);
            const fmt = v => n(v).toLocaleString('en-US', {
                maximumFractionDigits: 2
            });
            const setText = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = fmt(val);
            };
            const resetNumbers = () => ['fin_law', 'credit_movement', 'new_credit_status', 'credit',
                'deadline_balance', 'applying', 'remaining_credit'
            ].forEach(id => setText(id, 0));

            const budgetInput = document.getElementById('budget');
            const recomputeRemaining = () => {
                const apply = n(budgetInput?.value);
                const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0) budgetInput.value = '';
            };

            const fetchEarlyBalance = async () => {
                const programId = document.getElementById('cboProgram').value;
                const programSubId = document.getElementById('cboProgramSub').value;
                const clusterId = document.getElementById('cboCluster').value;
                const accountSubId = document.getElementById('cboSubAccount').value;

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    resetNumbers();
                    return;
                }

                const url = new URL(
                    "{{ route('budgetDirectPayment.paymentDeadline.getEarlyBalance', ['params' => $params]) }}",
                    window.location.origin);
                url.searchParams.set('program_id', programId);
                url.searchParams.set('program_sub_id', programSubId);
                url.searchParams.set('cluster_id', clusterId);
                url.searchParams.set('account_sub_id', accountSubId);

                try {
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    setText('fin_law', data.fin_law);
                    setText('credit_movement', data.credit_movement);
                    setText('new_credit_status', data.new_credit_status);
                    setText('credit', data.credit);
                    setText('deadline_balance', data.deadline_balance);
                    recomputeRemaining();
                } catch (err) {
                    console.error(err);
                    resetNumbers();
                }
            };

            ['cboProgram', 'cboProgramSub', 'cboCluster', 'cboSubAccount'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', fetchEarlyBalance);
            });
            budgetInput?.addEventListener('input', recomputeRemaining);

            // ==========================================
            // 7. SKIP TEMPORARY ID LOGIC
            // ==========================================
            const cboTempIdInput = document.getElementById('cbotemporaryId');
            const skipCboTempCheckbox = document.getElementById('skipCboTemporaryId');

            if (skipCboTempCheckbox && cboTempIdInput) {
                const handleSkipToggle = () => {
                    const formGroup = cboTempIdInput.closest('.form-group');
                    if (skipCboTempCheckbox.checked) {
                        cboTempIdInput.value = '';
                        cboTempIdInput.disabled = true;
                        cboTempIdInput.readOnly = true;
                        cboTempIdInput.classList.add('bg-light', 'text-muted');

                        cboTempIdInput.removeAttribute('required');
                        cboTempIdInput.removeAttribute('min');
                        cboTempIdInput.removeAttribute('data-pristine-required-message');
                        cboTempIdInput.removeAttribute('data-pristine-min-message');
                        cboTempIdInput.removeAttribute('data-pristine-integer-message');

                        pristine.reset(cboTempIdInput);
                        if (formGroup) {
                            formGroup.classList.remove('has-success', 'has-danger');
                        }
                    } else {
                        if (cboTempIdInput.value === '') {
                            cboTempIdInput.value = '0';
                        }
                        cboTempIdInput.disabled = false;
                        cboTempIdInput.readOnly = false;
                        cboTempIdInput.classList.remove('bg-light', 'text-muted');

                        cboTempIdInput.setAttribute('required', 'true');
                        cboTempIdInput.setAttribute('min', '1');
                        cboTempIdInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                        cboTempIdInput.setAttribute('data-pristine-min-message', 'លំដាប់ ត្រូវតែធំជាងសូន្យ');
                        cboTempIdInput.setAttribute('data-pristine-integer-message', 'លំដាប់ ត្រូវតែលេខ');
                    }
                    refreshPristine();

                    // Ensure clean neutral state after Pristine re-initializes
                    if (skipCboTempCheckbox.checked && formGroup) {
                        pristine.reset(cboTempIdInput);
                        formGroup.classList.remove('has-success', 'has-danger');
                    }
                };

                skipCboTempCheckbox.addEventListener('change', handleSkipToggle);
            }

            // ==========================================
            // 8. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formGroup = cboTempIdInput?.closest('.form-group');

                // Pre-submit safety check
                if (skipCboTempCheckbox && skipCboTempCheckbox.checked && cboTempIdInput) {
                    cboTempIdInput.disabled = true;
                    cboTempIdInput.removeAttribute('required');
                    cboTempIdInput.removeAttribute('min');
                }

                // Sync Summernote code before final check
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                }

                const isValid = pristine.validate();

                // Post-validation cleanup for skipped field:
                // When skip is ON, pristine.validate() marks the empty optional field with 'has-success'.
                // We strip those classes immediately so the disabled field stays neutral gray!
                if (skipCboTempCheckbox && skipCboTempCheckbox.checked && cboTempIdInput) {
                    pristine.reset(cboTempIdInput);
                    if (formGroup) {
                        formGroup.classList.remove('has-success', 'has-danger');
                    }
                }

                if (isValid) {
                    // Unlock disabled fields instantly so Laravel receives empty values instead of throwing missing key exceptions
                    if (cboTempIdInput && skipCboTempCheckbox && skipCboTempCheckbox.checked) {
                        cboTempIdInput.disabled = false;
                    }
                    HTMLFormElement.prototype.submit.call(form);
                } else {
                    const firstError = form.querySelector('.has-danger');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // ==========================================
            // 8. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Pre-submit safety check
                if (skipCboTempCheckbox && skipCboTempCheckbox.checked && cboTempIdInput) {
                    cboTempIdInput.disabled = true;
                    cboTempIdInput.removeAttribute('required');
                    cboTempIdInput.removeAttribute('min');
                    pristine.reset(cboTempIdInput);
                }

                // Sync Summernote code before final check
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                }

                const isValid = pristine.validate();

                if (isValid) {
                    // Unlock disabled fields instantly so Laravel receives empty values instead of throwing missing key exceptions
                    if (cboTempIdInput && skipCboTempCheckbox && skipCboTempCheckbox.checked) {
                        cboTempIdInput.disabled = false;
                    }
                    HTMLFormElement.prototype.submit.call(form);
                } else {
                    const firstError = form.querySelector('.has-danger');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        });
    </script>
@endsection
