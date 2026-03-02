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
                                        <label>{{ __('forms.legal.number') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            type="text" class="form-control" name="legalNumber" tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboAgency" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboAgency" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($agency as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->no }} -
                                                    {{ $item->name ?? 'មិនមានទិន្ន័យ' }}
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
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboSubAccount" name="cboSubAccount"
                                            required data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($beginVoucher as $bv)
                                                <option value="{{ $bv->account_sub_id }}"
                                                    data-program="{{ $bv->voucher_no }}">
                                                    {{ $bv->account_sub_id }} - {{ $bv->voucher_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboSubAccount')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Sub Account Number --}}
                                <div class="col-xl-4 col-md-6 d-none">
                                    <div class="form-group mb-3">
                                        <label for="no"
                                            class="form-label font-size-13 text-muted">{{ __('forms.cluster.act') }}</label>
                                        <input type="number" min="0" name="no" id="programInput" readonly
                                            placeholder="xxxxxxx" required class="form-control"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('no')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Program Code (auto-filled from JS) --}}
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
                                        <label for="cboExpenseType"
                                            class="form-label text-muted">{{ __('forms.voucher.type') }}</label>
                                        <select class="form-control" name="cboExpenseType" id="cboExpenseType" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($expenseType as $ts)
                                                <option value="{{ $ts->id }}">{{ $ts->name_kh }}</option>
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
                                    href="{{ route('budgetVoucher.index', $params) }}">{{ __('buttons.back') }}</a>

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
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script> --}}
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                if (!pristine.validate()) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vDescription').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subAccountSelect = document.getElementById('cboSubAccount');
            const programInput = document.getElementById('programInput');
            const budgetInput = document.getElementById('budget');
            const programHidden = document.getElementById('programHiddenInput');

            if (typeof Choices !== 'undefined') {
                new Choices(subAccountSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholderValue: '',
                    searchPlaceholderValue: 'ជ្រើសរើស...'
                });
            }

            // ✅ Handle SubAccount selection change
            subAccountSelect.addEventListener('change', function() {
                const selectedOption = subAccountSelect.options[subAccountSelect.selectedIndex];
                const subAccountId = this.value;
                const programCode = selectedOption.getAttribute('data-program');

                // Set program code in the input field
                if (programInput) {
                    programInput.value = programCode;
                }

                // If using a hidden input field to store program
                if (programHidden) {
                    programHidden.value = programCode;
                }

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
        // ---------- helpers ----------
        function initChoicesOnce(selectEl, opts = {}) {
            if (!selectEl) return null;
            if (selectEl.dataset.choicesInit === '1') return null; // our own guard
            selectEl.dataset.choicesInit = '1';
            return new Choices(selectEl, Object.assign({
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false
            }, opts));
        }
        const n = v => (isNaN(+v) ? 0 : +v);
        const fmt = v => n(v).toLocaleString('en-US', {
            maximumFractionDigits: 2
        });

        function setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = fmt(val);
        }

        function resetNumbers() {
            ['fin_law', 'credit_movement', 'new_credit_status', 'credit', 'deadline_balance', 'applying',
                'remaining_credit'
            ]
            .forEach(id => setText(id, 0));
        }

        // ---------- init once DOM ready ----------
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const subAccount = document.getElementById('cboSubAccount');
            const programInput = document.getElementById('programInput'); // readonly "no"
            const budgetInput = document.getElementById('budget');

            // named route -> correct URL always
            const earlyEP = "{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}";

            // Pristine
            if (form) {
                const pristine = new Pristine(form);
                form.addEventListener('submit', (e) => {
                    if (!pristine.validate()) e.preventDefault();
                });
            }

            // Summernote
            if (window.jQuery) {
                jQuery('#vDescription').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['color', ['color']]
                    ]
                });
            }

            // Choices — once per element
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

            // compute remaining credit
            function recomputeRemaining() {
                const apply = n(budgetInput?.value);
                const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0) budgetInput.value = '';
            }

            // fetch numbers
            async function fetchEarlyBalance(accountSubId, noVal) {
                if (!accountSubId || !noVal) {
                    resetNumbers();
                    return;
                }
                const url = new URL(earlyEP, window.location.origin);
                url.searchParams.set('account_sub_id', accountSubId);
                url.searchParams.set('no', noVal);

                try {
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data?.message || 'Early-balance error');

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
            }

            subAccount?.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const subId = this.value || '';
                const programNo = opt ? (opt.getAttribute('data-program') || '') : '';

                if (programInput) programInput.value = programNo;
                fetchEarlyBalance(subId, programNo);
            });

            budgetInput?.addEventListener('input', recomputeRemaining);
        });
    </script>
@endsection
