/**
 * Sync overlay height with title height inside category cards.
 * For each `.category-card` the height of `.category-card__title`
 * is applied to `.category-card__overlay` via CSS variable.
 *
 * Runs on page load and window resize.
 */
function syncCategoryCardHeights() {
    const cards = document.querySelectorAll('.category-card');

    cards.forEach(card => {
        const source = card.querySelector('.category-card__title');
        const target = card.querySelector('.category-card__overlay');

        if (!source || !target) return;

        const height = source.offsetHeight;

        target.style.setProperty('--overlay-height', `${height}px`);
    });
}

window.addEventListener('load', syncCategoryCardHeights);

const roCatCards = new ResizeObserver(syncCategoryCardHeights);
roCatCards.observe(document.body);