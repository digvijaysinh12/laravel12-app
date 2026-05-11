@props([
    'notifications' => [],
    'unreadCount' => 0,
    'title' => 'Notifications',
    'subtitle' => 'Latest updates and alerts',
    'viewAllUrl' => null,
    'widthClass' => 'w-[380px]',
    'dropdownClass' => '',
    'listClass' => '',
    'itemClass' => '',
    'emptyClass' => '',
    'footerClass' => '',
])

<div
    id="notificationDropdown"
    class="{{ $dropdownClass }} {{ $widthClass }}"
>
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
            <p class="text-xs text-slate-500">{{ $subtitle }}</p>
        </div>

        <button
            id="markAllNotificationsBtn"
            type="button"
            class="{{ $unreadCount > 0 ? '' : 'hidden' }} text-xs font-medium text-blue-600 transition hover:text-blue-700"
        >
            Mark all read
        </button>
    </div>

    <div id="notificationList" class="{{ $listClass }}">
        @forelse($notifications as $notification)
            <x-notifications.item
                :notification="$notification"
                :wrapper-class="$itemClass"
            />
        @empty
            <x-notifications.empty :class="$emptyClass" />
        @endforelse
    </div>

    @if($viewAllUrl)
        <div class="{{ $footerClass }}">
            <a href="{{ $viewAllUrl }}" class="text-sm font-medium text-slate-700 transition hover:text-slate-900">
                View All Notifications
            </a>
        </div>
    @endif
</div>
