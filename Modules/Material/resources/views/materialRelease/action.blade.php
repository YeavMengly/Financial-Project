@if (hasPermission('materialRelease.edit') or hasPermission('materialRelease.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('materialRelease.edit'))
                    <a href="{{ route('materialRelease.edit', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit') }}
                    </a>
                @endif
                @if (hasPermission('materialRelease.destroy'))
                    <a href="javascript:void(0);"
                        onclick="confirm('{{ route('materialRelease.destroy', [encode_params($module->ministry_id), encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @endif
        </ul>
    </div>
@endif
