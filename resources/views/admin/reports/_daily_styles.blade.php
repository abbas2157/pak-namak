<style>
    .daily-report-table th { white-space: nowrap; vertical-align: middle; }
    .daily-report-table thead tr:first-child th { font-size: .8rem; }
    .daily-report-table thead tr:last-child th { font-weight: 500; }
    .daily-report-table .grp-salt  { background: rgba(42, 120, 214, .08); }
    .daily-report-table .grp-spice { background: rgba(235, 104, 52, .08); }
    .daily-report-table .grp-total { background: rgba(137, 135, 129, .10); }
    .daily-report-table tbody tr.quiet-day td { color: #adb5bd; }
    .daily-report-table tbody tr.sunday td:first-child { color: #c9302c; }
    .daily-report-table tbody tr.day-high td:first-child::after { content: ' ▲ high'; color: #1baf7a; font-size: .7rem; font-weight: 600; }
    .daily-report-table tbody tr.day-low td:first-child::after  { content: ' ▼ low';  color: #e34948; font-size: .7rem; font-weight: 600; }

    .delta { display: inline-block; margin-top: 6px; padding: 2px 8px; border-radius: 10px; font-size: .72rem; font-weight: 600; }
    .delta-up   { background: rgba(27, 175, 122, .12); color: #0f7a53; }
    .delta-down { background: rgba(227, 73, 72, .12);  color: #b52b2a; }
    .delta-flat, .delta-na { background: rgba(137, 135, 129, .12); color: #52514e; }

    .hl-tag { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .02em; }
    .hl-high { color: #0f7a53; }
    .hl-low  { color: #b52b2a; }
    .hl-avg  { color: #52514e; }
    .hl-val  { font-size: 1.25rem; font-weight: 700; line-height: 1.3; }
    .hl-sub  { font-size: .75rem; color: #898781; }

    .chart-box { position: relative; height: 240px; margin-top: 8px; }
    .chart-box-wide { height: 260px; }

    @media print {
        .no-print, .main-sidebar, .main-header, .content-header .breadcrumb { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .daily-report-table { font-size: 10px; }
        .card { box-shadow: none !important; break-inside: avoid; }
        .chart-box { height: 200px; }
    }
</style>
