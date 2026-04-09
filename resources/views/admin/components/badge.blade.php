@php
    $classes = match ($tone) {
        'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'danger' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
        'info' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
        default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium '.$classes]) }}>
    {{ $slot }}
</span>
