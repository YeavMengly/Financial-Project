@if (hasPermission('budgetMandate.edit') or hasPermission('budgetMandate.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('budgetMandate.edit'))
                    <a href="{{ route('budgetMandate.edit', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit') }}
                    </a>
                @endif
                @if (hasPermission('budgetMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('budgetMandate.destroy', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @else
                @if (hasPermission('budgetMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('budgetMandate.restore', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 2)"
                        class="dropdown-item">
                        <i class="bx bx-undo"></i> {{ __('buttons.restore') }}
                    </a>
                @endif
            @endif
        </ul>
    </div>
@endif
