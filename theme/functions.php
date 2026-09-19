<?php
defined( 'ABSPATH' ) || exit;

// Connecting template functions and hooks
require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/queries.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/woocommerce-hooks.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/class-steel-nav-menu.php';
require_once get_template_directory() . '/inc/class-steel-dropdown-categories.php';


// Theme version constant
if ( ! defined( 'THEME_VERSION' ) ) {
    define( 'THEME_VERSION', wp_get_theme()->get('Version') );
}


// Adding Theme support
add_action('after_setup_theme', function() {
    load_theme_textdomain( 'steel-eshop', get_stylesheet_directory() . '/languages' );

    add_theme_support( 'woocommerce' );

    add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );

    // Menu registration
    register_nav_menus(
        [
            'header-pages-menu' => __( 'Header pages menu', 'steel-eshop' ),
            'footer-pages-menu' => __( 'Footer pages menu', 'steel-eshop' ),
        ]
    );
});


// Completely disable default WooCommerce stylesheets (including block/shortcode render re-enqueues)
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// === SCRIPT CONNECTION HOOKS ===
add_action( 'wp_enqueue_scripts', 'steel_enqueue_style', 30 );
add_action( 'wp_enqueue_scripts', 'steel_enqueue_scripts', 35);
add_action( 'wp_footer', function() {
    wp_dequeue_style( 'woocommerce-layout' );
    wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_dequeue_style( 'woocommerce-general' );
    wp_dequeue_style( 'woocommerce-inline' );
}, 1 );

// Connecting styles
function steel_enqueue_style() {
    wp_dequeue_style( 'woocommerce-general' );
    wp_dequeue_style( 'woocommerce-layout' );
    wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_dequeue_style( 'woocommerce-inline' );
    wp_deregister_style( 'woocommerce-general' );
    wp_deregister_style( 'woocommerce-layout' );
    wp_deregister_style( 'woocommerce-smallscreen' );
    wp_deregister_style( 'woocommerce-inline' );

    wp_enqueue_style( 'Roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap', [], null );
    wp_enqueue_style( 'material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', [], null );

    $needs_swiper = is_front_page() || is_product() || is_shop() || is_product_taxonomy();
    if ( $needs_swiper ) {
        wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css', [], '12.0.0' );
    }

    if ( is_product() ) {
        wp_enqueue_style( 'fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css', [], '6.1.0' );
    }

    wp_enqueue_style( 'steel-main', get_template_directory_uri() . '/style.css', [], THEME_VERSION );
}

// Connecting scripts
function steel_enqueue_scripts() {
    $needs_swiper = is_front_page() || is_product() || is_shop() || is_product_taxonomy();
    if ( $needs_swiper ) {
        wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js', [], '12.0.0', true );
        wp_enqueue_script( 'steel-sliders', get_template_directory_uri() . '/assets/js/sliders.js', ['swiper-js'], THEME_VERSION, true );
    }
    
    if ( is_product() ) {
        wp_enqueue_script( 'fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js', [], '6.1.0', true );
        wp_enqueue_script( 'steel-gallery', get_template_directory_uri() . '/assets/js/gallery.js', ['fancybox-js'], THEME_VERSION, true );
        wp_enqueue_script( 'steel-add-to-cart', get_template_directory_uri() . '/assets/js/add-to-cart.js', ['jquery'], THEME_VERSION, true );
    }
    
    wp_enqueue_script( 'steel-card', get_template_directory_uri() . '/assets/js/card.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-modal', get_template_directory_uri() . '/assets/js/modal.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-mobile-menu', get_template_directory_uri() . '/assets/js/mobile-menu.js', ['steel-modal'], THEME_VERSION, true );
    wp_enqueue_script( 'steel-cart', get_template_directory_uri() . '/assets/js/cart.js', ['steel-modal'], THEME_VERSION, true );
    wp_localize_script( 'steel-cart', 'stCart', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'steel_cart_nonce' ),
    ] );

    wp_enqueue_script( 'steel-footer', get_template_directory_uri() . '/assets/js/footer.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-scroll', get_template_directory_uri() . '/assets/js/scroll.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-tabs', get_template_directory_uri() . '/assets/js/tabs.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-form', get_template_directory_uri() . '/assets/js/form.js', [], THEME_VERSION, true );
    wp_enqueue_script( 'steel-notices', get_template_directory_uri() . '/assets/js/notices.js', [], THEME_VERSION, true );

    wp_enqueue_script( 'steel-infinite-scroll', get_template_directory_uri() . '/assets/js/infinite-scroll.js', [], THEME_VERSION, true );
    wp_localize_script( 'steel-infinite-scroll', 'stInfiniteScroll', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'st_load_more' ),
    ] );

    wp_enqueue_script( 'steel-wishlist', get_template_directory_uri() . '/assets/js/wishlist.js', [], THEME_VERSION, true );
    wp_localize_script( 'steel-wishlist', 'steel_wishlist_obj', [
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'steel_wishlist_nonce' ),
		'i18n'     => [
			'added'   => __( 'Product added to wishlist', 'steel-eshop' ),
			'removed' => __( 'Product removed from wishlist', 'steel-eshop' ),
			'error'   => __( 'An error occurred. Please try again.', 'steel-eshop' ),
        ],
	] );

    if ( is_front_page() ) {
        wp_enqueue_script( 'steel-sidebar', get_template_directory_uri() . '/assets/js/sidebar.js', [], THEME_VERSION, true );
    } else {
        wp_enqueue_script( 'steel-header-catalog', get_template_directory_uri() . '/assets/js/header-catalog.js', [], THEME_VERSION, true );
    }
}

// add_filter( 'print_styles_array', function( $queued ) {
//     $target_styles = [
//         'wpc-filter-everything',
//         'wpc-filter-everything-custom'
//     ];

//     foreach ( $target_styles as $style ) {
//         if ( ( $key = array_search( $style, $queued ) ) !== false ) {
//             unset( $queued[$key] );
//         }
//     }
//     return $queued;
// }, 9999 );