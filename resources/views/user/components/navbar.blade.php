<nav class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-600">
    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('home')) bg-slate-100 text-slate-900 @endif">Home</a>
    <a href="{{ route('user.products.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.products.*')) bg-slate-100 text-slate-900 @endif">Products</a>
    <a href="{{ route('user.cart.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.cart.*')) bg-slate-100 text-slate-900 @endif">Cart</a>
    @auth
        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('dashboard')) bg-slate-100 text-slate-900 @endif">Dashboard</a>
        <a href="{{ route('user.orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.orders.*')) bg-slate-100 text-slate-900 @endif">Orders</a>
        <a href="{{ route('user.profile.edit') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.profile.*')) bg-slate-100 text-slate-900 @endif">Profile</a>
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
