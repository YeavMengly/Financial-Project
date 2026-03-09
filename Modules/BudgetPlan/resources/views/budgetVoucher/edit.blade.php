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
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('menus.voucher') }}</a></li>
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
                                    <label>{{ __('forms.legal.number') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="legalNumber"
                                        value="{{ old('legalNumber', $module->legalNumber) }}" tabindex="2" />
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('forms.legal.name') }}</label>
                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        type="text" class="form-control" name="legalName"
                                        value="{{ old('legalName', $module->legalName) }}" tabindex="2" />
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cboAgency" class="form-label font-size-13 text-muted">
                                        {{ __('forms.agency') }}
                                    </label>
                                    <select class="form-control" id="cboAgency" name="cboAgency" required>
                                        <option value="">{{ __('forms.search...') }}</option>
                                        @foreach ($agency as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $module->agency_id ? 'selected' : '' }}>
                                                {{ $item->no }} - {{ $item->name ?? 'មិនមានទិន្ន័យ' }}
                                            </option>
                                        @endforeach
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
                                        @foreach ($beginVoucher as $bv)
                                            <option value="{{ $bv->account_sub_id }}" data-program="{{ $bv->voucher_no }}"
                                                {{ old('cboSubAccount', $module->account_sub_id) == $bv->account_sub_id ? 'selected' : '' }}>
                                                {{ $bv->account_sub_id }} - {{ $bv->voucher_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cboSubAccount')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="programInput">{{ __('forms.program.code') }}</label>
                                    <input type="number" min="0" name="no" id="programInput" readonly required
                                        class="form-control" value="{{ old('no', $module->no) }}"
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('program')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

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

                            <div class="col-lg-4 col-md-6">
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
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date" class="form-label">{{ __('forms.select_date') }}</label>
                                    <input type="text" id="datepicker-basic" name="date" class="form-control"
                                        value="{{ old('date', $module->date) }}"
                                        placeholder="{{ __('forms.select_date') }}" required
                                        data-pristine-required-message="{{ __('messages.required') }}" />
                                    @error('date')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fileInput">{{ __('forms.file.type') }}</label>
                                    <input type="file" id="fileInput" name="attachments[]" class="form-control"
                                        value="{{ old('') }}" accept=".pdf,.doc,.docx" multiple />
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
                                    data-pristine-required-message="{{ __('messages.required') }}">{{ old('txtDescription', $module->txtDescription) }}</textarea>
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
    <script>
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
    </script>
    <script>
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
    </script>
@endsection
