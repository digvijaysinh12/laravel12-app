@props([
    'notificationData' => [
        'audience' => 'admin',
        'unreadCount' => 0,
        'latestNotifications' => [],
    ],
])

<div class="flex items-center gap-4">
    <div
        class="relative"
        data-notification-root
        data-notification-audience="{{ $notificationData['audience'] }}"
    >
        <x-notifications.bell
            :count="$notificationData['unreadCount']"
            label="Admin alerts"
            icon="Alerts"
            button-class="relative rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
            badge-class="absolute -right-2 -top-2 rounded-full bg-rose-600 px-1.5 text-xs text-white"
        />

        <x-notifications.dropdown
            :notifications="$notificationData['latestNotifications']"
            :unread-count="$notificationData['unreadCount']"
            dropdown-class="absolute right-0 z-50 mt-2 hidden rounded-xl border bg-white shadow-lg"
            width-class="w-80"
            list-class="max-h-64 overflow-y-auto text-sm"
            item-class="flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50"
            empty-class="p-3 text-slate-500"
        />
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white">
            Logout
        </button>
    </form>
</div>
