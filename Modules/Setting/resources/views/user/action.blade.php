<div class="dropdown">
    <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-dots-horizontal-rounded"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @if (is_null($module->deleted_at))
            <a href="{{ route("user.edit", $module->id) }}" class="dropdown-item"><i
                    class="bx bx-edit"></i> {{ __("buttons.edit") }}</a>
            <a href="#" onclick="confirm('{{ route("user.destroy", $module->id) }}', 1)" class="dropdown-item"><i
                    class="bx bx-trash"></i> {{ __("buttons.delete") }}</a>
            <hr/>
            <a href="{{ route("user.password", $module->id) }}" class="dropdown-item"><i
                    class="bx bx-key"></i> {{ __("buttons.change.password") }}</a>
        @else
            <a href="#" onclick="confirm('{{ route("user.restore", $module->id) }}', 2)" class="dropdown-item"><i
                    class="bx bx-undo"></i> {{ __("buttons.restore") }}</a>
        @endif
    </ul>
</div>
