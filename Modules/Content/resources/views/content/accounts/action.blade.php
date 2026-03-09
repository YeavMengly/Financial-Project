@if (hasPermission('accounts.edit') or hasPermission('accounts.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('accounts.index'))
                    <a href="{{ route('accountSub.index', ['params' => encode_params($module->ministry_id), 'chId' => encode_params($module->chapter_id), 'accId' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-folder"></i> {{ __('buttons.account.sub') }}</a>
                @endif
                @if (hasPermission('account.index') and (hasPermission('chapters.edit') or hasPermission('chapters.destroy')))
                    <hr />
                @endif
                @if (hasPermission('accounts.edit'))
                    <a href="{{ route('accounts.edit', ['params' => encode_params($module->ministry_id), 'chId' => encode_params($module->chapter_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif
                @if (hasPermission('accounts.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('accounts.destroy', ['params' => encode_params($module->ministry_id), 'chId' => encode_params($module->chapter_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif
            @else
                @if (hasPermission('accounts.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('accounts.restore', ['params' => encode_params($module->ministry_id), 'chId' => encode_params($module->chapter_id), 'id' => encode_params($module->id)]) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
