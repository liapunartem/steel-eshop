<?php
defined( 'ABSPATH' ) || exit;

/**
 * Remove default WooCommerce components from hooks because the theme renders
 * them in specific layout containers.
 */
add_action( 'init', function () {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
} );

// Adding a custom structure for breadcrumbs
add_filter( 'woocommerce_breadcrumb_defaults', function ( $defaults ) {
    return [
        'delimiter'   => '',
        'wrap_before' => '<nav class="breadcrumb scroll-shadow" aria-label="' . esc_attr__( 'Хлібні крихти', 'steel-eshop' ) . '"><div class="scroll-shadow__left"></div><ol class="breadcrumb__list" data-check-scroll="horizontal">',
        'wrap_after'  => '</ol><div class="scroll-shadow__right is-visible"></div></nav>',
        'before'      => '<li class="breadcrumb__item">',
        'after'       => '</li>',
        'home'        => __( 'Головна', 'steel-eshop' ),
    ];
});

add_filter( 'woocommerce_get_breadcrumb', function ( $crumbs ) {

    if ( ! is_search() ) {
        return $crumbs;
    }

    foreach ( $crumbs as $key => $crumb ) {
        if ( isset( $crumb[1] ) && str_ends_with( untrailingslashit( $crumb[1] ), '/shop' ) ) {
            unset( $crumbs[ $key ] );
        }
    }

    return array_values( $crumbs );
} );

add_filter( 'woocommerce_catalog_orderby', function( $defaults ) {
    return [
        'menu_order' => __( 'За замовчуванням', 'steel-eshop' ),
        'popularity' => __( 'За популярністю', 'steel-eshop' ),
        'date'       => __( 'Новинки', 'steel-eshop' ),
        'price'      => __( 'Від дешевих до дорогих', 'steel-eshop' ),
        'price-desc' => __( 'Від дорогих до дешевих', 'steel-eshop' ),
    ];
});



/**
 * Replace mini cart quantity text with quantity input.
 */
add_filter(
    'woocommerce_widget_cart_item_quantity',
    'steel_mini_cart_quantity_input',
    10,
    3
);

function steel_mini_cart_quantity_input( $html, $cart_item, $cart_item_key ) {

    $_product = $cart_item['data'];

    if ( ! $_product || ! $_product->exists() ) {
        return $html;
    }

    ob_start();

    woocommerce_quantity_input(
        [
            'input_name'  => 'cart[' . $cart_item_key . '][qty]',
            'input_value' => $cart_item['quantity'],
            'min_value'   => 1,
            'max_value'   => $_product->get_max_purchase_quantity(),
        ],
        $_product
    );

    return ob_get_clean();
}

/**
 * Add minus button before quantity input.
 */
add_action( 'woocommerce_before_quantity_input_field', function () {
    echo '<button type="button" class="quantity__button quantity__button--minus" aria-label="' . esc_attr__( 'Зменшити кількість', 'steel-eshop' ) . '">−</button>';
});


/**
 * Add plus button after quantity input.
 */
add_action('woocommerce_after_quantity_input_field', function () {
    echo '<button type="button" class="quantity__button quantity__button--plus" aria-label="' . esc_attr__( 'Збільшити кількість', 'steel-eshop' ) . '">+</button>';
});



add_action(
    'wp_ajax_steel_update_mini_cart',
    'steel_update_mini_cart'
);

add_action(
    'wp_ajax_nopriv_steel_update_mini_cart',
    'steel_update_mini_cart'
);

function steel_update_mini_cart() {
    check_ajax_referer( 'steel_cart_nonce', 'nonce' );

    $key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    $qty = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;

    if ( $key && $qty > 0 && WC()->cart ) {
        WC()->cart->set_quantity( $key, $qty, true );
    }

    wp_send_json_success();
}


/**
 * Update cart counter fragment.
 */
add_filter(
    'woocommerce_add_to_cart_fragments',
    'steel_cart_count_fragment'
);

function steel_cart_count_fragment( $fragments ) {
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ob_start();
    ?>

    <span class="cart-button__count header__nav-btn-count <?php echo $count ? '' : 'visually-hidden'; ?>">
        <?php echo esc_html( $count ); ?>
    </span>

    <?php

    $fragments['.cart-button__count'] = ob_get_clean();

    return $fragments;
}

/**
 * Implement Post-Redirect-Get (PRG) pattern for add-to-cart actions
 * to prevent duplicate additions and re-showing notices on page refresh.
 */
add_filter( 'woocommerce_add_to_cart_redirect', function( $url, $adding_to_cart = null ) {
    if ( ! empty( $adding_to_cart ) && ! $url && 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ) ) {
        $referer = wp_get_referer();
        $target  = $referer ? $referer : home_url( add_query_arg( [] ) );
        $url     = remove_query_arg( [ 'add-to-cart', 'quantity' ], $target );
    }
    return $url;
}, 10, 2 );

/**
 * Prevent WC_Form_Handler from executing on our AJAX add-to-cart requests.
 * WC_Form_Handler hooks to wp_loaded (priority 20) and triggers a 302 redirect
 * if $_REQUEST['add-to-cart'] is present in the request.
 */
