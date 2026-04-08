@props(['type' => 'info', 'message' => null])

@php
    $base = 'rounded-xl border px-4 py-3 text-sm';
    $classes = match ($type) {
        'success' => $base.' border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => $base.' border-rose-200 bg-rose-50 text-rose-800',
        default => $base.' border-sky-200 bg-sky-50 text-sky-800',
    };
@endphp

@if (! empty($message))
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $message }}
    </div>
@endif
