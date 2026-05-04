<nav class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-600">
<form method="POST" action="{{ route('locale.switch') }}">
    @csrf

    <select name="locale" onchange="this.form.submit()">
        <option value="">Language</option>

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

    {{-- Primary Links (Keep Text) --}}
    <a href="{{ route('home') }}"
       class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900
       @if(request()->routeIs('home')) bg-slate-100 text-slate-900 @endif">
       {{ __('nav.home') }}
    </a>

    <a href="{{ route('user.products.index') }}"
       class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900
       @if(request()->routeIs('user.products.*')) bg-slate-100 text-slate-900 @endif">
        {{ __('nav.products') }}
    </a>

    {{-- Cart (Icon + Text) --}}
    <a href="{{ route('user.cart.index') }}"
       class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900
       @if(request()->routeIs('user.cart.*')) bg-slate-100 text-slate-900 @endif">
        🛒 <span class="hidden sm:inline"><span class="hidden sm:inline">{{ __('nav.cart') }}</span></span>
    </a>

    @auth

        {{-- Orders --}}
        <a href="{{ route('user.orders.index') }}"
           class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900
           @if(request()->routeIs('user.orders.*')) bg-slate-100 text-slate-900 @endif">
            {{ __('nav.orders') }}
        </a>

        {{-- Profile (Icon better here) --}}
        <a href="{{ route('user.profile.edit') }}"
           class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900
           @if(request()->routeIs('user.profile.*')) bg-slate-100 text-slate-900 @endif">
                                  <img src="{{ asset('images/profile.png') }}"
             alt="profile"
             class="w-6 h-6 object-contain">
        </a>

        {{-- Notifications (ICON ONLY) --}}
        <div class="relative">
            <button id="notificationBtn"
                class="relative rounded-lg p-2 hover:bg-slate-100 hover:text-slate-900"
                type="button">

                      <img src="{{ asset('images/notification.png') }}"
             alt="Notification"
             class="w-6 h-6 object-contain">

                <span id="notificationCount"
                    class="hidden absolute -top-1 -right-1 rounded-full bg-rose-600 px-1.5 text-[10px] text-white">
                    0
                </span>
            </button>

            <div id="notificationDropdown"
                class="hidden absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 bg-white shadow-lg z-50">

                <div class="flex items-center justify-between border-b p-3">
                    <span class="font-semibold text-slate-900">{{ __('nav.notifications') }}</span>
                    <button id="markAllNotificationsBtn"
                        type="button"
                        class="text-xs font-medium text-slate-500 hover:text-slate-900">
                        Mark all read
                    </button>
                </div>

                <div id="notificationList" class="max-h-64 overflow-y-auto text-sm">
                    <div class="p-3 text-slate-500">No notifications</div>
                </div>
            </div>
        </div>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        title="Logout"
        class="p-2 rounded-lg hover:bg-slate-100 flex items-center justify-center">

        <img src="{{ asset('images/logout.png') }}"
             alt="Logout"
             class="w-6 h-6 object-contain">
    </button>
</form>

    @else

        <a href="{{ route('login') }}"
           class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">
            {{ __('nav.login') }}
        </a>

        <a href="{{ route('register') }}"
           class="rounded-lg bg-slate-900 px-3 py-2 text-white hover:bg-slate-800">
           {{ __('nav.register') }}
        </a>

    @endauth
</nav>
