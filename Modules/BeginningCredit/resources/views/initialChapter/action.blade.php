@if (hasPermission('initialChapter.edit') or hasPermission('initialChapter.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))

                {{-- @if (hasPermission('initialChapter.index'))
                    <a class="dropdown-item" href=""><i class="bx bx-download me-1"></i>
                        {{ __('buttons.download') }}</a>
                @endif
                @if (hasPermission('initialChapter.edit') or hasPermission('initialChapter.destroy'))
                    <hr />
                @endif --}}
                @if (hasPermission('initialChapter.edit'))
                    <a href="{{ route('chapter.index', encode_params($module->id)) }}" class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif
            @else
                @if (hasPermission('initialChapter.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('initialChapter.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
