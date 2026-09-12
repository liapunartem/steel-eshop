/**
 * Accessible Modal & Offcanvas Manager (WCAG 2.2 AA)
 */
function initModal() {
    let lastActiveTrigger = null;

    const focusableSelectors = 'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';

    function getFocusableElements(container) {
        return Array.from(container.querySelectorAll(focusableSelectors));
    }

    function openModal(modal, scrollClass, triggerBtn = null) {
        lastActiveTrigger = triggerBtn || document.activeElement;
        
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        modal.dataset.activeLockClass = scrollClass;
        document.body.classList.add(scrollClass);

        if (triggerBtn) {
            triggerBtn.setAttribute('aria-expanded', 'true');
        }

        // Focus first focusable element or close button
        requestAnimationFrame(() => {
            const focusables = getFocusableElements(modal);
            const closeBtn = modal.querySelector('.modal__close-btn');
            if (closeBtn) {
                closeBtn.focus();
            } else if (focusables.length) {
                focusables[0].focus();
            } else {
                modal.focus();
            }
        });
    }

    function closeModal(modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        
        const classToRemove = modal.dataset.activeLockClass || 'no-scroll';
        document.body.classList.remove(classToRemove);
        delete modal.dataset.activeLockClass;

        if (lastActiveTrigger && typeof lastActiveTrigger.focus === 'function') {
            lastActiveTrigger.setAttribute('aria-expanded', 'false');
            lastActiveTrigger.focus();
            lastActiveTrigger = null;
        }
    }

    function toggleModal(modal, scrollClass, triggerBtn) {
        if (modal.classList.contains('is-open')) {
            closeModal(modal);
        } else {
            openModal(modal, scrollClass, triggerBtn);
        }
    }

    // Expose programmatic modal methods
    window.steelOpenModal = function (modalId, scrollClass = 'no-scroll', triggerBtn = null) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (modal) {
            openModal(modal, scrollClass, triggerBtn);
        }
    };

    window.steelCloseModal = function (modalId) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (modal) {
            closeModal(modal);
        }
    };

    window.steelOpenCart = function (triggerBtn = null) {
        window.steelOpenModal('modal-cart', 'no-scroll', triggerBtn);
    };

    // Open button clicks
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-modal]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const targetId = btn.dataset.modal;
        const modal = document.getElementById(targetId);
        const scrollClass = btn.getAttribute('data-lock-class') || 'no-scroll';

        if (modal) toggleModal(modal, scrollClass, btn);
    });

    // Close button & backdrop clicks
    document.addEventListener('click', function (e) {
        const closeBtn = e.target.closest('.modal__close-btn');
        const backdrop = e.target.closest('.modal__backdrop');
        
        if (!closeBtn && !backdrop) return;

        const modal = e.target.closest('.modal');
        if (modal) closeModal(modal);
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            const openModalEl = document.querySelector('.modal.is-open');
            if (openModalEl) {
                closeModal(openModalEl);
            }
        }
    });

    // Trap focus inside modal when tabbing
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab') return;

        const openModalEl = document.querySelector('.modal.is-open');
        if (!openModalEl) return;

        const focusables = getFocusableElements(openModalEl);
        if (!focusables.length) {
            e.preventDefault();
            return;
        }

        const firstFocusable = focusables[0];
        const lastFocusable = focusables[focusables.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModal);
} else {
    initModal();
}