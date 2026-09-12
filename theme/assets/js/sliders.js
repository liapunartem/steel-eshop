/**
 * Sliders & Carousels Manager (Vanilla JS ES6+)
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swiper === 'undefined') {
        return;
    }

    // Hero slider
    if (document.querySelector('.hero-slider')) {
        new Swiper('.hero-slider', {
            loop: true,
            spaceBetween: 16,
            autoplay: {
                delay: 10000,
                disableOnInteraction: false,
            },
            speed: 600,
            effect: 'fade-in-left',
            pagination: {
                el: '.swiper__pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper__button--next',
                prevEl: '.swiper__button--prev',
            },
        });
    }

    // Product carousel
    if (document.querySelector('.product-carousel')) {
        new Swiper('.product-carousel', {
            slidesPerView: 'auto',
            spaceBetween: 10,
            loop: false,
            freeMode: true,
            pagination: {
                el: '.swiper__pagination',
                dynamicBullets: true,
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper__button--next',
                prevEl: '.swiper__button--prev',
            },
        });
    }

    // Category carousel
    if (document.querySelector('.category-carousel')) {
        new Swiper('.category-carousel', {
            slidesPerView: 2,
            spaceBetween: 5,
            loop: false,
            navigation: {
                nextEl: '.swiper__button--next',
                prevEl: '.swiper__button--prev',
            },
            breakpoints: {
                577: {
                    slidesPerView: 3,
                    spaceBetween: 14,
                },
                1025: {
                    slidesPerView: 4,
                    spaceBetween: 18,
                },
            },
        });
    }

    // Product gallery
    const swiperThumbsEl = document.querySelector('.swiper-thumbs');
    let swiperThumbs = null;
    if (swiperThumbsEl) {
        swiperThumbs = new Swiper(swiperThumbsEl, {
            breakpoints: {
                0: {
                    direction: 'horizontal',
                },
                1024: {
                    direction: 'vertical',
                },
            },
            spaceBetween: 10,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
        });
    }

    const productGalleryEl = document.querySelector('.product-gallery__slider');
    const productGalleryBox = document.querySelector('.product-gallery');
    let productGallery = null;

    if (productGalleryEl) {
        productGallery = new Swiper('.product-gallery__slider', {
            loop: true,
            autoHeight: true,
            spaceBetween: 16,
            speed: 600,
            effect: 'fade-in-left',
            navigation: {
                nextEl: '.swiper__button--next',
                prevEl: '.swiper__button--prev',
            },
            thumbs: {
                swiper: swiperThumbs || undefined,
            },
        });

        // Make productGallery globally available for gallery.js / Fancybox
        window.productGallery = productGallery;

        if (productGalleryBox) {
            const syncProductGalleryHeights = () => {
                const height = productGalleryEl.offsetHeight;
                productGalleryBox.style.setProperty('--galleryHeight', `${height}px`);
                productGalleryBox.style.setProperty('--galleryHeigt', `${height}px`);
            };

            window.addEventListener('load', syncProductGalleryHeights);
            const roProductGallery = new ResizeObserver(syncProductGalleryHeights);
            roProductGallery.observe(productGalleryEl);
        }
    }

    // Variations switch gallery image
    const variationsForm = document.querySelector('.variations_form');
    if (variationsForm && productGallery) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'current-image') {
                    const imageId = variationsForm.getAttribute('current-image');
                    if (!imageId) return;

                    const slides = Array.from(
                        document.querySelectorAll('.product-gallery__slider .swiper-wrapper > .swiper-slide:not(.swiper-slide-duplicate)')
                    );

                    const targetIndex = slides.findIndex(
                        (slide) => slide.getAttribute('product_img_id') === imageId
                    );

                    if (targetIndex !== -1) {
                        productGallery.slideToLoop(targetIndex);
                    }
                }
            });
        });

        observer.observe(variationsForm, {
            attributes: true,
            attributeFilter: ['current-image'],
        });
    }
});