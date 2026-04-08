<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice['invoice_no'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 14px; }
        .container { max-width: 960px; margin: 0 auto; padding: 24px; }
        .card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 18px; }
        h1, h2, p { margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; text-align: left; }
        th { background: #f8fafc; font-weight: 700; }
        .right { text-align: right; }
        .muted { color: #64748b; }
        .total { font-size: 20px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <p class="muted">Invoice</p>
            <h1>Invoice #{{ $invoice['invoice_no'] }}</h1>
            <p class="muted">Date: {{ $invoice['date']->format('d-m-Y') }}</p>
        </div>

        <div class="card">
            <p class="muted">Billed to</p>
            <h2>{{ $invoice['user']->name ?? 'Guest' }}</h2>
            <p class="muted">{{ $invoice['user']->email ?? '' }}</p>
        </div>

        <div class="card right">
            <p class="muted">Grand total</p>
            <div class="total">INR {{ number_format($invoice['grand_total'], 2) }}</div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>INR {{ number_format($item['price'], 2) }}</td>
                            <td>INR {{ number_format($item['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
