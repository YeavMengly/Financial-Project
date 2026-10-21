@if (hasPermission('budgetVoucher.edit') or hasPermission('budgetVoucher.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @php
                    // Checks if file exists directly under the public/ directory
                    $hasFile = !empty($module->attachments) && file_exists(public_path($module->attachments));
                    $hasOtherActions = hasPermission('budgetVoucher.edit') || hasPermission('budgetVoucher.destroy');
                @endphp
                @if (hasPermission('budgetVoucher.index') && $hasFile)
                    <a download href="{{ asset($module->attachments) }}" class="dropdown-item"><i
                            class="bx bx-download"></i>
                        {{ __('buttons.download') }}</a>
                    @if ($hasOtherActions)
                        <hr class="dropdown-divider" />
                    @endif
                @endif
                @if (hasPermission('budgetVoucher.edit') or
                        hasPermission('budgetVoucher.edit.doc') or
                        hasPermission('budgetVoucher.destroy'))
                @endif
                @if (hasPermission('budgetVoucher.edit'))
                    <a href="{{ route('budgetVoucher.edit', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif
                @if (hasPermission('budgetVoucher.edit.doc'))
                    <a href="{{ route('budgetVoucher.edit.doc', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}"
                        class="dropdown-item"><i class="bx bx-edit"></i> {{ __('buttons.edit.document') }}</a>
                @endif
                @if (hasPermission('budgetVoucher.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('budgetVoucher.destroy', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 1)"
                        class="dropdown-item">
                        <i class="bx bx-trash"></i> {{ __('buttons.delete') }}
                    </a>
                @endif
            @else
                @if (hasPermission('budgetVoucher.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('budgetVoucher.restore', ['params' => encode_params($module->ministry_id), 'id' => encode_params($module->id)]) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
