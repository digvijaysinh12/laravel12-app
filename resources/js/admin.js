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

const showAdminNotice = (title, message) => {
    const container = ensureRealtimeContainer();
    const notice = document.createElement('div');
    notice.className = 'rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm';
    const heading = document.createElement('strong');
    heading.textContent = title;
    const body = document.createElement('div');
    body.textContent = message;
    notice.appendChild(heading);
    notice.appendChild(body);
    container.appendChild(notice);

    setTimeout(() => {
        notice.remove();
    }, 5000);
};

const subscribeAdminOrders = () => {
    if (!window.Echo || userRole !== 'admin') {
        return;
    }

    window.Echo.private('admin.orders')
        .listen('.order.placed', (event) => {
            const customer = event.customer_name || 'Customer';
            const amount = Number(event.total || 0).toLocaleString('en-IN', {
                style: 'currency',
                currency: 'INR',
            });
            const message = `${customer} placed ${event.order_number} (${event.item_count} items, ${amount})`;
            showAdminNotice('New Order', message);
        });
};

document.addEventListener('DOMContentLoaded', subscribeAdminOrders);
