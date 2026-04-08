<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-6 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <p>&copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.</p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('user.products.index') }}" class="transition hover:text-slate-900">Products</a>
            <a href="{{ route('user.cart.index') }}" class="transition hover:text-slate-900">Cart</a>
            @auth
                <a href="{{ route('user.orders.index') }}" class="transition hover:text-slate-900">Orders</a>
                <a href="{{ route('user.profile.edit') }}" class="transition hover:text-slate-900">Profile</a>
            @endauth
        </div>
    </div>
</footer>
