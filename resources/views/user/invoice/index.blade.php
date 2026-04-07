@extends('layouts.app')

@section('title', 'Invoice')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Invoice</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Invoice #{{ $invoice['invoice_no'] }}</h1>
                <p class="mt-2 text-sm text-slate-500">Date: {{ $invoice['date']->format('d-m-Y') }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-right">
                <div class="font-semibold text-slate-900">{{ $app_name ?? config('app.name') }}</div>
                <div class="text-sm text-slate-500">Surat, India</div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-sm text-slate-500">Billed to</div>
            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $invoice['user']->name ?? 'Guest' }}</div>
            <div class="text-sm text-slate-600">{{ $invoice['user']->email ?? '' }}</div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-sm text-slate-500">Grand total</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">Rs. {{ number_format($invoice['grand_total'], 2) }}</div>
            <div class="mt-4">
                <x-button href="{{ route('user.invoice.pdf') }}" class="w-full">Download PDF</x-button>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <x-table :headers="['#', 'Product', 'Qty', 'Price', 'Total']">
            @foreach ($invoice['items'] as $index => $item)
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-4 text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-3 py-4 font-medium text-slate-900">{{ $item['name'] }}</td>
                    <td class="px-3 py-4 text-slate-600">{{ $item['quantity'] }}</td>
                    <td class="px-3 py-4 text-slate-600">Rs. {{ number_format($item['price'], 2) }}</td>
                    <td class="px-3 py-4 font-semibold text-slate-900">Rs. {{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </x-table>
    </section>
</div>
@endsection
