<div class="dropdown">

    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-columns me-1"></i>  
    </button>

    <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="min-width: 260px; max-height: 400px; overflow-y: auto;">

        <h6 class="dropdown-header px-0 text-uppercase fw-bold">
            {{ __('tables.hide') }} / {{ __('tables.show') }}
        </h6>

        <div class="dropdown-divider"></div>

        <!-- RESET BUTTON -->
        <button type="button" id="resetChapterColumns" class="btn btn-sm btn-outline-danger w-100 mb-3">
            <i class="bx bx-reset"></i> Reset Columns
        </button>

        <!-- COLUMN TOGGLES (MATCHING TABLE HEADERS 0 TO 9) -->
        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="0" id="col_0" checked>
            <label class="form-check-label" for="col_0">
                ១. ជំពូក
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="1" id="col_1" checked>
            <label class="form-check-label" for="col_1">
                ២. ច្បាប់ហិរញ្ញវត្ថុ
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="2" id="col_2" checked>
            <label class="form-check-label" for="col_2">
                ៣. ឥណទានថ្មី
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="3" id="col_3" checked>
            <label class="form-check-label" for="col_3">
                ៤. ដើមគ្រា (ធានាចំណាយ)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="4" id="col_4" checked>
            <label class="form-check-label" for="col_4">
                ៥. អនុវត្ត (ធានាចំណាយ)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="5" id="col_5" checked>
            <label class="form-check-label" for="col_5">
                ៦. ភាគរយអនុវត្ត (៥/២)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="6" id="col_6" checked>
            <label class="form-check-label" for="col_6">
                ៧. បូកយោង (៤+៥)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="7" id="col_7" checked>
            <label class="form-check-label" for="col_7">
                ៨. ភាគរយបូកយោង (៧/២)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="8" id="col_8" checked>
            <label class="form-check-label" for="col_8">
                ៩. នៅសល់ (២-៧)
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input toggle-column-chapter" type="checkbox" data-column="9" id="col_9" checked>
            <label class="form-check-label" for="col_9">
                ១០. ភាគរយនៅសល់ (៩/២)
            </label>
        </div>

    </div>
</div>