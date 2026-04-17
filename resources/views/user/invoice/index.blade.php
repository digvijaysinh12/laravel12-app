@extends('user.layouts.app')

@section('title', 'Invoice')

@section('content')
<div style="max-width:900px; margin:auto; background:#fff; padding:20px; border:1px solid #ddd;">

    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 style="margin:0;">{{ config('app.name') }}</h2>
            <p style="margin:0; font-size:14px; color:gray;">Order Invoice</p>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">Invoice</h3>
            <p style="margin:0;">#{{ $invoice['invoice_no'] }}</p>
            <p style="margin:0;">Date: {{ $invoice['date']->format('d-m-Y') }}</p>
        </div>
    </div>

    <hr>

    <!-- Customer + Summary -->
    <div style="display:flex; justify-content:space-between; margin-top:20px;">
        <div>
            <h4>Billed To</h4>
            <p style="margin:0;">{{ $invoice['user']->name ?? 'Guest' }}</p>
            <p style="margin:0;">{{ $invoice['user']->email ?? '' }}</p>
        </div>

        <div style="text-align:right;">
            <h4>Total Amount</h4>
            <h2 style="margin:0; color:#2874f0;">
                ₹{{ number_format($invoice['grand_total'], 2) }}
            </h2>
        </div>
    </div>

    <hr>

    <!-- Items Table -->
    <table width="100%" border="1" cellspacing="0" cellpadding="10" style="border-collapse:collapse; margin-top:20px;">
        <thead style="background:#f5f5f5;">
            <tr>
                <th>#</th>
                <th align="left">Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice['items'] as $index => $item)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td align="center">{{ $item['quantity'] }}</td>
                    <td align="right">₹{{ number_format($item['price'], 2) }}</td>
                    <td align="right">₹{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer Total -->
    <div style="margin-top:20px; text-align:right;">
        <h3>Grand Total: ₹{{ number_format($invoice['grand_total'], 2) }}</h3>
    </div>

    <hr>

    <!-- Download -->
    <div style="text-align:center; margin-top:20px;">
        <a href="{{ route('user.invoice.pdf') }}" 
           style="padding:10px 15px; background:#2874f0; color:#fff; text-decoration:none;">
            Download Invoice
        </a>

        <p style="color:red; margin-top:10px;">
            (Link valid for 10 minutes)
        </p>
    </div>

</div>
@endsection