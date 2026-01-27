@extends('layouts.master')
@section('css')
    <link href="{{ asset('assets/libs/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.cluster') }}
                </h4>

                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.ministries') }}</a>
                            </li>
                            <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ __('menus.credit') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.cluster') }}</a></li>
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

                                {{-- <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboProgramSub" class="form-label font-size-13 text-muted">
                                            {{ __('forms.program.sub') }}
                                        </label>
                                        <select id="cboProgramSub" class="form-select" name="cboProgramSub" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($programSub as $ps)
                                                <option value="{{ $ps->id }}"
                                                    {{ $module->program_sub_id == $ps->id ? 'selected' : '' }}>
                                                    {{ $ps->no }}-{{ $ps->decription }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cboProgramSub')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
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

                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="no"
                                            class="form-label font-size-13 text-muted">{{ __('forms.cluster') }}</label>
                                        <input type="text" name="no" class="form-control" required
                                            value="{{ old('no', substr($module->no, 6, 1)) }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                {{-- <div class="col-lg-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cboAgency" class="form-label font-size-13 text-muted">
                                            {{ __('forms.agency') }}
                                        </label>
                                        <select class="form-control" data-trigger id="cboAgency" name="cboAgency" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            @foreach ($agency as $agc)
                                                <option value="{{ $agc->id }}"
                                                    {{ $module->agency_id == $agc->id ? 'selected' : '' }}>
                                                    {{ $agc->no }}-
                                                    {{ $agc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('cboAgency')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}

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

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="txtDescription" class="form-label font-size-13 text-muted">
                                        {{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" data-pristine-required-message="{{ __('messages.required') }}" rows="5"
                                        class="form-control" name="txtDescription" required>
                                        {{ $module->txtDescription }}
                                    </textarea>
                                    @error('txtDescription')
                                        <div class="pristine-error text-help">{{ $message }}</div>
                                    @enderror
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
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pristinejs/dist/pristine.min.js"></script>

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
    {{-- <script>
        let programSubChoices = new Choices('#cboProgramSub', {
            searchEnabled: true,
            itemSelectText: '',
            placeholder: true,
        });

        $('#cboProgram').change(function() {
            var id = $(this).val();
            $.ajax({
                url: '{!! route('beginVoucher.by.program_id') !!}',
                type: 'get',
                data: {
                    program_id: id
                },
                success: function(data) {
                    $('#cboProgramSub').html(data);

                    programSubChoices.destroy();
                    programSubChoices = new Choices('#cboProgramSub', {
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "ស្វែងរក..."
                    });
                }
            });
        });
    </script> --}}
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

            let agencyChoices = new Choices('#cboAgency', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            let subChoices = new Choices('#cboProgramSub', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            function refreshChoices(instance, selector) {
                if (instance) instance.destroy();
                return new Choices(selector, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "ស្វែងរក..."
                });
            }

            function loadSubsAndAgencies(programId, selectedSubId = null, selectedAgencyId = null) {
                // reset selects
                $('#cboAgency').html(`<option value="">{{ __('forms.search...') }}</option>`);
                $('#cboProgramSub').html(`<option value="">{{ __('forms.search...') }}</option>`);

                agencyChoices = refreshChoices(agencyChoices, '#cboAgency');
                subChoices = refreshChoices(subChoices, '#cboProgramSub');

                if (!programId) return;

                // load program subs
                $.get("{{ route('beginVoucher.by.program_sub') }}", {
                        program_id: programId,
                        selected_id: selectedSubId
                    },
                    function(html) {
                        $('#cboProgramSub').html(html);
                        subChoices = refreshChoices(subChoices, '#cboProgramSub');
                    }
                );

                // load agencies
                $.get("{{ route('beginVoucher.by.agency') }}", {
                        program_id: programId,
                        selected_id: selectedAgencyId
                    },
                    function(html) {
                        $('#cboAgency').html(html);
                        agencyChoices = refreshChoices(agencyChoices, '#cboAgency');
                    }
                );
            }

            // ✅ Change program -> reload both lists (no selected)
            $('#cboProgram').on('change', function() {
                const programId = $(this).val();
                loadSubsAndAgencies(programId);
            });

            // ✅ Edit page: auto load once using existing saved values
            const initialProgramId = $('#cboProgram').val();
            const initialSubId = "{{ old('cboProgramSub', $row->program_sub_id ?? '') }}";
            const initialAgencyId = "{{ old('cboAgency', $row->agency_id ?? '') }}";
            loadSubsAndAgencies(initialProgramId, initialSubId, initialAgencyId);

        });
    </script>
@endsection
