<nav class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-600">
    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('home')) bg-slate-100 text-slate-900 @endif">Home</a>
    <a href="{{ route('user.products.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.products.*')) bg-slate-100 text-slate-900 @endif">Products</a>
    <a href="{{ route('user.cart.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.cart.*')) bg-slate-100 text-slate-900 @endif">Cart</a>
    @auth
        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('dashboard')) bg-slate-100 text-slate-900 @endif">Dashboard</a>
        <a href="{{ route('user.orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.orders.*')) bg-slate-100 text-slate-900 @endif">Orders</a>
        <a href="{{ route('user.profile.edit') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.profile.*')) bg-slate-100 text-slate-900 @endif">Profile</a>
        <div class="relative">
            <button id="notificationBtn" class="relative rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900" type="button">
                Notifications
                <span id="notificationCount" class="hidden absolute -top-1 -right-1 rounded-full bg-rose-600 px-1.5 text-[10px] text-white">0</span>
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 bg-white shadow-lg z-50">
                <div class="flex items-center justify-between border-b p-3">
                    <span class="font-semibold text-slate-900">Notifications</span>
                    <button id="markAllNotificationsBtn" type="button" class="text-xs font-medium text-slate-500 hover:text-slate-900">
                        Mark all read
                    </button>
                </div>
                <div id="notificationList" class="max-h-64 overflow-y-auto text-sm">
                    <div class="p-3 text-slate-500">No notifications</div>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
            @csrf
            <button type="submit" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                Logout
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">Login</a>
        <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-white hover:bg-slate-800">Register</a>
    @endauth
</nav>
