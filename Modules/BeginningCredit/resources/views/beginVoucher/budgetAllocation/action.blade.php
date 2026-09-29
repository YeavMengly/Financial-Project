@if (hasPermission('budgetAllocation.edit') or hasPermission('budgetAllocation.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('budgetAllocation.edit'))
                    <a href="{{ route('budgetAllocation.edit', ['params' => encode_params($module->ministry_id), 'budgetAllocationId' => encode_params($module->budget_begin_voucher_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit') }}
                    </a>
                @endif

                @if (hasPermission('budgetAllocation.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('budgetAllocation.destroy', ['params' => encode_params($module->ministry_id), 'budgetAllocationId' => encode_params($module->budget_begin_voucher_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @endif
        </ul>
    </div>
@endif
