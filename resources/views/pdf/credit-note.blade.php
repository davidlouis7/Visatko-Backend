<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 12px; }
        h1 { font-size: 26px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #f1f5f9; text-align: left; }
        th, td { border: 1px solid #d8dee8; padding: 8px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Credit Note</h1>
    <p><strong>{{ $creditNote->credit_note_number }}</strong></p>
    <p>Invoice: {{ $creditNote->invoice->invoice_number }} | Customer: {{ $creditNote->customer->full_name ?? $creditNote->invoice->customer->full_name }}</p>
    <p>Issued: {{ $creditNote->issued_at?->format('Y-m-d') ?? 'Draft' }}</p>
    @if($creditNote->reason)<p>Reason: {{ $creditNote->reason }}</p>@endif
    <table>
        <thead><tr><th>Description</th><th>Qty</th><th>Unit</th><th>VAT</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($creditNote->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                    <td>{{ $creditNote->invoice->currency }} {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>{{ $creditNote->invoice->currency }} {{ number_format((float) $item->vat_amount, 2) }}</td>
                    <td>{{ $creditNote->invoice->currency }} {{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="right">Subtotal: {{ $creditNote->invoice->currency }} {{ number_format((float) $creditNote->subtotal, 2) }}</p>
    <p class="right">VAT: {{ $creditNote->invoice->currency }} {{ number_format((float) $creditNote->vat_amount, 2) }}</p>
    <p class="right"><strong>Total: {{ $creditNote->invoice->currency }} {{ number_format((float) $creditNote->total, 2) }}</strong></p>
</body>
</html>
