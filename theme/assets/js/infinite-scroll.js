document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const loader = entry.target;
                const container = loader.previousElementSibling;

                if (!container) return;

                const mode = container.dataset.infiniteScroll;

                if (mode === 'pagination') {
                    loadFromPagination(container, loader);
                }

                if (mode === 'ajax') {
                    loadFromAjax(container, loader);
                }
            });
        },
        { rootMargin: '300px 0px' }
    );

    function initInfiniteScroll() {
        const loaders = document.querySelectorAll('[data-infinite-scroll-loader]');
        loaders.forEach((loader) => {
            observer.unobserve(loader);
            observer.observe(loader);
        });
    }

    initInfiniteScroll();

    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxComplete((event, xhr, settings) => {
            if (
                settings.data &&
                typeof settings.data === 'string' &&
                settings.data.includes('wpcAjaxAction=filter')
            ) {
                setTimeout(() => {
                    initInfiniteScroll();
                }, 50);
            }
        });
    }

    /* ==========================================================================
       СПОСІБ 1: PAGINATION (Завантаження HTML наступної сторінки)
       НЕ ЗМІНЮВАТИ! Використовується для каталогу товарів для збереження сортування.
       ========================================================================== */
    async function loadFromPagination(container, loader) {
        if (container.dataset.loading === 'true') return;

        const nextUrl = loader.dataset.nextUrl;
        if (!nextUrl) {
            removeLoader(loader);
            return;
        }

        container.dataset.loading = 'true';
        loader.classList.add('is-loading');

        try {
            const response = await fetch(nextUrl);
            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const html = await response.text();
            const documentParser = new DOMParser();
            const nextDocument = documentParser.parseFromString(html, 'text/html');

            const itemSelector = container.dataset.infiniteScrollItem;
            const nextItems = nextDocument.querySelectorAll(itemSelector);

            nextItems.forEach((item) => {
                container.append(item);
            });

            const nextLoader = nextDocument.querySelector('[data-infinite-scroll-loader]');
            if (nextLoader?.dataset.nextUrl) {
                loader.dataset.nextUrl = nextLoader.dataset.nextUrl;
            } else {
                removeLoader(loader);
            }
        } catch (error) {
            console.error('Infinite scroll pagination error:', error);
        } finally {
            container.dataset.loading = 'false';
            loader.classList.remove('is-loading');

            if (document.body.contains(loader)) {
                observer.unobserve(loader);
                observer.observe(loader);
            }
        }
    }

    /* ==========================================================================
       СПОСІБ 2: AJAX (Універсальне підвантаження записів/коментарів)
       Працює з data-атрибутами: data-type, data-post-type, data-template
       ========================================================================== */
    async function loadFromAjax(container, loader) {
        if (container.dataset.loading === 'true') return;

        const page = Number(container.dataset.page) + 1;
        const maxPages = Number(container.dataset.maxPages);

        if (page > maxPages) {
            removeLoader(loader);
            return;
        }

        container.dataset.loading = 'true';
        loader.classList.add('is-loading');

        const data = new FormData();
        data.append('action', 'st_load_more');
        data.append('nonce', stInfiniteScroll.nonce);
        
        // Збираємо параметри з HTML елемента container (<ul>)
        data.append('type', container.dataset.type || 'posts'); 
        data.append('post_type', container.dataset.postType || 'post'); 
        data.append('template', container.dataset.template || '');
        data.append('posts_per_page', container.dataset.postsPerPage || '');
        data.append('page', page);

        if (container.dataset.productId) {
            data.append('product_id', container.dataset.productId);
        }

        try {
            const response = await fetch(stInfiniteScroll.ajaxUrl, {
                method: 'POST',
                body: data,
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.data?.message || 'AJAX request failed.');
            }

            container.insertAdjacentHTML('beforeend', result.data.html);
            container.dataset.page = result.data.current;

            if (!result.data.has_more) {
                removeLoader(loader);
            }
        } catch (error) {
            console.error('Infinite scroll AJAX error:', error);
        } finally {
            container.dataset.loading = 'false';
            loader.classList.remove('is-loading');

            if (document.body.contains(loader)) {
                observer.unobserve(loader);
                observer.observe(loader);
            }
        }
    }

    function removeLoader(loader) {
        observer.unobserve(loader);
        loader.remove();
    }
});