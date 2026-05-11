@props([
    'count' => 0,
    'buttonClass' => '',
    'badgeClass' => '',
    'label' => 'Notifications',
    'icon' => '🔔',
])

<button
    id="notificationBtn"
    type="button"
    class="{{ $buttonClass }}"
    aria-label="{{ $label }}"
>
    {!! $icon !!}

    <x-badges.unread-count
        :count="$count"
        id="notificationCount"
        :class="$badgeClass"
    />
</button>
