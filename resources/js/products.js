const updateProductUI = ({ id, stock }) => {
    if (typeof id === 'undefined') return;
    const selectors = [
        `.card[data-product-id="${id}"] [data-product-id]`,
        `[data-product-id="${id}"].product-stock-badge`,
    ];

    document.querySelectorAll(selectors.join(',')).forEach((el) => {
        const isBadge = el.classList.contains('product-stock-badge');
        if (isBadge) {
            el.dataset.stock = stock;
            if (stock > 10) {
                el.className = 'badge bg-success product-stock-badge';
                el.textContent = `Stock: ${stock}`;
            } else if (stock > 0) {
                el.className = 'badge bg-warning text-dark product-stock-badge';
                el.textContent = `Stock: ${stock}`;
            } else {
                el.className = 'badge bg-danger product-stock-badge';
                el.textContent = 'Out of Stock';
            }
        }

        if (el.classList.contains('add-to-cart-btn')) {
            el.dataset.stock = stock;
            if (stock <= 0) {
                el.setAttribute('disabled', 'disabled');
                el.textContent = 'Out of Stock';
            } else {
                el.removeAttribute('disabled');
                el.textContent = 'Add to Cart';
            }
        }
    });
};

const listenProductStock = () => {
    if (!window.Echo) return;
    window.Echo.channel('products')
        .listen('ProductStockChanged', (e) => {
            const id = e.product_id ?? e.id ?? e.product?.id;
            const stock = e.stock ?? e.product?.stock;
            updateProductUI({ id, stock });
        });
};

document.addEventListener('DOMContentLoaded', () => {
    listenProductStock();
});
