@if (hasPermission('initialMandate.edit') or hasPermission('initialMandate.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('initialMandate.edit'))
                    <a href="{{ route('budget-mandate.index', encode_params($module->id)) }}" class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif
                @if (hasPermission('initialMandate.edit') or hasPermission('initialMandate.destroy'))
                    <hr />
                @endif
                {{-- @if (hasPermission('initialMandate.edit'))
                    <a href="{{ route('initialMandate.edit', encode_params($module->id)) }}" class="dropdown-item"><i
                            class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif --}}
                {{-- @if (hasPermission('initialMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialMandate.destroy', encode_params($module->id)) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif --}}
            @else
                @if (hasPermission('initialMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialMandate.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
