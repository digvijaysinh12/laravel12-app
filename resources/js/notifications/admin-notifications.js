import {
    fetchNotifications,
    markAllNotificationsAsRead,
    markNotificationAsRead,
} from './notification-api';

import { setupNotificationDropdown } from './notification-dropdown';

/**
 * =========================================================
 * ADMIN NOTIFICATION FRONTEND
 * =========================================================
 * Responsibilities:
 * - load notifications
 * - render dropdown
 * - handle read actions
 * - realtime refresh
 * - unread badge update
 *
 * IMPORTANT:
 * - no business logic here
 * - no filtering logic here
 * - backend already filters admin audience
 * =========================================================
 */

const ensureToastContainer = () => {
    let container = document.querySelector('[data-admin-realtime-notifications]');

    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-admin-realtime-notifications', 'true');
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
        <div class="mt-1 h-2 w-2 rounded-full bg-blue-500"></div>
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

/**
 * =========================================================
 * MAIN INITIALIZER
 * =========================================================
 */
function initializeAdminNotifications() {
    const adminRoot = document.querySelector(
        '[data-notification-root][data-notification-audience="admin"]',
    );
    const userRole = document
        .querySelector('meta[name="auth-user-role"]')
        ?.getAttribute('content');

    if (!adminRoot || userRole !== 'admin') {
        console.info('[notifications] admin notification system skipped');

        return;
    }

    console.info('[notifications] admin notification system initialized');

    const dropdown = setupNotificationDropdown(adminRoot);

    dropdown?.markAllButton?.addEventListener('click', async () => {
        try {
            console.info('[notifications] marking all notifications as read');

            await markAllNotificationsAsRead();
            await loadNotifications(adminRoot);

            console.info('[notifications] all notifications marked as read');
        } catch (error) {
            console.error(
                '[notifications] failed to mark all notifications as read',
                error,
            );
        }
    });

    loadNotifications(adminRoot);
    subscribeToRealtimeNotifications(adminRoot);
}

/**
 * =========================================================
 * DOM HELPERS
 * =========================================================
 */
function getNotificationNodes(adminRoot) {
    return {
        count: adminRoot.querySelector('#notificationCount'),
        list: adminRoot.querySelector('#notificationList'),
        markAll: adminRoot.querySelector('#markAllNotificationsBtn'),
    };
}

/**
 * =========================================================
 * EMPTY STATE
 * =========================================================
 */
function renderEmptyState() {
    return `
        <div class="p-4 text-center text-sm text-slate-500">
            No notifications found
        </div>
    `;
}

/**
 * =========================================================
 * SINGLE NOTIFICATION ITEM
 * =========================================================
 */
function renderNotificationItem(notification) {
    return `
        <button
            type="button"
            class="
                flex
                w-full
                items-start
                gap-3
                border-b
                border-slate-100
                px-4
                py-3
                text-left
                transition
                hover:bg-slate-50
                ${notification.is_read ? 'opacity-60' : ''}
            "
            data-notification-id="${notification.id}"
            data-notification-action="${notification.action_url || ''}"
        >
            <div
                class="
                    mt-1
                    flex
                    h-10
                    w-10
                    items-center
                    justify-center
                    rounded-full
                    bg-slate-100
                    text-xs
                    font-semibold
                    uppercase
                    text-slate-600
                "
            >
                AL
            </div>

            <div class="flex-1 overflow-hidden">
                <p class="truncate text-sm font-semibold text-slate-900">
                    ${escapeHtml(notification.title || 'Notification')}
                </p>

                <p class="mt-1 text-xs text-slate-600">
                    ${escapeHtml(notification.message || '')}
                </p>

                ${notification.created_at_human
            ? `
                            <p class="mt-1 text-[11px] text-slate-400">
                                ${escapeHtml(notification.created_at_human)}
                            </p>
                        `
            : ''
        }
            </div>
        </button>
    `;
}

/**
 * =========================================================
 * LOAD NOTIFICATIONS
 * =========================================================
 */
async function loadNotifications(adminRoot) {
    try {
        console.info('[notifications] loading notifications');

        const notifications = await fetchNotifications(10);

        console.info(
            `[notifications] loaded ${notifications.length} notifications`,
        );

        renderNotifications(adminRoot, notifications);
    } catch (error) {
        console.error(
            '[notifications] failed to load notifications',
            error,
        );
    }
}

/**
 * =========================================================
 * RENDER NOTIFICATION LIST
 * =========================================================
 */
function renderNotifications(adminRoot, notifications = []) {
    const { list } = getNotificationNodes(adminRoot);

    if (!list) {
        console.warn('[notifications] notification list node missing');

        return;
    }

    if (notifications.length === 0) {
        list.innerHTML = renderEmptyState();

        updateUnreadCount(adminRoot, []);

        return;
    }

    list.innerHTML = notifications
        .map(renderNotificationItem)
        .join('');

    updateUnreadCount(adminRoot, notifications);
    bindNotificationActions(adminRoot);
}

/**
 * =========================================================
 * UPDATE UNREAD BADGE
 * =========================================================
 */
function updateUnreadCount(adminRoot, notifications = []) {
    const { count } = getNotificationNodes(adminRoot);

    if (!count) {
        return;
    }

    const unreadCount = notifications.filter(
        (notification) => !notification.is_read,
    ).length;

    count.textContent = String(unreadCount);
    count.classList.toggle('hidden', unreadCount === 0);
    getNotificationNodes(adminRoot).markAll?.classList.toggle('hidden', unreadCount === 0);
}

/**
 * =========================================================
 * CLICK EVENTS
 * =========================================================
 */
function bindNotificationActions(adminRoot) {
    const { list } = getNotificationNodes(adminRoot);

    if (!list) {
        return;
    }

    const buttons = list.querySelectorAll('[data-notification-id]');

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const notificationId = button.dataset.notificationId;
            const actionUrl = button.dataset.notificationAction;

            if (!notificationId) {
                console.warn(
                    '[notifications] notification id missing',
                );

                return;
            }

            try {
                console.info(
                    `[notifications] marking notification ${notificationId} as read`,
                );

                const response = await markNotificationAsRead(
                    notificationId,
                );

                const redirectUrl =
                    response.redirect_url || actionUrl;

                if (redirectUrl) {
                    console.info(
                        `[notifications] redirecting to ${redirectUrl}`,
                    );

                    window.location.href = redirectUrl;

                    return;
                }

                await loadNotifications(adminRoot);
            } catch (error) {
                console.error(
                    '[notifications] failed to mark notification as read',
                    error,
                );
            }
        });
    });
}

/**
 * =========================================================
 * REALTIME SUBSCRIPTIONS
 * =========================================================
 */
function subscribeToRealtimeNotifications(adminRoot) {
    if (!window.Echo) {
        console.warn('[notifications] Laravel Echo not available');

        return;
    }

    console.info('[notifications] subscribing to realtime channels');

    window.Echo.private('admin.notifications')
        .listen(
            '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
            async (event) => {
                console.info(
                    '[notifications] realtime notification received',
                    event,
                );

                showToast(
                    event.title || 'Notification',
                    event.message || 'You have a new admin notification.',
                );

                await loadNotifications(adminRoot);
            },
        );

    window.Echo.private('admin.orders')
        .listen('.order.placed', (event) => {
            console.info(
                '[notifications] realtime order event received',
                event,
            );
        });
}

/**
 * =========================================================
 * SIMPLE XSS PROTECTION
 * =========================================================
 */
function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

document.addEventListener('DOMContentLoaded', initializeAdminNotifications);
