@extends('layouts.app')

@section('content')

<div class="py-5 max-w-900 mx-auto">

    <div class="card shadow-sm p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Invoice</h4>
                <small class="text-muted">
                    Invoice No: {{ $invoice['invoice_no'] }}
                </small><br>
                <small class="text-muted">
                    Date: {{ $invoice['date']->format('d-m-Y') }}
                </small>
            </div>

            <div class="text-end">
                <h5 class="mb-1">{{ $app_name ?? 'My Store' }}</h5>
                <small class="text-muted">Surat, India</small>
            </div>
        </div>

        {{-- User --}}
        <div class="mb-4">
            <strong>Billed To:</strong><br>
            {{ $invoice['user']->name ?? 'Guest' }}<br>
            <small class="text-muted">
                {{ $invoice['user']->email ?? '' }}
            </small>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($invoice['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td class="text-center">{{ $item['quantity'] }}</td>
                            <td class="text-end">@currency($item['price'])</td>
                            <td class="text-end">@currency($item['total'])</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- Total --}}
        <div class="d-flex justify-content-end mt-3">
            <div class="w-250">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">Total:</span>
                    <span class="fw-bold text-dark">
                        @currency($invoice['grand_total'])
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-4">
            <small class="text-muted">
                Thank you for your purchase 🙏
            </small>
        </div>

    </div>
    <a href="{{ route('invoice.pdf') }}" class="btn btn-dark mt-3">
    Download PDF
</a>

</div>

@endsection
