@php
    $base = 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2';
    $classes = match ($variant) {
        'secondary' => $base.' border border-slate-300 bg-white text-slate-700 hover:bg-slate-50',
        'danger' => $base.' bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-400',
        default => $base.' bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-900',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
