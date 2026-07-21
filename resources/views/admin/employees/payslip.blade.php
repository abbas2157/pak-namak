<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payslip — {{ $employee->name }} — {{ $salary->month->format('M Y') }}</title>
    <link rel="stylesheet" href="{{ asset('css/pn-payslip.css') }}">
</head>
<body>

<div class="no-print-bar">
    <button class="print-btn" onclick="window.print()">Print / پرنٹ</button>
</div>

<div class="payslip-page">
    <div class="payslip-watermark">{{ config('admin.shop_name') }}</div>

    <div class="payslip-content">

        {{-- Header --}}
        <div class="payslip-header">
            <div class="payslip-brand">
                <img class="payslip-logo" src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                <div>
                    <p class="payslip-company-name">{{ config('admin.shop_name') }}</p>
                    <div class="payslip-company-sub">Salt Trading &amp; Production</div>
                </div>
            </div>
            <div class="payslip-company-contact">
                <div>{{ config('admin.pak_namak.phone') }}</div>
                <div>{{ config('admin.pak_namak.email') }}</div>
                <div>{{ config('admin.pak_namak.website') }}</div>
            </div>
        </div>

        <div class="payslip-title-block">
            <p class="payslip-title">Salary Slip</p>
            <div class="payslip-subtitle">Payslip for the Month of {{ $salary->month->format('F, Y') }}</div>
        </div>

        {{-- Employee Details --}}
        <div class="payslip-section-label">Employee Details</div>
        <div class="emp-grid">
            <div class="cell"><span class="lbl">Employee Name</span><span class="val">{{ $employee->name }}</span></div>
            <div class="cell"><span class="lbl">Phone</span><span class="val">{{ $employee->phone ?? '—' }}</span></div>

            <div class="cell"><span class="lbl">Designation</span><span class="val">{{ $employee->designation ?? '—' }}</span></div>
            <div class="cell"><span class="lbl">CNIC</span><span class="val">{{ $employee->cnic ?? '—' }}</span></div>

            <div class="cell"><span class="lbl">Pay Period</span><span class="val">{{ $salary->month->format('F Y') }}</span></div>
            <div class="cell"><span class="lbl">Payment Date</span><span class="val">{{ $salary->paid_at ? $salary->paid_at->format('d M Y') : '—' }}</span></div>

            <div class="cell"><span class="lbl">Days Present (approx.)</span><span class="val">{{ $daysPresent }} / 30</span></div>
            <div class="cell"><span class="lbl">Unpaid Absences</span><span class="val">{{ $salary->absent_days }} day{{ $salary->absent_days == 1 ? '' : 's' }}</span></div>

            <div class="cell"><span class="lbl">Paid Leave Taken</span><span class="val">{{ $paidLeaveDays }} day{{ $paidLeaveDays == 1 ? '' : 's' }}</span></div>
            <div class="cell"><span class="lbl">Employee Status</span><span class="val text-capitalize">{{ str_replace('_',' ', $employee->status) }}</span></div>
        </div>

        {{-- Earnings / Deductions --}}
        <div class="pay-tables">
            <div>
                <div class="payslip-section-label" style="background:#1a5c35;">Earnings</div>
                <table class="pay-table">
                    <thead><tr><th>Description</th><th class="amt">Amount (PKR)</th></tr></thead>
                    <tbody>
                        <tr><td>Basic Salary</td><td class="amt">{{ number_format($salary->gross_amount, 2) }}</td></tr>
                    </tbody>
                    <tfoot>
                        <tr><td>Gross Salary</td><td class="amt">{{ number_format($salary->gross_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <div>
                <div class="payslip-section-label" style="background:#b8391f;">Deductions</div>
                <table class="pay-table">
                    <thead><tr><th>Description</th><th class="amt">Amount (PKR)</th></tr></thead>
                    <tbody>
                        <tr><td>Advance Deduction</td><td class="amt">{{ number_format($salary->advance_deducted, 2) }}</td></tr>
                        <tr><td>Absence Deduction ({{ $salary->absent_days }} day{{ $salary->absent_days == 1 ? '' : 's' }})</td><td class="amt">{{ number_format($salary->absence_deducted, 2) }}</td></tr>
                    </tbody>
                    <tfoot>
                        <tr><td>Total Deductions</td><td class="amt">{{ number_format($totalDeductions, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Net Summary --}}
        <div class="payslip-net">
            <div>
                <div class="net-label">Net Salary Payable</div>
                <div class="payslip-net-words">{{ $netInWords }}</div>
                @if($salary->note)
                    <div class="payslip-net-words">Remarks: {{ $salary->note }}</div>
                @endif
            </div>
            <div class="net-value">PKR {{ number_format($salary->amount, 2) }}</div>
        </div>

        {{-- Footer --}}
        <div class="payslip-footer">
            <div class="payslip-computer-note">
                This is a computer-generated salary slip and does not require a signature.
            </div>

            <div class="sign-row">
                <div class="sign-box">
                    <div class="sign-line">Employee Signature</div>
                </div>
                <div class="stamp-box">Company Stamp</div>
                <div class="sign-box">
                    <div class="sign-line">Authorized Signatory</div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    window.onload = function () {
        setTimeout(function () { window.print(); }, 300);
    };
</script>

</body>
</html>
