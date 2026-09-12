/**
 * WooCommerce & Custom Notices Manager (Vanilla JS ES6+)
 */
function initNotices() {
    const AUTO_DISMISS_DELAY = 10000; // 10 seconds

    // 1. Remove ?add-to-cart= from the browser URL to avoid re-triggering upon refresh
    if (window.history && window.history.replaceState) {
        const currentUrl = new URL(window.location.href);
        if (currentUrl.searchParams.has('add-to-cart')) {
            currentUrl.searchParams.delete('add-to-cart');
            currentUrl.searchParams.delete('quantity');
            window.history.replaceState(null, '', currentUrl.pathname + currentUrl.search + currentUrl.hash);
        }
    }

    // 2. Dismiss a single notice smoothly
    function dismissNotice(notice) {
        if (!notice || notice.dataset.noticeDismissing === 'true') return;
        notice.dataset.noticeDismissing = 'true';

        // Clear timer if pending
        if (notice._dismissTimeout) {
            clearTimeout(notice._dismissTimeout);
            notice._dismissTimeout = null;
        }

        notice.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        notice.style.opacity = '0';
        notice.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            const wrapper = notice.closest('.woocommerce-notices-wrapper');
            notice.remove();
            if (wrapper && wrapper.children.length === 0) {
                wrapper.innerHTML = '';
            }
        }, 300);
    }

    // 3. Initialize notice elements (attach button & 10s timer)
    function initNoticeElement(notice) {
        if (!notice || notice.dataset.noticeInit === 'true') return;
        notice.dataset.noticeInit = 'true';

        // Inject close button if not already present from PHP template
        if (!notice.querySelector('.close-notice-btn')) {
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'close-notice-btn';
            closeBtn.setAttribute('aria-label', 'Close notice');
            closeBtn.innerHTML = '<span class="material-symbols" aria-hidden="true">close</span>';
            notice.appendChild(closeBtn);
        }

        // Setup 10-second auto-dismiss with hover pause
        let remainingTime = AUTO_DISMISS_DELAY;
        let startTime = Date.now();

        const startTimer = () => {
            startTime = Date.now();
            notice._dismissTimeout = setTimeout(() => {
                dismissNotice(notice);
            }, remainingTime);
        };

        const pauseTimer = () => {
            if (notice._dismissTimeout) {
                clearTimeout(notice._dismissTimeout);
                notice._dismissTimeout = null;
                remainingTime -= (Date.now() - startTime);
                if (remainingTime < 1000) remainingTime = 1000;
            }
        };

        startTimer();

        notice.addEventListener('mouseenter', pauseTimer);
        notice.addEventListener('mouseleave', () => {
            if (notice.dataset.noticeDismissing !== 'true') {
                startTimer();
            }
        });
    }

    function initAllNotices() {
        const notices = document.querySelectorAll('.notices:not([data-notice-init]), .woocommerce-notices-wrapper li:not([data-notice-init])');
        notices.forEach(initNoticeElement);
    }

    initAllNotices();

    // 4. Close notice button click delegation
    document.addEventListener('click', (event) => {
        const closeBtn = event.target.closest('.close-notice-btn');
        if (!closeBtn) return;

        const notice = closeBtn.closest('.notices, .woocommerce-notices-wrapper li');
        if (notice) {
            dismissNotice(notice);
        }
    });

    // 5. Observer for dynamically added WooCommerce notices (AJAX add-to-cart, cart updates, etc.)
    const observeNotices = () => {
        const wrappers = document.querySelectorAll('.woocommerce-notices-wrapper');
        wrappers.forEach((wrapper) => {
            if (wrapper.dataset.observerInit === 'true') return;
            wrapper.dataset.observerInit = 'true';

            const noticeObserver = new MutationObserver(() => {
                initAllNotices();
            });
            noticeObserver.observe(wrapper, { childList: true, subtree: true });
        });
    };

    observeNotices();

    // Also observe document.body for newly added wrappers
    const bodyObserver = new MutationObserver(() => {
        observeNotices();
        initAllNotices();
    });
    bodyObserver.observe(document.body, { childList: true, subtree: true });

    // 6. Programmatically display a notice in the floating toast container
    window.steelShowNotice = function(noticeHtml) {
        if (!noticeHtml || typeof noticeHtml !== 'string' || !noticeHtml.trim()) return;

        let wrapper = document.querySelector('.woocommerce-notices-wrapper');
        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.className = 'woocommerce-notices-wrapper';
            document.body.appendChild(wrapper);
            observeNotices();
        }

        const temp = document.createElement('div');
        temp.innerHTML = noticeHtml.trim();

        const innerWrapper = temp.querySelector('.woocommerce-notices-wrapper');
        const nodes = innerWrapper ? Array.from(innerWrapper.children) : Array.from(temp.children);

        if (nodes.length > 0) {
            nodes.forEach((node) => {
                wrapper.appendChild(node);
                initNoticeElement(node);
            });
        } else {
            wrapper.insertAdjacentHTML('beforeend', noticeHtml);
            initAllNotices();
        }
    };

    // 7. Listen for WooCommerce AJAX add-to-cart events -> open mini-cart modal
    const initWcAjaxListener = () => {
        if (window.jQuery) {
            window.jQuery(document.body).on('added_to_cart', (event, fragments, cart_hash, $button) => {
                if (typeof window.steelOpenCart === 'function') {
                    window.steelOpenCart($button && $button.length ? $button[0] : null);
                } else if (typeof window.steelOpenModal === 'function') {
                    window.steelOpenModal('modal-cart', 'no-scroll', $button && $button.length ? $button[0] : null);
                }
            });
        }
    };

    initWcAjaxListener();

    // 8. bfcache support: hide stale notices if loaded from cache
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            document.querySelectorAll('.notices, .woocommerce-notices-wrapper li').forEach((n) => n.remove());
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotices);
} else {
    initNotices();
}