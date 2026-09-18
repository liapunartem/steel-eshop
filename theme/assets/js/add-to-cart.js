/**
 * WooCommerce Custom Variations UI Manager (Vanilla JS ES6+)
 */
(function () {
    function initVariationsUI() {
        const form = document.querySelector('.variations_form');
        if (!form) return;

        const skuDisplay = document.getElementById('js-sku-display');
        const availabilityDisplay = document.getElementById('js-availability-display');
        const priceDisplay = document.getElementById('js-price-display');

        const defaultSku = skuDisplay ? (skuDisplay.dataset.defaultSku || skuDisplay.textContent.trim()) : '';

        const handleFoundVariation = (variation) => {
            if (skuDisplay) {
                skuDisplay.textContent = variation?.sku || defaultSku;
            }
            if (availabilityDisplay) {
                availabilityDisplay.innerHTML = variation?.availability_html || '';
            }
            if (priceDisplay) {
                priceDisplay.innerHTML = variation?.price_html || '';
            }
        };

        const handleResetData = () => {
            if (skuDisplay) {
                skuDisplay.textContent = defaultSku;
            }
            if (availabilityDisplay) {
                availabilityDisplay.innerHTML = '';
            }
            if (priceDisplay) {
                priceDisplay.innerHTML = '';
            }
        };

        // WooCommerce variations script triggers jQuery custom events on variations form
        if (window.jQuery) {
            window.jQuery(form)
                .on('found_variation', (event, variation) => {
                    handleFoundVariation(variation);
                })
                .on('reset_data', () => {
                    handleResetData();
                });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVariationsUI);
    } else {
        initVariationsUI();
    }
})();