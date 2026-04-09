@php
    $wrapper = $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm']);
@endphp

<section {{ $wrapper }}>
    @if ($title || $description || $action)
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-4 py-4 sm:px-6">
            <div>
                @if ($title)
                    <h2 class="text-lg font-medium text-slate-900">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
                @endif
            </div>

            @if ($action)
                <div class="shrink-0">{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="px-4 py-4 sm:px-6">
        {{ $slot }}
    </div>
</section>
