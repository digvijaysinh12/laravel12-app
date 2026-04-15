@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Products', 'route' => 'admin.products.index', 'pattern' => 'admin.products.*'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'pattern' => 'admin.orders.*'],
        ['label' => 'Customers', 'route' => 'admin.customers.index', 'pattern' => 'admin.customers.*'],
        ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'pattern' => 'admin.inventory.*'],
        ['label' => 'Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin.reports.*'],
    ];
@endphp

<nav class="flex-1 space-y-1 overflow-y-auto p-4 text-sm font-medium">
    @foreach ($items as $item)
        <a
            href="{{ route($item['route']) }}"
            class="sidebar-link block rounded-lg px-3 py-2.5 transition
class="sidebar-link block rounded-lg px-3 py-2.5 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
        >
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>