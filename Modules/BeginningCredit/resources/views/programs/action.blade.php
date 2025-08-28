@if (hasPermission('programs.edit') or hasPermission('programs.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('programs.edit'))
                    @if (hasPermission('programs.edit'))
                        <a href="{{ route('programs.edit', encode_params($module->id)) }}" class="dropdown-item"><i
                                class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                    @endif
                    @if (hasPermission('programs.destroy'))
                        <a href="#"
                            onclick="confirm('{{ route('programs.destroy', encode_params($module->id)) }}', 1)"
                            class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                    @endif
                @else
                    @if (hasPermission('programs.destroy'))
                        <a href="#"
                            onclick="confirm('{{ route('programs.restore', encode_params($module->id)) }}', 2)"
                            class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                    @endif
                @endif
            @endif
        </ul>
    </div>
@endif
