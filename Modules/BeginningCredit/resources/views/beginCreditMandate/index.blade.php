 @extends('layouts.master')
 @section('css')
     <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
         type="text/css" />
     <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
         type="text/css" />
     <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
         type="text/css" />
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
 @endsection
 @section('content')
     <!-- start page title -->
     <div class="row">
         <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                 {{-- <h4 class="mb-sm-0 font-size-18">{{ __('menus.beginning.credit') }}</h4> --}}
                 <h4 class="mb-sm-0 font-size-18">
                     {{ __('menus.beginning.credit') }}
                 </h4>

                 <div class="page-title-right">
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                             <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.initial.budget') }}</a>
                             </li>
                             <li class="breadcrumb-item active">{{ __('buttons.credit') }}</li>
                         </ol>
                     </div>
                 </div>

             </div>
         </div>
     </div>
     <!-- end page title -->
     <div class="row">
         <div class="col-12">
             <div class="card">
                 <div class="card-body">
                     <form id="filter" class="row gx-3 gy-2 align-items-center mb-4 mb-lg-0">
                         <div class="col-sm-3">
                             <label class="visually-hidden" for="agencyNumber">{{ __('menus.sub.account') }}</label>
                             <select class="form-control" name="agencyNumber" id="agencyNumber">
                                 <option value="">{{ __('forms.search...') }}</option>

                                 @foreach ($agency as $agc)
                                     <option value="{{ $agc->agencyNumber }}">
                                         {{ $agc->agencyTitle }}</option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-sm-3">
                             <label class="visually-hidden" for="subAccountNumber">{{ __('menus.sub.account') }}</label>
                             <select class="form-control" name="subAccountNumber" id="subAccountNumber">
                                 <option value="">{{ __('forms.search...') }}</option>
                                 @foreach ($data as $ts)
                                     <option value="{{ $ts->subAccountNumber }}"
                                         {{ request('subAccountNumber') == $ts->subAccountNumber ? 'selected' : '' }}>
                                         {{ $ts->subAccountNumber }} - {{ $ts->program }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-sm-3">
                             <label class="visually-hidden" for="program">{{ __('menus.sub.account') }}</label>
                             <select class="form-control" name="program" id="program">
                                 <option value="">{{ __('forms.search...') }}</option>
                                 @foreach ($data as $ts)
                                     <option value="{{ $ts->program }}"
                                         {{ request('program') == $ts->program ? 'selected' : '' }}>
                                         {{ $ts->program }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-sm-3">
                             <button type="submit" class="btn btn-primary">{{ __('buttons.search') }}</button>
                         </div>
                     </form>

                 </div>
             </div>
         </div>
     </div>
     <div class="row">
         <div class="col-12">
             <div class="card">
                 <div class="card-body">
                     @if (hasPermission('beginCreditMandate.create'))
                         <div class="col-sm">
                             <div class="mb-4">
                                 <a class="btn btn-light waves-effect waves-light"
                                     href="{{ route('beginCreditMandate.create', encode_params($params)) }}">
                                     <i class="bx bx-plus me-1"></i> {{ __('buttons.create') }}
                                 </a>
                             </div>
                         </div>
                     @endif

                     <div class="table-responsive">
                         {!! $dataTable->table(['class' => 'table table-bordered dt-responsive  nowrap w-100']) !!}
                     </div>

                 </div>
             </div>
         </div>
     </div>
 @endsection
 @section('script')
     <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
     <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
     <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
     <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
     <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
     <script>
         function confirm(url, condi) {
             if (condi == 1) {
                 Swal.fire({
                     title: '{{ __('messages.confirm.delete') }}',
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#e7515a',
                     cancelButtonColor: '#e2a03f',
                     confirmButtonText: '{{ __('buttons.delete') }}!',
                     cancelButtonText: '{{ __('buttons.back') }}'
                 }).then((result) => {
                     if (result.isConfirmed) {
                         location.href = url;
                     }
                 });
             } else {
                 Swal.fire({
                     title: '{{ __('messages.confirm.back') }}',
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#2ab57d',
                     cancelButtonColor: '#e2a03f',
                     confirmButtonText: '{{ __('buttons.get.back') }}!',
                     cancelButtonText: '{{ __('buttons.back') }}'
                 }).then((result) => {
                     if (result.isConfirmed) {
                         location.href = url;
                     }
                 });
             }
         }
     </script>
     {!! $dataTable->scripts() !!}

     <!-- Choices.js (dropdowns) -->
     <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

     <!-- Custom logic for BeginCredit loading -->
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             const taskTypeSelect = document.getElementById('subAccountNumber');
             const taskTypeChoices = new Choices(taskTypeSelect, {
                 searchEnabled: true,
                 itemSelectText: '', // Hide "Press to select"
                 placeholderValue: 'ជ្រើសរើស', // Khmer placeholder
                 searchPlaceholderValue: 'ស្វែងរក...', // Khmer search placeholder
                 shouldSort: false
             });
         });
     </script>
     <script>
         $(document).ready(function() {
             const element = document.getElementById('agencyNumber');
             let choicesInstance = new Choices(element, {
                 searchEnabled: true,
                 itemSelectText: '',
                 shouldSort: false,
             });

             $('#agencyNumber').on('change', function() {
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

         $(document).ready(function() {
             const element = document.getElementById('program');
             let choicesInstance = new Choices(element, {
                 searchEnabled: true,
                 itemSelectText: '',
                 shouldSort: false,
             });

             $('#program').on('change', function() {
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
