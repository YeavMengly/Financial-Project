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
                <h4 class="mb-sm-0 font-size-18">
                </h4>
                <div class="page-title-right">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.ministries') }}
                                    <span>{{ $ministry->year }}</span></a>
                            </li>
                            <li class="breadcrumb-item active"><a href="javascript: void(0);">{{ __('menus.credit') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.content.cluster') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('buttons.create') }}</li>
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
                        <form id="pristine-valid-example" action="{{ route('missions.store', $params) }}" novalidate
                            method="post">
                            @csrf
                            <div class="row">

                                <div class="col-xl-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="id_number"
                                            class="form-label font-size-13 text-muted">{{ __('forms.legal.id') }}</label>
                                        <input type="number" min="1" name="id_number" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            data-pristine-min-message="លំដាប់ ត្រូវតែធំជាងសូន្យ"
                                            data-pristine-integer-message="លំដាប់ ត្រូវតែលេខ" class="form-control"
                                            tabindex="1" />
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="legal_date"
                                            class="form-label font-size-13 text-muted">{{ __('forms.select_legal_date') }}</label>
                                        <input type="text" id="legal_date" name="legal_date" class="form-control"
                                            placeholder="{{ __('forms.select_legal_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}"
                                            tabindex="2" />
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="cboName" class="form-label font-size-13 text-muted">
                                            {{ __('forms.name') }}
                                        </label>
                                        <select class="form-select" id="cboName" name="cboName" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            {{-- @foreach ($program as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->no }}-
                                                    {{ $p->title }}</option>
                                            @endforeach --}}
                                        </select>
                                        @error('cboName')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="cboPosition" class="form-label font-size-13 text-muted">
                                            {{ __('forms.position') }}
                                        </label>
                                        <select class="form-select" id="cboPosition" name="cboPosition" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            {{-- @foreach ($program as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->no }}-
                                                    {{ $p->title }}</option>
                                            @endforeach --}}
                                        </select>
                                        @error('cboPosition')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="cboLevel" class="form-label font-size-13 text-muted">
                                            {{ __('forms.level') }}
                                        </label>
                                        <select id="cboLevel" class="form-select" name="cboLevel" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                        </select>
                                        @error('cboLevel')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="cboProvince" class="form-label font-size-13 text-muted">
                                            {{ __('forms.province') }}
                                        </label>
                                        <select class="form-select" id="cboProvince" name="cboProvince" required
                                            data-pristine-required-message="{{ __('messages.required') }}">
                                            <option value="">{{ __('forms.search...') }}</option>
                                            {{-- @foreach ($program as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->no }}-
                                                    {{ $p->title }}</option>
                                            @endforeach --}}
                                        </select>
                                        @error('cboProvince')
                                            <div class="pristine-error text-help">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group mb-3">
                                        <label for="start_date"
                                            class="form-label font-size-13 text-muted">{{ __('menus.start_date') }}</label>
                                        {{-- <label class="visually-hidden" for="start_date">{{ __('menus.start_date') }}</label> --}}
                                        <input type="text" id="start_date" name="start_date" class="form-control"
                                            required placeholder="{{ __('forms.select_date') }}"
                                            value="{{ request('start_date') }}"
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-sm-2">
                                    <div class="form-group mb-3">
                                        <label for="end_date"
                                            class="form-label font-size-13 text-muted">{{ __('menus.end_date') }}</label>
                                        {{-- <label class="visually-hidden" for="end_date">{{ __('menus.end_date') }}</label> --}}
                                        <input type="text" id="end_date" name="end_date" class="form-control"
                                            placeholder="{{ __('forms.select_date') }}"
                                            value="{{ request('end_date') }}" required
                                            data-pristine-required-message="{{ __('messages.required') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="vDescription">{{ __('forms.document.description') }}</label>
                                    <textarea id="vDescription" name="txtDescription" required tabindex="2"></textarea>
                                    <div class="invalid-feedback">
                                        {{ __('messages.required') }}
                                    </div>
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
                                    href="{{ route('missions.index', $params) }}">{{ __('buttons.back') }}</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('pristine-valid-example');
            var pristine = new Pristine(form);

            // Track which button was clicked
            var clickedButton = null;
            var submitButtons = form.querySelectorAll('button[type="submit"]');

            submitButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    clickedButton = this;
                });
            });

            form.addEventListener('submit', function(e) {
                var valid = pristine.validate();

                if (!valid) {
                    // Form is invalid, stop submission
                    e.preventDefault();
                } else {
                    // Form is valid, prevent multiple clicks
                    submitButtons.forEach(function(button) {
                        button.disabled = true;
                        // Optional: add a small loading text
                        if (button === clickedButton) {
                            button.innerHTML += '...';
                        }
                    });

                    // Append the clicked button's name and value so Laravel receives it
                    if (clickedButton && clickedButton.name) {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = clickedButton.name;
                        hiddenInput.value = clickedButton.value;
                        form.appendChild(hiddenInput);
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboName');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });

            const cboNamePicker = preloader('#cboLevel', {
                url: "{{ route('missions.by.level') }}",
                method: 'GET',
                data: {},
                success: function(html) {
                    $('#cboLevel').html(html);
                    choices.setChoices($('#cboLevel option').map(function() {
                        return {
                            value: $(this).val(),
                            label: $(this).text()
                        };
                    }).get(), 'value', 'label', true);
                },
                error: function() {
                    // optional: keep empty if error
                    $('#cboLevel').html('<option value="">{{ __('forms.search...') }}</option>');
                }
            });

            // const legalDatePicker = dropzone('#cboLevel', {
            //     url: "{{ route('missions.by.level') }}",
            //     method: 'GET',
            //     data: {},
            //     success: function(html) {
            //         $('#cboLevel').html(html);
            //         choices.setChoices($('#cboLevel option').map(function() {
            //             return {
            //                 value: $(this).val(),
            //                 label: $(this).text()
            //             };
            //         }).get(), 'value', 'label', true);
            //     },
            //     error: function() {
            //         // optional: keep empty if error
            //         $('#cboLevel').html('<option value="">{{ __('forms.search...') }}</option>');
            //     }
            // });

        });



        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboPosition');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboLevel');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('cboProvince');
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'ស្វែងរក...',
                shouldSort: false,

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
        const dateInput = document.getElementById('legal_date');
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true,
                defaultDate: dateInput.value || null
            });
        }
    </script>

    <script>
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        if (startDateInput) {
            flatpickr(startDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: startDateInput.value || null
            });
        }

        if (endDateInput) {
            flatpickr(endDateInput, {
                dateFormat: 'Y-m-d', // value submitted to backend
                altInput: true,
                altFormat: 'd/m/Y', // pretty display for users
                allowInput: true,
                defaultDate: endDateInput.value || null
            });
        }
    </script>

    <script>
        const legalDatePicker = flatpickr('#legal_date', {
            dateFormat: 'Y-m-d'
        });

        const startDatePicker = flatpickr('#start_date', {
            dateFormat: 'Y-m-d',

            onChange: function(selectedDates) {

                if (selectedDates.length > 0) {

                    endDatePicker.set('minDate', selectedDates[0]);

                    // If current end date is before start date, clear it
                    if (
                        endDatePicker.selectedDates.length > 0 &&
                        endDatePicker.selectedDates[0] < selectedDates[0]
                    ) {
                        endDatePicker.clear();
                    }
                } else {

                    endDatePicker.set('minDate', null);
                }
            }
        });


        const endDatePicker = flatpickr('#end_date', {
            dateFormat: 'Y-m-d'
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========= Choices Instances =========
            let cboLevelChoose = new Choices('#cboLevel', {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "ស្វែងរក..."
            });

            // let agencyChoices = new Choices('#cboAgency', {
            //     searchEnabled: true,
            //     itemSelectText: '',
            //     placeholder: true,
            //     placeholderValue: "ស្វែងរក..."
            // });

            // let clusterChoices = new Choices('#cboCluster', {
            //     searchEnabled: true,
            //     itemSelectText: '',
            //     placeholder: true,
            //     placeholderValue: "ស្វែងរក..."
            // });

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
            function handleProgramChangeForProgramSub(positionId) {
                resetSelect('#cboLevel');
                cboLevelChoose = resetChoices('#cboLevel', cboLevelChoose);

                if (!positionId) return;

                loadOptions({
                    url: "{{ route('missions.by.level') }}",
                    data: {
                        position_id: positionId
                    },
                    targetSelect: '#cboLevel',
                    instanceRefSetter: () => {
                        cboLevelChoose = resetChoices('#cboLevel', cboLevelChoose);
                    }
                });
            }

            // ========= Script 2: Program -> Agency =========
            // function handleProgramChangeForAgency(programId) {
            //     resetSelect('#cboAgency');
            //     agencyChoices = resetChoices('#cboAgency', agencyChoices);

            //     if (!programId) return;

            //     loadOptions({
            //         url: "{{ route('beginVoucher.by.agency') }}",
            //         data: {
            //             program_id: programId
            //         },
            //         targetSelect: '#cboAgency',
            //         instanceRefSetter: () => {
            //             agencyChoices = resetChoices('#cboAgency', agencyChoices);
            //         }
            //     });
            // }

            // // ========= Script 3: ProgramSub -> Cluster =========
            // function handleProgramSubChangeForCluster(programSubId) {
            //     resetSelect('#cboCluster');
            //     clusterChoices = resetChoices('#cboCluster', clusterChoices);

            //     if (!programSubId) return;

            //     loadOptions({
            //         url: "{{ route('beginVoucher.by.cluster') }}",
            //         data: {
            //             program_sub_id: programSubId
            //         },
            //         targetSelect: '#cboCluster',
            //         instanceRefSetter: () => {
            //             clusterChoices = resetChoices('#cboCluster', clusterChoices);
            //         }
            //     });
            // }

            // ========= Events =========
            $('#cboPosition').on('change', function() {
                const levelId = $(this).val();

                // when program changes -> always clear cluster too
                handleProgramChangeForProgramSub(levelId);
                // handleProgramChangeForAgency(programId);
                // handleProgramSubChangeForCluster(null); // reset cluster
            });

            $('#cboLevel').on('change', function() {
                const programSubId = $(this).val();
                handleProgramSubChangeForCluster(programSubId);
            });

        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('missionForm');

            if (!form) {
                return;
            }

            // Initialize Pristine
            const pristine = new Pristine(form, {
                classTo: 'form-group',
                errorClass: 'is-invalid',
                successClass: 'is-valid',
                errorTextParent: 'form-group',
                errorTextTag: 'div',
                errorTextClass: 'invalid-feedback'
            });

            // Submit validation
            form.addEventListener('submit', function(e) {

                e.preventDefault();

                // Validate all fields
                const valid = pristine.validate();

                if (!valid) {

                    // Find first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');

                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        firstInvalid.focus();
                    }

                    return;
                }

                // Prevent double submit
                const btnSave = document.getElementById('btnSave');

                if (btnSave) {
                    btnSave.disabled = true;

                    btnSave.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                កំពុងរក្សាទុក...
            `;
                }

                // Submit form
                form.submit();
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('missionForm');

            if (!form) {
                return;
            }

            const pristine = new Pristine(form);

            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            // Validate end date
            if (endDate) {

                pristine.addValidator(
                    endDate,
                    function(value) {

                        if (!value || !startDate.value) {
                            return true;
                        }

                        const start = new Date(startDate.value);
                        const end = new Date(value);

                        return end >= start;

                    },
                    'ថ្ងៃបញ្ចប់ ត្រូវតែធំជាង ឬស្មើថ្ងៃចាប់ផ្តើម',
                    5,
                    false
                );
            }

        });
    </script>
@endsection
