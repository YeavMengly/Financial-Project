<div class="mb-3">
    <label for="agencyDropdown" class="form-label">Select Agency</label>
    <select id="agencyDropdown" class="form-control" name="agency_id">
        <option value="">-- Choose Agency --</option>
        @foreach ($agency as $item)
            <option value="{{ $item->id }}">{{ $item->agencyTitle }}</option>
        @endforeach
    </select>
</div>
