{{-- Charts need the same matrix data the table was built from; groups/months/
     monthTotals are inherited from the page view that @includes this partial. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    function applyFilter() {
        const params = new URLSearchParams();
        const from = document.getElementById('from').value;
        const to   = document.getElementById('to').value;
        if (from) params.set('from', from);
        if (to)   params.set('to', to);
        const query = params.toString();
        window.location.href = '{{ route($routeName) }}' + (query ? '?' + query : '');
    }

    (function () {
        const MM = @json(['months' => $months, 'groups' => $groups, 'monthTotals' => $monthTotals]);

        // Categorical palette, fixed slot order (validated for CVD adjacency on
        // a white surface). Colour follows the entity: group i / row i always
        // gets slot i, regardless of which metric is showing.
        const PAL  = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#4a3aa7', '#e34948'];
        const INK  = { primary: '#0b0b0b', secondary: '#52514e', muted: '#898781', grid: '#e1e0d9', axis: '#c3c2b7', surface: '#ffffff' };
        const FONT = 'system-ui, -apple-system, "Segoe UI", sans-serif';

        const METRIC_LABEL = { total: 'Revenue (PKR)', qty: 'Quantity', kg: 'Weight (KG)' };
        const METRIC_WORD  = { total: 'Revenue', qty: 'Quantity', kg: 'Weight' };

        const YM     = Object.keys(MM.months);
        const LABELS = Object.values(MM.months);
        const groups = MM.groups.filter(g => g.rows.length);

        const table   = document.getElementById('matrix');
        const buttons = document.querySelectorAll('#metricToggle [data-metric]');

        /* ───────── formatting ───────── */
        const compact = new Intl.NumberFormat('en', { notation: 'compact', maximumFractionDigits: 1 });
        const fmt = (v, metric) => metric === 'total'
            ? Math.round(v).toLocaleString('en')
            : Number(v).toLocaleString('en', { maximumFractionDigits: 2 });

        /* ───────── series helpers ───────── */
        // Past `max` series the tail folds into "Other" rather than a 9th hue.
        function fold(items, max, valueOf) {
            if (items.length <= max) return items.map(i => ({ label: i.label, data: valueOf(i) }));
            const head = items.slice(0, max - 1).map(i => ({ label: i.label, data: valueOf(i) }));
            const tail = items.slice(max - 1).map(valueOf);
            const other = tail[0].map((_, k) => tail.reduce((s, d) => s + d[k], 0));
            return [...head, { label: 'Other', data: other }];
        }

        // Mann + bags + packets don't stack, so cross-group charts show revenue
        // when Quantity is selected; the trend chart (one group) uses it as-is.
        const crossMetric = m => (m === 'qty' ? 'total' : m);

        const groupSeries = metric => fold(
            groups.map(g => ({ label: g.title, totals: g.totals })), 8,
            g => YM.map(m => g.totals[m][metric]),
        );

        /* ───────── chart chrome ───────── */
        if (window.Chart) {
            Chart.defaults.font.family = FONT;
            Chart.defaults.font.size   = 11;
            Chart.defaults.color       = INK.muted;
            Chart.defaults.plugins.tooltip.backgroundColor = INK.surface;
            Chart.defaults.plugins.tooltip.titleColor      = INK.primary;
            Chart.defaults.plugins.tooltip.bodyColor       = INK.secondary;
            Chart.defaults.plugins.tooltip.footerColor     = INK.primary;
            Chart.defaults.plugins.tooltip.borderColor     = INK.grid;
            Chart.defaults.plugins.tooltip.borderWidth     = 1;
            Chart.defaults.plugins.tooltip.padding         = 10;
            Chart.defaults.plugins.tooltip.boxPadding      = 4;
            Chart.defaults.plugins.legend.labels.boxWidth  = 10;
            Chart.defaults.plugins.legend.labels.boxHeight = 10;
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.color     = INK.secondary;
        }

        const axisY = metric => ({
            beginAtZero: true,
            grid:   { color: INK.grid, drawTicks: false },
            border: { display: false },
            ticks:  { padding: 8, callback: v => compact.format(v), font: { family: FONT }, color: INK.muted },
            title:  { display: true, text: METRIC_LABEL[metric], color: INK.muted, font: { size: 10 } },
        });
        const axisX = () => ({
            grid:   { display: false },
            border: { color: INK.axis },
            ticks:  { color: INK.muted },
        });

        // Value at the bar tip for the horizontal ranking chart — every value is
        // also in the matrix table, so this is a convenience, not the only route.
        const tipLabels = {
            id: 'tipLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                const meta = chart.getDatasetMeta(0);
                const metric = chart.$metric;
                ctx.save();
                ctx.font = `600 11px ${FONT}`;
                ctx.fillStyle = INK.secondary;
                ctx.textBaseline = 'middle';
                meta.data.forEach((bar, i) => {
                    const v = chart.data.datasets[0].data[i];
                    if (v > 0) ctx.fillText(fmt(v, metric), bar.x + 6, bar.y);
                });
                ctx.restore();
            },
        };

        /* ───────── the four charts ───────── */
        const charts = {};

        function stackedData(metric) {
            const series = groupSeries(metric);
            return {
                labels: LABELS,
                datasets: series.map((s, i) => ({
                    label: s.label, data: s.data,
                    backgroundColor: PAL[i],
                    // 2px surface border = the gap between stacked segments
                    borderColor: INK.surface, borderWidth: 2, borderSkipped: 'start',
                    borderRadius: i === series.length - 1 ? 4 : 0,
                    maxBarThickness: 24,
                })),
            };
        }

        function donutData(metric) {
            const series = fold(
                groups.map(g => ({ label: g.title, v: g.grand[metric] })), 6, g => [g.v],
            );
            return {
                labels: series.map(s => s.label),
                datasets: [{
                    data: series.map(s => s.data[0]),
                    backgroundColor: series.map((_, i) => PAL[i]),
                    borderColor: INK.surface, borderWidth: 2, hoverOffset: 4,
                }],
            };
        }

        function renderDonutLegend(metric) {
            const d = donutData(metric);
            const sum = d.datasets[0].data.reduce((a, b) => a + b, 0) || 1;
            document.getElementById('donutLegend').innerHTML = d.labels.map((l, i) => `
                <li class="d-flex align-items-center justify-content-between py-1">
                    <span><span class="mm-swatch" style="background:${PAL[i]}"></span>${l}</span>
                    <span class="text-right">
                        <span class="font-weight-bold">${(d.datasets[0].data[i] / sum * 100).toFixed(1)}%</span>
                        <span class="text-muted ml-2">${fmt(d.datasets[0].data[i], metric)}</span>
                    </span>
                </li>`).join('');
        }

        // Open on the group with the most sizes — that is where a trend comparison earns its place.
        let lineGroup = groups.reduce((best, g, i) => g.rows.length > groups[best].rows.length ? i : best, 0);
        function lineData(metric) {
            const g = groups[lineGroup];
            const series = fold(g.rows, 8, r => YM.map(m => r.cells[m][metric]));
            return {
                labels: LABELS,
                datasets: series.map((s, i) => ({
                    label: s.label, data: s.data,
                    borderColor: PAL[i], backgroundColor: PAL[i],
                    borderWidth: 2, tension: 0.25,
                    pointRadius: 4, pointHoverRadius: 6,
                    pointBorderColor: INK.surface, pointBorderWidth: 2,  // surface ring
                    pointHoverBorderColor: INK.surface, pointHoverBorderWidth: 2,
                })),
            };
        }

        // Spice rows are "200 G" under each type, so the same label can appear in
        // several groups — prefix with the group name only when that happens.
        const labelCounts = {};
        groups.forEach(g => g.rows.forEach(r => { labelCounts[r.label] = (labelCounts[r.label] || 0) + 1; }));
        const ambiguous = Object.values(labelCounts).some(n => n > 1);

        function rankData(metric) {
            const rows = groups.flatMap(g => g.rows.map(r => ({
                label: ambiguous ? `${g.title} ${r.label}` : r.label,
                v: r.total[metric],
            })))
            .filter(r => r.v > 0)
            .sort((a, b) => b.v - a.v)
            .slice(0, 15);
            document.getElementById('rankBox').style.height = (rows.length * 28 + 40) + 'px';
            return {
                labels: rows.map(r => r.label),
                datasets: [{
                    data: rows.map(r => r.v),
                    backgroundColor: PAL[0],           // one series, one colour
                    borderRadius: 4, borderSkipped: 'start',
                    maxBarThickness: 18, categoryPercentage: 0.7,
                }],
            };
        }

        function build(metric) {
            const cm = crossMetric(metric);

            charts.stacked = new Chart(document.getElementById('chartStacked'), {
                type: 'bar',
                data: stackedData(cm),
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: { x: { ...axisX(), stacked: true }, y: { ...axisY(cm), stacked: true } },
                    plugins: {
                        legend: { position: 'top', align: 'end' },
                        tooltip: {
                            callbacks: {
                                label: c => ` ${c.dataset.label}: ${fmt(c.parsed.y, charts.stacked.$metric)}`,
                                footer: items => 'Total: ' + fmt(items.reduce((s, i) => s + i.parsed.y, 0), charts.stacked.$metric),
                            },
                        },
                    },
                },
            });
            charts.stacked.$metric = cm;

            charts.donut = new Chart(document.getElementById('chartDonut'), {
                type: 'doughnut',
                data: donutData(cm),
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '62%',
                    plugins: {
                        legend: { display: false },   // HTML legend below carries value + %
                        tooltip: {
                            callbacks: {
                                label: c => {
                                    const sum = c.dataset.data.reduce((a, b) => a + b, 0) || 1;
                                    return ` ${fmt(c.parsed, charts.donut.$metric)} (${(c.parsed / sum * 100).toFixed(1)}%)`;
                                },
                            },
                        },
                    },
                },
            });
            charts.donut.$metric = cm;
            renderDonutLegend(cm);

            charts.line = new Chart(document.getElementById('chartLine'), {
                type: 'line',
                data: lineData(metric),
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: { x: axisX(), y: axisY(metric) },
                    plugins: {
                        // a single series needs no legend — the group button names it
                        legend: { position: 'top', align: 'end', display: groups[lineGroup].rows.length > 1 },
                        tooltip: {
                            callbacks: { label: c => ` ${c.dataset.label}: ${fmt(c.parsed.y, charts.line.$metric)}` },
                        },
                    },
                },
            });
            charts.line.$metric = metric;

            charts.rank = new Chart(document.getElementById('chartRank'), {
                type: 'bar',
                data: rankData(cm),
                plugins: [tipLabels],
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    layout: { padding: { right: 64 } },
                    scales: {
                        x: { ...axisY(cm), title: { display: false }, ticks: { display: false }, grid: { display: false } },
                        y: { grid: { display: false }, border: { display: false }, ticks: { color: INK.secondary, autoSkip: false } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => ' ' + fmt(c.parsed.x, charts.rank.$metric) } },
                    },
                },
            });
            charts.rank.$metric = cm;
        }

        function refresh(metric) {
            const cm = crossMetric(metric);

            charts.stacked.data = stackedData(cm);
            charts.stacked.options.scales.y.title.text = METRIC_LABEL[cm];
            charts.stacked.$metric = cm;
            charts.stacked.update();

            charts.donut.data = donutData(cm);
            charts.donut.$metric = cm;
            charts.donut.update();
            renderDonutLegend(cm);

            charts.line.data = lineData(metric);
            charts.line.options.scales.y.title.text = metric === 'qty'
                ? `Quantity (${groups[lineGroup].unit})` : METRIC_LABEL[metric];
            charts.line.$metric = metric;
            charts.line.options.plugins.legend.display = charts.line.data.datasets.length > 1;
            charts.line.update();

            charts.rank.data = rankData(cm);
            charts.rank.$metric = cm;
            charts.rank.resize();
            charts.rank.update();

            document.querySelectorAll('[data-chart-title]').forEach(el => {
                const isLine = el.dataset.chartTitle === 'line';
                const word = METRIC_WORD[isLine ? metric : cm];
                el.textContent = el.dataset.chartTitle === 'rank' ? word.toLowerCase() : word;
            });
            document.querySelectorAll('[data-unit-note]').forEach(el => { el.hidden = metric !== 'qty'; });
        }

        /* ───────── line-chart group selector ───────── */
        const lineToggle = document.getElementById('lineGroupToggle');
        function renderLineToggle() {
            if (!lineToggle) return;
            lineToggle.innerHTML = groups.map((g, i) => `
                <button type="button" class="btn ${i === lineGroup ? 'btn-primary' : 'btn-outline-primary'}" data-group="${i}">${g.title}</button>
            `).join('');
            lineToggle.querySelectorAll('[data-group]').forEach(b => b.addEventListener('click', () => {
                lineGroup = Number(b.dataset.group);
                renderLineToggle();
                refresh(current);
            }));
        }

        /* ───────── metric toggle: table + charts together ───────── */
        // Revenue / Quantity / KG live in every table cell; the toggle only swaps
        // which one the table shows, and re-feeds the charts the same metric.
        // Remembered per browser so the print view matches.
        let current = 'total';
        function setMetric(metric) {
            current = metric;
            if (table) {
                table.classList.remove('show-total', 'show-qty', 'show-kg');
                table.classList.add('show-' + metric);
            }
            buttons.forEach(b => {
                const on = b.dataset.metric === metric;
                b.classList.toggle('btn-primary', on);
                b.classList.toggle('btn-outline-primary', !on);
            });
            if (charts.stacked) refresh(metric);
            try { localStorage.setItem('mm-metric', metric); } catch (e) {}
        }

        buttons.forEach(b => b.addEventListener('click', () => setMetric(b.dataset.metric)));

        let saved = null;
        try { saved = localStorage.getItem('mm-metric'); } catch (e) {}
        if (saved && ['total', 'qty', 'kg'].includes(saved)) current = saved;

        if (window.Chart && document.getElementById('chartStacked') && groups.length) {
            renderLineToggle();
            build(current);
        }
        setMetric(current);
    })();
</script>
