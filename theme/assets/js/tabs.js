document.addEventListener('DOMContentLoaded', () => {
    const tabsContainers = document.querySelectorAll('.tabs');

    tabsContainers.forEach(container => {
        const triggers = container.querySelectorAll('.tabs__item');
        const panels = container.querySelectorAll('.tabs__panel');

        function activateTab(trigger, shouldScroll = false) {
            const targetId = trigger.getAttribute('data-target');
            if (!targetId) return;

            triggers.forEach(item => {
                item.classList.remove('is-active');
                const link = item.querySelector('.tabs__link');
                if (link) {
                    link.setAttribute('aria-selected', 'false');
                    link.setAttribute('tabindex', '-1');
                }
            });

            panels.forEach(panel => {
                panel.classList.remove('is-active');
            });

            trigger.classList.add('is-active');
            const activeLink = trigger.querySelector('.tabs__link');
            if (activeLink) {
                activeLink.setAttribute('aria-selected', 'true');
                activeLink.setAttribute('tabindex', '0');
            }

            const activePanel = container.querySelector(`#${targetId}`);
            if (activePanel) {
                activePanel.classList.add('is-active');
            }

            if (shouldScroll) {
                const tabsRect = container.getBoundingClientRect();
                const header = document.querySelector('.header');
                const headerHeight = header ? header.offsetHeight : 0;

                if (tabsRect.top < headerHeight) {
                    window.scrollTo({
                        top: window.scrollY + tabsRect.top - headerHeight,
                        behavior: 'smooth'
                    });
                }
            }
        }

        triggers.forEach((trigger, index) => {
            const link = trigger.querySelector('.tabs__link');
            if (!link) return;

            link.addEventListener('click', (e) => {
                e.preventDefault();
                activateTab(trigger, true);

                const targetId = trigger.getAttribute('data-target');
                if (targetId && window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', `#${targetId}`);
                }
            });

            // Keyboard navigation (ARIA tabs standard)
            link.addEventListener('keydown', (e) => {
                let newIndex = null;
                if (e.key === 'ArrowRight') {
                    newIndex = (index + 1) % triggers.length;
                } else if (e.key === 'ArrowLeft') {
                    newIndex = (index - 1 + triggers.length) % triggers.length;
                } else if (e.key === 'Home') {
                    newIndex = 0;
                } else if (e.key === 'End') {
                    newIndex = triggers.length - 1;
                }

                if (newIndex !== null) {
                    e.preventDefault();
                    const newTrigger = triggers[newIndex];
                    const newLink = newTrigger.querySelector('.tabs__link');
                    if (newLink) {
                        newLink.focus();
                        activateTab(newTrigger, false);
                    }
                }
            });
        });

        // Activate tab based on URL hash if present
        if (window.location.hash) {
            const rawHash = window.location.hash.replace('#', '');
            const matchingTrigger = Array.from(triggers).find(trigger => {
                const target = trigger.getAttribute('data-target');
                return target === rawHash || target === `tab-${rawHash}`;
            });
            if (matchingTrigger) {
                activateTab(matchingTrigger, false);
            }
        }
    });
});