const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
const userRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content');

const hasNotificationUi = () => !!document.querySelector('#notificationBtn')
    && !!document.querySelector('#notificationDropdown')
    && !!document.querySelector('#notificationList')
    && !!document.querySelector('#notificationCount');

const ensureToastContainer = () => {
    let container = document.querySelector('[data-realtime-notifications]');

    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-realtime-notifications', 'true');
        container.className = 'fixed right-4 top-20 z-50 flex max-w-sm flex-col gap-2';
        document.body.appendChild(container);
    }

    return container;
};

const showToast = (title, message) => {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = 'flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-md';

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
    closeBtn.addEventListener('click', () => toast.remove());

    content.appendChild(heading);
    content.appendChild(body);
    toast.appendChild(dot);
    toast.appendChild(content);
    toast.appendChild(closeBtn);
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 250);
    }, 4500);
};

const notificationChannel = () => {
    if (userRole === 'admin') {
        return 'admin.notifications';
    }

    if (userRole === 'user' && userId) {
        return `user.${userId}.notifications`;
    }

    return null;
};

const notificationUrl = (suffix = '') => `/notifications${suffix}`;

const currentNodes = () => ({
    btn: document.querySelector('#notificationBtn'),
    dropdown: document.querySelector('#notificationDropdown'),
    list: document.querySelector('#notificationList'),
    count: document.querySelector('#notificationCount'),
    markAll: document.querySelector('#markAllNotificationsBtn'),
});

const setUnreadCount = (notifications) => {
    const { count } = currentNodes();

    if (!count) {
        return;
    }

    const unread = notifications.filter((notification) => !notification.is_read).length;

    count.textContent = String(unread);
    count.classList.toggle('hidden', unread === 0);
};

const renderNotificationItem = (notification) => {
    const item = document.createElement('div');
    item.className = `border-b p-3 cursor-pointer ${notification.is_read ? 'opacity-50' : ''}`;
    item.dataset.id = notification.id;

    item.innerHTML = `
        <p class="text-sm font-semibold text-slate-900">${notification.title || 'Notification'}</p>
        <p class="mt-1 text-xs text-slate-600">${notification.message || ''}</p>
    `;

    item.addEventListener('click', async () => {
        await markAsRead(notification.id, item);
    });

    return item;
};

const loadNotifications = async () => {
    if (!hasNotificationUi()) {
        return;
    }

    try {
        const response = await axios.get(notificationUrl());
        const { list } = currentNodes();

        if (!list) {
            return;
        }

        list.innerHTML = '';

        if (!response.data.length) {
            list.innerHTML = '<div class="p-3 text-slate-500">No notifications</div>';
            setUnreadCount([]);
            return;
        }

        response.data.forEach((notification) => {
            list.appendChild(renderNotificationItem(notification));
        });

        setUnreadCount(response.data);
    } catch (error) {
        console.error('Notification load error:', error);
    }
};

const prependNotification = (notification) => {
    const { list } = currentNodes();

    if (!list) {
        return;
    }

    if (list.textContent.includes('No notifications')) {
        list.innerHTML = '';
    }

    const item = renderNotificationItem(notification);
    list.prepend(item);

    const count = Number(currentNodes().count?.textContent || 0) + 1;
    if (currentNodes().count) {
        currentNodes().count.textContent = String(count);
        currentNodes().count.classList.remove('hidden');
    }
};

const markAsRead = async (id, element) => {
    try {
        await axios.post(notificationUrl(`/${id}/read`));
        element.classList.add('opacity-50');

        const { count } = currentNodes();
        if (!count) {
            return;
        }

        const next = Math.max(0, Number(count.textContent || 0) - 1);
        count.textContent = String(next);
        count.classList.toggle('hidden', next === 0);
    } catch (error) {
        console.error('Mark as read failed:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(notificationUrl('/read-all'));
        await loadNotifications();
    } catch (error) {
        console.error('Mark all as read failed:', error);
    }
};

const setupDropdown = () => {
    const { btn, dropdown, markAll } = currentNodes();

    if (btn && dropdown) {
        btn.addEventListener('click', () => {
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!dropdown.contains(event.target) && !btn.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    if (markAll) {
        markAll.addEventListener('click', markAllAsRead);
    }
};

const subscribeNotifications = () => {
    const channel = notificationChannel();

    if (!window.Echo || !channel) {
        return;
    }

    window.Echo.private(channel).listen('.notification.created', (notification) => {
        const payload = {
            id: notification.id || Date.now(),
            title: notification.title || 'Notification',
            message: notification.message || '',
            is_read: Boolean(notification.is_read),
            created_at: notification.created_at || null,
        };

        showToast(payload.title, payload.message);
        prependNotification(payload);
    });
};

const subscribeLegacyAdminOrders = () => {
    if (!window.Echo || userRole !== 'admin') {
        return;
    }

    // FIXED: keep the legacy order broadcast alive without duplicating the new notifications UI.
    window.Echo.private('admin.orders')
        .listen('.order.placed', (event) => {
            console.log('[Realtime] order.placed', event);
        });
};

document.addEventListener('DOMContentLoaded', () => {
    if (!userRole || !hasNotificationUi()) {
        return;
    }

    setupDropdown();
    loadNotifications();
    subscribeNotifications();
    subscribeLegacyAdminOrders();
});
