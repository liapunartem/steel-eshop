// Control opening the categories drop-down menu in the header
const headerCategoriesBtn = document.querySelector('.header__categories-button');
const headerCategoriesBady = document.querySelector('.header__categories-body');

function openHeaderCategories() {
    headerCategoriesBady.classList.add('is-open');
}

function closeHeaderCategories() {
    headerCategoriesBady.classList.remove('is-open');
}

function toggleHeaderCategories(event) {
    event.stopPropagation();

    headerCategoriesBady.classList.toggle('is-open');
}

headerCategoriesBtn.addEventListener('click', toggleHeaderCategories);

document.addEventListener('click', function(event) {
    const clickInsideMenu = headerCategoriesBady.contains(event.target);
    const clickOnButton = headerCategoriesBtn.contains(event.target);

    if (!clickInsideMenu && !clickOnButton) {
        closeHeaderCategories();
    }
});
