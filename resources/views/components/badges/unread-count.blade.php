@props([
    'count' => 0,
    'id' => 'notificationCount',
    'class' => '',
])

<span
    id="{{ $id }}"
    class="{{ $count > 0 ? 'flex' : 'hidden' }} {{ $class }}"
>
    {{ $count }}
</span>
