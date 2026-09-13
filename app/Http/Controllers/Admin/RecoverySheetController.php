<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Sale, Shop, SpiceSale};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ZipArchive;

/**
 * The field Recovery Sheet: every shop carrying a pending balance, ordered
 * City → Area so a collector can work one locality at a time.
 *
 * index() renders it as a print-styled page — "PDF" here means the browser's
 * Save-as-PDF, the same convention the sales report and receipts use, which
 * also renders the Urdu labels correctly. excel() writes a native .xlsx with
 * ZipArchive and hand-written SpreadsheetML, so neither adds a dependency.
 */
class RecoverySheetController extends Controller
{
    // Style indexes — must match the order of <xf> entries in stylesXml().
    private const S_DEFAULT = 0;
    private const S_TITLE = 1;
    private const S_HEADER = 2;
    private const S_TEXT = 3;
    private const S_NUM = 4;
    private const S_RECOVER = 5;
    private const S_BOLD = 6;
    private const S_NUM_BOLD = 7;
    private const S_SUBTLE = 9;

    public function index()
    {
        $today = Carbon::today();
        $shops = $this->shopRows($today);
        $recovery = $shops->filter(fn ($s) => $s->pending > 0)->sortBy($this->geoOrder())->values();

        return view('admin.recovery-sheet', [
            'today'        => $today,
            'recovery'     => $recovery,
            'totalPending' => $recovery->sum('pending'),
            'clearCount'   => $shops->count() - $recovery->count(),
        ]);
    }

    public function excel()
    {
        $today = Carbon::today();
        $shops = $this->shopRows($today);
        $recovery = $shops->filter(fn ($s) => $s->pending > 0)->sortBy($this->geoOrder())->values();

        $zipPath = tempnam(sys_get_temp_dir(), 'recovery') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        [$sheet, $headerRow] = $this->recoverySheetXml($recovery, $shops, $today);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($headerRow));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->close();

        $filename = 'PAK NAMAK Recovery Sheet ' . $today->format('Y-m-d') . '.xlsx';

