@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
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

    <div class="row">
        <div class="col-12"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form id="pristine-valid-example"
                            action="{{ route('voucher.update', ['params' => $params, 'id' => $module->id]) }}"
                            method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
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
                                        <label for="internal">{{ __('forms.internal') }}</label>
                                        <input type="number" min="0" name="internal_increase" id="internal_increase"
                                            class="form-control"
                                            value="{{ old('internal_increase', $module->internal_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('internal_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="unexpected">{{ __('forms.unexpected') }}</label>
                                        <input type="number" min="0" name="unexpected_increase"
                                            id="unexpected_increase" class="form-control"
                                            value="{{ old('unexpected_increase', $module->unexpected_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('unexpected_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="additional">{{ __('forms.additional') }}</label>
                                        <input type="number" min="0" name="additional_increase"
                                            id="additional_increase" class="form-control"
                                            value="{{ old('additional_increase', $module->additional_increase) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('additional_increase')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="decrease">{{ __('forms.decrease') }}</label>
                                        <input type="number" min="0" name="decrease" id="decrease"
                                            class="form-control" value="{{ old('decrease', $module->decrease) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('decrease')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="editorial">{{ __('forms.editorial') }}</label>
                                        <input type="number" min="0" name="editorial" id="editorial"
                                            class="form-control" value="{{ old('editorial', $module->editorial) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                        @error('editorial')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="vDescription">{{ __('forms.document.description') }}</label>
                                        <textarea name="txtDescription" id="vDescription" rows="5" class="form-control" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                       {{ old('txtDescription', $module->txtDescription) }}</textarea>
                                        @error('txtDescription')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary"
                                    id="insertToTableBtn">{{ __('buttons.save') }}</button>
                                <a class="btn btn-dark"
                                    href="{{ route('voucher.index', $params) }}">{{ __('buttons.back') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
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
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
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
                    url: "{{ route('voucher.edit.program_sub') }}",
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
                    url: "{{ route('voucher.edit.agency') }}",
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
                    url: "{{ route('voucher.edit.cluster') }}",
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
