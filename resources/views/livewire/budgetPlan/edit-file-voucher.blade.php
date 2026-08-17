<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('menus.document') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('menus.document') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('buttons.edit.document') }}</li>
                    </ol>
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

                            <!-- Document Title Field -->
                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="validationCategory">{{ __('forms.document.title') }}</label>
                                    <input type="text" class="form-control" wire:model="documentTitle" disabled />
                                    @error('documentTitle')
                                        <div class="pristine-error text-help text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <!-- Download Existing File Button -->
                            {{-- @if ($budgetVoucherOldFile)
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <a download href="{{ asset('storage/' . $budgetVoucherOldFile) }}"
                                           class="btn btn-outline-primary waves-effect waves-light">
                                            <i class="bx bx-download font-size-16 align-middle me-2"></i>
                                            {{ __('buttons.download') }}View File
                                        </a>
                                    </div>
                                </div>
                            @endif --}}

                            <!-- Upload New File Input -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"
                                        for="validationCategory">{{ __('forms.document.file') }}</label>
                                    <input type="file" class="form-control" wire:model.live="attachments" />

                                    <div wire:loading wire:target="attachments" class="text-primary font-size-13 mt-1">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Uploading file...
                                    </div>

                                    @error('attachments')
                                        <div class="pristine-error text-help text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="submit" name="submit" value="save">
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
