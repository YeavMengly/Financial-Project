@if(hasPermission('category.edit') OR hasPermission('category.destroy') OR hasPermission('category.sub.index'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if(hasPermission('category.sub.index'))
                    <a href="{{ route("category.sub.index", $module->id) }}" class="dropdown-item"><i
                            class="bx bx-folder"></i> {{ __("buttons.sub.category") }}</a>
                @endif
                @if (hasPermission('category.sub.index') AND (hasPermission('category.edit') OR hasPermission('category.destroy')))
                    <hr/>
                @endif
                @if(hasPermission('category.edit'))
                    <a href="{{ route("category.edit", $module->id) }}" class="dropdown-item"><i
                            class="bx bx-edit"></i> {{ __("buttons.edit") }}</a>
                @endif
                @if(hasPermission('category.destroy'))
                    <a href="#" onclick="confirm('{{ route("category.destroy", $module->id) }}', 1)"
                       class="dropdown-item"><i class="bx bx-trash"></i> {{ __("buttons.delete") }}</a>
                @endif
            @else
                @if(hasPermission('category.destroy'))
                    <a href="#" onclick="confirm('{{ route("category.restore", $module->id) }}', 2)"
                       class="dropdown-item"><i
                            class="bx bx-undo"></i> {{ __("buttons.restore") }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
