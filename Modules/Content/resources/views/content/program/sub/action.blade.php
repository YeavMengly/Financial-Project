@if (hasPermission('program.sub.edit') or hasPermission('program.sub.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))

                @if (hasPermission('program.sub.index'))
                    <a href="{{ route('cluster.index', [
                        'params' => encode_params($module->ministry_id),
                        'pId' => encode_params($module->program_id),
                        'pSubId' => encode_params($module->id),
                    ]) }}"
                        class="dropdown-item"><i class="bx bx-folder"></i> {{ __('buttons.cluster') }}</a>
                @endif
                @if (hasPermission('program.sub.index') and (hasPermission('program.edit') or hasPermission('program.destroy')))
                    <hr />
                @endif
                @if (hasPermission('program.sub.edit'))
                    <a href="{{ route('program.sub.edit', ['params' => encode_params($module->ministry_id), 'pId' => encode_params($module->program_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif
                @if (hasPermission('program.sub.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('program.sub.destroy', ['params' => encode_params($module->ministry_id), 'pId' => encode_params($module->program_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif
            @else
                @if (hasPermission('program.sub.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('program.sub.restore', ['params' => encode_params($module->ministry_id), 'pId' => encode_params($module->program_id), 'id' => encode_params($module->id)]) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
