@php
    $selected = old($name, $selected);
@endphp

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $label)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @else
        <p class="text-xs text-transparent">.</p>
    @enderror
</div>
