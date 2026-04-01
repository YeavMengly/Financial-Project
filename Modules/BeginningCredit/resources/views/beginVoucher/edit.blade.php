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
                                        <input type="number" min="0" name="fin_law"
                                            value="{{ old('fin_law', $module->fin_law) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="current_loan"
                                            class="form-label font-size-13 text-muted">{{ __('forms.current.loan') }}</label>
                                        <input type="number" min="0" name="current_loan"
                                            value="{{ old('current_loan', $module->current_loan) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" required
                                            class="form-control" />
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
                    url: "{{ route('beginVoucher.edit.program_sub') }}",
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
                    url: "{{ route('beginVoucher.edit.agency') }}",
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
                    url: "{{ route('beginVoucher.edit.cluster') }}",
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

@endsection
