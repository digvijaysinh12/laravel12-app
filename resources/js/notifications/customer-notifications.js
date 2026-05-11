import { fetchNotifications, markAllNotificationsAsRead, markNotificationAsRead } from './notification-api';
import { setupNotificationDropdown } from './notification-dropdown';

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
    toast.className = 'flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-md transition-opacity duration-200';

    toast.innerHTML = `
        <div class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-slate-800">${escapeHtml(title)}</p>
            <p class="text-sm text-slate-600">${escapeHtml(message)}</p>
        </div>
        <button type="button" class="text-lg leading-none text-slate-400 hover:text-slate-600">x</button>
    `;

    toast.querySelector('button')?.addEventListener('click', () => toast.remove());
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 250);
    }, 4500);
};

const notificationNodes = (customerRoot) => ({
    count: customerRoot?.querySelector('#notificationCount'),
    list: customerRoot?.querySelector('#notificationList'),
    markAll: customerRoot?.querySelector('#markAllNotificationsBtn'),
});

const iconFor = (icon) => {
    switch (icon) {
    case 'truck':
        return 'TR';
    case 'order':
        return 'OR';
    case 'warning':
        return 'WR';
    default:
        return 'NT';
    }
};

const emptyMarkup = `
    <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
        <div class="mb-3 text-4xl">NT</div>
        <h4 class="text-sm font-semibold text-slate-700">No Notifications</h4>
        <p class="mt-1 text-xs text-slate-500">You're all caught up.</p>
    </div>
`;

const itemMarkup = (notification) => `
    <form method="POST" action="/notifications/${notification.id}/read">
        <button
            type="button"
            class="flex w-full items-start gap-3 border-b border-slate-100 px-4 py-4 text-left transition hover:bg-slate-50 ${notification.is_read ? 'opacity-50' : ''}"
            data-notification-id="${notification.id}"
            data-notification-action="${notification.action_url || ''}"
        >
            <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                ${iconFor(notification.icon)}
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">${escapeHtml(notification.title || 'Notification')}</h4>
                    ${notification.is_read ? '' : '<span class="h-2 w-2 rounded-full bg-blue-500"></span>'}
                </div>
                <p class="mt-1 text-sm text-slate-600">${escapeHtml(notification.message || '')}</p>
                ${notification.tracking_number ? `<p class="mt-1 text-xs text-slate-500">Tracking: <span class="font-medium">${escapeHtml(notification.tracking_number)}</span></p>` : ''}
                <span class="mt-2 block text-xs text-slate-400">${escapeHtml(notification.created_at_human || '')}</span>
            </div>
        </button>
    </form>
`;

const setUnreadCount = (customerRoot, notifications) => {
    const { count } = notificationNodes(customerRoot);

    if (!count) {
        return;
    }

    const unread = notifications.filter((notification) => !notification.is_read).length;
    count.textContent = String(unread);
    count.classList.toggle('hidden', unread === 0);
    notificationNodes(customerRoot).markAll?.classList.toggle('hidden', unread === 0);
};

const bindListActions = (customerRoot) => {
    const { list } = notificationNodes(customerRoot);

    list?.querySelectorAll('[data-notification-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            const notificationId = button.getAttribute('data-notification-id');
            const actionUrl = button.getAttribute('data-notification-action');

            if (!notificationId) {
                return;
            }

            try {
                const response = await markNotificationAsRead(notificationId);
                button.classList.add('opacity-50');

                const { count } = notificationNodes(customerRoot);
                if (count) {
                    const next = Math.max(0, Number(count.textContent || 0) - 1);
                    count.textContent = String(next);
                    count.classList.toggle('hidden', next === 0);
                }

                const redirectUrl = response.redirect_url || actionUrl;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            } catch (error) {
                console.error('Customer notification read failed.', error);
            }
        });
    });
};

const renderNotifications = (customerRoot, notifications) => {
    const { list } = notificationNodes(customerRoot);

    if (!list) {
        return;
    }

    list.innerHTML = notifications.length > 0
        ? notifications.map(itemMarkup).join('')
        : emptyMarkup;

    setUnreadCount(customerRoot, notifications);
    bindListActions(customerRoot);
};

const loadNotifications = async (customerRoot) => {
    try {
        renderNotifications(customerRoot, await fetchNotifications(10));
    } catch (error) {
        console.error('Customer notifications failed to load.', error);
    }
};

const subscribeNotifications = (customerRoot, userRole, userId) => {
    if (!window.Echo || userRole !== 'user' || !userId) {
        return;
    }

    window.Echo.private(`user.${userId}.notifications`)
        .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', async (event) => {
            const payload = {
                ...event,
                is_read: false,
            };

            showToast(payload.title || 'Notification', payload.message || '');
            await loadNotifications(customerRoot);
        });
};

document.addEventListener('DOMContentLoaded', () => {
    const customerRoot = document.querySelector('[data-notification-root][data-notification-audience="customer"]');
    const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
    const userRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content');

    if (!customerRoot || userRole !== 'user') {
        return;
    }

    const dropdown = setupNotificationDropdown(customerRoot);

    dropdown?.markAllButton?.addEventListener('click', async () => {
        try {
            await markAllNotificationsAsRead();
            await loadNotifications(customerRoot);
        } catch (error) {
            console.error('Customer mark-all failed.', error);
        }
    });

    loadNotifications(customerRoot);
    subscribeNotifications(customerRoot, userRole, userId);
});

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
