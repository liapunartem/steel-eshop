/**
 * Footer Categories accordion for mobile (Vanilla JS)
 */
document.addEventListener('DOMContentLoaded', () => {
    const footerCategoriesTitle = document.querySelector('.footer__categories-title');
    const footerCategories = document.querySelector('.footer__categories');

    if (!footerCategoriesTitle || !footerCategories) {
        return;
    }

    footerCategoriesTitle.addEventListener('click', () => {
        footerCategories.classList.toggle('is-open');
    });
});
