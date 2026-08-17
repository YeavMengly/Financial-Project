@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- preloader css -->
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.voucher') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.voucher') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="flashMessage"></div>

    <!-- end page title -->
    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example" action="{{ route('budgetVoucher.store', $params) }}"
                            method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.legal.id') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" type="number"
                                            class="form-control" placeholder="{{ __('forms.legal.id') }}" name="legalID"
                                            tabindex="1" name="legalID" min="0" max="999"
                                            oninput="if(this.value.length > 3) this.value = this.value.slice(0,3)" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="legalDate"
                                            class="form-label">{{ __('forms.select_legal_date') }}</label>
                                        <input type="text" id="legalDate" name="legalDate" class="form-control"
                                            placeholder="{{ __('forms.select_legal_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.payment.voucher') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" type="number"
                                            class="form-control" placeholder="{{ __('forms.payment.voucher') }}"
                                            name="paymentVoucher" tabindex="3" min="0" max="9999"
                                            oninput="if(this.value.length > 4) this.value = this.value.slice(0,4)" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="legalNumber"
                                                class="form-label mb-0">{{ __('forms.legal.number') }}</label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    tabindex="4" id="skipLegalNumber" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipLegalNumber" style="cursor: pointer;">
                                                    រំលង / មិនបញ្ចូលលេខ
                                                </label>
                                            </div>
                                        </div>

                                        <input class="form-control" id="legalNumber" name="legalNumber"
                                            data-pristine-required-message="{{ __('messages.required') }}" type="text"
                                            placeholder="{{ __('forms.legal.number') }}" required tabindex="5">
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="legalName"
                                                class="form-label mb-0">{{ __('forms.legal.name') }}</label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipLegalName" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipLegalName" style="cursor: pointer;">
                                                    រំលង / មិនបញ្ចូលលេខ
                                                </label>
                                            </div>
                                        </div>

                                        <input class="form-control" id="legalName" name="legalName"
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" type="text"
                                            placeholder="{{ __('forms.legal.name') }}" required tabindex="6">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProgram"
                                            class="form-label font-size-13 text-muted">{{ __('forms.program') }}</label>
                                        <select class="form-select" id="cboProgram" name="cboProgram" required
                                            tabindex="7"
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($program as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->no }}-{{ $p->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProgramSub"
                                            class="form-label font-size-13 text-muted">{{ __('forms.program.sub') }}</label>
                                        <select id="cboProgramSub" class="form-select" name="cboProgramSub" required
                                            tabindex="8"
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboCluster"
                                            class="form-label font-size-13 text-muted">{{ __('forms.cluster') }}</label>
                                        <select id="cboCluster" class="form-select" name="cboCluster" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            tabindex="9">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboAgency"
                                            class="form-label font-size-13 text-muted">{{ __('forms.agency') }}</label>
                                        <select id="cboAgency" class="form-select" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            tabindex="10">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccount"
                                            class="form-label font-size-13 text-muted">{{ __('forms.sub.account') }}</label>
                                        <select class="form-select" id="cboSubAccount" name="cboSubAccount" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            tabindex="11">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $bv)
                                                <option value="{{ $bv->no }}">
                                                    {{ $bv->no }}-{{ $bv->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="budget">{{ __('forms.budget') }}</label>
                                        <input type="number" min="0" name="budget" id="budget" required
                                            tabindex="12" class="form-control" placeholder="{{ __('forms.budget') }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboExpenseType"
                                            class="form-label font-size-13 text-muted">{{ __('forms.expense.type') }}</label>
                                        <select class="form-select" id="cboExpenseType" name="cboExpenseType" required
                                            tabindex="13"
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($expenseType as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name_kh }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="transactionDate" class="form-label">{{ __('forms.select_date') }}( PO
                                            FMIS )</label>
                                        <input type="text" id="transactionDate" name="transactionDate" tabindex="14"
                                            class="form-control" placeholder="{{ __('forms.select_transaction_date') }}"
                                            required data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="requestDate" class="form-label">{{ __('forms.select_date') }}( PO
                                            FMIS
                                            )</label>
                                        <input type="text" id="requestDate" name="requestDate" class="form-control"
                                            tabindex="15" placeholder="{{ __('forms.select_request_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="fileInput"
                                                class="form-label mb-0">{{ __('forms.file.type') }}</label>

                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipFileInput" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipFileInput" style="cursor: pointer;">
                                                    រំលង / មិនភ្ជាប់
                                                </label>
                                            </div>
                                        </div>

                                        <input type="file" id="fileInput" name="attachments" class="form-control"
                                            tabindex="16" accept=".pdf,.doc,.docx" required 
                                            data-allowed-extensions="pdf,doc,docx"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX (Max: 5MB per
                                            file)</small>

                                        @error('attachments.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="vDescription">{{ __('forms.document.description') }}</label>
                                    <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required tabindex="17"
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                </div>
                            </div>


                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <button class="btn btn-info" type="submit">{{ __('buttons.save.create') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('buttons.delete') }}
                                </a>
                                <a class="btn btn-dark"
                                    href="{{ route('budgetVoucher.index', $params) }}">{{ __('buttons.back') }}</a>

                            </div>
                        </form>

                    </div>
                </div>

                {{-- Data table viewing --}}
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
    <!-- Vendor Scripts (Loaded exactly once) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const taskTypeSelect = document.getElementById('cboExpenseType');
            const taskTypeChoices = new Choices(taskTypeSelect, {
                searchEnabled: true,
                itemSelectText: '', // Hide "Press to select"
                placeholderValue: 'ជ្រើសរើសប្រភេទ', // Khmer placeholder
                searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                shouldSort: false
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            if (!form) return;

            // ==========================================
            // 1. PRISTINE INITIALIZATION
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
            // 2. FLATPICKR SYNC
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
            initFlatpickr('legalDate');

            // ==========================================
            // 3. SUMMERNOTE SYNC
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

            // Sync validation on dropdown change
            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => pristine.validate(select));
            });

            // ==========================================
            // 5. CASCADING AJAX DROPDOWNS
            // ==========================================
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
                    url: "{{ route('budgetVoucher.by.program_sub') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => programSubChoices = resetChoices('#cboProgramSub',
                        programSubChoices)
                });

                loadOptions({
                    url: "{{ route('budgetVoucher.by.agency') }}",
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
                    url: "{{ route('budgetVoucher.by.cluster') }}",
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

                const url = new URL("{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}",
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
                    console.error("Failed to fetch balance:", err);
                    resetNumbers();
                }
            };

            ['cboProgram', 'cboProgramSub', 'cboCluster', 'cboSubAccount'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', fetchEarlyBalance);
            });
            budgetInput?.addEventListener('input', recomputeRemaining);

            // ==========================================
            // 7. SKIP LEGAL NUMBER LOGIC (With Visuals)
            // ==========================================
            const legalInput = document.getElementById('legalNumber');
            const skipLegalCheckbox = document.getElementById('skipLegalNumber');

            const legalNameInput = document.getElementById('legalName');
            const skipLegalNameCheckbox = document.getElementById('skipLegalName');

            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');
            const setupSkipField = ({
                checkbox,
                input,
                defaultValue = '',
                restoreValidation = () => {}
            }) => {

                if (!checkbox || !input) return;

                checkbox.addEventListener('change', function() {

                    const parentGroup = input.closest('.form-group');

                    if (this.checked) {
                        input.value = '';
                        input.disabled = true;

                        input.classList.add('border-success', 'bg-success-subtle');
                        parentGroup?.classList.add('has-success');

                        input.removeAttribute('required');
                        input.removeAttribute('min');
                        input.removeAttribute('data-pristine-required-message');

                        pristine.reset(input);
                    } else {
                        input.value = defaultValue;
                        input.disabled = false;

                        input.classList.remove('border-success', 'bg-success-subtle');
                        parentGroup?.classList.remove('has-success');

                        restoreValidation();
                    }

                    refreshPristine();
                });
            };
            setupSkipField({
                checkbox: skipLegalCheckbox,
                input: legalInput,
                defaultValue: '0',
                restoreValidation: () => {
                    legalInput.setAttribute('required', true);
                    legalInput.setAttribute(
                        'data-pristine-required-message',
                        "{{ __('messages.required') }}"
                    );
                }
            });

            setupSkipField({
                checkbox: skipLegalNameCheckbox,
                input: legalNameInput,
                defaultValue: '',
                restoreValidation: () => {
                    legalNameInput.setAttribute('required', true);
                    legalNameInput.setAttribute(
                        'data-pristine-required-message',
                        "{{ __('messages.required') }}"
                    );
                }
            });

            setupSkipField({
                checkbox: skipFileCheckbox,
                input: fileInput,
                restoreValidation: () => {
                    fileInput.setAttribute('required', true);
                    fileInput.setAttribute(
                        'data-pristine-required-message',
                        "{{ __('messages.required') }}"
                    );
                }
            });
            // ==========================================
            // 8. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                // 1. Stop the normal form submission
                e.preventDefault();

                // 2. Capture which button was clicked (Save vs Save & Create)
                const submitter = e.submitter;
                if (submitter && submitter.name === 'action') {
                    let hidden = form.querySelector('input[name="action"]');

                    // If the hidden input doesn't exist, create it
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'action';
                        form.appendChild(hidden);
                    }
                    hidden.value = submitter.value;
                }

                // 3. Handle the "skip" checkboxes to bypass Pristine validation
                [
                    [skipLegalCheckbox, legalInput],
                    [skipLegalNameCheckbox, legalNameInput],
                    [skipFileCheckbox, fileInput]
                ].forEach(([checkbox, input]) => {
                    if (checkbox?.checked && input) {
                        input.disabled = true; // Disable so Pristine ignores it
                        input.removeAttribute('required');
                        pristine.reset(input); // Clear any existing error messages for this field
                    }
                });

                // 4. Run Pristine validation
                const isValid = pristine.validate();

                // 5. If validation fails, stop here (do not submit)
                if (!isValid) {
                    return;
                }

                // 6. Re-enable the skipped inputs just before submitting!
                // We must do this so Laravel receives the empty/null values instead of missing keys.
                [legalInput, legalNameInput, fileInput].forEach(input => {
                    if (input) {
                        input.disabled = false;
                    }
                });

                // 7. Finally, submit the form natively to Laravel
                HTMLFormElement.prototype.submit.call(form);
            });

        });
    </script> --}}

    <!-- 1. Pass Laravel routes and translations to JavaScript -->
    <script>
        window.BudgetFormConfig = {
            urls: {
                programSub: "{{ route('budgetVoucher.by.program_sub') }}",
                agency: "{{ route('budgetVoucher.by.agency') }}",
                cluster: "{{ route('budgetVoucher.by.cluster') }}",
                earlyBalance: "{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}"
            },
            translations: {
                search: "{{ __('forms.search...') }}",
                required: "{{ __('messages.required') }}"
            }
        };
    </script>

    <!-- 2. Include the relative external JavaScript file -->
    <script src="{{ asset('js/budget-form.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            if (!form) return;

            // ==========================================
            // 1. PRISTINE INITIALIZATION
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
            // 2. FLATPICKR SYNC
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
            initFlatpickr('legalDate');

            // ==========================================
            // 3. SUMMERNOTE SYNC
            // ==========================================
            if (window.jQuery && $('#vDescription').length) {
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
                placeholderValue: 'ស្វែងរក...', // Can also be moved to translations if needed
                shouldSort: false
            };

            let programChoices = new Choices('#cboProgram', defaultChoicesOpts);
            let programSubChoices = new Choices('#cboProgramSub', defaultChoicesOpts);
            let clusterChoices = new Choices('#cboCluster', defaultChoicesOpts);
            let agencyChoices = new Choices('#cboAgency', defaultChoicesOpts);
            let subAccountChoices = new Choices('#cboSubAccount', defaultChoicesOpts);

            const resetSelect = (selector) => {
                $(selector).html(`<option value="">${window.BudgetFormConfig.translations.search}</option>`);
            };

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
                    url: url,
                    type: "GET",
                    data: data,
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter();
                    },
                    error: () => resetSelect(targetSelect)
                });
            };

            // Sync validation on dropdown change
            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => pristine.validate(select));
            });

            // ==========================================
            // 5. CASCADING AJAX DROPDOWNS
            // ==========================================
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
                    url: window.BudgetFormConfig.urls.programSub,
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => programSubChoices = resetChoices('#cboProgramSub',
                        programSubChoices)
                });

                loadOptions({
                    url: window.BudgetFormConfig.urls.agency,
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
                    url: window.BudgetFormConfig.urls.cluster,
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

            const resetNumbers = () => {
                ['fin_law', 'credit_movement', 'new_credit_status', 'credit',
                    'deadline_balance', 'applying', 'remaining_credit'
                ].forEach(id => setText(id, 0));
            };

            const budgetInput = document.getElementById('budget');
            const recomputeRemaining = () => {
                const apply = n(budgetInput?.value);
                const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0 && budgetInput) budgetInput.value = '';
            };

            const fetchEarlyBalance = async () => {
                const programId = document.getElementById('cboProgram')?.value;
                const programSubId = document.getElementById('cboProgramSub')?.value;
                const clusterId = document.getElementById('cboCluster')?.value;
                const accountSubId = document.getElementById('cboSubAccount')?.value;

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    resetNumbers();
                    return;
                }

                // Initialize URL properly using absolute or relative paths seamlessly
                const url = new URL(window.BudgetFormConfig.urls.earlyBalance, window.location.origin);
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
                    console.error("Failed to fetch balance:", err);
                    resetNumbers();
                }
            };

            ['cboProgram', 'cboProgramSub', 'cboCluster', 'cboSubAccount'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', fetchEarlyBalance);
            });
            budgetInput?.addEventListener('input', recomputeRemaining);

            // ==========================================
            // 7. SKIP LEGAL NUMBER LOGIC 
            // ==========================================
            const legalInput = document.getElementById('legalNumber');
            const skipLegalCheckbox = document.getElementById('skipLegalNumber');

            const legalNameInput = document.getElementById('legalName');
            const skipLegalNameCheckbox = document.getElementById('skipLegalName');

            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');

            const setupSkipField = ({
                checkbox,
                input,
                defaultValue = '',
                restoreValidation = () => {}
            }) => {
                if (!checkbox || !input) return;

                checkbox.addEventListener('change', function() {
                    const parentGroup = input.closest('.form-group');

                    if (this.checked) {
                        input.value = '';
                        input.disabled = true;

                        input.classList.add('border-success', 'bg-success-subtle');
                        parentGroup?.classList.add('has-success');

                        input.removeAttribute('required');
                        input.removeAttribute('min');
                        input.removeAttribute('data-pristine-required-message');

                        pristine.reset(input);
                    } else {
                        input.value = defaultValue;
                        input.disabled = false;

                        input.classList.remove('border-success', 'bg-success-subtle');
                        parentGroup?.classList.remove('has-success');

                        restoreValidation();
                    }

                    refreshPristine();
                });
            };

            setupSkipField({
                checkbox: skipLegalCheckbox,
                input: legalInput,
                defaultValue: '0',
                restoreValidation: () => {
                    legalInput.setAttribute('required', true);
                    legalInput.setAttribute('data-pristine-required-message', window.BudgetFormConfig
                        .translations.required);
                }
            });

            setupSkipField({
                checkbox: skipLegalNameCheckbox,
                input: legalNameInput,
                defaultValue: '',
                restoreValidation: () => {
                    legalNameInput.setAttribute('required', true);
                    legalNameInput.setAttribute('data-pristine-required-message', window
                        .BudgetFormConfig.translations.required);
                }
            });

            setupSkipField({
                checkbox: skipFileCheckbox,
                input: fileInput,
                restoreValidation: () => {
                    fileInput.setAttribute('required', true);
                    fileInput.setAttribute('data-pristine-required-message', window.BudgetFormConfig
                        .translations.required);
                }
            });

            // ==========================================
            // 8. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitter = e.submitter;
                if (submitter && submitter.name === 'action') {
                    let hidden = form.querySelector('input[name="action"]');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'action';
                        form.appendChild(hidden);
                    }
                    hidden.value = submitter.value;
                }

                [
                    [skipLegalCheckbox, legalInput],
                    [skipLegalNameCheckbox, legalNameInput],
                    [skipFileCheckbox, fileInput]
                ].forEach(([checkbox, input]) => {
                    if (checkbox?.checked && input) {
                        input.disabled = true;
                        input.removeAttribute('required');
                        pristine.reset(input);
                    }
                });

                const isValid = pristine.validate();

                if (!isValid) return;

                // Re-enable before submitting so Laravel receives empty/null values instead of missing keys
                [legalInput, legalNameInput, fileInput].forEach(input => {
                    if (input) input.disabled = false;
                });

                HTMLFormElement.prototype.submit.call(form);
            });
        });
    </script>
@endsection
