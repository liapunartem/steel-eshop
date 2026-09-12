document.addEventListener('DOMContentLoaded', () => {
    const tabsContainers = document.querySelectorAll('.tabs');

    tabsContainers.forEach(container => {
        const triggers = container.querySelectorAll('.tabs__item');
        const panels = container.querySelectorAll('.tabs__panel');

        triggers.forEach(trigger => {
            trigger.querySelector('.tabs__link').addEventListener('click', (e) => {
                e.preventDefault();

                const targetId = trigger.getAttribute('data-target');

                triggers.forEach(item => {
                    item.classList.remove('is-active');
                });

                panels.forEach(panel => {
                    panel.classList.remove('is-active');
                });

                trigger.classList.add('is-active');

                const activePanel = container.querySelector(`#${targetId}`);

                if (activePanel) {
                    activePanel.classList.add('is-active');
                }

                const tabsRect = container.getBoundingClientRect();

                const header = document.querySelector('.header');
                const headerHeight = header ? header.offsetHeight : 0;

                if (tabsRect.top < headerHeight) {
                    window.scrollTo({
                        top: window.scrollY + tabsRect.top - headerHeight,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
});