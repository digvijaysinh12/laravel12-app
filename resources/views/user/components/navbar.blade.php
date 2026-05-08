@php
    use App\Support\Notifications\NotificationCache;
@endphp
<nav class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/90 px-5 py-3 shadow-sm backdrop-blur">

    {{-- LEFT SIDE --}}
    <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-700">

        {{-- Language Switch --}}
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf

            <select name="locale"
                onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none transition hover:border-slate-300 focus:ring-2 focus:ring-slate-200">

                <option value="">🌐 Language</option>

                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                    English
                </option>

                <option value="hi" {{ app()->getLocale() == 'hi' ? 'selected' : '' }}>
                    Hindi
                </option>

                <option value="gu" {{ app()->getLocale() == 'gu' ? 'selected' : '' }}>
                    Gujarati
                </option>

            </select>
        </form>

        {{-- Home --}}
        <a href="{{ route('home') }}"
            class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900
            @if(request()->routeIs('home')) bg-slate-900 text-white shadow-sm @endif">

            {{ __('nav.home') }}
        </a>

        {{-- Products --}}
        <a href="{{ route('user.products.index') }}"
            class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900
            @if(request()->routeIs('user.products.*')) bg-slate-900 text-white shadow-sm @endif">

            {{ __('nav.products') }}
        </a>

        {{-- Cart --}}
        <a href="{{ route('user.cart.index') }}"
            class="flex items-center gap-2 rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900
            @if(request()->routeIs('user.cart.*')) bg-slate-900 text-white shadow-sm @endif">

            🛒

            <span class="hidden sm:inline">
                {{ __('nav.cart') }}
            </span>

        </a>

        @auth

            {{-- Orders --}}
            <a href="{{ route('user.orders.index') }}"
                class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900
                @if(request()->routeIs('user.orders.*')) bg-slate-900 text-white shadow-sm @endif">

                {{ __('nav.orders') }}
            </a>

        @endauth

    </div>

    {{-- RIGHT SIDE --}}
    <div class="flex items-center gap-3">

        @auth

            {{-- Notifications --}}
            <div class="relative">

                @php
                    $unreadCount = NotificationCache::unreadCountFor(auth()->user());

                    $latestNotifications = auth()->user()
                        ->notifications()
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp

                {{-- Bell Button --}}
                <button id="notificationBtn"
                    type="button"
                    class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-slate-100 hover:shadow-sm">

                    🔔

                    @if($unreadCount > 0)

                        <span id="notificationCount" class="absolute -right-1 -top-1 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white">

                            {{ $unreadCount }}

                        </span>

                    @else

                        <span id="notificationCount" class="absolute -right-1 -top-1 hidden h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white">
                            0
                        </span>

                    @endif

                </button>

                {{-- Dropdown --}}
                <div id="notificationDropdown"
                    class="absolute right-0 z-50 mt-3 hidden w-[380px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">

                        <div>

                            <h3 class="text-sm font-semibold text-slate-900">
                                Notifications
                            </h3>

                            <p class="text-xs text-slate-500">
                                Latest updates and alerts
                            </p>

                        </div>

                        @if($unreadCount > 0)

                            <button id="markAllNotificationsBtn" type="button"
                                class="text-xs font-medium text-blue-600 transition hover:text-blue-700">

                                Mark all read

                            </button>

                        @endif

                    </div>

                    {{-- Notifications List --}}
                    <div id="notificationList"
                        class="max-h-[400px] overflow-y-auto">

                        @forelse($latestNotifications as $notification)

                            <form method="POST"
                                action="{{ route('notifications.read', $notification->id) }}">

                                @csrf

                                <button type="submit"
                                    class="flex w-full items-start gap-3 border-b border-slate-100 px-4 py-4 text-left transition hover:bg-slate-50">

                                    {{-- Icon --}}
                                    <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg">

                                        @switch($notification->data['icon'] ?? 'default')

                                            @case('truck')
                                                🚚
                                                @break

                                            @case('order')
                                                📦
                                                @break

                                            @case('warning')
                                                ⚠️
                                                @break

                                            @default
                                                🔔

                                        @endswitch

                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1">

                                        <div class="flex items-center justify-between">

                                            <h4 class="text-sm font-semibold text-slate-900">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h4>

                                            @if(is_null($notification->read_at))

                                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>

                                            @endif

                                        </div>

                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $notification->data['message'] ?? 'New notification received.' }}
                                        </p>

                                        @if(isset($notification->data['tracking_number']))

                                            <p class="mt-1 text-xs text-slate-500">

                                                Tracking:

                                                <span class="font-medium">
                                                    {{ $notification->data['tracking_number'] }}
                                                </span>

                                            </p>

                                        @endif

                                        <span class="mt-2 block text-xs text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>

                                    </div>

                                </button>

                            </form>

                        @empty

                            <div class="flex flex-col items-center justify-center px-6 py-10 text-center">

                                <div class="mb-3 text-4xl">
                                    🔔
                                </div>

                                <h4 class="text-sm font-semibold text-slate-700">
                                    No Notifications
                                </h4>

                                <p class="mt-1 text-xs text-slate-500">
                                    You're all caught up.
                                </p>

                            </div>

                        @endforelse

                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-slate-100 bg-slate-50 px-4 py-3 text-center">

                        <a href="{{ route('notifications.index') }}"
                            class="text-sm font-medium text-slate-700 transition hover:text-slate-900">

                            View All Notifications

                        </a>

                    </div>

                </div>

            </div>

            {{-- Profile --}}
            <a href="{{ route('user.profile.edit') }}"
                class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-slate-100 hover:shadow-sm">

                <img src="{{ asset('images/profile.png') }}"
                    alt="Profile"
                    class="h-6 w-6 object-contain">

            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    title="Logout"
                    class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-rose-50 hover:shadow-sm">

                    <img src="{{ asset('images/logout.png') }}"
                        alt="Logout"
                        class="h-6 w-6 object-contain">

                </button>

            </form>

        @else

            {{-- Login --}}
            <a href="{{ route('login') }}"
                class="rounded-xl px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">

                {{ __('nav.login') }}
            </a>

            {{-- Register --}}
            <a href="{{ route('register') }}"
                class="rounded-xl bg-slate-900 px-5 py-2 font-medium text-white shadow-sm transition hover:bg-slate-800">

                {{ __('nav.register') }}
            </a>

        @endauth

    </div>

</nav>
