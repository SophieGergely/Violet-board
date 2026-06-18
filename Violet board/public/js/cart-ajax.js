document.addEventListener('DOMContentLoaded', () => {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : null;

    async function sendCartRequest(url, formData) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            if (!response.ok) return null;
            return await response.json();
        } catch (err) {
            return null;
        }
    }

    function applyResult(container, data) {
        if (!container || !data) return;

        if (data.quantity !== undefined && data.quantity !== null) {
            container.querySelectorAll('.js-cart-qty-input').forEach((el) => {
                el.value = data.quantity;
            });
        }

        if (data.item_total_html) {
            const itemTotalEl = container.querySelector('.item-total');
            if (itemTotalEl) itemTotalEl.innerHTML = data.item_total_html;
        }

        if (data.cart_total !== undefined) {
            document.querySelectorAll('.cart-grand-total').forEach((el) => {
                el.textContent = data.cart_total + ' €';
            });
        }

        if (data.cart_count !== undefined) {
            document.querySelectorAll('.cart-grand-count').forEach((el) => {
                el.textContent = data.cart_count;
            });
        }
    }

    function showCounter(container) {
        if (!container) return;
        const addForm = container.querySelector('.ajax-add-form');
        const counter = container.querySelector('.cart-counter');
        if (addForm) addForm.style.display = 'none';
        if (counter) counter.style.display = 'flex';
    }

    function handleRemoved(container) {
        if (!container) return;
        const addForm = container.querySelector('.ajax-add-form');
        const counter = container.querySelector('.cart-counter');

        if (addForm && counter) {
            counter.style.display = 'none';
            addForm.style.display = 'block';
        } else {
            container.remove();
        }
    }

    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.classList.contains('ajax-cart-form')) return;

        e.preventDefault();

        const formData = new FormData(form);
        const data = await sendCartRequest(form.action, formData);

        if (!data) {
            form.submit();
            return;
        }

        const container = form.closest('[data-product-id]');

        if (data.removed) {
            handleRemoved(container);
            return;
        }

        if (form.classList.contains('ajax-add-form')) {
            showCounter(container);
        }

        applyResult(container, data);
    });

    document.querySelectorAll('.js-cart-qty-input').forEach((input) => {
        input.addEventListener('change', async () => {
            const url = input.dataset.updateUrl;
            if (!url) return;

            let qty = parseInt(input.value, 10);
            if (isNaN(qty)) qty = 1;
            if (qty < 0) qty = 0;
            input.value = qty;

            const formData = new FormData();
            formData.append('action', 'set');
            formData.append('quantity', qty);
            if (csrfToken) formData.append('_token', csrfToken);

            const container = input.closest('[data-product-id]');
            const data = await sendCartRequest(url, formData);
            if (!data) return;

            if (data.removed) {
                handleRemoved(container);
                return;
            }

            applyResult(container, data);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            }
        });
    });
});
