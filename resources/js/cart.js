const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const formatCurrency = (amount) => `INR ${Number(amount || 0).toFixed(2)}`;

const request = async (url, method = 'POST') => {
    const response = await fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': token,
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error(`Cart request failed: ${response.status}`);
    }

    return response.json();
};

const updateGrandTotal = (grandTotal) => {
    const totalEl = document.querySelector('#cart-grand-total');
    if (totalEl) {
        totalEl.textContent = formatCurrency(grandTotal);
    }
};

const getRow = (button) => button.closest('.cart-row');

const renderEmptyState = () => {
    const cartPage = document.querySelector('.js-cart-page');
    if (!cartPage) {
        return;
    }
    const productsUrl = cartPage.dataset.productsUrl || '/products';

    const existingState = cartPage.querySelector('[data-empty-state]');
    if (existingState) {
        return;
    }

    const wrapper = document.createElement('section');
    wrapper.setAttribute('data-empty-state', 'true');
    wrapper.className = 'rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm';
    wrapper.innerHTML = `
        <h2 class="text-xl font-semibold text-slate-900">Your cart is empty</h2>
        <p class="mt-2 text-sm text-slate-600">Add products to continue shopping.</p>
        <a href="${productsUrl}" class="mt-5 inline-flex rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black">
            Browse Products
        </a>
    `;

    cartPage.appendChild(wrapper);
};

const removeRowIfEmpty = () => {
    const rows = document.querySelectorAll('.cart-row');
    if (rows.length === 0) {
        const summary = document.querySelector('aside');
        if (summary) {
            summary.remove();
        }
        renderEmptyState();
    }
};

const bindIncreaseDecrease = () => {
    document.querySelectorAll('.btn-inc, .btn-dec').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.dataset.url;
            const row = getRow(button);
            if (!url || !row) {
                return;
            }

            try {
                const result = await request(url, 'POST');
                const quantityEl = row.querySelector('.qty');
                const itemTotalEl = row.querySelector('.item-total');
                const price = Number(row.dataset.price || 0);

                if (result.quantity <= 0) {
                    row.remove();
                    removeRowIfEmpty();
                } else {
                    if (quantityEl) {
                        quantityEl.textContent = result.quantity;
                    }
                    if (itemTotalEl) {
                        itemTotalEl.textContent = formatCurrency(price * result.quantity);
                    }
                }

                updateGrandTotal(result.grandTotal);
            } catch (error) {
                console.error(error);
            }
        });
    });
};

const bindRemove = () => {
    document.querySelectorAll('.btn-remove').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.dataset.url;
            const row = getRow(button);
            if (!url || !row) {
                return;
            }

            try {
                const result = await request(url, 'DELETE');
                row.remove();
                updateGrandTotal(result.grandTotal);
                removeRowIfEmpty();
            } catch (error) {
                console.error(error);
            }
        });
    });
};

const bindClear = () => {
    const clearBtn = document.querySelector('.btn-clear');
    if (!clearBtn) {
        return;
    }

    clearBtn.addEventListener('click', async () => {
        const url = clearBtn.dataset.url;
        if (!url) {
            return;
        }

        try {
            await request(url, 'DELETE');
            document.querySelectorAll('.cart-row').forEach((row) => row.remove());
            updateGrandTotal(0);
            const summary = document.querySelector('aside');
            if (summary) {
                summary.remove();
            }
            renderEmptyState();
        } catch (error) {
            console.error(error);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('.js-cart-page')) {
        return;
    }

    bindIncreaseDecrease();
    bindRemove();
    bindClear();
});
