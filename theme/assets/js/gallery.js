Fancybox.bind('[data-fancybox="product-gallery"]', {
    // synchronizes slider and fancybox
    on: {
        destroy: (fancybox) => {
            const index = fancybox.getSlide().index;
            
            productGallery.slideTo(index, 0);
        },

        "Carousel.change": (fancybox, carousel, to) => {
            productGallery.slideTo(to, 300);
        }
    }
});