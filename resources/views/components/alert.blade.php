@props(['type' => 'info'])
@php
    $variants = [
        'info' => 'alert-info',
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
    ];
    $class = $variants[$type] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => 'alert '.$class]) }} role="alert">
    {{ $slot }}
</div>
