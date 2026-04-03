<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>

<body>

    <h2>Invoice</h2>

    <p><strong>Invoice No:</strong> {{ $invoice['invoice_no'] }}</p>
    <p><strong>Date:</strong> {{ $invoice['date']->format('d-m-Y') }}</p>
    <p><strong>User:</strong> {{ $invoice['user']->name ?? 'Guest' }}</p>

    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>

        @foreach($invoice['items'] as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td>{{ $item['quantity'] }}</td>
            <td>Rs. {{ $item['price'] }}</td>
            <td>Rs. {{ $item['total'] }}</td>
        </tr>
        @endforeach
    </table>

    <h3 class="text-right">
        Total: Rs. {{ $invoice['grand_total'] }}
    </h3>

</body>
</html>
