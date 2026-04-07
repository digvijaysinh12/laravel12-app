@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Products', 'route' => 'admin.products.index', 'pattern' => 'admin.products.*'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'pattern' => 'admin.orders.*'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*'],
    ];
@endphp

<div>
    <div
        x-show="sidebarOpen"
        x-cloak
        class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full border-r border-slate-200 bg-white transition-transform duration-200 lg:static lg:z-0 lg:translate-x-0"
        :class="{ 'translate-x-0': sidebarOpen }"
    >
        <div class="flex h-16 items-center border-b border-slate-200 px-5">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-slate-900">
                {{ config('app.name') }}
            </a>
        </div>

        <nav class="space-y-1 p-4">
            @foreach ($items as $item)
                @php
                    $canVisit = \Illuminate\Support\Facades\Route::has($item['route']);
                    $isActive = request()->routeIs($item['pattern']);
                @endphp

                @if ($canVisit)
                    <a
                        href="{{ route($item['route']) }}"
                        @click="sidebarOpen = false"
                        class="{{ $isActive ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }} block rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    >
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="block cursor-not-allowed rounded-lg px-3 py-2.5 text-sm font-medium text-slate-400">
                        {{ $item['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    </aside>
</div>
