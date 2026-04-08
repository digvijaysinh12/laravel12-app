const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const formatCurrency = (amount) => `INR ${Number(amount || 0).toFixed(2)}`;

const submitRequest = async (form) => {
    const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
    const body = new FormData(form);

    const response = await fetch(form.action, {
        method: method === 'GET' ? 'GET' : 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            Accept: 'application/json',
        },
        body: method === 'GET' ? undefined : body,
    });

    if (!response.ok) {
        throw new Error(`Cart request failed: ${response.status}`);
    }

    return response.json();
};

const updateTotals = (data) => {
    const subtotalEl = document.querySelector('#cart-subtotal');
    const taxEl = document.querySelector('#cart-tax');
    const shippingEl = document.querySelector('#cart-shipping');
    const totalEl = document.querySelector('#cart-grand-total');

    if (subtotalEl && typeof data.subtotal !== 'undefined') {
        subtotalEl.textContent = formatCurrency(data.subtotal);
    }

    if (taxEl && typeof data.tax !== 'undefined') {
        taxEl.textContent = formatCurrency(data.tax);
    }

    if (shippingEl && typeof data.shipping !== 'undefined') {
        shippingEl.textContent = formatCurrency(data.shipping);
    }

    if (totalEl && typeof data.grandTotal !== 'undefined') {
        totalEl.textContent = formatCurrency(data.grandTotal);
    }
};

const renderEmptyState = () => {
    const cartPage = document.querySelector('.js-cart-page');
    if (!cartPage || cartPage.querySelector('[data-empty-state]')) {
        return;
    }

    const emptyState = document.createElement('section');
    emptyState.setAttribute('data-empty-state', 'true');
    emptyState.className = 'rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm';
    emptyState.innerHTML = `
        <h2 class="text-xl font-semibold text-slate-900">Your cart is empty</h2>
        <p class="mt-2 text-sm text-slate-600">Add products to continue shopping.</p>
        <a href="${cartPage.dataset.productsUrl || '/products'}" class="mt-5 inline-flex rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700">
            Browse Products
        </a>
    `;

    cartPage.appendChild(emptyState);
};

const removeSummaryIfNeeded = () => {
    const rows = document.querySelectorAll('.cart-row');
    if (rows.length > 0) {
        return;
    }

    document.querySelector('[data-cart-summary]')?.remove();
    renderEmptyState();
};

const handleCartResponse = (form, result) => {
    const action = form.dataset.cartAction;
    const row = form.closest('.cart-row');

    if (action === 'clear') {
        document.querySelectorAll('.cart-row').forEach((existingRow) => existingRow.remove());
        document.querySelector('[data-cart-summary]')?.remove();
        updateTotals({ subtotal: 0, tax: 0, shipping: 0, grandTotal: 0 });
        renderEmptyState();
        return;
    }

    if (!row) {
        return;
    }

    if (action === 'remove' || Number(result.quantity || 0) <= 0) {
        row.remove();
        updateTotals(result);
        removeSummaryIfNeeded();
        return;
    }

    const quantityEl = row.querySelector('.qty');
    const itemTotalEl = row.querySelector('.item-total');
    const price = Number(row.dataset.price || 0);

    if (quantityEl) {
        quantityEl.textContent = result.quantity;
    }

    if (itemTotalEl) {
        itemTotalEl.textContent = formatCurrency(price * Number(result.quantity || 0));
    }

    updateTotals(result);
};

document.addEventListener('DOMContentLoaded', () => {
    const cartPage = document.querySelector('.js-cart-page');
    if (!cartPage) {
        return;
    }

    cartPage.querySelectorAll('[data-cart-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            try {
                const result = await submitRequest(form);
                handleCartResponse(form, result);
            } catch (error) {
                console.error(error);
            }
        });
    });
});
