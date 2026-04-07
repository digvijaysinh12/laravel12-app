const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
const userRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content');

const ensureRealtimeContainer = () => {
    let container = document.querySelector('[data-realtime-notifications]');

    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-realtime-notifications', 'true');
        container.className = 'fixed right-4 top-20 z-50 flex max-w-sm flex-col gap-2';
        document.body.appendChild(container);
    }

    return container;
};

const showRealtimeNotice = (message) => {
    const container = ensureRealtimeContainer();
    const notice = document.createElement('div');
    notice.className = 'rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800 shadow-sm';
    notice.textContent = message;
    container.appendChild(notice);

    setTimeout(() => {
        notice.remove();
    }, 4500);
};

const subscribeToOrderStatus = () => {
    if (!window.Echo || !userId || userRole !== 'user') {
        return;
    }

    window.Echo.private(`orders.${userId}`)
        .listen('.order.status.updated', (event) => {
            const label = event.order_number || `#${event.order_id}`;
            showRealtimeNotice(`Order ${label} is now ${event.status}.`);
        });
};

document.addEventListener('DOMContentLoaded', subscribeToOrderStatus);
