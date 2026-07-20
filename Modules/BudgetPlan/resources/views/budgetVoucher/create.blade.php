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
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.payment') }}</h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.payment') }}</a></li>
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
                                        <label for="cboPaymentVoucherNumber" class="form-label font-size-13 text-muted">
                                            {{ __('forms.payment.voucher') }}
                                        </label>
                                        <select id="cboPaymentVoucherNumber" class="form-select"
                                            name="cboPaymentVoucherNumber" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>

                                        @error('cboPaymentVoucherNumber')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.legal.name') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            type="text" class="form-control" id="legalName" name="legalName"
                                            tabindex="2" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="temporaryId"
                                                class="form-label mb-0">{{ __('forms.temporary.id') }}</label>

                                            <!-- Modern Switch -->
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipTemporaryId" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipTemporaryId" style="cursor: pointer;">
                                                    រំលង / មិនបញ្ចូល
                                                </label>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">#</span>
                                            <input class="form-control" id="temporaryId"
                                                data-pristine-required-message="{{ __('messages.required') }}"
                                                data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                                data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0"
                                                min="1" type="number" placeholder="{{ __('forms.temporary.id') }}"
                                                name="temporaryId" tabindex="2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.day.number') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            type="text" class="form-control"
                                            placeholder="{{ __('forms.day.number') }}" name="cbodayOfNumber"
                                            tabindex="2" />
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
                                                    {{ $p->no }}-
                                                    {{ $p->title }}</option>
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
                                                    {{ $bv->no }}-{{ $bv->name }}
                                                </option>
                                            @endforeach
                                        </select>
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

                                {{-- <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="fileInput">{{ __('forms.file.type') }}</label>
                                        <input type="file" id="fileInput" name="attachments[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple />
                                        <small class="form-text text-muted">Allowed types: PDF, DOC, DOCX</small>
                                        @error('attachments.*')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
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

                                        <!-- Added data-max-size="5" (in MB) and data-allowed-extensions -->
                                        <input type="file" id="fileInput" name="attachments[]" class="form-control"
                                            accept=".pdf,.doc,.docx" multiple required data-max-size="5"
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
                                    <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                    @error('txtDescription')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    {{-- <script>
        const transactionDateInput = document.getElementById('transactionDate');
        if (transactionDateInput) {
            flatpickr(transactionDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: transactionDateInput.value || null
            });
        }
    </script>
    <script>
        const requestDateInput = document.getElementById('requestDate');
        if (requestDateInput) {
            flatpickr(requestDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: requestDateInput.value || null
            });
        }
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');

            // 1. Initialize Pristine
            let pristine = new Pristine(form, {
                classTo: 'form-group',
                errorClass: 'has-danger',
                successClass: 'has-success',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'pristine-error text-help text-danger font-size-14 mt-1'
            });

            // 2. Initialize Flatpickr with Auto-Validation Sync
            const initFlatpickr = (elementId) => {
                const inputEl = document.getElementById(elementId);
                if (!inputEl) return;

                flatpickr(inputEl, {
                    dateFormat: 'Y-m-d', // Submitted to backend
                    altInput: true,
                    altFormat: 'd/m/Y', // Displayed to user
                    allowInput: true,
                    defaultDate: inputEl.value || null,
                    // THE FIX: When a user picks or changes a date, tell Pristine to validate
                    onChange: function(selectedDates, dateStr, instance) {
                        pristine.validate(instance.element);
                    },
                    onClose: function(selectedDates, dateStr, instance) {
                        pristine.validate(instance.element);
                    }
                });
            };

            initFlatpickr('transactionDate');
            initFlatpickr('requestDate');

            // 3. Setup Skip Temporary ID Logic
            const tempIdInput = document.getElementById('temporaryId');
            const skipCheckbox = document.getElementById('skipTemporaryId');

            if (skipCheckbox && tempIdInput) {
                skipCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        tempIdInput.value = '';
                        tempIdInput.readOnly = true;
                        tempIdInput.classList.add('bg-light', 'text-muted');

                        // Strip validation rules when skipped
                        tempIdInput.removeAttribute('required');
                        tempIdInput.removeAttribute('data-pristine-required-message');
                        tempIdInput.removeAttribute('data-pristine-min-message');
                        tempIdInput.removeAttribute('data-pristine-integer-message');
                    } else {
                        tempIdInput.value = '0';
                        tempIdInput.readOnly = false;
                        tempIdInput.classList.remove('bg-light', 'text-muted');

                        // Restore validation rules
                        tempIdInput.setAttribute('required', 'true');
                        tempIdInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                        tempIdInput.setAttribute('data-pristine-min-message', 'លំដាប់ ត្រូវតែធំជាងសូន្យ');
                        tempIdInput.setAttribute('data-pristine-integer-message', 'លំដាប់ ត្រូវតែលេខ');
                    }

                    // Refresh Pristine to register the attribute changes
                    pristine.destroy();
                    pristine = new Pristine(form, {
                        classTo: 'form-group',
                        errorClass: 'has-danger',
                        successClass: 'has-success',
                        errorTextParent: 'form-group',
                        errorTextTag: 'div',
                        errorTextClass: 'pristine-error text-help text-danger font-size-14 mt-1'
                    });
                });
            }

            // 4. Setup Summernote Validation Sync
            $('#vDescription').on('summernote.change', function(we, contents) {
                const cleanContent = contents.replace(/<\/?[^>]+(>|$)/g, "").trim();
                document.getElementById('vDescription').value = cleanContent === '' ? '' : contents;
                pristine.validate(document.getElementById('vDescription'));
            });

            // 5. Setup Dropdowns (Choices.js / Selects) Validation Sync
            const selectFields = form.querySelectorAll('select');
            selectFields.forEach(select => {
                select.addEventListener('change', function() {
                    pristine.validate(this);
                });
            });

            // 6. Master Form Submit Verification
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop default form submission

                // Final sync for Summernote
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;

                    // Temporarily enable editor so browser permits form submission
                    if ($('#vDescription').summernote('isDisabled')) {
                        $('#vDescription').summernote('enable');
                    }
                }

                // Execute full form validation across ALL fields
                const isValid = pristine.validate();

                if (isValid) {
                    // Submit form cleanly to Laravel
                    form.submit();
                } else {
                    // Scroll smoothly to the first validation error
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

        $('form').on('submit', function() {
            // Re-enable before submit so the browser sends the value to Laravel
            $('#vDescription').summernote('enable');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
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
            // const programInput = document.getElementById('programInput'); // readonly "no"
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
            async function fetchEarlyBalance() {

                const programId = document.getElementById('cboProgram').value;
                const programSubId = document.getElementById('cboProgramSub').value;
                const clusterId = document.getElementById('cboCluster').value;
                const accountSubId = document.getElementById('cboSubAccount').value;

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    resetNumbers();
                    return;
                }

                const url = new URL(earlyEP, window.location.origin);

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
            }
            document.getElementById('cboProgram').addEventListener('change', fetchEarlyBalance);
            document.getElementById('cboProgramSub').addEventListener('change', fetchEarlyBalance);
            document.getElementById('cboCluster').addEventListener('change', fetchEarlyBalance);
            document.getElementById('cboSubAccount').addEventListener('change', fetchEarlyBalance);

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
        document.addEventListener('DOMContentLoaded', function() {

            // ========= Choices Instances =========
            let programSubChoices = new Choices('#cboProgramSub', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            let agencyChoices = new Choices('#cboAgency', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            let clusterChoices = new Choices('#cboCluster', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            // ========= Helpers =========
            function resetSelect(selector) {
                $(selector).html(`<option value="">{{ __('forms.search...') }}</option>`);
            }

            function resetChoices(selector, instance) {
                instance.destroy();
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "ស្វែងរក..."
                });
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
                        // optional: keep empty if error
                        resetSelect(targetSelect);
                    }
                });
            }

            // ========= Script 1: Program -> ProgramSub =========
            function handleProgramChangeForProgramSub(programId) {
                resetSelect('#cboProgramSub');
                programSubChoices = resetChoices('#cboProgramSub', programSubChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.by.program_sub') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => {
                        programSubChoices = resetChoices('#cboProgramSub', programSubChoices);
                    }
                });
            }

            // ========= Script 2: Program -> Agency =========
            function handleProgramChangeForAgency(programId) {
                resetSelect('#cboAgency');
                agencyChoices = resetChoices('#cboAgency', agencyChoices);

                if (!programId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.by.agency') }}",
                    data: {
                        program_id: programId
                    },
                    targetSelect: '#cboAgency',
                    instanceRefSetter: () => {
                        agencyChoices = resetChoices('#cboAgency', agencyChoices);
                    }
                });
            }

            // ========= Script 3: ProgramSub -> Cluster =========
            function handleProgramSubChangeForCluster(programSubId) {
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);

                if (!programSubId) return;

                loadOptions({
                    url: "{{ route('budgetVoucher.by.cluster') }}",
                    data: {
                        program_sub_id: programSubId
                    },
                    targetSelect: '#cboCluster',
                    instanceRefSetter: () => {
                        clusterChoices = resetChoices('#cboCluster', clusterChoices);
                    }
                });
            }

            // ========= Events =========
            $('#cboProgram').on('change', function() {
                const programId = $(this).val();

                // when program changes -> always clear cluster too
                handleProgramChangeForProgramSub(programId);
                handleProgramChangeForAgency(programId);
                handleProgramSubChangeForCluster(null); // reset cluster
            });

            $('#cboProgramSub').on('change', function() {
                const programSubId = $(this).val();
                handleProgramSubChangeForCluster(programSubId);
            });

        });
    </script>

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
    {{-- <script>
        let cboLegalChoices;

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboPaymentVoucherNumber');

            cboLegalChoices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        $('#cboExpenseType').change(function() {
            var expenseTypeId = $(this).val();

            $.ajax({
                url: '{!! route('budgetVoucher.by.expense_type_id') !!}',
                type: 'get',
                global: false,
                data: {
                    expense_type_id: expenseTypeId
                },
                success: function(data) {

                    // remove old choices
                    cboLegalChoices.clearChoices();

                    // append new options
                    cboLegalChoices.setChoices(
                        $(data).map(function() {
                            return {
                                value: $(this).val(),
                                label: $(this).text(),
                                selected: false
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

    {{-- <script>
        let cboLegalChoices;

        document.addEventListener('DOMContentLoaded', function() {

            const element = document.getElementById('cboPaymentVoucherNumber');

            cboLegalChoices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

        });

        $('#cboExpenseType').on('change', function() {

            var expenseTypeId = $(this).val();

            /* ===== Reset cboPaymentVoucherNumber ===== */
            cboLegalChoices.clearStore(); // remove selected item
            cboLegalChoices.clearChoices(); // remove all options

            // add default option
            // cboLegalChoices.setChoices([{
            //     value: '',
            //     label: 'ស្វែងរក...',
            //     selected: true,
            //     disabled: true
            // }], 'value', 'label', true);

            if (!expenseTypeId) return;

            /* ===== Load new options ===== */
            $.ajax({
                url: "{{ route('budgetVoucher.get.expense_type_id') }}",
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

                    cboLegalChoices.setChoices(
                        options,
                        'value',
                        'label',
                        true
                    );

                }
            });

        });
    </script> --}}

    <script>
        let cboLegalChoices;

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboPaymentVoucherNumber');
            const legalNameInput = document.getElementById('legalName');

            // Initialize Choices.js
            cboLegalChoices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            // Auto-populate and toggle read-only/disabled status when a Voucher Number is selected
            element.addEventListener('change', function(event) {
                const choices = cboLegalChoices.getValue();

                // Check if a valid choice with custom properties is selected
                if (choices && choices.customProperties && choices.value !== '') {
                    // 1. Populate and lock Legal Name (Standard input)
                    legalNameInput.value = choices.customProperties.legal_name || '';
                    legalNameInput.readOnly = true;

                    // 2. Populate and lock Summernote Description
                    $('#vDescription').summernote('code', choices.customProperties.description || '');
                    $('#vDescription').summernote('disable');
                } else {
                    // 1. Clear and unlock Legal Name
                    legalNameInput.value = '';
                    legalNameInput.readOnly = false;

                    // 2. Clear and unlock Summernote Description
                    $('#vDescription').summernote('code', '');
                    $('#vDescription').summernote('enable');
                }
            });
        });

        $(document).ready(function() {
            // Initialize Summernote
            $('#vDescription').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['color', ['color']],
                ]
            });

            // Handle Expense Type change
            $('#cboExpenseType').on('change', function() {
                var expenseTypeId = $(this).val();

                // Reset Choices
                cboLegalChoices.clearStore();
                cboLegalChoices.clearChoices();

                // Reset and unlock Legal Name
                $('#legalName').val('').prop('readonly', false);

                // Reset and unlock Summernote Description
                $('#vDescription').summernote('code', '');
                $('#vDescription').summernote('enable');

                if (!expenseTypeId) return;

                // Show loading state in the dropdown
                cboLegalChoices.setChoices([{
                    value: '',
                    label: 'កំពុងផ្ទុក...',
                    disabled: true,
                    selected: true
                }], 'value', 'label', true);

                $.ajax({
                    url: "{{ route('budgetVoucher.get.expense_type_id') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        expense_type_id: expenseTypeId
                    },
                    success: function(data) {
                        cboLegalChoices.clearChoices();
                        cboLegalChoices.setChoices(data, 'value', 'label', true);
                    },
                    error: function() {
                        cboLegalChoices.clearChoices();
                        console.error("Failed to load voucher numbers.");
                    }
                });
            });
        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            const checkbox = document.getElementById('skipLegalNumber');
            const input = document.getElementById('legalNumber');

            checkbox.addEventListener('change', function() {

                if (this.checked) {
                    input.value = '';
                    input.disabled = true;
                    input.removeAttribute('required');
                } else {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                    input.value = 0;
                }

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tempIdInput = document.getElementById('temporaryId');
            const skipCheckbox = document.getElementById('skipTemporaryId');

            skipCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    tempIdInput.value = ''; // Clear the value
                    tempIdInput.readOnly = true; // Prevent typing
                    tempIdInput.classList.add('bg-light', 'text-muted'); // Dim the background

                    // Optional: Remove required attribute dynamically if using Pristine.js validation
                    tempIdInput.removeAttribute('required');
                } else {
                    tempIdInput.value = '0'; // Reset to default
                    tempIdInput.readOnly = false; // Enable typing
                    tempIdInput.classList.remove('bg-light', 'text-muted');
                }
            });
        });
    </script> --}}

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const form = document.getElementById('pristine-valid-example');
        //     const fileInput = document.getElementById('fileInput');
        //     const skipFileCheckbox = document.getElementById('skipFileInput');

        //     // 1. Helper to initialize Pristine
        //     const createPristineInstance = () => {
        //         return new Pristine(form, {
        //             classTo: 'form-group',
        //             errorClass: 'has-danger',
        //             successClass: 'has-success',
        //             errorTextParent: 'form-group',
        //             errorTextTag: 'div',
        //             errorTextClass: 'pristine-error text-help text-danger font-size-12 mt-1'
        //         });
        //     };

        //     let pristine = createPristineInstance();

        //     // ==========================================
        //     // CUSTOM FILE VALIDATORS
        //     // ==========================================
        //     const applyFileValidators = () => {
        //         if (!fileInput) return;

        //         const maxMB = parseFloat(fileInput.getAttribute('data-max-size')) || 5;
        //         const maxBytes = maxMB * 1024 * 1024;
        //         const allowedExts = (fileInput.getAttribute('data-allowed-extensions') || '')
        //             .toLowerCase()
        //             .split(',')
        //             .map(ext => ext.trim());

        //         // --- Size Validator ---
        //         pristine.addValidator(fileInput, function(value) {
        //             // IMPROVEMENT 1: Immediately pass validation if the field is disabled or empty
        //             if (fileInput.disabled || fileInput.files.length === 0) return true;

        //             for (let i = 0; i < fileInput.files.length; i++) {
        //                 if (fileInput.files[i].size > maxBytes) return false;
        //             }
        //             return true;
        //         }, `ទំហំឯកសារនីមួយៗមិនត្រូវលើសពី ${maxMB}MB ឡើយ`, 2, false);

        //         // --- Extension Validator ---
        //         pristine.addValidator(fileInput, function(value) {
        //                 // IMPROVEMENT 1: Immediately pass validation if the field is disabled or empty
        //                 if (fileInput.disabled || fileInput.files.length === 0) return true;

        //                 for (let i = 0; i < fileInput.files.length; i++) {
        //                     const fileExt = fileInput.files[i].name.split('.').pop().toLowerCase();
        //                     if (!allowedExts.includes(fileExt)) return false;
        //                 }
        //                 return true;
        //             }, `ប្រភេទឯកសារមិនត្រឹមត្រូវ! អនុញ្ញាតតែ (${allowedExts.join(', '.toUpperCase())})`, 2,
        //             false);
        //     };

        //     applyFileValidators();

        //     // ==========================================
        //     // SKIP FILE TOGGLE LOGIC
        //     // ==========================================
        //     if (skipFileCheckbox && fileInput) {
        //         skipFileCheckbox.addEventListener('change', function() {
        //             if (this.checked) {
        //                 fileInput.value = ''; // Wipe selected files
        //                 fileInput.disabled = true; // Lock input (excluded from POST and Pristine)
        //                 fileInput.classList.add('bg-light', 'text-muted');
        //                 fileInput.removeAttribute('required');
        //                 fileInput.removeAttribute('data-pristine-required-message');
        //             } else {
        //                 fileInput.disabled = false; // Unlock input
        //                 fileInput.classList.remove('bg-light', 'text-muted');
        //                 fileInput.setAttribute('required', 'true');
        //                 fileInput.setAttribute('data-pristine-required-message',
        //                     "{{ __('messages.required') }}");
        //             }

        //             // IMPROVEMENT 2: Reset any existing red error text instantly before rebuilding
        //             pristine.reset(fileInput);

        //             pristine.destroy();
        //             pristine = createPristineInstance();
        //             applyFileValidators();
        //         });

        //         // Validate immediately when user selects files
        //         fileInput.addEventListener('change', function() {
        //             pristine.validate(this);
        //         });
        //     }

        //     // ==========================================
        //     // MASTER FORM SUBMIT VERIFICATION
        //     // ==========================================
        //     form.addEventListener('submit', function(e) {
        //         e.preventDefault(); // Stop native submission

        //         // IMPROVEMENT 3: Safety check before running final validation
        //         // If skip is checked, ensure the input is definitely disabled and not required
        //         if (skipFileCheckbox && skipFileCheckbox.checked) {
        //             fileInput.disabled = true;
        //             fileInput.removeAttribute('required');
        //             pristine.reset(fileInput); // Force clear any lingering visual errors
        //         }

        //         // Sync Summernote if it exists on the page
        //         if ($('#vDescription').length) {
        //             const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
        //                 '#vDescription').summernote('code');
        //             document.getElementById('vDescription').value = summernoteContent;
        //             if ($('#vDescription').summernote('isDisabled')) {
        //                 $('#vDescription').summernote('enable');
        //             }
        //         }

        //         // Execute validation across all active fields
        //         const isValid = pristine.validate();

        //         if (isValid) {
        //             // Form is valid! Submit cleanly to Laravel backend
        //             form.submit();
        //         } else {
        //             // Form is invalid! Smoothly scroll the user to the first error
        //             const firstError = form.querySelector('.has-danger');
        //             if (firstError) {
        //                 firstError.scrollIntoView({
        //                     behavior: 'smooth',
        //                     block: 'center'
        //                 });
        //             }
        //         }
        //     });
        // });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');

            // 1. Helper to create Pristine instance
            const createPristineInstance = () => {
                return new Pristine(form, {
                    classTo: 'form-group',
                    errorClass: 'has-danger',
                    successClass: 'has-success',
                    errorTextParent: 'form-group',
                    errorTextTag: 'div',
                    errorTextClass: 'pristine-error text-help text-danger font-size-12 mt-1'
                });
            };

            let pristine = createPristineInstance();

            // ==========================================
            // CUSTOM FILE VALIDATORS (WITH SKIP BYPASS)
            // ==========================================
            const applyFileValidators = () => {
                if (!fileInput) return;

                const maxMB = parseFloat(fileInput.getAttribute('data-max-size')) || 5;
                const maxBytes = maxMB * 1024 * 1024;
                const allowedExts = (fileInput.getAttribute('data-allowed-extensions') || '')
                    .toLowerCase()
                    .split(',')
                    .map(ext => ext.trim());

                // --- Size Validator ---
                pristine.addValidator(fileInput, function(value) {
                    // LAYER 1 BYPASS: Immediately pass if Skip is checked, input is disabled, or empty
                    if ((skipFileCheckbox && skipFileCheckbox.checked) || fileInput.disabled ||
                        fileInput.files.length === 0) {
                        return true;
                    }

                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (fileInput.files[i].size > maxBytes) return false;
                    }
                    return true;
                }, `ទំហំឯកសារនីមួយៗមិនត្រូវលើសពី ${maxMB}MB ឡើយ`, 2, false);

                // --- Extension Validator ---
                pristine.addValidator(fileInput, function(value) {
                        // LAYER 1 BYPASS: Immediately pass if Skip is checked, input is disabled, or empty
                        if ((skipFileCheckbox && skipFileCheckbox.checked) || fileInput.disabled ||
                            fileInput.files.length === 0) {
                            return true;
                        }

                        for (let i = 0; i < fileInput.files.length; i++) {
                            const fileExt = fileInput.files[i].name.split('.').pop().toLowerCase();
                            if (!allowedExts.includes(fileExt)) return false;
                        }
                        return true;
                    }, `ប្រភេទឯកសារមិនត្រឹមត្រូវ! អនុញ្ញាតតែ (${allowedExts.join(', '.toUpperCase())})`, 2,
                    false);
            };

            applyFileValidators();

            // ==========================================
            // SKIP FILE TOGGLE LOGIC
            // ==========================================
            if (skipFileCheckbox && fileInput) {
                skipFileCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        fileInput.value = ''; // 1. Clear any chosen files
                        fileInput.disabled = true; // 2. Disable input so browser ignores it
                        fileInput.classList.add('bg-light', 'text-muted');

                        // 3. Strip Pristine validation attributes
                        fileInput.removeAttribute('required');
                        fileInput.removeAttribute('data-pristine-required-message');

                        // LAYER 2 BYPASS: Instantly remove visual red borders & DOM errors
                        pristine.reset(fileInput);
                    } else {
                        fileInput.disabled = false; // Unlock input
                        fileInput.classList.remove('bg-light', 'text-muted');

                        // Restore required rules
                        fileInput.setAttribute('required', 'true');
                        fileInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                    }

                    // Rebuild Pristine and re-register custom validators
                    pristine.destroy();
                    pristine = createPristineInstance();
                    applyFileValidators();
                });

                // Validate immediately on file selection
                fileInput.addEventListener('change', function() {
                    pristine.validate(this);
                });
            }

            // ==========================================
            // MASTER FORM SUBMIT VERIFICATION
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Intercept form submission

                // LAYER 3 BYPASS: Pre-Submit Safety Enforcement
                // Ensure disabled state and strip attributes right before pristine.validate() runs
                if (skipFileCheckbox && skipFileCheckbox.checked) {
                    fileInput.value = '';
                    fileInput.disabled = true;
                    fileInput.removeAttribute('required');
                    pristine.reset(fileInput); // Force wipe any lingering red text
                }

                // Sync Summernote if present
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                    if ($('#vDescription').summernote('isDisabled')) {
                        $('#vDescription').summernote('enable');
                    }
                }

                // Execute full form validation across all active fields
                const isValid = pristine.validate();

                if (isValid) {
                    // Form is valid! Allow submission to Laravel backend
                    form.submit();
                } else {
                    // Smoothly scroll user to the first validation error on page
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
    </script> --}}
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
                    errorTextClass: 'pristine-error text-help text-danger font-size-12 mt-1'
                });
            };

            let pristine = createPristineInstance();

            // Helper function to safely refresh Pristine after toggling ANY skip switch
            const refreshPristine = () => {
                pristine.destroy();
                pristine = createPristineInstance();
                applyFileValidators(); // Must re-attach file rules after destroy
            };

            // ==========================================
            // 2. SKIP LEGAL NUMBER LOGIC
            // ==========================================
            const skipLegalCheckbox = document.getElementById('skipLegalNumber');
            const legalInput = document.getElementById('legalNumber');

            if (skipLegalCheckbox && legalInput) {
                skipLegalCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        legalInput.value = '';
                        legalInput.disabled = true;
                        legalInput.removeAttribute('required');
                        legalInput.removeAttribute('data-pristine-required-message');
                        pristine.reset(legalInput); // Wipe lingering red error text
                    } else {
                        legalInput.disabled = false;
                        legalInput.value = '0';
                        legalInput.setAttribute('required', 'true');
                        legalInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                    }
                    refreshPristine();
                });
            }

            // ==========================================
            // 3. SKIP TEMPORARY ID LOGIC
            // ==========================================
            const tempIdInput = document.getElementById('temporaryId');
            const skipTempCheckbox = document.getElementById('skipTemporaryId');

            if (skipTempCheckbox && tempIdInput) {
                skipTempCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        tempIdInput.value = '';
                        tempIdInput.readOnly = true;
                        tempIdInput.disabled = true; // Use disabled so browser ignores it
                        tempIdInput.classList.add('bg-light', 'text-muted');
                        tempIdInput.removeAttribute('required');
                        tempIdInput.removeAttribute('data-pristine-required-message');
                        pristine.reset(tempIdInput);
                    } else {
                        tempIdInput.value = '0';
                        tempIdInput.readOnly = false;
                        tempIdInput.disabled = false;
                        tempIdInput.classList.remove('bg-light', 'text-muted');
                        tempIdInput.setAttribute('required', 'true');
                        tempIdInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                    }
                    refreshPristine();
                });
            }

            // ==========================================
            // 4. CUSTOM FILE VALIDATORS & SKIP LOGIC
            // ==========================================
            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');

            const applyFileValidators = () => {
                if (!fileInput) return;

                const maxMB = parseFloat(fileInput.getAttribute('data-max-size')) || 5;
                const maxBytes = maxMB * 1024 * 1024;
                const allowedExts = (fileInput.getAttribute('data-allowed-extensions') || '')
                    .toLowerCase().split(',').map(ext => ext.trim());

                // --- Size Validator ---
                pristine.addValidator(fileInput, function(value) {
                    if ((skipFileCheckbox && skipFileCheckbox.checked) || fileInput.disabled ||
                        fileInput.files.length === 0) return true;
                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (fileInput.files[i].size > maxBytes) return false;
                    }
                    return true;
                }, `ទំហំឯកសារនីមួយៗមិនត្រូវលើសពី ${maxMB}MB ឡើយ`, 2, false);

                // --- Extension Validator ---
                pristine.addValidator(fileInput, function(value) {
                        if ((skipFileCheckbox && skipFileCheckbox.checked) || fileInput.disabled ||
                            fileInput.files.length === 0) return true;
                        for (let i = 0; i < fileInput.files.length; i++) {
                            const fileExt = fileInput.files[i].name.split('.').pop().toLowerCase();
                            if (!allowedExts.includes(fileExt)) return false;
                        }
                        return true;
                    }, `ប្រភេទឯកសារមិនត្រឹមត្រូវ! អនុញ្ញាតតែ (${allowedExts.join(', '.toUpperCase())})`, 2,
                    false);
            };

            applyFileValidators(); // Apply on load

            if (skipFileCheckbox && fileInput) {
                skipFileCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        fileInput.value = '';
                        fileInput.disabled = true;
                        fileInput.classList.add('bg-light', 'text-muted');
                        fileInput.removeAttribute('required');
                        fileInput.removeAttribute('data-pristine-required-message');
                        pristine.reset(fileInput);
                    } else {
                        fileInput.disabled = false;
                        fileInput.classList.remove('bg-light', 'text-muted');
                        fileInput.setAttribute('required', 'true');
                        fileInput.setAttribute('data-pristine-required-message',
                            "{{ __('messages.required') }}");
                    }
                    refreshPristine();
                });

                fileInput.addEventListener('change', function() {
                    pristine.validate(this);
                });
            }

            // ==========================================
            // 5. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop native submission immediately

                // Pre-submit Safety: Ensure disabled fields are stripped of validation rules
                if (skipFileCheckbox && skipFileCheckbox.checked && fileInput) {
                    fileInput.removeAttribute('required');
                    pristine.reset(fileInput);
                }
                if (skipLegalCheckbox && skipLegalCheckbox.checked && legalInput) {
                    legalInput.removeAttribute('required');
                    pristine.reset(legalInput);
                }
                if (skipTempCheckbox && skipTempCheckbox.checked && tempIdInput) {
                    tempIdInput.removeAttribute('required');
                    pristine.reset(tempIdInput);
                }

                // Sync Summernote HTML before validating
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                }

                // Run master validation
                const isValid = pristine.validate();

                if (isValid) {
                    // THE FIX: Browsers ignore "disabled" fields during POST requests!
                    // We must briefly enable them right before submitting so Laravel receives empty/null values instead of throwing a missing key error.
                    if (fileInput && skipFileCheckbox && skipFileCheckbox.checked) fileInput.disabled =
                        false;
                    if (legalInput && skipLegalCheckbox && skipLegalCheckbox.checked) legalInput.disabled =
                        false;
                    if (tempIdInput && skipTempCheckbox && skipTempCheckbox.checked) tempIdInput.disabled =
                        false;
                    if ($('#vDescription').length && $('#vDescription').summernote('isDisabled')) $(
                        '#vDescription').summernote('enable');

                    // Submit cleanly using HTMLFormElement.prototype to prevent event loops
                    HTMLFormElement.prototype.submit.call(form);
                } else {
                    // Smoothly scroll to the first red error on the page
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
