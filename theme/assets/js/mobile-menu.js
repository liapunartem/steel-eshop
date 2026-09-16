/**
 * Mobile Menu Controller (Category Accordion)
 */
function initMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    if (!mobileMenu) return;

    mobileMenu.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.mobile-menu__category-toggle');
        if (toggleBtn) {
            e.preventDefault();
            const item = toggleBtn.closest('.mobile-menu__category-item');
            if (item) {
                const isOpen = item.classList.toggle('is-expanded');
                toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileMenu);
} else {
    initMobileMenu();
}
