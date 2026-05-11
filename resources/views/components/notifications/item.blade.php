@props([
    'notification',
    'interactive' => true,
    'wrapperClass' => '',
])

@php
    $icon = match ($notification['icon'] ?? 'default') {
        'truck' => '🚚',
        'order' => '📦',
        'warning' => '⚠️',
        default => '🔔',
    };
@endphp

@if($interactive)
    <form method="POST" action="{{ route('notifications.read', $notification['id']) }}">
        @csrf
        <button
            type="submit"
            class="{{ $wrapperClass }}"
            data-notification-action="{{ $notification['action_url'] ?? '' }}"
        >
            <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg">
                {{ $icon }}
            </div>

            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">
                        {{ $notification['title'] ?? 'Notification' }}
                    </h4>

                    @unless($notification['is_read'] ?? false)
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    @endunless
                </div>

                <p class="mt-1 text-sm text-slate-600">
                    {{ $notification['message'] ?? 'New notification received.' }}
                </p>

                @if(! empty($notification['tracking_number']))
                    <p class="mt-1 text-xs text-slate-500">
                        Tracking:
                        <span class="font-medium">{{ $notification['tracking_number'] }}</span>
                    </p>
                @endif

                <span class="mt-2 block text-xs text-slate-400">
                    {{ $notification['created_at_human'] ?? '' }}
                </span>
            </div>
        </button>
    </form>
@else
    <div class="{{ $wrapperClass }}">
        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg">
            {{ $icon }}
        </div>

        <div class="flex-1">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-slate-900">
                    {{ $notification['title'] ?? 'Notification' }}
                </h4>

                @unless($notification['is_read'] ?? false)
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                @endunless
            </div>

            <p class="mt-1 text-sm text-slate-600">
                {{ $notification['message'] ?? 'New notification received.' }}
            </p>

            <span class="mt-2 block text-xs text-slate-400">
                {{ $notification['created_at_human'] ?? '' }}
            </span>
        </div>
    </div>
@endif
