/**
 * Admin real-time handlers.
 * Loaded only on admin layout pages (body.hasClass('layout-admin')).
 */

const isAdminPage = () => document.body?.classList?.contains('layout-admin');

const ensureToastContainer = () => {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }
    return container;
};

const renderToast = ({ title = 'New Order', message, variant = 'success' }) => {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${variant} border-0 show shadow`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button>
        </div>
    `;
    container.appendChild(toast);

    const closeBtn = toast.querySelector('.btn-close');
    closeBtn?.addEventListener('click', () => toast.remove());
    setTimeout(() => toast.remove(), 5000);
};

const subscribeAdminOrders = () => {
    if (!window.Echo) return;

    window.Echo.private('admin.orders')
        .listen('OrderPlaced', (e) => {
            const { customer_name, total, item_count } = e.data ?? {};
            const amount = Number(total ?? 0).toLocaleString('en-IN', { style: 'currency', currency: 'INR' });
            const message = `${customer_name ?? 'Customer'} • ${item_count ?? 0} items • ${amount}`;
            renderToast({ title: 'New Order', message });
        });
};

document.addEventListener('DOMContentLoaded', () => {
    if (!isAdminPage()) return;
    subscribeAdminOrders();
});
