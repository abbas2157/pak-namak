@php
    $fmtDate = fn ($d) => $d ? $d->format('d M Y') : '—';
    $fmtNum  = fn ($n) => $n === null ? '' : number_format($n, 0);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recovery Sheet — {{ $today->format('d M Y') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Calibri, "Segoe UI", Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #f0f2f5; }

        .toolbar { position: sticky; top: 0; z-index: 10; background: #fff; border-bottom: 1px solid #d8dbe0; padding: 10px 18px; display: flex; gap: 10px; align-items: center; }
        .toolbar .spacer { flex: 1; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 6px; border: 1px solid transparent; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #1a5c35; color: #fff; }
        .btn-outline { background: #fff; color: #333; border-color: #c9ccd1; }

        .page { background: #fff; width: 297mm; min-height: 210mm; margin: 14px auto; padding: 10mm 12mm; box-shadow: 0 2px 10px rgba(0,0,0,.08); }

        .hdr { display: flex; align-items: center; gap: 14px; border-bottom: 2px solid #1f3864; padding-bottom: 8px; margin-bottom: 8px; }
        .hdr img { height: 46px; }
        .hdr h1 { margin: 0; font-size: 18px; color: #1f3864; }
        .hdr .sub { color: #595959; font-size: 10px; margin-top: 2px; }
        .hdr .blanks { margin-left: auto; text-align: right; font-size: 11px; line-height: 1.9; }
        .hdr .blanks span { display: inline-block; border-bottom: 1px solid #333; min-width: 160px; }

        .summary { display: flex; gap: 22px; margin: 8px 0 10px; font-size: 11px; }
        .summary b { font-size: 14px; color: #1f3864; margin-left: 4px; }

        h2 { font-size: 13px; color: #1f3864; margin: 14px 0 6px; }

        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        th, td { border: 1px solid #333; padding: 4px 6px; vertical-align: middle; }
        th { background: #d9e1f2; font-weight: 700; text-align: center; font-size: 10px; line-height: 1.25; }
        td.num { text-align: right; font-variant-numeric: tabular-nums; }
        td.center { text-align: center; }
        td.pending { font-weight: 700; }
        td.recover { background: #fff2cc; min-width: 22mm; }
        tr { page-break-inside: avoid; }
        tr.total td { font-weight: 700; background: #f5f5f5; }
        tr.blank td { height: 24px; }
        tbody tr:nth-child(even) td:not(.recover) { background: #fafbfc; }

        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            body { background: #fff; }
            .toolbar { display: none; }
            .page { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            th, td.recover, tr.total td, tbody tr:nth-child(even) td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <a href="{{ route('dashboard') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Dashboard</a>
    <div class="spacer"></div>
    <a href="{{ route('admin.recovery_sheet.excel') }}" class="btn btn-outline"><i class="fas fa-file-excel"></i> Download Excel</a>
    <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print / Save as PDF</button>
</div>

<div class="page">

    <div class="hdr">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        <div>
            <h1>{{ config('admin.shop_name') }}</h1>
            <div class="sub">Recovery Sheet / ریکوری شیٹ &nbsp;·&nbsp; Generated {{ $today->format('l, d F Y') }}</div>
        </div>
        <div class="blanks">
            Collector / وصول کنندہ: <span></span><br>
            Date of round / تاریخ: <span></span>
        </div>
    </div>

    <div class="summary">
        <div>Shops with balance <b>{{ $recovery->count() }}</b></div>
        <div>Total outstanding <b>PKR {{ $fmtNum($totalPending) }}</b></div>
        <div>Shops fully clear <b>{{ $clearCount }}</b></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>City<br>شہر</th>
                <th>Area<br>علاقہ</th>
                <th>Shop<br>دکان</th>
                <th>Owner<br>مالک</th>
                <th>Phone<br>فون</th>
                <th>Last Order<br>آخری آرڈر</th>
                <th>Last Order<br>Amt (PKR)</th>
                <th>Days<br>Since</th>
                <th>Pending (PKR)<br>بقایا</th>
                <th>Recovered (PKR)<br>وصولی</th>
                <th>Remarks<br>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recovery as $i => $s)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $s->city ?: '—' }}</td>
                    <td>{{ $s->area ?: '—' }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->owner }}</td>
                    <td>{{ $s->phone }}</td>
                    <td class="center">{{ $fmtDate($s->last_date) }}</td>
                    <td class="num">{{ $fmtNum($s->last_amount) }}</td>
                    <td class="num">{{ $s->days_since }}</td>
                    <td class="num pending">{{ $fmtNum($s->pending) }}</td>
                    <td class="recover"></td>
                    <td></td>
                </tr>
            @empty
                <tr><td colspan="12" class="center" style="padding:14px;">No shop currently has a pending balance.</td></tr>
            @endforelse
            @for($k = 0; $k < 3; $k++)
                <tr class="blank"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="recover"></td><td></td></tr>
            @endfor
            <tr class="total">
                <td></td><td colspan="8">TOTAL / کل</td>
                <td class="num">{{ $fmtNum($totalPending) }}</td>
                <td class="recover"></td>
                <td></td>
            </tr>
        </tbody>
    </table>

</div>

</body>
</html>
