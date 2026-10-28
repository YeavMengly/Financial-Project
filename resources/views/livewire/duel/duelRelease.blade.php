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
                             @if ($DuelReleaseOldFile)
                                 <a hidden href="{{ asset('storage/' . $DuelReleaseOldFile) }}" target="_blank">
                                     View File
                                 </a>
                             @endif
                             {{-- <div class="col-md-12">
                                 <div class="mb-3">
                                     <label class="form-label"
                                         for="validationCategory">{{ __('forms.document.file') }}</label>
                                     <input type="file" class="form-control" wire:model="file" />
                                     @error('file')
                                         <div class="pristine-error text-help">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div> --}}
                             <div class="form-group">
                                 <label> Upload {{ __('menus.document') }}</label>
                                 <input type="file" id="documentFile" class="form-control" wire:model="file">
                                 <div wire:loading wire:target="file" class="text-info mt-1">
                                     Uploading file, please wait...
                                 </div>
                                 @error('file')
                                     <span class="text-danger d-block mt-1">
                                         {{ $message }}
                                     </span>
                                 @enderror
                             </div>
                             <div class="d-flex flex-wrap gap-2">
                                 <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled"
                                     wire:target="newDocumentFile" type="submit" name="submit" value="save">
                                     <span wire:loading.remove wire:target="save">{{ __('buttons.save') }}</span>
                                     <span wire:loading wire:target="save">
                                         <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                                     </span>
                                 </button>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
         <div class="col-3"></div>
     </div>
 </div>
