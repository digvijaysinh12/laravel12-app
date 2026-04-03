@props(['label', 'value'])

<div {{ $attributes->merge(['class' => 'stat']) }}>
    <div class="stat__label">{{ $label }}</div>
    <div class="stat__value">{{ $value }}</div>
</div>
