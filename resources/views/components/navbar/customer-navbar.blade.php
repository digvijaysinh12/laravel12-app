<nav class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/90 px-5 py-3 shadow-sm backdrop-blur">
    <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-700">
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf

            <select
                name="locale"
                onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none transition hover:border-slate-300 focus:ring-2 focus:ring-slate-200"
            >
                <option value="">🌐 Language</option>
                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                <option value="hi" {{ app()->getLocale() == 'hi' ? 'selected' : '' }}>Hindi</option>
                <option value="gu" {{ app()->getLocale() == 'gu' ? 'selected' : '' }}>Gujarati</option>
            </select>
        </form>

        <a href="{{ route('home') }}" class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('home')) bg-slate-900 text-white shadow-sm @endif">
            {{ __('nav.home') }}
        </a>

        <a href="{{ route('user.products.index') }}" class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.products.*')) bg-slate-900 text-white shadow-sm @endif">
            {{ __('nav.products') }}
        </a>

        <a href="{{ route('user.cart.index') }}" class="flex items-center gap-2 rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.cart.*')) bg-slate-900 text-white shadow-sm @endif">
            🛒
            <span class="hidden sm:inline">{{ __('nav.cart') }}</span>
        </a>

        @auth
            <a href="{{ route('user.orders.index') }}" class="rounded-xl px-4 py-2 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.orders.*')) bg-slate-900 text-white shadow-sm @endif">
                {{ __('nav.orders') }}
            </a>
        @endauth
    </div>

    <div class="flex items-center gap-3">
        @auth
            <div
                class="relative"
                data-notification-root
                data-notification-audience="{{ $notificationAudience }}"
            >
                <x-notifications.bell
                    :count="$notificationUnreadCount"
                    button-class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-slate-100 hover:shadow-sm"
                    badge-class="absolute -right-1 -top-1 h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white"
                />

                <x-notifications.dropdown
                    :notifications="$latestNotifications"
                    :unread-count="$notificationUnreadCount"
                    view-all-url="{{ route('notifications.index') }}"
                    dropdown-class="absolute right-0 z-50 mt-3 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                    list-class="max-h-[400px] overflow-y-auto"
                    item-class="flex w-full items-start gap-3 border-b border-slate-100 px-4 py-4 text-left transition hover:bg-slate-50"
                    empty-class="flex flex-col items-center justify-center px-6 py-10 text-center"
                    footer-class="border-t border-slate-100 bg-slate-50 px-4 py-3 text-center"
                />
            </div>

            <a href="{{ route('user.profile.edit') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-slate-100 hover:shadow-sm">
                <img src="{{ asset('images/profile.png') }}" alt="Profile" class="h-6 w-6 object-contain">
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 transition-all duration-200 hover:bg-rose-50 hover:shadow-sm">
                    <img src="{{ asset('images/logout.png') }}" alt="Logout" class="h-6 w-6 object-contain">
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                {{ __('nav.login') }}
            </a>

            <a href="{{ route('register') }}" class="rounded-xl bg-slate-900 px-5 py-2 font-medium text-white shadow-sm transition hover:bg-slate-800">
                {{ __('nav.register') }}
            </a>
        @endauth
    </div>
</nav>
