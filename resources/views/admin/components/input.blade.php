@php
    $value = old($name, $value);
@endphp

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        @if($step) step="{{ $step }}" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200']) }}
    >
    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @else
        <p class="text-xs text-transparent">.</p>
    @enderror
</div>
