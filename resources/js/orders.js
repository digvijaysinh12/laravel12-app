const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');

const statusClass = (status) => {
    switch (status) {
        case 'delivered':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-info text-dark';
    }
};

const updateOrderStatusBadge = (orderId, status) => {
    document.querySelectorAll(`[data-order-status-badge][data-order-id="${orderId}"]`).forEach((badge) => {
        badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        badge.className = `badge ${statusClass(status)}`;
    });
};

const listenOrderStatus = () => {
    if (!window.Echo || !userId) return;
    window.Echo.private(`orders.${userId}`)
        .listen('OrderStatusUpdated', (e) => {
            const orderId = e.order_id ?? e.order?.id;
            const status = e.status ?? e.order?.status;
            if (orderId && status) updateOrderStatusBadge(orderId, status);
        });
};

document.addEventListener('DOMContentLoaded', listenOrderStatus);
