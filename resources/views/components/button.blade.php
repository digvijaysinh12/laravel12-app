@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
    'loadingText' => 'Working...'
])

@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
    ];
    $class = $variants[$variant] ?? $variants['primary'];
    $baseClasses = 'btn ' . $class;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" data-loading-text="{{ $loadingText }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </button>
@endif
