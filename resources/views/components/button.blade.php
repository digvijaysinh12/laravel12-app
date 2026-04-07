@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
])

@php
$variants = [
    'primary' => 'bg-gray-900 text-white hover:bg-black',
    'secondary' => 'bg-gray-100 text-gray-800 hover:bg-gray-200',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
];
$class = $variants[$variant] ?? $variants['primary'];
@endphp

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' => "inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition $class"
        ]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge([
            'class' => "inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition $class"
        ]) }}>
        {{ $slot }}
    </button>
@endif
