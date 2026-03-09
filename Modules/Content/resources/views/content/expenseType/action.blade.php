@if (hasPermission('expenseType.edit') or hasPermission('expenseType.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            @if (is_null($module->deleted_at))
                @if (hasPermission('expenseType.edit'))
                    <a href="{{ route('expenseType.edit', encode_params($module->id)) }}" class="dropdown-item"><i
                            class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif

                @if (hasPermission('expenseType.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('expenseType.destroy', encode_params($module->id)) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif
            @else
                @if (hasPermission('expenseType.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('expenseType.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
