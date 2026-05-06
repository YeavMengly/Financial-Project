@extends('layouts.master')

@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('buttons.edit') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('menus.payment.deadline') }}</a></li>
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

                    <form id="pristine-valid-example"
                        action="{{ route('budgetVoucher.update', ['params' => $params, 'id' => $module->id]) }}"
                        method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
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
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $module->expense_type_id ? 'selected' : '' }}>
                                                {{ $item->name_kh }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboLegalNumber" class="form-label font-size-13 text-muted">
                                        {{ __('forms.legal.number') }}
                                    </label>
                                    <select id="cboLegalNumber" class="form-select" name="cboLegalNumber" required
                                        data-old="{{ old('cboLegalNumber', $module->legal_number ?? '') }}"
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                    </select>

                                    @error('cboLegalNumber')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.legal.name') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="legalName"
                                        value="{{ old('legalName', $module->legal_name) }}" tabindex="2" />
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboProgram" class="form-label font-size-13 text-muted">
                                        {{ __('forms.program') }}
                                    </label>
                                    <select id="cboProgram" class="form-select" name="cboProgram" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($program as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $module->program_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->no }}-{{ $p->title }}
                                            </option>
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
                                        data-old="{{ old('cboProgramSub', $module->program_sub_id ?? '') }}"
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
                                    <select id="cboCluster" class="form-select" name="cboCluster"
                                        data-old="{{ old('cboCluster', $module->cluster_id ?? '') }}">
                                        <option value="">ស្វែងរក...</option>
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
                                        data-old="{{ old('cboAgency', $module->agency_id ?? '') }}"
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
                                    <label for="cboSubAccount"
                                        class="form-label font-size-13 text-muted">{{ __('forms.sub.account') }}</label>
                                    <select class="form-control" id="cboSubAccount" name="cboSubAccount" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($accountSub as $bv)
                                            <option value="{{ $bv->no }}"
                                                {{ old('cboSubAccount', $module->account_sub_id) == $bv->no ? 'selected' : '' }}>
                                                {{ $bv->no }}-{{ $bv->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboSubAccount')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="programInput">{{ __('forms.program.code') }}</label>
                                    <input type="number" min="0" name="no" id="programInput" readonly required
                                        class="form-control" value="{{ old('no', $module->no) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('program')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="budget">{{ __('forms.budget') }}</label>
                                    <input type="number" min="0" name="budget" id="budget" required
                                        class="form-control" value="{{ old('budget', $module->budget) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('budget')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboExpenseType"
                                        class="form-label text-muted">{{ __('forms.voucher.type') }}</label>
                                    <select class="form-control" name="cboExpenseType" id="cboExpenseType" required
                                        data-pristine-required-message="{{ __('messages.required') }}">
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($expenseType as $ts)
                                            <option value="{{ $ts->id }}"
                                                {{ old('expense_type_id', $module->expense_type_id == $ts->id ? 'selected' : '') }}>
                                                {{ $ts->name_kh }}</option>
                                        @endforeach
                                    </select>
                                    @error('cboExpenseType')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="budget">{{ __('forms.budget') }}</label>
                                    <input type="number" min="0" name="budget" id="budget" required
                                        class="form-control" value="{{ old('budget', $module->budget) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('budget')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="transactionDate" class="form-label">{{ __('forms.select_date') }}</label>
                                    <input type="text" id="transactionDate" name="transactionDate"
                                        class="form-control"
                                        value="{{ old('transactionDate', $module->transaction_date) }}"
                                        placeholder="{{ __('forms.select_transaction_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="requestDate" class="form-label">{{ __('forms.select_date') }}</label>
                                    <input type="text" id="requestDate" name="requestDate" class="form-control"
                                        value="{{ old('date', $module->request_date) }}"
                                        placeholder="{{ __('forms.select_request_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('date')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="vDescription">{{ __('forms.document.description') }}</label>
                                <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                    data-pristine-required-message="{{ __('messages.required') }}">{{ old('txtDescription', $module->description) }}</textarea>
                                @error('txtDescription')
                                    <div class="pristine-error text-help">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary"
                                id="insertToTableBtn">{{ __('buttons.save') }}</button>
                            <a class="btn btn-dark"
                                href="{{ route('budgetVoucher.index', $params) }}">{{ __('buttons.back') }}</a>

                        </div>
                    </form>


                </div>

                {{-- Numbers table (same as create) --}}
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
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    {{-- <script>
        const dateInput = document.getElementById('datepicker-basic');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }
    </script> --}}
    {{-- <script>
        /** ===============================
         *  Utilities
         *  =============================== */
        const toNumber = (v) => (isNaN(+v) ? 0 : +v);
        const formatNumber = (v) => toNumber(v).toLocaleString('en-US', {
            maximumFractionDigits: 2
        });

        function setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = formatNumber(val);
        }

        function resetBalances() {
            ['fin_law', 'credit_movement', 'new_credit_status', 'credit', 'deadline_balance', 'applying',
                'remaining_credit'
            ]
            .forEach(id => setText(id, 0));
        }

        /** ===============================
         *  DOM & constants
         *  =============================== */
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('pristine-valid-example');
            const subAccount = document.getElementById('cboSubAccount');
            const programInput = document.getElementById('programInput'); // hidden/readonly "no"
            const budgetInput = document.getElementById('budget');

            // The endpoint already includes {params} (ministry) in the URL.
            const EARLY_EP = "{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}";

            initValidation(form);
            initEditors();
            initChoicesOnce(document.getElementById('cboExpenseType'), {
                placeholderValue: 'ជ្រើសរើស',
                searchPlaceholderValue: 'ស្វែងរក...'
            });
            initChoicesOnce(document.getElementById('cboAgency'), {
                placeholder: true,
                placeholderValue: 'ស្វែងរក...'
            });
            initChoicesOnce(subAccount, {
                placeholder: true,
                placeholderValue: 'ស្វែងរក...'
            });

            /** ===============================
             *  Event wiring
             *  =============================== */
            subAccount?.addEventListener('change', () => {
                const {
                    subId,
                    no
                } = readSelections(subAccount, programInput);
                if (!no) resetBalances(); // if we cannot resolve a program code, clear panel
                updateBalancesFromServer(EARLY_EP, subId, no, onBalancesUpdated, onBalancesFailed);
            });

            budgetInput?.addEventListener('input', recomputeRemaining);

            // Initial fetch on page load (edit page)
            const initial = initialSelections(subAccount, programInput);
            if (initial.subId && initial.no) {
                updateBalancesFromServer(EARLY_EP, initial.subId, initial.no, onBalancesUpdated, onBalancesFailed);
            }

            /** ===============================
             *  Handlers (pure-ish)
             *  =============================== */
            function onBalancesUpdated(payload) {
                // Payload must contain fin_law, credit_movement, new_credit_status, credit, deadline_balance
                setText('fin_law', payload.fin_law);
                setText('credit_movement', payload.credit_movement);
                setText('new_credit_status', payload.new_credit_status);
                setText('credit', payload.credit);
                setText('deadline_balance', payload.deadline_balance);
                recomputeRemaining();
            }

            function onBalancesFailed(err) {
                console.error('[EarlyBalance]', err);
                resetBalances();
            }

            function recomputeRemaining() {
                const apply = toNumber(budgetInput?.value);
                const credit = toNumber((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0) budgetInput.value = '';
                // const deadline_balance = toNumber((document.getElementById('deadline_balance')?.textContent || '0')
                //     .replace(/,/g, ''));
                // setText('deadline_balance', Math.max(deadline_balance - apply, 0));
            }
        });

        /** ===============================
         *  Fetch layer
         *  =============================== */
        async function updateBalancesFromServer(endpoint, accountSubId, no, onOk, onErr) {
            if (!endpoint || !accountSubId || !no) {
                onErr?.('Missing params');
                return;
            }
            try {
                const url = new URL(endpoint, window.location.origin);
                // ministry_id is already encoded by the route {params}; we still pass the two filters:
                url.searchParams.set('account_sub_id', accountSubId);
                url.searchParams.set('no', no);

                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json?.message || 'Early-balance error');
                onOk?.(json);
            } catch (e) {
                onErr?.(e);
            }
        }

        /** ===============================
         *  Form helpers
         *  =============================== */
        function readSelections(subAccountSelect, programInput) {
            const opt = subAccountSelect?.options[subAccountSelect.selectedIndex];
            const subId = subAccountSelect?.value || '';
            const no = programInput?.value || (opt ? (opt.getAttribute('data-program') || '') : '');
            // keep programInput synced for server validation
            if (programInput && !programInput.value && no) programInput.value = no;
            return {
                subId,
                no
            };
        }

        function initialSelections(subAccountSelect, programInput) {
            const opt = subAccountSelect?.options[subAccountSelect.selectedIndex];
            const subId = subAccountSelect?.value || '';
            const no = programInput?.value || (opt ? (opt.getAttribute('data-program') || '') : '');
            if (programInput && !programInput.value && no) programInput.value = no;
            return {
                subId,
                no
            };
        }

        /** ===============================
         *  UI libs init
         *  =============================== */
        function initChoicesOnce(selectEl, opts = {}) {
            if (!selectEl) return null;
            if (selectEl.dataset.choicesInit === '1') return null;
            selectEl.dataset.choicesInit = '1';
            return new Choices(selectEl, Object.assign({
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false
            }, opts));
        }

        function initValidation(form) {
            if (!form || typeof Pristine === 'undefined') return;
            const pristine = new Pristine(form);
            form.addEventListener('submit', (e) => {
                if (!pristine.validate()) e.preventDefault();
            });
        }

        function initEditors() {
            if (!window.jQuery) return;
            jQuery('#vDescription').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']]
                ]
            });
        }
    </script> --}}

    <script>
        $(document).ready(function() {
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
    </script>
    <script>
        const transactionDateInput = document.getElementById('transactionDate');
        const requestDateInput = document.getElementById('requestDate');
        const legalDateInput = document.getElementById('legalDate');

        if (transactionDateInput) {
            flatpickr(transactionDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: transactionDateInput.value || null
            });
        }

        if (requestDateInput) {
            flatpickr(requestDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: requestDateInput.value || null
            });
        }

        if (legalDateInput) {
            flatpickr(legalDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: legalDateInput.value || null
            });
        }
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {

            const cboProgram = document.getElementById('cboProgram');
            const cboProgramSub = document.getElementById('cboProgramSub');
            const cboCluster = document.getElementById('cboCluster');
            const cboSubAccount = document.getElementById('cboSubAccount');
            const budgetInput = document.getElementById('budget');

            const ENDPOINT = "{{ route('budgetVoucher.editEarlyBalance', ['params' => $params]) }}";

            function toNumber(v) {
                v = (v || '').toString().replace(/,/g, '');
                return isNaN(parseFloat(v)) ? 0 : parseFloat(v);
            }

            function formatNumber(v) {
                return toNumber(v).toLocaleString('en-US', {
                    maximumFractionDigits: 2
                });
            }

            function setText(id, val) {
                const el = document.getElementById(id);
                if (el) el.textContent = formatNumber(val);
            }

            function resetBalances() {
                ['fin_law', 'credit_movement', 'new_credit_status', 'credit', 'deadline_balance', 'applying',
                    'remaining_credit'
                ]
                .forEach(id => setText(id, 0));
            }

            function recomputeRemaining() {
                const apply = toNumber(budgetInput?.value);
                const credit = toNumber(document.getElementById('credit')?.textContent);
                const deadline = toNumber(document.getElementById('deadline_balance')?.textContent);

                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                setText('deadline_balance', Math.max(deadline - apply, 0));
            }

            function getSelections() {
                return {
                    programId: cboProgram?.value || '',
                    programSubId: cboProgramSub?.value || '',
                    clusterId: cboCluster?.value || '',
                    accountSubId: cboSubAccount?.value || ''
                };
            }

            async function loadBalances() {
                const {
                    programId,
                    programSubId,
                    clusterId,
                    accountSubId
                } = getSelections();

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    resetBalances();
                    return;
                }

                try {
                    const url = new URL(ENDPOINT, window.location.origin);
                    url.searchParams.set('program_id', programId);
                    url.searchParams.set('program_sub_id', programSubId);
                    url.searchParams.set('cluster_id', clusterId);
                    url.searchParams.set('account_sub_id', accountSubId);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data?.message || 'Failed to load balances');

                    // Update table
                    setText('fin_law', data.fin_law);
                    setText('credit_movement', data.credit_movement);
                    setText('new_credit_status', data.new_credit_status);
                    setText('credit', data.credit);
                    setText('deadline_balance', data.deadline_balance);

                    recomputeRemaining();

                } catch (err) {
                    console.error(err);
                    resetBalances();
                }
            }

            // Load table on page load
            loadBalances();

            // Optional: reload table if user changes any dropdown
            [cboProgram, cboProgramSub, cboCluster, cboSubAccount].forEach(el => {
                el?.addEventListener('change', loadBalances);
            });

            // Update remaining budget live
            budgetInput?.addEventListener('input', recomputeRemaining);

        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const cboProgram = document.getElementById('cboProgram');
            const cboProgramSub = document.getElementById('cboProgramSub');
            const cboCluster = document.getElementById('cboCluster');
            const cboSubAccount = document.getElementById('cboSubAccount');
            const budgetInput = document.getElementById('budget');

            const ENDPOINT = "{{ route('budgetVoucher.editEarlyBalance', ['params' => $params]) }}";

            function toNumber(v) {
                v = (v || '').toString().replace(/,/g, '');
                return isNaN(parseFloat(v)) ? 0 : parseFloat(v);
            }

            function formatNumber(v) {
                return toNumber(v).toLocaleString('en-US', {
                    maximumFractionDigits: 2
                });
            }

            function setText(id, val) {
                const el = document.getElementById(id);
                if (el) el.textContent = formatNumber(val);
            }

            function resetBalances() {
                ['fin_law', 'credit_movement', 'new_credit_status', 'credit', 'deadline_balance', 'applying',
                    'remaining_credit'
                ]
                .forEach(id => setText(id, 0));
            }

            function recomputeRemaining() {
                const apply = toNumber(budgetInput?.value);
                const credit = toNumber(document.getElementById('credit')?.textContent);
                const deadline = toNumber(document.getElementById('deadline_balance')?.textContent);

                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                setText('deadline_balance', Math.max(deadline - apply, 0));
            }

            function getSelections() {
                return {
                    programId: cboProgram?.value || '',
                    programSubId: cboProgramSub?.value || '',
                    clusterId: cboCluster?.value || '',
                    accountSubId: cboSubAccount?.value || ''
                };
            }

            async function loadBalances() {
                const {
                    programId,
                    programSubId,
                    clusterId,
                    accountSubId
                } = getSelections();

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    resetBalances();
                    return;
                }

                try {
                    const url = new URL(ENDPOINT, window.location.origin);
                    url.searchParams.set('program_id', programId);
                    url.searchParams.set('program_sub_id', programSubId);
                    url.searchParams.set('cluster_id', clusterId);
                    url.searchParams.set('account_sub_id', accountSubId);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data?.message || 'Failed to load balances');

                    // Update table
                    setText('fin_law', data.fin_law);
                    setText('credit_movement', data.credit_movement);
                    setText('new_credit_status', data.new_credit_status);
                    setText('credit', data.credit);
                    setText('deadline_balance', data.deadline_balance);

                    recomputeRemaining();

                } catch (err) {
                    console.error(err);
                    resetBalances();
                }
            }

            // Load table on page load
            loadBalances();

            // Optional: reload table if user changes any dropdown
            [cboProgram, cboProgramSub, cboCluster, cboSubAccount].forEach(el => {
                el?.addEventListener('change', loadBalances);
            });

            // Update remaining budget live
            budgetInput?.addEventListener('input', recomputeRemaining);

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProgram');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboSubAccount');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const element = document.getElementById('cboAgency');
            let choicesInstance = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            $('#cboAgency').on('change', function() {
                const selected = $(this).val();
                let message = '';

                switch (selected) {
                    case '1':
                        message = 'You selected Choice 1';
                        break;
                    case '2':
                        message = 'You selected Choice 2';
                        break;
                    case '3':
                        message = 'You selected Choice 3';
                        break;
                    default:
                        message = '';
                }
                $('#resultDisplay').text(message);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ================== Choices Instances ================== */
            let programSubChoices = initChoices('#cboProgramSub');
            let agencyChoices = initChoices('#cboAgency');
            let clusterChoices = initChoices('#cboCluster');

            function initChoices(selector) {
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "ស្វែងរក..."
                });
            }

            /* ================== Helpers ================== */
            function resetSelect(selector) {
                $(selector).html(`<option value="">{{ __('forms.search...') }}</option>`);
            }

            function resetChoices(selector, instance) {
                instance.destroy();
                return initChoices(selector);
            }

            function loadOptions({
                url,
                data,
                targetSelect,
                instanceRefSetter
            }) {
                $.ajax({
                    url,
                    type: "GET",
                    data,
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter();
                    },
                    error: function() {
                        resetSelect(targetSelect);
                    }
                });
            }

            /* ================== Handlers ================== */
            function handleProgramChangeForProgramSub(programId, selectedId = null) {
                resetSelect('#cboProgramSub');
                programSubChoices = resetChoices('#cboProgramSub', programSubChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.edit.program_sub') }}",
                    data: {
                        program_id: programId,
                        selected_id: selectedId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => {
                        programSubChoices = resetChoices('#cboProgramSub', programSubChoices);
                    }
                });
            }

            function handleProgramChangeForAgency(programId, selectedId = null) {
                resetSelect('#cboAgency');
                agencyChoices = resetChoices('#cboAgency', agencyChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.edit.agency') }}",
                    data: {
                        program_id: programId,
                        selected_id: selectedId
                    },
                    targetSelect: '#cboAgency',
                    instanceRefSetter: () => {
                        agencyChoices = resetChoices('#cboAgency', agencyChoices);
                    }
                });
            }

            function handleProgramSubChangeForCluster(programSubId, selectedId = null) {
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);

                if (!programSubId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.edit.cluster') }}",
                    data: {
                        program_sub_id: programSubId,
                        selected_id: selectedId
                    },
                    targetSelect: '#cboCluster',
                    instanceRefSetter: () => {
                        clusterChoices = resetChoices('#cboCluster', clusterChoices);
                    }
                });
            }

            /* ================== PRELOAD EDIT DATA ================== */
            const programId = $('#cboProgram').val();
            const oldProgramSubId = $('#cboProgramSub').data('old');
            const oldAgencyId = $('#cboAgency').data('old');
            const oldClusterId = $('#cboCluster').data('old');

            if (programId) {
                handleProgramChangeForProgramSub(programId, oldProgramSubId);
                handleProgramChangeForAgency(programId, oldAgencyId);

                if (oldProgramSubId) {
                    handleProgramSubChangeForCluster(oldProgramSubId, oldClusterId);
                }
            }

            /* ================== EVENTS ================== */
            $('#cboProgram').on('change', function() {
                const programId = $(this).val();

                handleProgramChangeForProgramSub(programId);
                handleProgramChangeForAgency(programId);
                handleProgramSubChangeForCluster(null);
            });

            $('#cboProgramSub').on('change', function() {
                handleProgramSubChangeForCluster($(this).val());
            });
        });
    </script>

    {{-- <script>
        let cboLegalChoices;

        document.addEventListener('DOMContentLoaded', function() {

            const element = document.getElementById('cboLegalNumber');

            cboLegalChoices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // ===== for EDIT page =====
            let selectedLegal = "{{ old('cboLegalNumber', $data->legal_number_id ?? '') }}";

            if (selectedLegal) {
                cboLegalChoices.setChoiceByValue(selectedLegal);
            }

        });

        // when expense type change
        $('#cboExpenseType').change(function() {

            var expenseTypeId = $(this).val();

            $.ajax({
                url: '{!! route('budgetVoucher.by.expense_type_id') !!}',
                type: 'get',
                data: {
                    expense_type_id: expenseTypeId
                },
                success: function(data) {

                    cboLegalChoices.clearChoices();

                    cboLegalChoices.setChoices(
                        $(data).map(function() {
                            return {
                                value: $(this).val(),
                                label: $(this).text()
                            };
                        }).get(),
                        'value',
                        'label',
                        true
                    );

                }
            });

        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboExpenseType');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ================== Choices Instance ================== */
            let legalChoices = initChoices('#cboLegalNumber');

            function initChoices(selector) {
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "ស្វែងរក...",
                    shouldSort: false
                });
            }

            function resetSelect(selector) {
                $(selector).html(`<option value="">ស្វែងរក...</option>`);
            }

            function resetChoices(selector, instance) {
                instance.destroy();
                return initChoices(selector);
            }

            function loadLegalNumber(expenseTypeId, selectedId = null) {

                resetSelect('#cboLegalNumber');
                legalChoices = resetChoices('#cboLegalNumber', legalChoices);

                if (!expenseTypeId) return;

                $.ajax({
                    url: "{{ route('budgetVoucher.edit.expense_type_id') }}",
                    type: "GET",
                    data: {
                        expense_type_id: expenseTypeId,
                        selected_id: selectedId
                    },
                    success: function(html) {

                        $('#cboLegalNumber').html(html);

                        legalChoices = resetChoices('#cboLegalNumber', legalChoices);
                    }
                });
            }

            /* ================== PRELOAD EDIT DATA ================== */

            const expenseTypeId = $('#cboExpenseType').val();
            const oldLegalId = $('#cboLegalNumber').data('old');

            if (expenseTypeId) {
                loadLegalNumber(expenseTypeId, oldLegalId);
            }

            /* ================== EVENT ================== */

            $('#cboExpenseType').on('change', function() {
                loadLegalNumber($(this).val());
            });

        });
    </script>
@endsection
