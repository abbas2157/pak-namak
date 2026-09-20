{{-- Sales list filter bar (shared by salt + spice). Expects: $indexRoute, $months,
     $selectedMonth, $shops (with area), $areas (with city), $cities, $filters, $hasFilters.
     Rendered full-width above the list; every dropdown is a searchable select2. --}}
@php $activeCount = collect($filters)->filter()->count(); @endphp
<div class="card card-pn shadow-sm pn-filters mb-3">
    <div class="pn-filters-head">
        <h6 class="pn-filters-title"><i class="fas fa-filter"></i>Filters <span class="text-muted font-weight-normal">/ فلٹر</span></h6>
        @if($activeCount)
            <span class="pn-filters-count">{{ $activeCount }} active</span>
        @endif
    </div>
    <div class="pn-filters-body">
        <form method="GET" id="salesFilterForm">
            <div class="pn-filter-grid">
                <div class="pn-filter-field">
                    <label class="filter-lbl">Month / مہینہ</label>
                    <select name="month" class="form-control fc-pn filter-select" data-placeholder="All Time">
                        <option value="">All Time</option>
                        @foreach($months as $m)
                            <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>{{ $m->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pn-filter-field is-wide">
                    <label class="filter-lbl">Date / تاریخ</label>
                    <div class="pn-date-pair">
                        <input type="date" name="from" class="form-control fc-pn" value="{{ $filters['from'] ?? '' }}" title="From">
                        <span>TO</span>
                        <input type="date" name="to" class="form-control fc-pn" value="{{ $filters['to'] ?? '' }}" title="To">
                    </div>
                </div>

                <div class="pn-filter-field">
                    <label class="filter-lbl">City / شہر</label>
                    <select name="city_id" id="filterCity" class="form-control fc-pn filter-select" data-placeholder="All Cities">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ (string) ($filters['city_id'] ?? '') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pn-filter-field">
                    <label class="filter-lbl">Area / علاقہ</label>
                    <select name="area_id" id="filterArea" class="form-control fc-pn filter-select" data-placeholder="All Areas">
                        <option value="">All Areas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" data-city="{{ $area->city_id }}" {{ (string) ($filters['area_id'] ?? '') === (string) $area->id ? 'selected' : '' }}>
                                {{ $area->name }}{{ $area->city ? ' — '.$area->city->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pn-filter-field is-wide">
                    <label class="filter-lbl">Shop / دکان</label>
                    <select name="shop_id" id="filterShop" class="form-control fc-pn filter-select" data-placeholder="All Shops">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" data-city="{{ $shop->city_id }}" data-area="{{ $shop->area_id }}" {{ (string) ($filters['shop_id'] ?? '') === (string) $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}{{ $shop->location ? ' — '.$shop->location : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pn-filter-field pn-filter-actions">
                    <button class="btn btn-primary btn-pn px-3"><i class="fas fa-search mr-1"></i> Apply</button>
                    @if($hasFilters)
                        <a href="{{ route($indexRoute) }}" class="btn btn-pn btn-clear-filter px-3" title="Clear all filters"><i class="fas fa-times mr-1"></i> Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@push('filter-scripts')
<script>
$(function () {
    $('#salesFilterForm .filter-select').each(function () {
        $(this).select2({ placeholder: $(this).data('placeholder'), allowClear: true, width: '100%' });
    });

    // City → Area → Shop: narrow the lower lists to the chosen city / area.
    function narrow() {
        const city = $('#filterCity').val();

        $('#filterArea option[data-city]').each(function () {
            $(this).prop('disabled', !(!city || String($(this).data('city')) === city));
        });
        if ($('#filterArea option:selected').prop('disabled')) $('#filterArea').val('');
        const area = $('#filterArea').val();

        $('#filterShop option[data-city]').each(function () {
            const ok = (!city || String($(this).data('city')) === city)
                    && (!area || String($(this).data('area')) === area);
            $(this).prop('disabled', !ok);
        });
        if ($('#filterShop option:selected').prop('disabled')) $('#filterShop').val('');

        $('#filterArea, #filterShop').trigger('change.select2');
    }
    $('#filterCity, #filterArea').on('change', narrow);
    narrow();
});
</script>
@endpush
