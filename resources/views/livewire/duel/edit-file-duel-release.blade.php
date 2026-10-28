<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('menus.document') }}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">
                                {{ __('menus.document') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ __('buttons.edit.doc') }}
                        </li>
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
                        <div class="form-group">
                            <label> Upload {{ __('menus.document') }}</label>
                            <input type="file" id="documentFile" class="form-control" wire:model="newDocumentFile">
                            <div wire:loading wire:target="newDocumentFile" class="text-info mt-1">
                                Uploading file, please wait...
                            </div>
                            @error('newDocumentFile')
                                <span class="text-danger d-block mt-1">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled"
                            wire:target="newDocumentFile" type="submit" name="submit" value="save">
                            <span wire:loading.remove wire:target="save">{{ __('buttons.save') }}</span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                            </span>
                        </button>

                    </form>

                </div>
            </div>
        </div>

        <div class="col-3"></div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {

        const input = document.getElementById('documentFile');

        if (!input) {
            return;
        }

        input.addEventListener('change', function() {

            const file = this.files[0];

            if (!file) {
                return;
            }

            // Get extension
            const extension = file.name.includes('.') ?
                file.name.substring(file.name.lastIndexOf('.')).toLowerCase() :
                '';

            // Generate short filename
            const shortName =
                Date.now() +
                '_' +
                Math.random().toString(36).substring(2, 10) +
                extension;

            // Create a new File with a short filename
            const renamedFile = new File(
                [file],
                shortName, {
                    type: file.type,
                    lastModified: file.lastModified
                }
            );

            // Replace the selected file
            const dataTransfer = new DataTransfer();

            dataTransfer.items.add(renamedFile);

            this.files = dataTransfer.files;

            // Trigger Livewire upload
            this.dispatchEvent(new Event('change', {
                bubbles: true
            }));
        });

    });
</script>
