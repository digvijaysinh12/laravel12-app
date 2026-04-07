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

const updateNotificationDropdown = (title, message) => {
    const countEl = document.querySelector('#notificationCount');
    const listEl = document.querySelector('#notificationList');

    if (!countEl || !listEl) {
        return;
    }

    if (listEl.textContent?.trim() === 'No notifications') {
        listEl.innerHTML = '';
    }

    const item = document.createElement('div');
    item.className = 'border-b border-slate-100 p-3 last:border-b-0';
    item.innerHTML = `
        <p class="text-sm font-semibold text-slate-900">${title}</p>
        <p class="mt-1 text-xs text-slate-600">${message}</p>
    `;
    listEl.prepend(item);

    const current = Number(countEl.textContent || 0) + 1;
    countEl.textContent = String(current);
    countEl.classList.remove('hidden');
};

const showAdminNotice = (title, message) => {
    const container = ensureRealtimeContainer();
    const notice = document.createElement('div');
    notice.className =
        'flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-md transition-all';

    const dot = document.createElement('div');
    dot.className = 'mt-1 h-2 w-2 rounded-full bg-emerald-500';

    const content = document.createElement('div');
    content.className = 'flex-1';

    const heading = document.createElement('p');
    heading.className = 'text-sm font-semibold text-slate-800';
    heading.textContent = title;

    const body = document.createElement('p');
    body.className = 'text-sm text-slate-600';
    body.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.textContent = 'x';
    closeBtn.className = 'text-lg leading-none text-slate-400 hover:text-slate-600';
    closeBtn.addEventListener('click', () => notice.remove());

    content.appendChild(heading);
    content.appendChild(body);
    notice.appendChild(dot);
    notice.appendChild(content);
    notice.appendChild(closeBtn);
    container.appendChild(notice);

    setTimeout(() => {
        notice.classList.add('opacity-0');
        setTimeout(() => notice.remove(), 300);
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
            updateNotificationDropdown('New Order', message);
        });
};

const setupNotificationToggle = () => {
    const btn = document.querySelector('#notificationBtn');
    const dropdown = document.querySelector('#notificationDropdown');

    if (!btn || !dropdown) {
        return;
    }

    btn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!dropdown.contains(event.target) && !btn.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setupNotificationToggle();
    subscribeAdminOrders();
});
