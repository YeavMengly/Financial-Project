@if (hasPermission('initialBudgetMandate.edit') or hasPermission('initialBudgetMandate.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('initialBudgetMandate.edit'))
                    <a href="{{ route('beginCreditMandate.index', encode_params($module->id)) }}" class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif
                @if (hasPermission('initialBudgetMandate.edit') or hasPermission('initialBudgetMandate.destroy'))
                    <hr />
                @endif
                {{--  @if (hasPermission('initialBudgetMandate.edit'))
                    <a href="{{ route('initialBudgetMandate.edit', encode_params($module->id)) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif 
                @if (hasPermission('initialBudgetMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialBudgetMandate.destroy', encode_params($module->id)) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif ​--}}
            @else
                @if (hasPermission('initialBudgetMandate.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialBudgetMandate.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
