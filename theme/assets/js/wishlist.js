/**
 * Wishlist Manager (Vanilla JS ES6+)
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof steel_wishlist_obj === 'undefined') {
        return;
    }

    /**
     * Display a floating toast notification
     */
    function showToast(message, isError = false) {
        let container = document.querySelector('.steel-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'steel-toast-container';
            container.setAttribute('aria-live', 'polite');
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `steel-toast ${isError ? 'steel-toast--error' : 'steel-toast--success'}`;
        toast.textContent = message;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('is-visible');
        });

        // Auto remove after 3.5s
        setTimeout(() => {
            toast.classList.remove('is-visible');
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, 3500);
    }

    /**
     * Refresh the wishlist HTML container if present
     */
    async function refreshWishlist() {
        const wishlistWrapper = document.querySelector('.wishlist');
        if (!wishlistWrapper) return;

        const formData = new FormData();
        formData.append('action', 'steel_get_wishlist_html_ajax');
        formData.append('nonce', steel_wishlist_obj.nonce);

        try {
            const response = await fetch(steel_wishlist_obj.ajax_url, {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            if (result.success && result.data?.html) {
                const temp = document.createElement('div');
                temp.innerHTML = result.data.html;
                const newWishlist = temp.firstElementChild;
                if (newWishlist) {
                    wishlistWrapper.replaceWith(newWishlist);
                }
            }
        } catch (error) {
            console.error('Failed to refresh wishlist:', error);
        }
    }

    /**
     * Unified event delegation handler for wishlist buttons
     */
    document.addEventListener('click', async (e) => {
        const button = e.target.closest('.wishlist-button, .wishlist__remove-button');
        if (!button) return;

        e.preventDefault();

        const productId = button.dataset.productId || button.getAttribute('data-product_id');
        const isRemoveBtn = button.classList.contains('wishlist__remove-button');
        const listItem = button.closest('.wishlist__item');

        if (!productId || button.classList.contains('is-loading')) {
            return;
        }

        button.classList.add('is-loading');

        const formData = new FormData();
        formData.append('action', 'steel_toggle_wishlist');
        formData.append('product_id', productId);
        formData.append('nonce', steel_wishlist_obj.nonce);

        try {
            const response = await fetch(steel_wishlist_obj.ajax_url, {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            button.classList.remove('is-loading');

            if (result.success) {
                const isRemoved = result.data.action === 'removed';

                // 1. Sync header counters
                const count = result.data.count || 0;
                document.querySelectorAll('.header__wishlist-count').forEach((counter) => {
                    counter.textContent = count;
                    counter.classList.toggle('visually-hidden', count === 0);
                });

                // 2. Sync all card buttons for this product ID
                document.querySelectorAll(`.wishlist-button[data-product-id="${productId}"]`).forEach((btn) => {
                    btn.classList.toggle('is-active', !isRemoved);
                });

                // 3. Update Wishlist list HTML
                const currentWishlist = document.querySelector('.wishlist');
                if (currentWishlist) {
                    if (isRemoveBtn && listItem) {
                        listItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        listItem.style.opacity = '0';
                        listItem.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            listItem.remove();
                            if (count === 0) {
                                refreshWishlist();
                            }
                        }, 300);
                    } else {
                        refreshWishlist();
                    }
                }

                // 4. Notification
                if (result.data.message) {
                    showToast(result.data.message, false);
                }
            } else {
                showToast(result.data?.message || steel_wishlist_obj.i18n.error, true);
            }
        } catch (error) {
            button.classList.remove('is-loading');
            console.error('Wishlist request error:', error);
            showToast(steel_wishlist_obj.i18n.error, true);
        }
    });
});
