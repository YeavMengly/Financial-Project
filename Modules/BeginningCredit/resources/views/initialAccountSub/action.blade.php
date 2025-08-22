@if (hasPermission('initialAccountSub.edit') or hasPermission('initialAccountSub.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('initialAccountSub.edit'))
                    <a href="{{ route('accountSub.index', encode_params($module->id)) }}" class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif
                {{-- @if (hasPermission('initialAccountSub.edit') or hasPermission('initialAccountSub.destroy'))
                    <hr />
                @endif --}}
            @else
                @if (hasPermission('initialAccountSub.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialAccountSub.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
