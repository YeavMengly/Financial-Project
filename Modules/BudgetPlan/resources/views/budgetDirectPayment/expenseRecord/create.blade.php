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
        /* Optional: Turn border green when Pristine marks the form-group as valid */
        .has-success .form-control,
        .has-success .form-select,
        .has-success .choices__inner {
            border-color: #198754 !important;
        }
    </style>
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"> {{ __('menus.expense.record.book') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.expense.record.book') }}</a></li>
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
                            action="{{ route('budgetDirectPayment.expenseRecord.store', $params) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.legal.id.payment') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            type="number" class="form-control" placeholder="{{ __('forms.legal.id') }}"
                                            name="legalID" tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="legalDate" class="form-label">{{ __('forms.select_date') }}</label>
                                        <input type="text" id="legalDate" name="legalDate" class="form-control"
                                            placeholder="{{ __('forms.select_legal_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.payment.voucher') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            type="number" class="form-control"
                                            placeholder="{{ __('forms.payment.voucher.number') }}" name="paymentVoucher"
                                            tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.legal.name') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            type="text" class="form-control" placeholder="{{ __('forms.legal.name') }}"
                                            name="legalName" tabindex="2" />
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
                                                <option value="{{ $p->id }}">{{ $p->no }}-{{ $p->title }}</option>
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
                                                <option value="{{ $bv->no }}">{{ $bv->no }}-{{ $bv->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="budget">{{ __('forms.budget') }}</label>
                                        <input type="number" min="0" name="budget" id="budget" required
                                            class="form-control" placeholder="{{ __('forms.budget') }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
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
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="vDescription">{{ __('forms.document.description') }}</label>
                                    <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                        data-pristine-required-message="{{ __('messages.required') }}"></textarea>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a href="{{ url()->current() }}" class="btn btn-danger" style="width: 80px;">{{ __('buttons.delete') }}</a>
                                <a class="btn btn-dark" href="{{ route('budgetDirectPayment.expenseRecord.index', $params) }}">{{ __('buttons.back') }}</a>
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
    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>

    <!-- Master Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            if (!form) return;

            // ==========================================
            // 1. PRISTINE INITIALIZATION (With Bootstrap 5 UX)
            // ==========================================
            const pristine = new Pristine(form, {
                classTo: 'form-group',
                errorClass: 'has-danger',
                successClass: 'has-success',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'pristine-error text-help text-danger font-size-14 mt-1'
            });

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
            initFlatpickr('legalDate');
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
                            document.getElementById('vDescription').value = clean === '' ? '' : contents;
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

            const resetSelect = (selector) => $(selector).html(`<option value="">{{ __('forms.search...') }}</option>`);
            const resetChoices = (selector, instance) => {
                instance.destroy();
                return new Choices(selector, defaultChoicesOpts);
            };

            const loadOptions = ({ url, data, targetSelect, instanceRefSetter }) => {
                $.ajax({
                    url, type: "GET", data,
                    success: function(html) {
                        $(targetSelect).html(html);
                        instanceRefSetter();
                    },
                    error: () => resetSelect(targetSelect)
                });
            };

            // Trigger validation sync when choices change
            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => pristine.validate(select));
            });

            // ==========================================
            // 5. AJAX CASCADING DROPDOWNS
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
                    url: "{{ route('budgetDirectPayment.expenseRecord.by.program_sub') }}",
                    data: { program_id: programId },
                    targetSelect: '#cboProgramSub',
                    instanceRefSetter: () => programSubChoices = resetChoices('#cboProgramSub', programSubChoices)
                });

                loadOptions({
                    url: "{{ route('budgetDirectPayment.expenseRecord.by.agency') }}",
                    data: { program_id: programId },
                    targetSelect: '#cboAgency',
                    instanceRefSetter: () => agencyChoices = resetChoices('#cboAgency', agencyChoices)
                });
            });

            $('#cboProgramSub').on('change', function() {
                const programSubId = $(this).val();
                resetSelect('#cboCluster');
                clusterChoices = resetChoices('#cboCluster', clusterChoices);
                if (!programSubId) return;

                loadOptions({
                    url: "{{ route('budgetDirectPayment.expenseRecord.by.cluster') }}",
                    data: { program_sub_id: programSubId },
                    targetSelect: '#cboCluster',
                    instanceRefSetter: () => clusterChoices = resetChoices('#cboCluster', clusterChoices)
                });
            });

            // ==========================================
            // 6. EARLY BALANCE & CREDIT CALCULATIONS
            // ==========================================
            const n = v => (isNaN(+v) ? 0 : +v);
            const fmt = v => n(v).toLocaleString('en-US', { maximumFractionDigits: 2 });
            const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = fmt(val); };
            const resetNumbers = () => ['fin_law', 'credit_movement', 'new_credit_status', 'credit', 'deadline_balance', 'applying', 'remaining_credit'].forEach(id => setText(id, 0));

            const budgetInput = document.getElementById('budget');
            const recomputeRemaining = () => {
                const apply = n(budgetInput?.value);
                const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0) budgetInput.value = ''; // Prevent negative apply
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

                const url = new URL("{{ route('budgetDirectPayment.expenseRecord.getEarlyBalance', ['params' => $params]) }}", window.location.origin);
                url.searchParams.set('program_id', programId);
                url.searchParams.set('program_sub_id', programSubId);
                url.searchParams.set('cluster_id', clusterId);
                url.searchParams.set('account_sub_id', accountSubId);

                try {
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
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
            // 7. MASTER FORM SUBMIT GATEKEEPER
            // ==========================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Force Summernote sync just in case
                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $('#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                }

                const isValid = pristine.validate();

                if (isValid) {
                    // Safe form submission without javascript loops
                    HTMLFormElement.prototype.submit.call(form);
                } else {
                    const firstError = form.querySelector('.has-danger');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
@endsection