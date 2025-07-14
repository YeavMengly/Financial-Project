<div class="dropdown">
    <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-dots-horizontal-rounded"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @if (is_null($module->deleted_at))
            @if(hasPermission('document.index'))
                <a download href="{{ asset($module->fileName) }}" class="dropdown-item"><i
                        class="bx bx-download"></i> {{ __("buttons.download") }}</a>
            @endif
            @if (hasPermission('document.edit') OR hasPermission('document.edit.doc') OR hasPermission('document.destroy'))
                <hr/>
            @endif
            @if(hasPermission('document.edit'))
                <a href="{{ route("document.edit", encode_params($module->id)) }}" class="dropdown-item"><i
                        class="bx bx-edit"></i> {{ __("buttons.edit") }}</a>
            @endif
            @if(hasPermission('document.edit.doc'))
                <a href="{{ route("document.edit.doc", encode_params($module->id)) }}" class="dropdown-item"><i
                        class="bx bx-edit"></i> {{ __("buttons.edit.document") }}</a>
            @endif
            @if(hasPermission('document.destroy'))
                <a href="#" onclick="confirm('{{ route("document.destroy", encode_params($module->id)) }}', 1)"
                   class="dropdown-item"><i class="bx bx-trash"></i> {{ __("buttons.delete") }}</a>
            @endif
        @else
            @if(hasPermission('document.destroy'))
                <a href="#" onclick="confirm('{{ route("document.restore", encode_params($module->id)) }}', 2)"
                   class="dropdown-item"><i class="bx bx-undo"></i> {{ __("buttons.restore") }}</a>
            @endif
        @endif
    </ul>
</div>
