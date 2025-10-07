@if (hasPermission('voucherLoan.edit') or hasPermission('voucherLoan.destroy'))
    <div class="dropdown">
        <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if (is_null($module->deleted_at))
                @if (hasPermission('voucherLoan.edit'))
                    <a href="{{ route('voucher.index', encode_params($module->id)) }}" class="dropdown-item">
                        <i class="bx bx-show"></i> {{ __('buttons.show') }}
                    </a>
                @endif
                {{-- @if (hasPermission('voucherLoan.edit') or hasPermission('voucherLoan.destroy'))
                    <hr />
                @endif --}}
                {{-- @if (hasPermission('voucherLoan.edit'))
                    <a href="{{ route('voucherLoan.edit', encode_params($module->id)) }}" class="dropdown-item"><i
                            class="bx bx-edit"></i> {{ __('buttons.edit') }}</a>
                @endif --}}
                {{-- @if (hasPermission('voucherLoan.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('voucherLoan.destroy', encode_params($module->id)) }}', 1)"
                        class="dropdown-item"><i class="bx bx-trash"></i> {{ __('buttons.delete') }}</a>
                @endif --}}
            @else
                @if (hasPermission('voucherLoan.destroy'))
                    <a href="#"
                        onclick="confirm('{{ route('voucherLoan.restore', encode_params($module->id)) }}', 2)"
                        class="dropdown-item"><i class="bx bx-undo"></i> {{ __('buttons.restore') }}</a>
                @endif
            @endif
        </ul>
    </div>
@endif