        return response()->download(
            $zipPath,
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    // ───────────────────────── data ─────────────────────────

    private function shopRows(Carbon $today): Collection
    {
        return Shop::with('cityRecord', 'area')
            ->withSum('sales', 'pending_amount')
            ->withSum('spiceSales', 'pending_amount')
            ->get()
            ->map(function (Shop $s) use ($today) {
                $lastSalt  = Sale::where('shop_id', $s->id)->orderByDesc('sale_date')->orderByDesc('id')->first(['sale_date', 'total_amount']);
                $lastSpice = SpiceSale::where('shop_id', $s->id)->orderByDesc('sale_date')->orderByDesc('id')->first(['sale_date', 'total_amount']);
                $last = collect([$lastSalt, $lastSpice])->filter()->sortByDesc(fn ($x) => (string) $x->sale_date)->first();
                $lastDate = $last?->sale_date ? Carbon::parse($last->sale_date) : null;

                return (object) [
                    'name'        => $s->name,
                    'owner'       => $s->owner_name,
                    'phone'       => $s->phone_number,
                    'city'        => $s->cityRecord?->name ?? $s->city ?? '',
                    'area'        => $s->area?->name ?? '',
                    'last_date'   => $lastDate,
                    'last_amount' => $last ? (float) $last->total_amount : null,
                    'days_since'  => $lastDate ? $lastDate->diffInDays($today) : null,
                    'pending'     => (float) $s->sales_sum_pending_amount + (float) $s->spice_sales_sum_pending_amount,
                ];
            });
    }

    /**
     * City → Area → largest balance → name. Shops missing a city or area sort
     * last rather than floating to the top under an empty string.
     */
    private function geoOrder(): \Closure
    {
        return fn ($s) => [
            $s->city === '' ? "\u{FFFF}" : mb_strtolower($s->city),
            $s->area === '' ? "\u{FFFF}" : mb_strtolower($s->area),
            -$s->pending,
            mb_strtolower($s->name),
        ];
    }


    // ───────────────────────── sheets ─────────────────────────

    /** @return array{0: string, 1: int} [xml, header row number] */
    private function recoverySheetXml(Collection $recovery, Collection $shops, Carbon $today): array
    {
        $totalPending = $recovery->sum('pending');
        $fmtDate = fn (?Carbon $d) => $d ? $d->format('d M Y') : '—';

        $r = 1;
        $rows = [];

        $rows[] = $this->row($r++, [$this->cell(1, 1, 'PAK NAMAK & MASALA JAAT — Recovery Sheet / ریکوری شیٹ', self::S_TITLE)], 24);
        $rows[] = $this->row($r++, [$this->cell(1, 2, 'Generated ' . $today->format('l, d F Y') . '     |     Collector name: ______________________     |     Date of round: ____ / ____ / ______', self::S_SUBTLE)]);
        $r++;
        $rows[] = $this->row($r++, [
            $this->cell(1, 4, 'Shops with balance', self::S_BOLD), $this->cell(2, 4, $recovery->count(), self::S_NUM),
            $this->cell(4, 4, 'Total outstanding (PKR)', self::S_BOLD), $this->cell(5, 4, $totalPending, self::S_NUM_BOLD),
            $this->cell(7, 4, 'Shops fully clear', self::S_BOLD), $this->cell(8, 4, $shops->count() - $recovery->count(), self::S_NUM),
        ]);
        $r++;

        $headers = ['#', 'City / شہر', 'Area / علاقہ', 'Shop / دکان', 'Owner / مالک', 'Phone / فون', 'Last Order / آخری آرڈر', 'Last Order Amt (PKR)', 'Days Since Order', 'Pending (PKR) / بقایا', 'Recovered (PKR) / وصولی', 'Remarks / ملاحظات'];
        $headerRow = $r;
        $rows[] = $this->row($r++, $this->headerCells($headers, $headerRow), 32);

        $i = 1;
        foreach ($recovery as $s) {
            $rows[] = $this->row($r, [
                $this->cell(1, $r, $i++, self::S_NUM),
                $this->cell(2, $r, $s->city ?: '—', self::S_TEXT),
                $this->cell(3, $r, $s->area ?: '—', self::S_TEXT),
                $this->cell(4, $r, $s->name, self::S_TEXT),
                $this->cell(5, $r, $s->owner ?: '', self::S_TEXT),
                $this->cell(6, $r, $s->phone ?: '', self::S_TEXT),
                $this->cell(7, $r, $fmtDate($s->last_date), self::S_TEXT),
                $this->cell(8, $r, $s->last_amount, self::S_NUM),
                $this->cell(9, $r, $s->days_since, self::S_NUM),
                $this->cell(10, $r, $s->pending, self::S_NUM_BOLD),
                $this->cell(11, $r, null, self::S_RECOVER),
                $this->cell(12, $r, null, self::S_TEXT),
            ], 22);
            $r++;
        }

        for ($k = 0; $k < 3; $k++) {
            $rows[] = $this->row($r, $this->blankCells($r, 12, [8, 9, 10], 11), 22);
            $r++;
        }

        $rows[] = $this->row($r, [
            $this->cell(1, $r, null, self::S_TEXT), $this->cell(2, $r, 'TOTAL / کل', self::S_BOLD), $this->cell(3, $r, null, self::S_TEXT),
            $this->cell(4, $r, null, self::S_TEXT), $this->cell(5, $r, null, self::S_TEXT), $this->cell(6, $r, null, self::S_TEXT),
            $this->cell(7, $r, null, self::S_TEXT), $this->cell(8, $r, null, self::S_NUM), $this->cell(9, $r, null, self::S_NUM),
            $this->cell(10, $r, $totalPending, self::S_NUM_BOLD),
            $this->cell(11, $r, null, self::S_RECOVER),
            $this->cell(12, $r, null, self::S_TEXT),
        ]);

        return [$this->sheetXml($rows, [4, 14, 18, 26, 18, 15, 15, 14, 10, 15, 16, 24]), $headerRow];
    }


    // ───────────────────────── xml helpers ─────────────────────────

    private function esc(string $v): string
    {
        return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function colLetter(int $i): string
    {
        $s = '';
        while ($i > 0) {
            $m = ($i - 1) % 26;
            $s = chr(65 + $m) . $s;
            $i = intdiv($i - 1, 26);
        }

        return $s;
    }

    private function cell(int $col, int $row, $value, int $style = self::S_DEFAULT): string
    {
        $ref = $this->colLetter($col) . $row;
        if ($value === null || $value === '') {
            return "<c r=\"$ref\" s=\"$style\"/>";
        }
        if (is_int($value) || is_float($value)) {
            return "<c r=\"$ref\" s=\"$style\"><v>$value</v></c>";
        }

        return "<c r=\"$ref\" s=\"$style\" t=\"inlineStr\"><is><t xml:space=\"preserve\">" . $this->esc((string) $value) . '</t></is></c>';
    }

    private function row(int $r, array $cells, ?float $height = null): string
    {
        $ht = $height ? " ht=\"$height\" customHeight=\"1\"" : '';

        return "<row r=\"$r\"$ht>" . implode('', $cells) . '</row>';
    }

    private function headerCells(array $headers, int $row): array
    {
        return array_map(fn ($h, $i) => $this->cell($i + 1, $row, $h, self::S_HEADER), $headers, array_keys($headers));
    }

    /** Empty bordered row; $numCols get number style, $recoverCol gets the yellow fill. */
    private function blankCells(int $row, int $count, array $numCols, int $recoverCol): array
    {
        $cells = [];
        for ($c = 1; $c <= $count; $c++) {
            $style = $c === $recoverCol ? self::S_RECOVER : (in_array($c, $numCols, true) ? self::S_NUM : self::S_TEXT);
            $cells[] = $this->cell($c, $row, null, $style);
        }

        return $cells;
    }

    private function sheetXml(array $rows, array $widths, array $merges = []): string
    {
        $cols = '';
        foreach ($widths as $i => $w) {
            $n = $i + 1;
            $cols .= "<col min=\"$n\" max=\"$n\" width=\"$w\" customWidth=\"1\"/>";
        }
        $mergeXml = $merges
            ? '<mergeCells count="' . count($merges) . '">' . implode('', array_map(fn ($m) => "<mergeCell ref=\"$m\"/>", $merges)) . '</mergeCells>'
            : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            . '<sheetViews><sheetView workbookViewId="0" showGridLines="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="16"/>'
            . "<cols>$cols</cols>"
            . '<sheetData>' . implode('', $rows) . '</sheetData>'
            . $mergeXml
            . '<pageMargins left="0.4" right="0.4" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>'
            . '<pageSetup orientation="landscape" paperSize="9" fitToWidth="1" fitToHeight="0"/>'
            . '</worksheet>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0"/></numFmts>
<fonts count="4">
<font><sz val="10"/><name val="Calibri"/></font>
<font><b/><sz val="10"/><name val="Calibri"/></font>
<font><b/><sz val="14"/><color rgb="FF1F3864"/><name val="Calibri"/></font>
<font><i/><sz val="9"/><color rgb="FF595959"/><name val="Calibri"/></font>
</fonts>
<fills count="4">
<fill><patternFill patternType="none"/></fill>
<fill><patternFill patternType="gray125"/></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFD9E1F2"/><bgColor indexed="64"/></patternFill></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFFFF2CC"/><bgColor indexed="64"/></patternFill></fill>
</fills>
<borders count="2">
<border><left/><right/><top/><bottom/><diagonal/></border>
<border><left style="thin"><color auto="1"/></left><right style="thin"><color auto="1"/></right><top style="thin"><color auto="1"/></top><bottom style="thin"><color auto="1"/></bottom><diagonal/></border>
</borders>
<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
<cellXfs count="10">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>
<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="0" fontId="1" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
<xf numFmtId="164" fontId="1" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>
<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>
</cellXfs>
<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    private function workbookXml(int $headerRow): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets>
<sheet name="Recovery" sheetId="1" r:id="rId1"/>
</sheets>
<definedNames>
<definedName name="_xlnm.Print_Titles" localSheetId="0">Recovery!$' . $headerRow . ':$' . $headerRow . '</definedName>
</definedNames>
</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
    }
}
