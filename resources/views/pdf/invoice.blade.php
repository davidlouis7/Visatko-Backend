<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 12px; }
        .header { display: table; width: 100%; margin-bottom: 28px; }
        .left, .right { display: table-cell; width: 50%; vertical-align: top; }
        .right { text-align: right; }
        h1 { font-size: 28px; margin: 0 0 6px; }
        h2 { font-size: 16px; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #f1f5f9; text-align: left; }
        th, td { border: 1px solid #d8dee8; padding: 8px; }
        .totals { width: 42%; margin-left: auto; }
        .muted { color: #64748b; }
        .section { margin-top: 24px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <h1>{{ $finance->get('company_legal_name') }}</h1>
            @if($finance->get('company_trn'))
                <div>TRN: {{ $finance->get('company_trn') }}</div>
            @endif
        </div>
        <div class="right">
            <h2>Tax Invoice</h2>
            <div><strong>{{ $invoice->invoice_number }}</strong></div>
            <div>Issue date: {{ $invoice->issued_at?->format('Y-m-d') ?? $invoice->created_at->format('Y-m-d') }}</div>
            <div>Due date: {{ $invoice->due_at?->format('Y-m-d') ?? '—' }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Bill To</h2>
        <div>{{ $invoice->customer->full_name }}</div>
        <div>{{ $invoice->customer->email }}</div>
        <div>{{ $invoice->customer->phone }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format((float) $item->discount_amount, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format((float) $item->vat_amount, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        <tr><td>Discount</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->discount_total, 2) }}</td></tr>
        <tr><td>VAT {{ number_format((float) $invoice->vat_rate, 2) }}%</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->vat_amount, 2) }}</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>{{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}</strong></td></tr>
        <tr><td>Amount paid</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->amount_paid, 2) }}</td></tr>
        <tr><td>Amount due</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->amount_due, 2) }}</td></tr>
    </table>

    <div class="section">
        <h2>Bank Transfer Instructions</h2>
        <div>Account: {{ $finance->get('bank_account_name') ?? '—' }}</div>
        <div>Bank: {{ $finance->get('bank_name') ?? '—' }}</div>
        <div>IBAN: {{ $finance->get('iban') ?? '—' }}</div>
        <div>SWIFT: {{ $finance->get('swift_code') ?? '—' }}</div>
        <p>{{ $finance->get('bank_transfer_instructions') }}</p>
    </div>

    @if($invoice->notes || $invoice->terms)
        <div class="section">
            @if($invoice->notes)<p><strong>Notes:</strong> {{ $invoice->notes }}</p>@endif
            @if($invoice->terms)<p><strong>Terms:</strong> {{ $invoice->terms }}</p>@endif
        </div>
    @endif

    <p class="muted">{{ $finance->get('invoice_footer_note') }}</p>
</body>
</html>
