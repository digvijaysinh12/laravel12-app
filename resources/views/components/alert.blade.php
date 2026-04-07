@props(['type' => 'info'])

@php
$styles = [
    'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    'success' => 'bg-green-50 text-green-700 border-green-200',
    'warning' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
];
$class = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg px-4 py-3 text-sm $class"]) }}>
    {{ $slot }}
</div>