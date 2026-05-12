<div class="dropdown">

    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bx bx-columns"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="min-width:250px;">

        <h6 class="dropdown-header">
            {{ __('tables.hide') }} / {{ __('tables.show') }}
        </h6>

        <div class="dropdown-divider"></div>

        <!-- RESET -->
        <button type="button" id="resetImportantsColumns" class="btn btn-sm btn-outline-danger w-100">
            <i class="bx bx-reset"></i>
            Reset Columns
        </button>

        <!-- COLUMNS -->
        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="0" checked>
            <label class="form-check-label">
                {{ __('tables.th.id') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="1" checked>
            <label class="form-check-label">
                {{ __('tables.th.description') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="2" checked>
            <label class="form-check-label">
                {{ __('tables.th.program') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="3" checked>
            <label class="form-check-label">
                {{ __('tables.th.agency') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="4" checked>
            <label class="form-check-label">
                {{ __('tables.th.agency.execute') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="5" checked>
            <label class="form-check-label">
                {{ __('tables.th.financeLaw') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="6" checked>
            <label class="form-check-label">
                {{ __('tables.th.new_credit_status') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="7" checked>
            <label class="form-check-label">
                {{ __('tables.th.deadline_balance') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="8" checked>
            <label class="form-check-label">
                {{ __('tables.th.law_average') }}
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-importants" type="checkbox" data-column="9" checked>
            <label class="form-check-label">
                {{ __('tables.th.law_correction') }}
            </label>
        </div>

    </div>
</div>
