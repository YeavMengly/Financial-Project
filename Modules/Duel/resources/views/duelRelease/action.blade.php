@if (hasPermission('duelRelease.edit') or hasPermission('duelRelease.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @php
                    // Checks if file exists directly under the public/ directory
                    $hasFile = !empty($module->file) && file_exists(public_path($module->file));
                    $hasOtherActions = hasPermission('duelRelease.edit') || hasPermission('duelRelease.destroy');
                @endphp
                @if (hasPermission('duelRelease.index') && $hasFile)
                    <a download href="{{ asset($module->file) }}" class="dropdown-item">
                        <i class="bx bx-download"></i>
                        {{ __('buttons.download') }}
                    </a>
                    @if ($hasOtherActions)
                        <hr class="dropdown-divider" />
                    @endif
                @endif
                @if (hasPermission('duelRelease.edit'))
                    <a href="{{ route('duelRelease.edit', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif
                @if (hasPermission('duelRelease.edit.doc'))
                    <a href="{{ route('duelRelease.edit.doc', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item">
                        <i class="bx bx-edit"></i> {{ __('buttons.edit.document') }}
                    </a>
                @endif
                @if (hasPermission('duelRelease.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('duelRelease.destroy', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @endif
        </ul>
    </div>
@endif
