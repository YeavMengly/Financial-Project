@if (hasPermission('missions.edit') or hasPermission('missions.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @php
                    $hasFile = !empty($module->fileName) && file_exists(public_path($module->fileName));
                    $hasOtherActions = hasPermission('missions.edit') || hasPermission('missions.destroy');
                @endphp

                @if (hasPermission('missions.index') && $hasFile)
                    <a download href="{{ asset($module->fileName) }}" class="dropdown-item">
                        <i class="bx bx-download"></i>
                        {{ __('buttons.download') }}
                    </a>
                    @if ($hasOtherActions)
                        <hr />
                    @endif
                @endif

                @if (hasPermission('missions.show'))
                    <a href="{{ route('missions.show', [
                        'params' => encode_params($module->ministry_id),
                        'id' => encode_params($module->id),
                    ]) }}"
                        class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif

                @if (hasPermission('missions.edit') or hasPermission('missions.edit.doc') or hasPermission('missions.destroy'))
                    <hr />
                @endif

                @if (hasPermission('missions.edit'))
                    <a href="{{ route('missions.edit', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit') }}
                    </a>
                @endif
                @if (hasPermission('missions.edit.doc'))
                    <a href="{{ route('missions.edit.doc', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit.document') }}
                    </a>
                @endif
                @if (hasPermission('missions.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('missions.destroy', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @else
                @if (hasPermission('missions.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('missions.restore', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 2)"
                        class="dropdown-item">
                        <i class="bx bx-undo"></i> {{ __('buttons.restore') }}
                    </a>
                @endif
            @endif
        </ul>
    </div>
@endif
