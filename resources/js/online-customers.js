const authUserId = Number(document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content') || 0);
const authUserRole = document.querySelector('meta[name="auth-user-role"]')?.getAttribute('content') || '';
const browsingChannel = 'store.browsing';
const onlineUsers = new Map();

const hasJquery = () => typeof window.jQuery !== 'undefined';

const isCustomer = (user) => (user?.role || 'user') === 'user';

const upsertUser = (user) => {
    if (!user || typeof user.id === 'undefined') {
        return;
    }

    onlineUsers.set(Number(user.id), user);
};

const removeUser = (user) => {
    if (!user || typeof user.id === 'undefined') {
        return;
    }

    onlineUsers.delete(Number(user.id));
};

const renderAdminOnlineCustomers = () => {
    if (authUserRole !== 'admin' || !hasJquery()) {
        return;
    }

    const $count = window.jQuery('#online-customers-count');
    const $list = window.jQuery('#online-customers-list');

    if ($count.length === 0 || $list.length === 0) {
        return;
    }

    const customers = Array.from(onlineUsers.values())
        .filter(isCustomer)
        .sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));

    $count.text(customers.length);
    $list.empty();

    if (customers.length === 0) {
        $list.append(
            '<li class="rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-500">No customers online</li>'
        );
        return;
    }

    customers.forEach((user) => {
        const $item = window.jQuery('<li class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm"></li>');
        const $name = window.jQuery('<span class="font-medium text-slate-700"></span>').text(user.name || 'Customer');
        const $status = window.jQuery('<span class="text-xs text-emerald-600">Online</span>');
        $item.append($name, $status);
        $list.append($item);
    });
};

const bindStoreBrowsingPresence = () => {
    if (!window.Echo || !authUserId || window.__storeBrowsingBound) {
        return;
    }

    window.__storeBrowsingBound = true;

    window.Echo.join(browsingChannel)
        .here((users) => {
            console.log('[Presence] here', users);
            onlineUsers.clear();
            users.forEach(upsertUser);
            renderAdminOnlineCustomers();
        })
        .joining((user) => {
            console.log('[Presence] joining', user);
            upsertUser(user);
            renderAdminOnlineCustomers();
        })
        .leaving((user) => {
            console.log('[Presence] leaving', user);
            removeUser(user);
            renderAdminOnlineCustomers();
        })
        .error((error) => {
            console.error('[Presence] store.browsing error', error);
        });
};

document.addEventListener('DOMContentLoaded', bindStoreBrowsingPresence);
