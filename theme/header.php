<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?php
    global $steel_theme_settings;
    $steel_theme_settings = steel_get_theme_settings();
    $wishlist_count       = count( steel_get_wishlist_ids() );
    $cart_count           = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ?>

    <!-- START HEADER -->
    <header class="header">
        <!-- middle header -->
        <div class="header__top">
            <div class="container header__inner">
                <a class="header__logo logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Homepage', 'steel-eshop' ); ?>">
                    <?php echo wp_get_attachment_image(
                        get_theme_mod('custom_logo'), 'medium', false,
                        ['loading' => 'eager', 'alt' => get_bloginfo( 'name' ),]
                    ); ?>
                </a>

                <nav aria-label="<?php esc_attr_e( 'Header Navigation', 'steel-eshop' ); ?>">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'header-pages-menu',
                        'menu_class'     => 'header__menu menu hidden-tablet',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'walker'         => new Steel_Nav_Menu(),
                    ] );
                    ?>
                </nav>

                <nav class="header__nav-buttons visible-tablet" aria-label="<?php esc_attr_e( 'Mobile Actions', 'steel-eshop' ); ?>">
                    <button class="header__nav-btn header__wishlist-button" data-modal="modal-wishlist" aria-label="<?php esc_attr_e( 'View wishlist', 'steel-eshop' ); ?>">
                        <span class="material-symbols material-symbols--filled">favorite</span>
                        <span class="header__wishlist-count header__nav-btn-count <?php echo $wishlist_count ? '' : 'visually-hidden'; ?>">
                            <?php echo esc_html( $wishlist_count ); ?>
                        </span>
                    </button>
                    <button class="header__nav-btn cart-button" data-modal="modal-cart" aria-label="<?php esc_attr_e( 'View cart', 'steel-eshop' ); ?>">
                        <span class="material-symbols material-symbols--filled">shopping_cart</span>
                        <span class="cart-button__count header__nav-btn-count <?php echo $cart_count ? '' : 'visually-hidden'; ?>">
                            <?php echo esc_html( $cart_count ); ?>
                        </span>
                    </button>
                    <button class="header__nav-btn burger-menu-button" data-modal="mobile-menu" aria-label="<?php esc_attr_e( 'Open mobile menu', 'steel-eshop' ); ?>">
                        <span class="material-symbols material-symbols--filled">density_medium</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- bottom header -->
        <div class="header__bottom">
            <div class="container header__inner">

                <div class="header__categories hidden-tablet">
                    <button class="header__categories-button <?php if ( function_exists('is_shop') && is_shop() ): echo 'sidebar-toggle-button'; endif; ?>" aria-label="<?php esc_attr_e( 'Toggle product catalog', 'steel-eshop' ); ?>">
                        <div class="header__categories-button-title">
                            <span class="material-symbols">widgets</span>
                            <?php _e( 'Product catalog', 'steel-eshop' ); ?>
                        </div>
                        <span class="material-symbols">stat_minus_1</span>
                    </button>
                    <nav class="header__categories-body" aria-label="<?php esc_attr_e( 'Categories Navigation', 'steel-eshop' ); ?>">
                        <?php steel_dropdown_categories(); ?>
                    </nav>
                </div>
                <!-- ./header__categories -->

                <?php get_product_search_form(); ?>

                <nav class="header__nav-buttons hidden-tablet" aria-label="<?php esc_attr_e( 'User Actions', 'steel-eshop' ); ?>">
                    <button class="header__nav-btn header__wishlist-button" data-modal="modal-wishlist" aria-label="<?php esc_attr_e( 'View wishlist', 'steel-eshop' ); ?>">
                        <span class="material-symbols material-symbols--filled">favorite</span>
                        <span class="header__wishlist-count header__nav-btn-count <?php echo $wishlist_count ? '' : 'visually-hidden'; ?>">
                            <?php echo esc_html( $wishlist_count ); ?>
                        </span>
                    </button>
                    <button class="header__nav-btn cart-button" data-modal="modal-cart" aria-label="<?php esc_attr_e( 'View cart', 'steel-eshop' ); ?>">
                        <span class="material-symbols material-symbols--filled">shopping_cart</span>
                        <span class="cart-button__count header__nav-btn-count <?php echo $cart_count ? '' : 'visually-hidden'; ?>">
                            <?php echo esc_html( $cart_count ); ?>
                        </span>
                    </button>
                </nav>

            </div>
        </div>

    </header>
    <!-- END HEADER -->

    <!-- START CONTENT -->
