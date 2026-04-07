const resolveProductId = () => {
    const root = document.querySelector('[data-product-detail]');
    const id = root?.getAttribute('data-product-id');
    return id ? Number(id) : null;
};

const stockBadgeClass = (stock) => {
    if (stock > 10) {
        return 'product-stock-badge inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
    }

    if (stock > 0) {
        return 'product-stock-badge inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
    }

    return 'product-stock-badge inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700';
};

const stockBadgeText = (stock) => (stock > 0 ? `Stock: ${stock}` : 'Out of Stock');

const updateStockUI = (productId, stock) => {
    document.querySelectorAll(`.product-stock-badge[data-product-id="${productId}"]`).forEach((badge) => {
        badge.textContent = stockBadgeText(stock);
        badge.className = stockBadgeClass(stock);
    });

    document.querySelectorAll(`.add-to-cart-btn[data-product-id="${productId}"]`).forEach((button) => {
        button.disabled = stock <= 0;
        button.textContent = stock <= 0 ? 'Out of Stock' : 'Add to Cart';
        button.classList.toggle('cursor-not-allowed', stock <= 0);
        button.classList.toggle('bg-slate-300', stock <= 0);
        button.classList.toggle('hover:bg-black', stock > 0);
    });
};

const subscribeProductStock = () => {
    const productId = resolveProductId();

    if (!window.Echo || !productId) {
        return;
    }

    window.Echo.channel(`product.${productId}`)
        .listen('.stock.updated', (event) => {
            const liveStock = Number(event.stock);
            const liveProductId = Number(event.productId);

            console.log('[Realtime] stock.updated', event);

            if (!Number.isFinite(liveStock) || !Number.isFinite(liveProductId)) {
                return;
            }

            updateStockUI(liveProductId, liveStock);
        });
};

document.addEventListener('DOMContentLoaded', subscribeProductStock);
