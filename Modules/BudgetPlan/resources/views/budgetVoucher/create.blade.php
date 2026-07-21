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
                                {{-- <div class="col-lg-4 col-md-6">
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
                                </div> --}}
                                {{-- <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label>{{ __('forms.day.number') }}</label>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                            type="text" class="form-control"
                                            placeholder="{{ __('forms.day.number') }}" name="cbodayOfNumber"
                                            tabindex="2" />
                                    </div>
                                </div> --}}
                                {{-- <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="cbodayOfNumber"
                                            class="form-label mb-0">{{ __('forms.day.number') }}</label>

                                        <!-- Modern Skip Switch -->
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="skipDayNumber" style="cursor: pointer;">
                                            <label class="form-check-label font-size-12 text-muted" for="skipDayNumber"
                                                style="cursor: pointer;">
                                                រំលង / មិនបញ្ចូល
                                            </label>
                                        </div>
                                    </div>

                                    <input required data-pristine-required-message="{{ __('messages.required') }}"
                                        data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                        data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value="0" min="1"
                                        type="number" class="form-control" id="cbodayOfNumber"
                                        placeholder="{{ __('forms.day.number') }}" name="cbodayOfNumber"
                                        tabindex="2" />
                                </div> --}}

                                <!-- Temporary ID Field -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="temporaryId"
                                                class="form-label mb-0">{{ __('forms.temporary.id') }}</label>
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
                                            <input required data-pristine-required-message="{{ __('messages.required') }}"
                                                data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                                data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value=""
                                                min="1" type="number" class="form-control" id="temporaryId"
                                                placeholder="{{ __('forms.temporary.id') }}" name="temporaryId"
                                                tabindex="2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Day Number Field -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="cbodayOfNumber"
                                                class="form-label mb-0">{{ __('forms.day.number') }}</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="skipDayNumber" style="cursor: pointer;">
                                                <label class="form-check-label font-size-12 text-muted"
                                                    for="skipDayNumber" style="cursor: pointer;">
                                                    រំលង / មិនបញ្ចូល
                                                </label>
                                            </div>
                                        </div>
                                        <input required data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" value=""
                                            min="1" type="number" class="form-control" id="cbodayOfNumber"
                                            placeholder="{{ __('forms.day.number') }}" name="cbodayOfNumber"
                                            tabindex="2" />
                                    </div>
                                </div>

                                {{-- <div class="col-lg-4 col-md-6">
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
                                </div> --}}

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

                                {{-- <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <!-- Added missing Pristine validation message attribute -->
                                        <select class="form-control" id="cboSubAccount" name="cboSubAccount" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $bv)
                                                <!-- FIX: Send database primary key ID as the value, preserve account number in data-no -->
                                                <option value="{{ $bv->id }}" data-no="{{ $bv->no }}">
                                                    {{ $bv->no }}-{{ $bv->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboSubAccount')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" id="cboSubAccount" name="cboSubAccount" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $bv)
                                                <!-- THE FIX: Must send $bv->no to match the account code string stored in begin_vouchers -->
                                                <option value="{{ $bv->no }}">
                                                    {{ $bv->no }}-{{ $bv->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboSubAccount')
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
    <!-- Vendor Scripts (Loaded exactly once) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pristine-valid-example');
            if (!form) return;

            // =========================================================================
            // 1. MASTER PRISTINE VALIDATION ENGINE
            // =========================================================================
            const createPristine = () => {
                return new Pristine(form, {
                    classTo: 'form-group',
                    errorClass: 'has-danger',
                    successClass: 'has-success',
                    errorTextParent: 'form-group',
                    errorTextTag: 'div',
                    errorTextClass: 'pristine-error text-help text-danger font-size-14 mt-1'
                });
            };

            let pristine = createPristine();

            const refreshPristine = () => {
                pristine.destroy();
                pristine = createPristine();
                applyCustomFileValidators();
            };

            // =========================================================================
            // 2. UNIVERSAL SKIP TOGGLE ENGINE (WITH GREEN HIGHLIGHT)
            // =========================================================================
            const setupSkipToggle = (checkboxId, inputId, skippedValue = '0', isFile = false) => {
                const checkbox = document.getElementById(checkboxId);
                const input = document.getElementById(inputId);
                if (!checkbox || !input) return;

                // Cache original validation attributes
                const origRequiredMsg = input.getAttribute('data-pristine-required-message') ||
                    "{{ __('messages.required') }}";
                const origMinMsg = input.getAttribute('data-pristine-min-message');
                const origIntMsg = input.getAttribute('data-pristine-integer-message');
                const origMin = input.getAttribute('min');

                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // SKIP STATE: Apply green highlight, set skipped value, lock input
                        input.value = skippedValue;
                        input.disabled = true;
                        input.readOnly = true;

                        // Apply soft green styling (#d1e7dd background, dark green text/border)
                        input.classList.add('bg-success-subtle', 'text-success-emphasis',
                            'border-success');
                        input.style.setProperty('background-color', '#d1e7dd', 'important');
                        input.style.setProperty('border-color', '#198754', 'important');
                        input.style.setProperty('color', '#0f5132', 'important');

                        // Strip validation rules so Pristine ignores it
                        input.removeAttribute('required');
                        input.removeAttribute('data-pristine-required-message');
                        if (origMinMsg) input.removeAttribute('data-pristine-min-message');
                        if (origIntMsg) input.removeAttribute('data-pristine-integer-message');
                        if (origMin) input.removeAttribute('min');

                        pristine.reset(input); // Wipe any active red error text instantly
                    } else {
                        // ACTIVE STATE: Remove green styling, restore default empty state and rules
                        input.value = '';
                        input.disabled = false;
                        input.readOnly = false;

                        input.classList.remove('bg-success-subtle', 'text-success-emphasis',
                            'border-success');
                        input.style.removeProperty('background-color');
                        input.style.removeProperty('border-color');
                        input.style.removeProperty('color');

                        input.setAttribute('required', 'true');
                        input.setAttribute('data-pristine-required-message', origRequiredMsg);
                        if (origMinMsg) input.setAttribute('data-pristine-min-message', origMinMsg);
                        if (origIntMsg) input.setAttribute('data-pristine-integer-message', origIntMsg);
                        if (origMin) input.setAttribute('min', origMin);
                    }
                    refreshPristine();
                });

                if (isFile) {
                    input.addEventListener('change', () => pristine.validate(input));
                }
            };

            // Register all skippable fields here:
            setupSkipToggle('skipTemporaryId', 'temporaryId', '0');
            setupSkipToggle('skipDayNumber', 'cbodayOfNumber', '0');
            setupSkipToggle('skipFileInput', 'fileInput', '', true);

            // =========================================================================
            // 3. CUSTOM FILE VALIDATORS (SIZE & EXTENSION)
            // =========================================================================
            const fileInput = document.getElementById('fileInput');
            const skipFileCheckbox = document.getElementById('skipFileInput');

            const applyCustomFileValidators = () => {
                if (!fileInput) return;
                const maxMB = parseFloat(fileInput.getAttribute('data-max-size')) || 5;
                const maxBytes = maxMB * 1024 * 1024;
                const allowedExts = (fileInput.getAttribute('data-allowed-extensions') || '').toLowerCase()
                    .split(',').map(ext => ext.trim());

                pristine.addValidator(fileInput, function(value) {
                    if ((skipFileCheckbox && skipFileCheckbox.checked) || fileInput.disabled ||
                        fileInput.files.length === 0) return true;
                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (fileInput.files[i].size > maxBytes) return false;
                    }
                    return true;
                }, `ទំហំឯកសារនីមួយៗមិនត្រូវលើសពី ${maxMB}MB ឡើយ`, 2, false);

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

            applyCustomFileValidators();

            // =========================================================================
            // 4. CHOICES.JS DROPDOWNS & AJAX DEPENDENCY ROUTING
            // =========================================================================
            // const initChoices = (selector, opts = {}) => {
            //     const el = document.querySelector(selector);
            //     if (!el) return null;
            //     return new Choices(el, Object.assign({
            //         searchEnabled: true,
            //         itemSelectText: '',
            //         placeholder: true,
            //         placeholderValue: 'ស្វែងរក...',
            //         shouldSort: false
            //     }, opts));
            // };

            // let voucherChoices = initChoices('#cboPaymentVoucherNumber');
            // let programSubChoices = initChoices('#cboProgramSub');
            // let clusterChoices = initChoices('#cboCluster');
            // let agencyChoices = initChoices('#cboAgency');
            // initChoices('#cboExpenseType');
            // initChoices('#cboProgram');
            // initChoices('#cboSubAccount');

            // const resetSelectChoices = (selector, instanceRef) => {
            //     if (instanceRef) instanceRef.destroy();
            //     const el = document.querySelector(selector);
            //     if (el) el.innerHTML = `<option value="">{{ __('forms.search...') }}</option>`;
            //     return initChoices(selector);
            // };

            // const loadDependentOptions = (url, data, targetSelector, instanceRef, onSuccessCallback) => {
            //     $.ajax({
            //         url: url,
            //         type: 'GET',
            //         data: data,
            //         success: function(response) {
            //             const el = document.querySelector(targetSelector);
            //             if (el) el.innerHTML = response;
            //             const newInstance = resetSelectChoices(targetSelector, instanceRef);
            //             if (onSuccessCallback) onSuccessCallback(newInstance);
            //         },
            //         error: () => resetSelectChoices(targetSelector, instanceRef)
            //     });
            // };

            // $('#cboExpenseType').on('change', function() {
            //     const expenseTypeId = $(this).val();
            //     voucherChoices.clearStore();
            //     voucherChoices.clearChoices();
            //     $('#legalName').val('').prop('readonly', false);
            //     if ($('#vDescription').length) {
            //         $('#vDescription').summernote('code', '');
            //         $('#vDescription').summernote('enable');
            //     }
            //     if (!expenseTypeId) return;

            //     voucherChoices.setChoices([{
            //         value: '',
            //         label: 'កំពុងផ្ទុក...',
            //         disabled: true,
            //         selected: true
            //     }], 'value', 'label', true);

            //     $.ajax({
            //         url: "{{ route('budgetVoucher.get.expense_type_id') }}",
            //         type: 'GET',
            //         dataType: 'json',
            //         data: {
            //             expense_type_id: expenseTypeId
            //         },
            //         success: (data) => {
            //             voucherChoices.clearChoices();
            //             voucherChoices.setChoices(data, 'value', 'label', true);
            //         },
            //         error: () => voucherChoices.clearChoices()
            //     });
            // });

            // const voucherEl = document.getElementById('cboPaymentVoucherNumber');
            // if (voucherEl && voucherChoices) {
            //     voucherEl.addEventListener('change', function() {
            //         const selected = voucherChoices.getValue();
            //         const legalNameInput = document.getElementById('legalName');
            //         if (selected && selected.customProperties && selected.value !== '') {
            //             if (legalNameInput) {
            //                 legalNameInput.value = selected.customProperties.legal_name || '';
            //                 legalNameInput.readOnly = true;
            //             }
            //             if ($('#vDescription').length) {
            //                 $('#vDescription').summernote('code', selected.customProperties.description ||
            //                     '');
            //                 $('#vDescription').summernote('disable');
            //             }
            //         } else {
            //             if (legalNameInput) {
            //                 legalNameInput.value = '';
            //                 legalNameInput.readOnly = false;
            //             }
            //             if ($('#vDescription').length) {
            //                 $('#vDescription').summernote('code', '');
            //                 $('#vDescription').summernote('enable');
            //             }
            //         }
            //         pristine.validate(legalNameInput);
            //     });
            // }

            // $('#cboProgram').on('change', function() {
            //     const programId = $(this).val();
            //     programSubChoices = resetSelectChoices('#cboProgramSub', programSubChoices);
            //     agencyChoices = resetSelectChoices('#cboAgency', agencyChoices);
            //     clusterChoices = resetSelectChoices('#cboCluster', clusterChoices);
            //     if (!programId) return;

            //     loadDependentOptions("{{ route('budgetVoucher.by.program_sub') }}", {
            //         program_id: programId
            //     }, '#cboProgramSub', programSubChoices, (inst) => programSubChoices = inst);
            //     loadDependentOptions("{{ route('budgetVoucher.by.agency') }}", {
            //         program_id: programId
            //     }, '#cboAgency', agencyChoices, (inst) => agencyChoices = inst);
            // });

            // $('#cboProgramSub').on('change', function() {
            //     const programSubId = $(this).val();
            //     clusterChoices = resetSelectChoices('#cboCluster', clusterChoices);
            //     if (!programSubId) return;
            //     loadDependentOptions("{{ route('budgetVoucher.by.cluster') }}", {
            //         program_sub_id: programSubId
            //     }, '#cboCluster', clusterChoices, (inst) => clusterChoices = inst);
            // });

            // form.querySelectorAll('select').forEach(select => {
            //     select.addEventListener('change', () => pristine.validate(select));
            // });

            // =========================================================================
            // 5. FLATPICKR DATEPICKERS & SUMMERNOTE EDITOR
            // =========================================================================
            const initFlatpickr = (id) => {
                const el = document.getElementById(id);
                if (!el) return;
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: el.value || null,
                    onChange: (dates, str, inst) => pristine.validate(inst.element),
                    onClose: (dates, str, inst) => pristine.validate(inst.element)
                });
            };

            initFlatpickr('transactionDate');
            initFlatpickr('requestDate');

            if ($('#vDescription').length) {
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

            // =========================================================================
            // 6. LIVE BUDGET BALANCE CALCULATOR
            // =========================================================================
            // const n = v => (isNaN(+v) ? 0 : +v);
            // const fmt = v => n(v).toLocaleString('en-US', {
            //     maximumFractionDigits: 2
            // });
            // const setText = (id, val) => {
            //     const el = document.getElementById(id);
            //     if (el) el.textContent = fmt(val);
            // };
            // const resetNumbers = () => ['fin_law', 'credit_movement', 'new_credit_status', 'credit',
            //     'deadline_balance', 'applying', 'remaining_credit'
            // ].forEach(id => setText(id, 0));

            // const recomputeRemaining = () => {
            //     const budgetInput = document.getElementById('budget');
            //     const apply = n(budgetInput?.value);
            //     const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
            //     setText('applying', apply);
            //     setText('remaining_credit', Math.max(credit - apply, 0));
            //     if (credit - apply < 0 && budgetInput) budgetInput.value = '';
            // };

            // const fetchEarlyBalance = async () => {
            //     const programId = document.getElementById('cboProgram')?.value;
            //     const programSubId = document.getElementById('cboProgramSub')?.value;
            //     const clusterId = document.getElementById('cboCluster')?.value;
            //     const accountSubId = document.getElementById('cboSubAccount')?.value;

            //     if (!programId || !programSubId || !clusterId || !accountSubId) {
            //         resetNumbers();
            //         return;
            //     }

            //     const url = new URL("{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}",
            //         window.location.origin);
            //     url.searchParams.set('program_id', programId);
            //     url.searchParams.set('program_sub_id', programSubId);
            //     url.searchParams.set('cluster_id', clusterId);
            //     url.searchParams.set('account_sub_id', accountSubId);

            //     try {
            //         const res = await fetch(url.toString(), {
            //             headers: {
            //                 'Accept': 'application/json'
            //             }
            //         });
            //         const data = await res.json();
            //         setText('fin_law', data.fin_law);
            //         setText('credit_movement', data.credit_movement);
            //         setText('new_credit_status', data.new_credit_status);
            //         setText('credit', data.credit);
            //         setText('deadline_balance', data.deadline_balance);
            //         recomputeRemaining();
            //     } catch (err) {
            //         console.error("Balance fetch failed:", err);
            //         resetNumbers();
            //     }
            // };

            // ['cboProgram', 'cboProgramSub', 'cboCluster', 'cboSubAccount'].forEach(id => {
            //     document.getElementById(id)?.addEventListener('change', fetchEarlyBalance);
            // });
            // document.getElementById('budget')?.addEventListener('input', recomputeRemaining);

            // =========================================================================
            // 6. LIVE BUDGET BALANCE CALCULATOR (ROBUST AJAX & EVENT DELEGATION)
            // =========================================================================
            // const n = v => (isNaN(+v) ? 0 : +v);
            // const fmt = v => n(v).toLocaleString('en-US', {
            //     maximumFractionDigits: 2
            // });
            // const setText = (id, val) => {
            //     const el = document.getElementById(id);
            //     if (el) el.textContent = fmt(val);
            // };
            // const resetNumbers = () => ['fin_law', 'credit_movement', 'new_credit_status', 'credit',
            //     'deadline_balance', 'applying', 'remaining_credit'
            // ].forEach(id => setText(id, 0));

            // const recomputeRemaining = () => {
            //     const budgetInput = document.getElementById('budget');
            //     const apply = n(budgetInput?.value);
            //     const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
            //     setText('applying', apply);
            //     setText('remaining_credit', Math.max(credit - apply, 0));
            //     if (credit - apply < 0 && budgetInput) budgetInput.value = '';
            // };

            // function fetchEarlyBalance() {
            //     // Read clean values directly using jQuery
            //     const programId = $('#cboProgram').val();
            //     const programSubId = $('#cboProgramSub').val();
            //     const clusterId = $('#cboCluster').val();
            //     const accountSubId = $('#cboSubAccount').val();

            //     // Abort if any of the 4 required accounting classification dropdowns are empty
            //     if (!programId || !programSubId || !clusterId || !accountSubId) {
            //         resetNumbers();
            //         return;
            //     }

            //     $.ajax({
            //         url: "{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}",
            //         type: "GET",
            //         dataType: "json",
            //         data: {
            //             program_id: programId,
            //             program_sub_id: programSubId,
            //             cluster_id: clusterId,
            //             account_sub_id: accountSubId
            //         },
            //         success: function(data) {
            //             if (data && data.exists !== false) {
            //                 setText('fin_law', data.fin_law);
            //                 setText('credit_movement', data.credit_movement);
            //                 setText('new_credit_status', data.new_credit_status);
            //                 setText('credit', data.credit);
            //                 setText('deadline_balance', data.deadline_balance);
            //                 recomputeRemaining();
            //             } else {
            //                 resetNumbers();
            //             }
            //         },
            //         error: function(err) {
            //             console.error("Balance fetch failed:", err);
            //             resetNumbers();
            //         }
            //     });
            // }

            // // THE FIX: Use jQuery event delegation on 'document'. 
            // // This guarantees that even when Choices.js destroys and recreates #cboProgramSub or #cboCluster via AJAX, the change listener is never lost.
            // $(document).on('change', '#cboProgram, #cboProgramSub, #cboCluster, #cboSubAccount', function() {
            //     fetchEarlyBalance();
            // });

            // document.getElementById('budget')?.addEventListener('input', recomputeRemaining);
            // =========================================================================
            // 7. MASTER FORM SUBMIT GATEKEEPER
            // =========================================================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if ($('#vDescription').length) {
                    const summernoteContent = $('#vDescription').summernote('isEmpty') ? '' : $(
                        '#vDescription').summernote('code');
                    document.getElementById('vDescription').value = summernoteContent;
                }

                const isValid = pristine.validate();

                if (isValid) {
                    // THE CRITICAL FIX: Re-enable disabled fields right before submit
                    // Browsers ignore disabled inputs during POST requests. Enabling them ensures Laravel receives clean '0' or null values instead of throwing missing parameter errors.
                    form.querySelectorAll(':disabled').forEach(el => {
                        el.disabled = false;
                    });
                    if ($('#vDescription').length && $('#vDescription').summernote('isDisabled')) {
                        $('#vDescription').summernote('enable');
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

            // =========================================================================
            // 4. CHOICES.JS DROPDOWNS & AJAX DEPENDENCY ROUTING
            // =========================================================================
            const initChoices = (selector, opts = {}) => {
                const el = document.querySelector(selector);
                if (!el) return null;
                return new Choices(el, Object.assign({
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'ស្វែងរក...',
                    shouldSort: false
                }, opts));
            };

            let voucherChoices = initChoices('#cboPaymentVoucherNumber');
            let programSubChoices = initChoices('#cboProgramSub');
            let clusterChoices = initChoices('#cboCluster');
            let agencyChoices = initChoices('#cboAgency');
            initChoices('#cboExpenseType');
            initChoices('#cboProgram');
            initChoices('#cboSubAccount');

            // HELPER 1: Wipes out a dropdown and sets it back to default "Search..."
            const clearDropdown = (selector, instanceRef) => {
                if (instanceRef) instanceRef.destroy();
                const el = document.querySelector(selector);
                if (el) el.innerHTML = `<option value="">{{ __('forms.search...') }}</option>`;
                return initChoices(selector);
            };

            // HELPER 2: Safely destroys Choices, injects server HTML, and re-initializes WITHOUT erasing data
            const setDropdownHtml = (selector, instanceRef, htmlContent) => {
                if (instanceRef) instanceRef.destroy();
                const el = document.querySelector(selector);
                if (el) el.innerHTML = htmlContent;
                return initChoices(selector);
            };

            // AJAX Dependency Loader
            const loadDependentOptions = (url, data, targetSelector, instanceRef, onSuccessCallback) => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        const newInstance = setDropdownHtml(targetSelector, instanceRef, response);
                        if (onSuccessCallback) onSuccessCallback(newInstance);
                    },
                    error: () => clearDropdown(targetSelector, instanceRef)
                });
            };

            // Expense Type -> Payment Voucher Number
            $('#cboExpenseType').on('change', function() {
                const expenseTypeId = $(this).val();
                voucherChoices.clearStore();
                voucherChoices.clearChoices();
                $('#legalName').val('').prop('readonly', false);
                if ($('#vDescription').length) {
                    $('#vDescription').summernote('code', '');
                    $('#vDescription').summernote('enable');
                }
                if (!expenseTypeId) return;

                voucherChoices.setChoices([{
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
                    success: (data) => {
                        voucherChoices.clearChoices();
                        voucherChoices.setChoices(data, 'value', 'label', true);
                    },
                    error: () => voucherChoices.clearChoices()
                });
            });

            // Auto-fill Legal Name & Description
            const voucherEl = document.getElementById('cboPaymentVoucherNumber');
            if (voucherEl && voucherChoices) {
                voucherEl.addEventListener('change', function() {
                    const selected = voucherChoices.getValue();
                    const legalNameInput = document.getElementById('legalName');
                    if (selected && selected.customProperties && selected.value !== '') {
                        if (legalNameInput) {
                            legalNameInput.value = selected.customProperties.legal_name || '';
                            legalNameInput.readOnly = true;
                        }
                        if ($('#vDescription').length) {
                            $('#vDescription').summernote('code', selected.customProperties.description ||
                                '');
                            $('#vDescription').summernote('disable');
                        }
                    } else {
                        if (legalNameInput) {
                            legalNameInput.value = '';
                            legalNameInput.readOnly = false;
                        }
                        if ($('#vDescription').length) {
                            $('#vDescription').summernote('code', '');
                            $('#vDescription').summernote('enable');
                        }
                    }
                    pristine.validate(legalNameInput);
                });
            }

            // Program -> ProgramSub & Agency
            $('#cboProgram').on('change', function() {
                const programId = $(this).val();
                programSubChoices = clearDropdown('#cboProgramSub', programSubChoices);
                agencyChoices = clearDropdown('#cboAgency', agencyChoices);
                clusterChoices = clearDropdown('#cboCluster', clusterChoices);
                if (!programId) return;

                loadDependentOptions("{{ route('budgetVoucher.by.program_sub') }}", {
                    program_id: programId
                }, '#cboProgramSub', programSubChoices, (inst) => programSubChoices = inst);
                loadDependentOptions("{{ route('budgetVoucher.by.agency') }}", {
                    program_id: programId
                }, '#cboAgency', agencyChoices, (inst) => agencyChoices = inst);
            });

            // ProgramSub -> Cluster
            $('#cboProgramSub').on('change', function() {
                const programSubId = $(this).val();
                clusterChoices = clearDropdown('#cboCluster', clusterChoices);
                if (!programSubId) return;
                loadDependentOptions("{{ route('budgetVoucher.by.cluster') }}", {
                    program_sub_id: programSubId
                }, '#cboCluster', clusterChoices, (inst) => clusterChoices = inst);
            });

            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => pristine.validate(select));
            });

            // =========================================================================
            // 6. LIVE BUDGET BALANCE CALCULATOR (WITH CONSOLE LOGGING)
            // =========================================================================
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

            const recomputeRemaining = () => {
                const budgetInput = document.getElementById('budget');
                const apply = n(budgetInput?.value);
                const credit = n((document.getElementById('credit')?.textContent || '0').replace(/,/g, ''));
                setText('applying', apply);
                setText('remaining_credit', Math.max(credit - apply, 0));
                if (credit - apply < 0 && budgetInput) budgetInput.value = '';
            };

            function fetchEarlyBalance() {
                const programId = $('#cboProgram').val();
                const programSubId = $('#cboProgramSub').val();
                const clusterId = $('#cboCluster').val();
                const accountSubId = $('#cboSubAccount').val();

                // Debug print to your browser console (Press F12 to view)
                console.log("Checking Balance IDs:", {
                    programId,
                    programSubId,
                    clusterId,
                    accountSubId
                });

                if (!programId || !programSubId || !clusterId || !accountSubId) {
                    console.warn("Balance check aborted: One or more required accounting fields are still empty.");
                    resetNumbers();
                    return;
                }

                console.info("Sending AJAX balance request to Laravel...");

                $.ajax({
                    url: "{{ route('budgetVoucher.getEarlyBalance', ['params' => $params]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        program_id: programId,
                        program_sub_id: programSubId,
                        cluster_id: clusterId,
                        account_sub_id: accountSubId
                    },
                    success: function(data) {
                        console.log("Laravel Balance Response:", data);
                        if (data && data.exists !== false) {
                            setText('fin_law', data.fin_law);
                            setText('credit_movement', data.credit_movement);
                            setText('new_credit_status', data.new_credit_status);
                            setText('credit', data.credit);
                            setText('deadline_balance', data.deadline_balance);
                            recomputeRemaining();
                        } else {
                            console.warn("No matching voucher row found in database for these 4 IDs.");
                            resetNumbers();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error details:", status, error, xhr.responseText);
                        resetNumbers();
                    }
                });
            }

            // Event delegation guarantees this fires even after Choices.js reloads DOM elements
            $(document).on('change', '#cboProgram, #cboProgramSub, #cboCluster, #cboSubAccount', function() {
                fetchEarlyBalance();
            });

            document.getElementById('budget')?.addEventListener('input', recomputeRemaining);
        });
    </script>
@endsection