add_action( 'init', function() {
    if ( isset( $_REQUEST['action'] ) && 'steel_ajax_add_to_cart' === $_REQUEST['action'] ) {
        remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );
    }
}, 5 );

/**
 * Native AJAX Add to Cart handler for single product forms (simple, variable, grouped).
 */
add_action( 'wp_ajax_steel_ajax_add_to_cart', 'steel_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_steel_ajax_add_to_cart', 'steel_ajax_add_to_cart' );

function steel_ajax_add_to_cart() {
    if ( ! check_ajax_referer( 'steel_cart_nonce', 'nonce', false ) ) {
        if ( ob_get_length() ) {
            ob_clean();
        }
        wp_send_json_error( [
            'notices' => '<div class="notices notices--error" role="alert"><div class="notices__content">' . esc_html__( 'Помилка безпеки. Будь ласка, оновіть сторінку.', 'steel-eshop' ) . '</div><button type="button" class="close-notice-btn" aria-label="' . esc_attr__( 'Закрити сповіщення', 'steel-eshop' ) . '"><span class="material-symbols" translate="no" aria-hidden="true">close</span></button></div>',
        ] );
    }

    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id && isset( $_POST['add-to-cart'] ) ) {
        $product_id = absint( $_POST['add-to-cart'] );
    }

    $quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
    if ( $quantity <= 0 ) {
        $quantity = 1;
    }

    $variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
    $variations   = [];

    foreach ( $_POST as $key => $value ) {
        if ( str_starts_with( $key, 'attribute_' ) ) {
            $variations[ sanitize_title( wp_unslash( $key ) ) ] = sanitize_text_field( wp_unslash( $value ) );
        }
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        if ( ob_get_length() ) {
            ob_clean();
        }
        wp_send_json_error( [
            'notices' => '<div class="notices notices--error" role="alert"><div class="notices__content">' . esc_html__( 'Товар не знайдено.', 'steel-eshop' ) . '</div><button type="button" class="close-notice-btn" aria-label="' . esc_attr__( 'Закрити сповіщення', 'steel-eshop' ) . '"><span class="material-symbols" translate="no" aria-hidden="true">close</span></button></div>',
        ] );
    }

    // Check variable product options
    if ( $product->is_type( 'variable' ) && ! $variation_id ) {
        if ( ob_get_length() ) {
            ob_clean();
        }
        wp_send_json_error( [
            'notices' => '<div class="notices notices--error" role="alert"><div class="notices__content">' . esc_html__( 'Будь ласка, оберіть параметри товару перед додаванням у кошик.', 'steel-eshop' ) . '</div><button type="button" class="close-notice-btn" aria-label="' . esc_attr__( 'Закрити сповіщення', 'steel-eshop' ) . '"><span class="material-symbols" translate="no" aria-hidden="true">close</span></button></div>',
        ] );
    }

    // Handle grouped products
    if ( $product->is_type( 'grouped' ) && isset( $_POST['quantity'] ) && is_array( $_POST['quantity'] ) ) {
        $added_any = false;
        foreach ( $_POST['quantity'] as $item_id => $qty ) {
            $qty = wc_stock_amount( wp_unslash( $qty ) );
            if ( $qty > 0 ) {
                if ( false !== WC()->cart->add_to_cart( absint( $item_id ), $qty ) ) {
                    $added_any = true;
                }
            }
        }

        if ( $added_any ) {
            wc_add_to_cart_message( array_filter( $_POST['quantity'] ), true );

            ob_start();
            wc_print_notices();
            $notices_html = ob_get_clean();
            wc_clear_notices();

            ob_start();
            woocommerce_mini_cart( [ 'list_class' => '' ] );
            $mini_cart_html = ob_get_clean();

            if ( ob_get_length() ) {
                ob_clean();
            }

            wp_send_json_success( [
                'notices'   => $notices_html,
                'cart_hash' => WC()->cart->get_cart_hash(),
                'fragments' => apply_filters(
                    'woocommerce_add_to_cart_fragments',
                    [
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart_html . '</div>',
                    ]
                ),
            ] );
        }
    }

    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

    if ( $passed_validation ) {
        $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );

        if ( $cart_item_key ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            do_action( 'internal_woocommerce_cart_item_added_from_user_request', $variation_id ? $variation_id : $product_id, $quantity );

            wc_add_to_cart_message( [ $product_id => $quantity ], true );

            ob_start();
            wc_print_notices();
            $notices_html = ob_get_clean();
            wc_clear_notices();

            ob_start();
            woocommerce_mini_cart( [ 'list_class' => '' ] );
            $mini_cart_html = ob_get_clean();

            if ( ob_get_length() ) {
                ob_clean();
            }

            $data = [
                'notices'   => $notices_html,
                'cart_hash' => WC()->cart->get_cart_hash(),
                'fragments' => apply_filters(
                    'woocommerce_add_to_cart_fragments',
                    [
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart_html . '</div>',
                    ]
                ),
            ];

            wp_send_json_success( $data );
        }
    }

    // Collect errors if any
    ob_start();
    wc_print_notices();
    $notices_html = ob_get_clean();
    wc_clear_notices();

    if ( empty( $notices_html ) ) {
        $notices_html = '<div class="notices notices--error" role="alert"><div class="notices__content">' . esc_html__( 'Не вдалося додати товар у кошик.', 'steel-eshop' ) . '</div><button type="button" class="close-notice-btn" aria-label="' . esc_attr__( 'Закрити сповіщення', 'steel-eshop' ) . '"><span class="material-symbols" translate="no" aria-hidden="true">close</span></button></div>';
    }

    if ( ob_get_length() ) {
        ob_clean();
    }

    wp_send_json_error( [
        'notices' => $notices_html,
    ] );
}

