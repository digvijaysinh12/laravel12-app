const userRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content');
const orderId = document.querySelector('[data-order-id]')?.getAttribute('data-order-id');
const visibleStatuses = ['paid', 'shipped', 'delivered'];

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

const appendFeedEntry = (message) => {
    const feed = document.querySelector('[data-order-status-feed]');
    if (!feed) {
        return;
    }

    const item = document.createElement('div');
    item.className = 'rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700';
    item.textContent = message;
    feed.prepend(item);
};

const subscribeToOrderStatus = () => {
    if (!window.Echo || !orderId || userRole !== 'user') {
        return;
    }

    window.Echo.private(`order.${orderId}`)
        .listen('.order.status.updated', (event) => {
            const status = String(event.status || '').toLowerCase();

            if (!visibleStatuses.includes(status)) {
                return;
            }

            const label = event.order_number || `#${event.order_id}`;
            const message = `Order ${label} is now ${status}.`;

            appendFeedEntry(message);
        });
};

document.addEventListener('DOMContentLoaded', subscribeToOrderStatus);
