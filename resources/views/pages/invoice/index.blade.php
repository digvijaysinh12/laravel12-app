@extends('layouts.app')

@section('title', 'Invoice')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Invoice</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Invoice #{{ $invoice['invoice_no'] }}</h1>
                <p class="mt-2 text-sm text-slate-500">Date: {{ $invoice['date']->format('d-m-Y') }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-right">
                <div class="font-semibold text-slate-900">{{ config('app.name') }}</div>
                <div class="text-sm text-slate-500">Order summary</div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-sm text-slate-500">Billed to</div>
            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $invoice['user']->name ?? 'Guest' }}</div>
            <div class="text-sm text-slate-600">{{ $invoice['user']->email ?? '' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-sm text-slate-500">Grand total</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">INR {{ number_format($invoice['grand_total'], 2) }}</div>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('user.invoice.pdf') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                    Download PDF
                </a>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Items</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">#</th>
                        <th class="px-5 py-3 text-left font-medium">Product</th>
                        <th class="px-5 py-3 text-left font-medium">Qty</th>
                        <th class="px-5 py-3 text-left font-medium">Price</th>
                        <th class="px-5 py-3 text-left font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($invoice['items'] as $index => $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-slate-900">{{ $item['name'] }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $item['quantity'] }}</td>
                            <td class="px-5 py-3 text-slate-600">INR {{ number_format($item['price'], 2) }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-900">INR {{ number_format($item['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
