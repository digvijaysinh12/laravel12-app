@if ($message)
    @php
        $classes = match ($tone) {
            'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'danger' => 'border-rose-200 bg-rose-50 text-rose-800',
            'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
            default => 'border-slate-200 bg-white text-slate-700',
        };
    @endphp

    <div class="rounded-xl border px-4 py-3 text-sm shadow-sm {{ $classes }}">
        {{ $message }}
    </div>
@endif
