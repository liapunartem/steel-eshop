/**
 * Universal scroll tracker for elements with [data-check-scroll]
 */
const initSmartScrollTracker = () => {
    const scrollContainers = document.querySelectorAll('[data-check-scroll="horizontal"]:not([data-scroll-initialized])');

    if (!scrollContainers.length) return;

    const updateVisuals = (el) => {
        const wrapper = el.closest('.scroll-shadow') || el.parentElement;
        if (!wrapper) return;
        
        const shadowLeft = wrapper.querySelector('.scroll-shadow__left');
        const shadowRight = wrapper.querySelector('.scroll-shadow__right');

        const currentScroll = el.scrollLeft;
        const maxScroll = el.scrollWidth - el.clientWidth;
        const hasScroll = el.scrollWidth > el.clientWidth;

        const showLeft = hasScroll && currentScroll > 5;
        const showRight = hasScroll && currentScroll < maxScroll - 5;

        if (shadowLeft) {
            shadowLeft.classList.toggle('is-visible', showLeft);
        }
        if (shadowRight) {
            shadowRight.classList.toggle('is-visible', showRight);
        }

        el.classList.toggle('has-scroll-left', showLeft);
        el.classList.toggle('has-scroll-right', showRight);
        el.classList.toggle('is-scrollable', hasScroll);
    };

    const resizeObserver = new ResizeObserver((entries) => {
        entries.forEach((entry) => updateVisuals(entry.target));
    });

    scrollContainers.forEach((el) => {
        el.dataset.scrollInitialized = 'true';
        updateVisuals(el);
        resizeObserver.observe(el);
        el.addEventListener('scroll', () => updateVisuals(el), { passive: true });
    });
};

document.addEventListener('DOMContentLoaded', initSmartScrollTracker);

const feObserver = new MutationObserver((mutations) => {
    for (let mutation of mutations) {
        if (mutation.type === 'childList') {
            const hasNewScrolls = document.querySelector('[data-check-scroll="horizontal"]:not([data-scroll-initialized])');
            if (hasNewScrolls) {
                initSmartScrollTracker();
                break;
            }
        }
    }
});

if (document.body) {
    feObserver.observe(document.body, {
        childList: true,
        subtree: true,
    });
} else {
    document.addEventListener('DOMContentLoaded', () => {
        feObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });
    });
}

/**
 * Hiding header when scrolling down
 */ 
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.header');
    if (!header) return;

    let lastScrollY = window.scrollY;
    const root = document.documentElement;

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;
        const headerHeight = header.offsetHeight;
        
        if (currentScrollY > lastScrollY && currentScrollY > headerHeight) {
            root.style.setProperty('--st-top-header-position', `-${headerHeight / 16}rem`);
        } else {
            root.style.setProperty('--st-top-header-position', '0rem');
        }
        
        lastScrollY = currentScrollY;
    }, { passive: true });
});