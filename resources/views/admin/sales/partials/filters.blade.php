{{-- Sales list filters (shared by salt + spice). Expects: $indexRoute, $months,
     $selectedMonth, $shops (with area), $areas (with city), $cities, $filters, $hasFilters.
     Every dropdown is a searchable select2 (see the JS at the bottom). --}}
<div class="card border-0 shadow-sm mb-3 card-pn">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bold text-c-blue2">
            <i class="fas fa-filter mr-2"></i>Filters / فلٹر
        </h6>
        @if($hasFilters)
            <span class="badge pn-bdg pn-bdg-blue">on</span>
        @endif
    </div>
    <div class="card-body py-3">
        <form method="GET" id="salesFilterForm">
            <label class="filter-lbl">Month / مہینہ</label>
            <select name="month" class="form-control form-control-sm fc-pn mb-2 filter-select" data-placeholder="All Time">
                <option value="">All Time</option>
                @foreach($months as $m)
                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>{{ $m->label }}</option>
                @endforeach
            </select>

            <label class="filter-lbl">Date / تاریخ</label>
            <div class="d-flex mb-2">
                <input type="date" name="from" class="form-control form-control-sm fc-pn mr-1" value="{{ $filters['from'] ?? '' }}" title="From">
                <input type="date" name="to" class="form-control form-control-sm fc-pn" value="{{ $filters['to'] ?? '' }}" title="To">
            </div>

            <label class="filter-lbl">City / شہر</label>
            <select name="city_id" id="filterCity" class="form-control form-control-sm fc-pn mb-2 filter-select" data-placeholder="All Cities">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ (string) ($filters['city_id'] ?? '') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>

            <label class="filter-lbl">Area / علاقہ</label>
            <select name="area_id" id="filterArea" class="form-control form-control-sm fc-pn mb-2 filter-select" data-placeholder="All Areas">
                <option value="">All Areas</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" data-city="{{ $area->city_id }}" {{ (string) ($filters['area_id'] ?? '') === (string) $area->id ? 'selected' : '' }}>
                        {{ $area->name }}{{ $area->city ? ' — '.$area->city->name : '' }}
                    </option>
                @endforeach
            </select>

            <label class="filter-lbl">Shop / دکان</label>
            <select name="shop_id" id="filterShop" class="form-control form-control-sm fc-pn mb-3 filter-select" data-placeholder="All Shops">
                <option value="">All Shops</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" data-city="{{ $shop->city_id }}" data-area="{{ $shop->area_id }}" {{ (string) ($filters['shop_id'] ?? '') === (string) $shop->id ? 'selected' : '' }}>
                        {{ $shop->name }}{{ $shop->location ? ' — '.$shop->location : '' }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-block btn-sm btn-primary btn-pn"><i class="fas fa-search mr-1"></i> Apply / لاگو کریں</button>
            @if($hasFilters)
                <a href="{{ route($indexRoute) }}" class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                    <i class="fas fa-times mr-1"></i> Clear Filters
                </a>
            @endif
        </form>
    </div>
</div>

@push('filter-scripts')
<script>
$(function () {
    // Searchable dropdowns
    $('.filter-select').each(function () {
        $(this).select2({ placeholder: $(this).data('placeholder'), allowClear: true, width: '100%' });
    });

    // City → Area → Shop: narrow the lower lists to the chosen city / area.
    function narrow() {
        const city = $('#filterCity').val();
        const area = $('#filterArea').val();

        $('#filterArea option[data-city]').each(function () {
            const ok = !city || String($(this).data('city')) === city;
            $(this).prop('disabled', !ok);
        });
        if ($('#filterArea option:selected').prop('disabled')) $('#filterArea').val('');

        $('#filterShop option[data-city]').each(function () {
            const ok = (!city || String($(this).data('city')) === city)
                    && (!$('#filterArea').val() || String($(this).data('area')) === $('#filterArea').val());
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
