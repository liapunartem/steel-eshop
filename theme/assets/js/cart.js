/**
 * Cart & Mini-cart Manager (Vanilla JS ES6+)
 */
(function () {
    let updateTimeout = null;

    // 1. Plus / Minus quantity buttons delegation
    document.addEventListener('click', (event) => {
        const minusBtn = event.target.closest('.quantity__button--minus');
        const plusBtn = event.target.closest('.quantity__button--plus');

        if (!minusBtn && !plusBtn) return;

        const btn = minusBtn || plusBtn;
        const container = btn.closest('.quantity');
        const input = container?.querySelector('.qty');

        if (!input) return;

        const min = input.min !== '' ? parseFloat(input.min) : 1;
        const max = input.max !== '' ? parseFloat(input.max) : Infinity;
        const step = input.step !== '' ? parseFloat(input.step) : 1;
        let currentVal = parseFloat(input.value) || min;

        if (minusBtn) {
            currentVal = Math.max(min, currentVal - step);
        } else if (plusBtn) {
            currentVal = Math.min(max, currentVal + step);
        }

        input.value = currentVal;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    function getAjaxUrl() {
        if (window.stCart?.ajaxUrl) {
            try {
                const parsed = new URL(window.stCart.ajaxUrl, window.location.origin);
                if (parsed.origin === window.location.origin) {
                    return parsed.href;
                }
                return parsed.pathname + parsed.search;
            } catch (e) {
                return '/wp-admin/admin-ajax.php';
            }
        }
        return '/wp-admin/admin-ajax.php';
    }

    // 2. Mini-cart quantity change with debouncing
    document.addEventListener('change', (event) => {
        if (!event.target.matches('.mini_cart_item .qty')) {
            return;
        }

        const input = event.target;
        const item = input.closest('.mini_cart_item');
        const removeButton = item?.querySelector('.remove_from_cart_button');

        if (!removeButton?.dataset.cart_item_key) return;

        clearTimeout(updateTimeout);
        updateTimeout = setTimeout(async () => {
            const ajaxUrl = getAjaxUrl();
            const nonce = window.stCart?.nonce || '';

            const data = new URLSearchParams();
            data.append('action', 'steel_update_mini_cart');
            data.append('cart_item_key', removeButton.dataset.cart_item_key);
            data.append('quantity', input.value);
            data.append('nonce', nonce);

            try {
                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    body: data,
                });
                const result = await response.json();

                if (result.success && window.jQuery) {
                    window.jQuery(document.body).trigger('wc_fragment_refresh');
                }
            } catch (error) {
                console.error('Mini cart update failed:', error);
            }
        }, 300);
    });

    // 3. Helper to build FormData for AJAX add to cart
    function getProductFormData(form, submitBtn) {
        const formData = new FormData(form);

        if (submitBtn && submitBtn.name && submitBtn.value) {
            formData.set(submitBtn.name, submitBtn.value);
        }

        const productId = formData.get('product_id') ||
                          formData.get('add-to-cart') ||
                          (submitBtn ? (submitBtn.value || submitBtn.getAttribute('value')) : null) ||
                          form.dataset.productId ||
                          form.querySelector('[name="add-to-cart"]')?.value ||
                          form.querySelector('[name="product_id"]')?.value;

        if (productId) {
            formData.set('product_id', productId);
        }

        // Delete 'add-to-cart' from formData so WooCommerce's
        // WC_Form_Handler::add_to_cart_action() on wp_loaded does not intercept the request and trigger a 302 redirect
        formData.delete('add-to-cart');

        let qty = formData.get('quantity');
        if (!qty || isNaN(parseFloat(qty)) || parseFloat(qty) <= 0) {
            const qtyInput = form.querySelector('.qty');
            qty = qtyInput ? qtyInput.value : 1;
            formData.set('quantity', qty || 1);
        }

        return formData;
    }

    // 4. Core AJAX Add to Cart submission handler
    async function handleAddToCart(form, submitBtn) {
        if (!form) return;

        if ((form.method && form.method.toLowerCase() === 'get') || form.classList.contains('no-ajax-cart')) {
            form.submit();
            return;
        }

        if (submitBtn && (submitBtn.disabled || submitBtn.classList.contains('is-loading'))) {
            return;
        }

        const formData = getProductFormData(form, submitBtn);

        // Validate variable products
        if (form.classList.contains('variations_form')) {
            const variationId = formData.get('variation_id');
            if (!variationId || parseInt(variationId, 10) === 0) {
                const msg = 'Будь ласка, оберіть параметри товару перед додаванням до кошика.';
                if (window.steelShowNotice) {
                    window.steelShowNotice(`
                        <div class="notices notices--error" role="alert">
                            <div class="notices__content">${msg}</div>
                            <button type="button" class="close-notice-btn" aria-label="Закрити сповіщення">
                                <span class="material-symbols" aria-hidden="true">close</span>
                            </button>
                        </div>
                    `);
                }
                return;
            }
        }

        const ajaxUrl = getAjaxUrl();
        const nonce   = window.stCart?.nonce || '';

        formData.set('action', 'steel_ajax_add_to_cart');
        formData.set('nonce', nonce);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('loading', 'is-loading');
        }

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (jsonErr) {
                console.error('Server returned non-JSON response:', text);
                throw jsonErr;
            }

            if (result.success) {
                // Update fragments (counters, mini-cart content)
                if (result.data?.fragments) {
                    Object.entries(result.data.fragments).forEach(([selector, html]) => {
                        document.querySelectorAll(selector).forEach((el) => {
                            el.outerHTML = html;
                        });
                    });
                }

                // Trigger WooCommerce fragment refresh & added_to_cart
                if (window.jQuery) {
                    window.jQuery(document.body).trigger('added_to_cart', [result.data?.fragments, result.data?.cart_hash, submitBtn ? window.jQuery(submitBtn) : null]);
                    window.jQuery(document.body).trigger('wc_fragment_refresh');
                }

                // Dispatch native CustomEvent for any vanilla listeners
                document.body.dispatchEvent(new CustomEvent('added_to_cart', {
                    bubbles: true,
                    detail: {
                        fragments: result.data?.fragments,
                        cart_hash: result.data?.cart_hash,
                        button: submitBtn,
                    },
                }));

                // Open mini-cart modal id="modal-cart" instead of notice
                if (typeof window.steelOpenCart === 'function') {
                    window.steelOpenCart(submitBtn);
                } else if (typeof window.steelOpenModal === 'function') {
                    window.steelOpenModal('modal-cart', 'no-scroll', submitBtn);
                } else {
                    const cartModal = document.getElementById('modal-cart');
                    if (cartModal) {
                        cartModal.classList.add('is-open');
                        cartModal.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('no-scroll');
                    }
                }
            } else {
                const errorHtml = result.data?.notices || `
                    <div class="notices notices--error" role="alert">
                        <div class="notices__content">Не вдалося додати товар до кошика. Спробуйте ще раз.</div>
                        <button type="button" class="close-notice-btn" aria-label="Закрити сповіщення">
                            <span class="material-symbols" aria-hidden="true">close</span>
                        </button>
                    </div>
                `;
                if (window.steelShowNotice) {
                    window.steelShowNotice(errorHtml);
                }
            }
        } catch (error) {
            console.error('AJAX add to cart failed:', error);
            if (window.steelShowNotice) {
                window.steelShowNotice(`
                    <div class="notices notices--error" role="alert">
                        <div class="notices__content">Помилка під час відправки запиту. Спробуйте пізніше.</div>
                        <button type="button" class="close-notice-btn" aria-label="Закрити сповіщення">
                            <span class="material-symbols" aria-hidden="true">close</span>
                        </button>
                    </div>
                `);
            }
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading', 'is-loading');
            }
        }
    }

    // 5. Intercept clicks on single add to cart buttons (Capture phase)
    document.addEventListener('click', (event) => {
        const btn = event.target.closest('.single_add_to_cart_button');
        if (!btn) return;

        const form = btn.closest('form.cart');
        if (!form) return;

        event.preventDefault();
        event.stopPropagation();
        handleAddToCart(form, btn);
    }, true);

    // 6. Intercept submit events on form.cart (Capture phase, e.g. Enter key)
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form.cart');
        if (!form) return;

        event.preventDefault();
        event.stopPropagation();
        const submitBtn = event.submitter || form.querySelector('.single_add_to_cart_button, button[type="submit"]');
        handleAddToCart(form, submitBtn);
    }, true);
})();