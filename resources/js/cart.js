const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const updateNumbers = (container, data) => {
    if (!container) return;
    const qtyEl = container.querySelector('.qty');
    const itemTotalEl = container.querySelector('.item-total');
    if (qtyEl) qtyEl.textContent = data.quantity;
    if (itemTotalEl) itemTotalEl.textContent = `₹${data.itemTotal.toFixed(2)}`;
};

const updateSummary = (id, data) => {
    const summaryItem = document.querySelector(`.summary-item[data-id="${id}"]`);
    if (summaryItem) {
        summaryItem.querySelector('.summary-qty').textContent = data.quantity;
        summaryItem.querySelector('.summary-price').textContent = `₹${(data.price * data.quantity).toFixed(2)}`;
        if (data.quantity <= 0) summaryItem.remove();
    }
    const totalEl = document.querySelector('#final-total');
    if (totalEl) totalEl.textContent = `₹${data.grandTotal.toFixed(2)}`;
};

const request = async (url, method = 'POST') => {
    const res = await fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
    });
    return res.json();
};

const bindCart = () => {
    document.querySelectorAll('.btn-inc').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.dataset.id;
            const row = e.currentTarget.closest('.cart-row');
            const data = await request(`/cart/increment/${id}`);
            updateNumbers(row, data);
            updateSummary(id, { ...data, price: Number(row.dataset.price) || data.itemPrice });
        });
    });

    document.querySelectorAll('.btn-dec').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.dataset.id;
            const row = e.currentTarget.closest('.cart-row');
            const data = await request(`/cart/decrement/${id}`);
            if (data.quantity <= 0 && row) row.remove();
            else updateNumbers(row, data);
            updateSummary(id, { ...data, price: Number(row?.dataset.price) || data.itemPrice });
            if (data.grandTotal <= 0) location.reload();
        });
    });

    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.dataset.id;
            const row = e.currentTarget.closest('.cart-row');
            const data = await request(`/cart/remove/${id}`, 'DELETE');
            if (row) row.remove();
            updateSummary(id, { ...data, quantity: 0, price: 0 });
            if (data.grandTotal <= 0) location.reload();
        });
    });
};

document.addEventListener('DOMContentLoaded', bindCart);
