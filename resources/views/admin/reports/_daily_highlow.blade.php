{{-- One High / Low card. Expects: $x (highLow array or null), $label, $urdu, $unit, $icon, $cls, $noun --}}
<div class="card card-pn border-0 shadow-sm h-100">
    <div class="card-body py-3 px-4">
        <div class="pn-stat-lbl mb-2"><i class="fas {{ $icon }} mr-1 {{ $cls }}"></i>{{ $label }} — High / Low <span class="text-muted">{{ $urdu }}</span></div>
        @if($x)
            <div class="row">
                <div class="col-4">
                    <div class="hl-tag hl-high"><i class="fas fa-arrow-up mr-1"></i>Highest day</div>
                    <div class="hl-val">{{ number_format($x['high']['value'], 0) }} <small class="text-muted">{{ $unit }}</small></div>
                    <div class="hl-sub">{{ $x['high']['date']->format('D, d M') }} · {{ $x['high']['share'] }}% of month</div>
                </div>
                <div class="col-4">
                    <div class="hl-tag hl-low"><i class="fas fa-arrow-down mr-1"></i>Lowest day</div>
                    <div class="hl-val">{{ number_format($x['low']['value'], 0) }} <small class="text-muted">{{ $unit }}</small></div>
                    <div class="hl-sub">{{ $x['low']['date']->format('D, d M') }} · {{ $x['low']['share'] }}% of month</div>
                </div>
                <div class="col-4">
                    <div class="hl-tag hl-avg"><i class="fas fa-equals mr-1"></i>Avg / active day</div>
                    <div class="hl-val">{{ number_format($x['average'], 0) }} <small class="text-muted">{{ $unit }}</small></div>
                    <div class="hl-sub">high is {{ $x['average'] > 0 ? round($x['high']['value'] / $x['average'] * 100) : 0 }}% of average</div>
                </div>
            </div>
        @else
            <p class="text-muted mb-0">No {{ $noun }} recorded this month.</p>
        @endif
    </div>
</div>
