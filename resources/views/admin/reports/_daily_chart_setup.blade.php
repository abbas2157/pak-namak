{{-- Chart.js + shared chrome for the daily reports. Defines window.DailyCharts
     with helpers: stackedBars(id, unit, series[]), singleBars(id, unit, label, data, color),
     runningLine(id, unit, curLabel, prevLabel, D). Same palette/ink as the monthly
     comparison reports; colour follows the entity: Salt = blue, Spice = orange,
     last month = neutral ink. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
window.DailyCharts = (function () {
    if (!window.Chart) return null;

    const PAL  = { salt: '#2a78d6', spice: '#eb6834' };
    const INK  = { primary: '#0b0b0b', secondary: '#52514e', muted: '#898781', grid: '#e1e0d9', axis: '#c3c2b7', surface: '#ffffff' };
    const fmt  = n => Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 });

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = INK.muted;
    Object.assign(Chart.defaults.plugins.tooltip, {
        backgroundColor: INK.surface, titleColor: INK.primary, bodyColor: INK.secondary,
        footerColor: INK.primary, borderColor: INK.grid, borderWidth: 1, padding: 10, boxPadding: 4,
        mode: 'index', intersect: false,
    });
    Object.assign(Chart.defaults.plugins.legend.labels, { boxWidth: 10, boxHeight: 10, usePointStyle: true, color: INK.secondary });

    const axes = (unit, stacked = false) => ({
        x: { stacked, grid: { display: false }, border: { color: INK.axis }, ticks: { maxRotation: 0, autoSkip: true } },
        y: { stacked, beginAtZero: true, grid: { color: INK.grid }, border: { display: false },
             ticks: { callback: v => fmt(v), maxTicksLimit: 6 }, title: { display: true, text: unit, color: INK.muted } },
    });
    const bar = (label, data, color) => ({ label, data, backgroundColor: color, borderColor: INK.surface, borderWidth: 2, borderRadius: 3, borderSkipped: false });
    const tip = (footer) => ({ callbacks: { title: i => 'Day ' + i[0].label, label: i => ' ' + i.dataset.label + ': ' + fmt(i.parsed.y), footer } });

    return {
        PAL, INK, fmt,

        // Stacked bars — series: [{label, data, color}], 2px surface gap, per-day hover with total.
        stackedBars(id, unit, days, series) {
            return new Chart(document.getElementById(id), {
                type: 'bar',
                data: { labels: days, datasets: series.map(s => bar(s.label, s.data, s.color)) },
                options: {
                    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    scales: axes(unit, true),
                    plugins: { legend: { position: 'top', align: 'end' },
                               tooltip: tip(items => 'Total: ' + fmt(items.reduce((s, i) => s + (i.parsed.y || 0), 0))) },
                },
            });
        },

        // One series — the title names it, so no legend box.
        singleBars(id, unit, days, label, data, color) {
            return new Chart(document.getElementById(id), {
                type: 'bar',
                data: { labels: days, datasets: [bar(label, data, color)] },
                options: {
                    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    scales: axes(unit),
                    plugins: { legend: { display: false }, tooltip: tip(() => '') },
                },
            });
        },

        // Cumulative line — this month vs last month on a shared day-of-month axis.
        runningLine(id, unit, curLabel, prevLabel, D) {
            const pt = { pointRadius: 0, pointHoverRadius: 5, pointHoverBorderColor: INK.surface, pointHoverBorderWidth: 2, tension: 0.2, borderWidth: 2 };
            return new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: D.cumulative_days,
                    datasets: [
                        { label: curLabel,  data: D.cumulative,      borderColor: PAL.salt,  backgroundColor: PAL.salt,  ...pt },
                        { label: prevLabel, data: D.cumulative_prev, borderColor: INK.muted, backgroundColor: INK.muted, borderDash: [5, 4], ...pt },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                    scales: axes(unit),
                    plugins: {
                        legend: { position: 'top', align: 'end' },
                        tooltip: tip(items => {
                            if (items.length < 2 || !items[1].parsed.y) return '';
                            const pct = ((items[0].parsed.y - items[1].parsed.y) / items[1].parsed.y * 100).toFixed(1);
                            return (pct >= 0 ? '▲ ' + pct + '% ahead' : '▼ ' + Math.abs(pct) + '% behind') + ' of last month';
                        }),
                    },
                },
            });
        },
    };
})();
</script>