/**
 * Remove billing address from My Account addresses.
 */
add_filter( 'woocommerce_my_account_get_addresses', 'steel_remove_billing_address_from_my_account', 10, 2 );
function steel_remove_billing_address_from_my_account( $addresses, $customer_id ) {
    unset( $addresses['billing'] );
    if ( ! isset( $addresses['shipping'] ) ) {
        $addresses['shipping'] = __( 'Адреса доставки', 'steel-eshop' );
    }
    return $addresses;
}

/**
 * Redirect /my-account/edit-address/billing to /my-account/edit-address/shipping.
 */
add_action( 'template_redirect', 'steel_redirect_billing_address_edit' );
function steel_redirect_billing_address_edit() {
    if ( is_account_page() && is_wc_endpoint_url( 'edit-address' ) ) {
        global $wp;
        if ( isset( $wp->query_vars['edit-address'] ) && 'billing' === $wp->query_vars['edit-address'] ) {
            wp_safe_redirect( wc_get_endpoint_url( 'edit-address', 'shipping' ) );
            exit;
        }
    }
}

/**
 * Update My Account addresses description.
 */
add_filter( 'woocommerce_my_account_my_address_description', function() {
    return esc_html__( 'Ця адреса буде використовуватися за замовчуванням на сторінці оформлення замовлення.', 'steel-eshop' );
} );

/**
 * Rename and translate account menu items into Ukrainian.
 */
add_filter( 'woocommerce_account_menu_items', function( $items ) {
    $translated_items = [
        'dashboard'       => __( 'Панель керування', 'steel-eshop' ),
        'orders'          => __( 'Замовлення', 'steel-eshop' ),
        'downloads'       => __( 'Завантаження', 'steel-eshop' ),
        'edit-address'    => __( 'Адреса доставки', 'steel-eshop' ),
        'payment-methods' => __( 'Способи оплати', 'steel-eshop' ),
        'edit-account'    => __( 'Деталі облікового запису', 'steel-eshop' ),
        'customer-logout' => __( 'Вийти', 'steel-eshop' ),
    ];

    foreach ( $translated_items as $endpoint => $translated_label ) {
        if ( isset( $items[ $endpoint ] ) ) {
            $items[ $endpoint ] = $translated_label;
        }
    }

    return $items;
}, 20 );

/**
 * Localize product data tabs into Ukrainian.
 */
add_filter( 'woocommerce_product_tabs', function( $tabs ) {
    if ( isset( $tabs['description'] ) ) {
        $tabs['description']['title'] = __( 'Опис', 'steel-eshop' );
    }
    if ( isset( $tabs['additional_information'] ) ) {
        $tabs['additional_information']['title'] = __( 'Додаткова інформація', 'steel-eshop' );
    }
    if ( isset( $tabs['reviews'] ) ) {
        global $product;
        $count = $product ? (int) $product->get_review_count() : 0;
        $tabs['reviews']['title'] = sprintf( __( 'Відгуки (%d)', 'steel-eshop' ), $count );
    }
    return $tabs;
}, 98 );

/**
 * Localize account orders table columns.
 */
add_filter( 'woocommerce_account_orders_columns', function( $columns ) {
    return [
        'order-number'  => __( 'Замовлення', 'steel-eshop' ),
        'order-date'    => __( 'Дата', 'steel-eshop' ),
        'order-status'  => __( 'Статус', 'steel-eshop' ),
        'order-total'   => __( 'Сума', 'steel-eshop' ),
        'order-actions' => __( 'Дії', 'steel-eshop' ),
    ];
} );

/**
 * Localize account orders table actions.
 */
add_filter( 'woocommerce_my_account_my_orders_actions', function( $actions, $order ) {
    if ( isset( $actions['pay'] ) ) {
        $actions['pay']['name'] = __( 'Оплатити', 'steel-eshop' );
    }
    if ( isset( $actions['view'] ) ) {
        $actions['view']['name'] = __( 'Переглянути', 'steel-eshop' );
    }
    if ( isset( $actions['cancel'] ) ) {
        $actions['cancel']['name'] = __( 'Скасувати', 'steel-eshop' );
    }
    return $actions;
}, 10, 2 );

