@extends('layouts.master')

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">កំណត់អង្គភាពថវិកា តាមកម្មវិធី / អនុកម្មវិធី / ចង្កោម</h5>
            <a href="{{ route('agency.index', $params) }}" class="btn btn-dark btn-sm">ត្រឡប់វិញ</a>
        </div>

        {{-- Ministry Filter & Live Search --}}
        <form method="GET" action="{{ route('agencies.assignments', ['params' => $params]) }}"
            class="card card-body mb-3 py-2">
            <div class="row align-items-center g-2">
                <div class="col-md-4">
                    <label class="form-label small fw-bold mb-1">ជ្រើសរើសក្រសួង</label>
                    <select name="ministry_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($ministries as $ministry)
                            <option value="{{ $ministry->id }}"
                                {{ (int) $ministryId === (int) $ministry->id ? 'selected' : '' }}>
                                [ឆ្នាំ {{ $ministry->year }}] {{ $ministry->no }} - {{ $ministry->name }}
                                ({{ $ministry->title }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold mb-1">ស្វែងរកកូដ ឬ បរិយាយ</label>
                    <input type="text" id="treeSearch" class="form-control form-control-sm"
                        placeholder="វាយពាក្យស្វែងរក...">
                </div>
                <div class="col-md-4 text-end pt-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="expandAllBtn">ពង្រីកទាំងអស់</button>
                </div>
            </div>
        </form>
        {{-- Assignment Matrix Form --}}
        <form action="{{ route('agencies.assignments.save', ['params' => $params]) }}" method="POST">
            @csrf
            <input type="hidden" name="ministry_id" value="{{ $ministryId }}">

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="matrixTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 14%;">កូដ</th>
                                <th style="width: 51%;">បរិយាយ</th>
                                <th style="width: 35%;">អង្គភាពថវិកា</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $program)
                                @php
                                    $selectedProgAgency = $progMap[$program->id] ?? '';
                                @endphp
                                {{-- LEVEL 1: PROGRAM --}}
                                <tr class="table-primary prog-row" data-prog="{{ $program->id }}">
                                    <td class="fw-bold">
                                        <button type="button" class="btn btn-sm btn-outline-dark py-0 px-1 me-1 toggle-btn"
                                            data-target=".sub-of-{{ $program->id }}">+</button>
                                        {{ $program->no }}
                                    </td>
                                    <td class="fw-bold text-primary">កម្មវិធីទី{{ $program->no }}៖ {{ $program->title }}
                                    </td>
                                    <td>
                                        <select name="prog_agency[{{ $program->id }}]"
                                            class="form-select form-select-sm level-prog" data-prog="{{ $program->id }}">
                                            <option value="">-- ជ្រើសរើសសម្រាប់កម្មវិធីទាំងមូល --</option>
                                            @foreach ($agencies as $agency)
                                                <option value="{{ $agency->id }}"
                                                    {{ $selectedProgAgency == $agency->id ? 'selected' : '' }}>
                                                    {{ $agency->no }}-{{ $agency->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                {{-- LEVEL 2: SUB-PROGRAMS --}}
                                @foreach ($program->programSubs as $sub)
                                    @php
                                        $selectedSubAgency = $subMap[$sub->id] ?? $selectedProgAgency;
                                    @endphp
                                    <tr class="table-light sub-row sub-of-{{ $program->id }} d-none"
                                        data-prog="{{ $program->id }}" data-sub="{{ $sub->id }}">
                                        <td class="ps-3 fw-semibold">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 toggle-btn"
                                                data-target=".clus-of-{{ $sub->id }}">+</button>
                                            {{ $program->no }}.{{ $sub->no }}
                                        </td>
                                        <td class="fw-semibold">អនុកម្មវិធី {{ $sub->no }}៖ {{ $sub->decription }}
                                        </td>
                                        <td>
                                            <select name="sub_agency[{{ $sub->id }}]"
                                                class="form-select form-select-sm level-sub"
                                                data-prog="{{ $program->id }}" data-sub="{{ $sub->id }}">
                                                <option value="">-- ជ្រើសរើសសម្រាប់អនុកម្មវិធីទាំងមូល --</option>
                                                @foreach ($agencies as $agency)
                                                    <option value="{{ $agency->id }}"
                                                        {{ $selectedSubAgency == $agency->id ? 'selected' : '' }}>
                                                        {{ $agency->no }}-{{ $agency->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>

                                    {{-- LEVEL 3: CLUSTERS --}}
                                    @foreach ($sub->clusters as $cluster)
                                        @php
                                            $selectedClusAgency = $clusMap[$cluster->id] ?? $selectedSubAgency;
                                        @endphp
                                        <tr class="clus-row sub-of-{{ $program->id }} clus-of-{{ $sub->id }} d-none"
                                            data-prog="{{ $program->id }}" data-sub="{{ $sub->id }}">
                                            <td class="ps-5 text-muted">
                                                {{ $program->no }}.{{ $sub->no }}.{{ $cluster->no }}</td>
                                            <td>ចង្កោម {{ $cluster->no }}៖ {{ $cluster->decription }}</td>
                                            <td>
                                                <select name="clus_agency[{{ $cluster->id }}]"
                                                    class="form-select form-select-sm level-clus"
                                                    data-prog="{{ $program->id }}" data-sub="{{ $sub->id }}">
                                                    <option value="">-- ជ្រើសរើសអង្គភាព --</option>
                                                    @foreach ($agencies as $agency)
                                                        <option value="{{ $agency->id }}"
                                                            {{ $selectedClusAgency == $agency->id ? 'selected' : '' }}>
                                                            {{ $agency->no }}-{{ $agency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        មិនមានទិន្នន័យកម្មវិធីសម្រាប់ក្រសួងនេះទេ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white sticky-bottom text-end">
                    <button type="submit" class="btn btn-primary px-4">រក្សាទុកការកំណត់</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Expand / Collapse individual rows
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetSelector = this.dataset.target;
                    const targets = document.querySelectorAll(targetSelector);
                    const isOpening = this.textContent.trim() === '+';

                    targets.forEach(row => {
                        if (isOpening && targetSelector.startsWith('.sub-of-') && row
                            .classList.contains('clus-row')) {
                            return;
                        }
                        row.classList.toggle('d-none', !isOpening);
                        if (!isOpening && row.classList.contains('sub-row')) {
                            const childBtn = row.querySelector('.toggle-btn');
                            if (childBtn) childBtn.textContent = '+';
                        }
                    });

                    this.textContent = isOpening ? '-' : '+';
                });
            });

            // 2. Expand / Collapse All button
            let allExpanded = false;
            document.getElementById('expandAllBtn').addEventListener('click', function() {
                allExpanded = !allExpanded;
                document.querySelectorAll('.sub-row, .clus-row').forEach(r => r.classList.toggle('d-none', !
                    allExpanded));
                document.querySelectorAll('.toggle-btn').forEach(b => b.textContent = allExpanded ? '-' :
                    '+');
                this.textContent = allExpanded ? 'បង្រួមទាំងអស់' : 'ពង្រីកទាំងអស់';
            });

            // 3. Program Dropdown -> Auto-fill all Sub-Programs & Clusters underneath
            document.querySelectorAll('.level-prog').forEach(select => {
                select.addEventListener('change', function() {
                    const progId = this.dataset.prog;
                    const val = this.value;
                    document.querySelectorAll(
                            `.level-sub[data-prog="${progId}"], .level-clus[data-prog="${progId}"]`)
                        .forEach(el => el.value = val);
                });
            });

            // 4. Sub-Program Dropdown -> Auto-fill Clusters underneath & clear Program-level wildcard
            document.querySelectorAll('.level-sub').forEach(select => {
                select.addEventListener('change', function() {
                    const progId = this.dataset.prog;
                    const subId = this.dataset.sub;
                    const val = this.value;

                    document.querySelector(`.level-prog[data-prog="${progId}"]`).value = '';
                    document.querySelectorAll(`.level-clus[data-sub="${subId}"]`)
                        .forEach(el => el.value = val);
                });
            });

            // 5. Cluster Dropdown -> Clear Program & Sub-Program wildcards so individual cluster rules save
            document.querySelectorAll('.level-clus').forEach(select => {
                select.addEventListener('change', function() {
                    const progId = this.dataset.prog;
                    const subId = this.dataset.sub;
                    document.querySelector(`.level-prog[data-prog="${progId}"]`).value = '';
                    document.querySelector(`.level-sub[data-sub="${subId}"]`).value = '';
                });
            });

            // 6. Live Search Filter
            document.getElementById('treeSearch').addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                if (!term) {
                    document.querySelectorAll('.prog-row').forEach(r => r.classList.remove('d-none'));
                    document.querySelectorAll('.sub-row, .clus-row').forEach(r => r.classList.add(
                        'd-none'));
                    document.querySelectorAll('.toggle-btn').forEach(b => b.textContent = '+');
                    return;
                }
                document.querySelectorAll('#matrixTable tbody tr').forEach(row => {
                    const match = row.textContent.toLowerCase().includes(term);
                    row.classList.toggle('d-none', !match);
                });
            });
        });
    </script>
@endsection
