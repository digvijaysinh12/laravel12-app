@extends('layouts.app')

@section('title', 'Realtime Order Status')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Realtime demo</p>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Realtime Order Status</h1>
    <p class="mt-2 text-sm text-slate-600">
        Live order updates will appear here and as notification toasts when the broadcast event fires.
    </p>

    <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
        <div class="mb-3 text-sm font-medium text-slate-700">Live feed</div>
        <div data-order-status-feed class="space-y-2 text-sm text-slate-600">
            <div class="rounded-lg border border-slate-200 bg-white px-4 py-3">
                Waiting for order updates...
            </div>
        </div>
    </div>
</div>
@endsection
