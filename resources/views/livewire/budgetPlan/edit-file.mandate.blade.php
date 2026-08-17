 <div>
     <div class="row">
         <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                 <h4 class="mb-sm-0 font-size-18">{{ __('menus.document') }}</h4>
                 <div class="page-title-right">
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                             <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.document') }}</a>
                             </li>
                             <li class="breadcrumb-item active">{{ __('buttons.edit.document') }}</li>
                         </ol>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="row">
         <div class="col-3"></div>
         <div class="col-6">
             <div class="card">
                 <div class="card-body">
                     <form wire:submit="save">
                         <div class="row">
                             @if ($budgetMandateOldFile)
                                 <a hidden href="{{ asset('storage/' . $budgetMandateOldFile) }}" target="_blank">
                                     View File
                                 </a>
                             @endif
                             <div class="col-md-12">
                                 <div class="mb-3">
                                     <label class="form-label"
                                         for="validationCategory">{{ __('forms.document.file') }}</label>
                                     <input type="file" class="form-control" wire:model="attachments" />
                                     @error('attachments')
                                         <div class="pristine-error text-help">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             <div class="d-flex flex-wrap gap-2">
                                 <button class="btn btn-primary" type="submit" name="submit"
                                     value="save">{{ __('buttons.save') }}</button>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
         <div class="col-3"></div>
     </div>
 </div>
