@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.content.cluster') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.ministries') }}</a>
                            </li>
                            <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ __('menus.credit') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.content.cluster') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.edit') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example"
                            action="{{ route('beginVoucher.update', ['params' => $params, 'id' => $module->id]) }}"
                            novalidate method="post">
                            @csrf
                            <div class="row">
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

                                <div class="col-lg-4 col-md-6" hidden>
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
                                        <label for="cboSubAccount" class="form-label font-size-13 text-muted">
                                            {{ __('forms.sub.account') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboSubAccount" name="cboSubAccount"
                                            required data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($accountSub as $as)
                                                <option value="{{ $as->no }}"
                                                    {{ $as->no == $module->account_sub_id ? 'selected' : '' }}>
                                                    {{ $as->no }}-{{ $as->name }}
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
                                        <label for="fin_law"
                                            class="form-label font-size-13 text-muted">{{ __('forms.fin.law') }}</label>
                                        <input type="number" min="0" id="fin_law" name="fin_law"
                                            value="{{ old('fin_law', $module->fin_law) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="current_loan"
                                            class="form-label font-size-13 text-muted">{{ __('forms.current.loan') }}</label>
                                        <input type="number" min="0" id="current_loan" name="current_loan"
                                            value="{{ old('current_loan', $module->current_loan) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            readonly class="form-control bg-light" />
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit"
                                    value="save">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('beginVoucher.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/pristinejs/pristine.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validations.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);

            form.addEventListener('submit', function(e) {
                var valid = pristine.validate();
                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script>
        const finLawInput = document.getElementById('fin_law');
        const currentLoanInput = document.getElementById('current_loan');

        if (finLawInput && currentLoanInput) {
            finLawInput.addEventListener('input', function() {
                currentLoanInput.value = this.value;
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* ================== Choices Config ================== */
            const choicesConfig = {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            };

            const choicesInstances = {};

            function initChoices(selector) {
                const el = document.querySelector(selector);
                if (!el) return null;

                if (choicesInstances[selector]) {
                    choicesInstances[selector].destroy();
                    choicesInstances[selector] = null;
                }

                const instance = new Choices(el, choicesConfig);
                choicesInstances[selector] = instance;
                return instance;
            }

            function resetSelectHtml(selector, html = null) {
                const el = document.querySelector(selector);
                if (!el) return null;

                if (choicesInstances[selector]) {
                    choicesInstances[selector].destroy();
                    choicesInstances[selector] = null;
                }

                $(selector).html(html || `<option value="">{{ __('forms.search...') }}</option>`);
                return initChoices(selector);
            }

            /* ================== Initialize All Selects ONCE ================== */
            initChoices('#cboProgram');
            initChoices('#cboSubAccount');
            initChoices('#cboProgramSub');
            initChoices('#cboCluster');
            initChoices('#cboAgency');

            /* ================== AJAX Loaders ================== */
            function loadProgramSubs(programId, selectedId = null) {
                resetSelectHtml('#cboProgramSub');
                resetSelectHtml('#cboCluster');
                resetSelectHtml('#cboAgency');

                if (!programId) {
                    return $.Deferred().resolve().promise();
                }

                return $.ajax({
                    url: "{{ route('beginVoucher.edit.program_sub') }}",
                    type: "GET",
                    data: {
                        program_id: programId,
                        selected_id: selectedId
                    }
                }).done(function(html) {
                    resetSelectHtml('#cboProgramSub', html);
                }).fail(function() {
                    resetSelectHtml('#cboProgramSub');
                });
            }

            function loadClusters(programSubId, selectedId = null) {
                resetSelectHtml('#cboCluster');
                resetSelectHtml('#cboAgency');

                if (!programSubId) {
                    return $.Deferred().resolve().promise();
                }

                return $.ajax({
                    url: "{{ route('beginVoucher.edit.cluster') }}",
                    type: "GET",
                    data: {
                        program_sub_id: programSubId,
                        selected_id: selectedId
                    }
                }).done(function(html) {
                    resetSelectHtml('#cboCluster', html);
                }).fail(function() {
                    resetSelectHtml('#cboCluster');
                });
            }

            function loadAgencies(programId, programSubId, clusterId, selectedId = null) {
                resetSelectHtml('#cboAgency');

                if (!programId || !programSubId || !clusterId) {
                    return $.Deferred().resolve().promise();
                }

                return $.ajax({
                    url: "{{ route('beginVoucher.edit.agency') }}",
                    type: "GET",
                    data: {
                        program_id: programId,
                        program_sub_id: programSubId,
                        cluster_id: clusterId,
                        selected_id: selectedId
                    }
                }).done(function(html) {
                    const selector = '#cboAgency';
                    if (choicesInstances[selector]) {
                        choicesInstances[selector].destroy();
                        choicesInstances[selector] = null;
                    }

                    const $agency = $(selector);
                    $agency.html(html);

                    // Ensure a valid agency is selected if none is pre-selected
                    if (!$agency.val()) {
                        const $firstOption = $agency.find('option').filter(function() {
                            return $(this).val() !== '';
                        }).first();

                        if ($firstOption.length) {
                            $firstOption.prop('selected', true);
                            $agency.val($firstOption.val());
                        }
                    }

                    initChoices(selector);
                    $agency.trigger('change');
                }).fail(function() {
                    resetSelectHtml('#cboAgency');
                });
            }

            /* ================== Sequential Preload Edit Data ================== */
            const initProgramId = $('#cboProgram').val();
            const initProgramSubId = $('#cboProgramSub').data('old');
            const initClusterId = $('#cboCluster').data('old');
            const initAgencyId = $('#cboAgency').data('old');

            if (initProgramId) {
                loadProgramSubs(initProgramId, initProgramSubId).then(function() {
                    const subId = $('#cboProgramSub').val() || initProgramSubId;
                    if (!subId) return;

                    return loadClusters(subId, initClusterId).then(function() {
                        const clusterId = $('#cboCluster').val() || initClusterId;
                        if (!clusterId) return;

                        return loadAgencies(initProgramId, subId, clusterId, initAgencyId);
                    });
                });
            }

            /* ================== Cascade & Agency Change Events ================== */
            $('#cboProgram').on('change', function() {
                loadProgramSubs($(this).val());
            });

            $('#cboProgramSub').on('change', function() {
                loadClusters($(this).val());
            });

            $('#cboCluster').on('change', function() {
                loadAgencies(
                    $('#cboProgram').val(),
                    $('#cboProgramSub').val(),
                    $(this).val()
                );
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
@endsection
