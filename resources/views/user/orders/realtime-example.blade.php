@extends('layouts.app')

@section('title', 'Realtime Order Status')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h1 class="text-lg font-semibold text-slate-900">Realtime Order Status Example</h1>
    <p class="mt-2 text-sm text-slate-600">
        This page listens to your private channel and shows incoming order status updates.
    </p>

    <div id="order-status-events" class="mt-5 space-y-2"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
    const userRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content');
    const list = document.getElementById('order-status-events');

    if (!userId || userRole !== 'user' || !window.Echo || !list) {
        return;
    }

    window.Echo.private(`orders.${userId}`)
        .listen('.order.status.updated', (event) => {
            const node = document.createElement('div');
            node.className = 'rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700';
            node.textContent = `Order ${event.order_number || ('#' + event.order_id)} status changed to ${event.status}.`;
            list.prepend(node);
        });
});
</script>
@endsection
