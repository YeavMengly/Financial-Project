@if (hasPermission('mandate.edit') or hasPermission('mandate.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('mandate.edit'))
                    <a href="{{ route('mandate.edit', encode_params($module->id)) }}" class="dropdown-item"><i
                            class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif
                @if (hasPermission('mandate.destroy'))
                    <a href="#" onclick="confirm('{{ route('mandate.destroy', encode_params($module->id)) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
