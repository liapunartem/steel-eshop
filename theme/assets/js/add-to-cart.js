/**
 * WooCommerce Custom Variations UI Manager
 */
var WCCustomVariations = {
    // 1. Кеш селекторів (щоб не шукати елементи в DOM щоразу)
    selectors: {
        form: '.variations_form',
        sku: '#js-sku-display',
        availability: '#js-availability-display',
        price: '#js-price-display'
    },

    // 2. Головний метод ініціалізації
    init: function() {
        var $form = jQuery(this.selectors.form);
        
        // Якщо форми на сторінці немає, нічого не робимо
        if (!$form.length) return;

        // Запускаємо наші модулі та передаємо їм форму
        this.initSkuModule($form);
        this.initAvailabilityModule($form);
        this.initPriceModule($form);
    },

    /**
     * МОДУЛЬ 1: Керування відображенням SKU
     */
    initSkuModule: function($form) {
        var $skuDisplay = jQuery(this.selectors.sku);
        if (!$skuDisplay.length) return;

        // Зберігаємо дефолтний SKU
        var defaultSku = $skuDisplay.data('default-sku') || $skuDisplay.text();

        $form.on('found_variation', function(event, variation) {
            if (variation && variation.sku) {
                $skuDisplay.text(variation.sku);
            } else {
                $skuDisplay.text(defaultSku);
            }
        });

        $form.on('reset_data', function() {
            $skuDisplay.text(defaultSku);
        });
    },

    /**
     * МОДУЛЬ 2: Керування відображенням наявності (Availability)
     */
    initAvailabilityModule: function($form) {
        var $availabilityDisplay = jQuery(this.selectors.availability);
        if (!$availabilityDisplay.length) return;

        // Коли варіацію знайдено
        $form.on('found_variation', function(event, variation) {
            if (variation && variation.availability_html) {
                $availabilityDisplay.html(variation.availability_html);
            } else {
                $availabilityDisplay.html('');
            }
        });

        // Коли користувач скинув вибір
        $form.on('reset_data', function() {
            $availabilityDisplay.html('');
        });
    },

    /**
     * МОДУЛЬ 3: Керування відображенням ціни (price)
     */
    initPriceModule: function($form) {
        var $priceDisplay = jQuery(this.selectors.price);
        if (!$priceDisplay.length) return;

        // Коли варіацію знайдено
        $form.on('found_variation', function(event, variation) {
            if (variation && variation.price_html) {
                $priceDisplay.html(variation.price_html);
            } else {
                $priceDisplay.html('');
            }
        });

        // Коли користувач скинув вибір
        $form.on('reset_data', function() {
            $priceDisplay.html('');
        });
    },

};

// Запуск архітектури після повного завантаження DOM
jQuery(document).ready(function() {
    WCCustomVariations.init();
});