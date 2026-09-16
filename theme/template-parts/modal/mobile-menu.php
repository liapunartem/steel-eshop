<?php
defined( 'ABSPATH' ) || exit;

global $steel_theme_settings;
if ( ! isset( $steel_theme_settings ) && function_exists( 'steel_get_theme_settings' ) ) {
    $steel_theme_settings = steel_get_theme_settings();
}

$is_logged_in = is_user_logged_in();
$account_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();

$store_address = get_option( 'woocommerce_store_address' );
$store_city    = get_option( 'woocommerce_store_city' );
$store_email   = get_option( 'woocommerce_pos_store_email' ) ?: get_option( 'admin_email' );
$store_phone   = get_option( 'woocommerce_pos_store_phone' );

$categories_tree = function_exists( 'steel_get_dropdown_categories_hierarchy' ) ? steel_get_dropdown_categories_hierarchy() : [];
$parents         = $categories_tree['parents'] ?? [];
$children        = $categories_tree['children'] ?? [];
?>

<div class="modal modal--offcanvas" id="mobile-menu" role="dialog" aria-modal="true" aria-labelledby="modal-mobile-menu-title" aria-hidden="true">
    <div class="modal__backdrop"></div>

    <!-- MAIN BODY -->
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-mobile-menu-title"><?php _e( 'Menu', 'steel-eshop' ); ?></span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Close mobile menu', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled" translate="no">close</span>
            </button>
        </div>

        <div class="mobile-menu__content">

            <!-- Account block -->
            <div class="mobile-menu__account">
                <?php if ( $is_logged_in ) :
                    $current_user = wp_get_current_user();
                ?>
                    <div class="mobile-menu__account-user">
                        <div class="mobile-menu__account-avatar">
                            <span class="material-symbols material-symbols--filled" translate="no">account_circle</span>
                        </div>
                        <div class="mobile-menu__account-info">
                            <span class="mobile-menu__account-greeting"><?php _e( 'Hello,', 'steel-eshop' ); ?></span>
                            <span class="mobile-menu__account-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                        </div>
                    </div>
                    <div class="mobile-menu__account-actions">
                        <a href="<?php echo esc_url( $account_url ); ?>" class="mobile-menu__account-link">
                            <span class="material-symbols" translate="no">person</span>
                            <?php _e( 'My account', 'steel-eshop' ); ?>
                        </a>
                        <a href="<?php echo esc_url( function_exists( 'wc_logout_url' ) ? wc_logout_url() : wp_logout_url() ); ?>" class="mobile-menu__account-link mobile-menu__account-link--logout">
                            <span class="material-symbols" translate="no">logout</span>
                            <?php _e( 'Log out', 'steel-eshop' ); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="mobile-menu__account-user">
                        <div class="mobile-menu__account-avatar">
                            <span class="material-symbols material-symbols--filled" translate="no">account_circle</span>
                        </div>
                        <div class="mobile-menu__account-info">
                            <span class="mobile-menu__account-title"><?php _e( 'Personal Account', 'steel-eshop' ); ?></span>
                            <span class="mobile-menu__account-desc"><?php _e( 'Log in to track orders and save wishlist', 'steel-eshop' ); ?></span>
                        </div>
                    </div>
                    <div class="mobile-menu__account-actions">
                        <a href="<?php echo esc_url( $account_url ); ?>" class="button button--dynamic mobile-menu__account-btn">
                            <span><?php _e( 'Log in / Register', 'steel-eshop' ); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Categories trigger -->
            <div class="mobile-menu__categories-trigger-wrapper">
                <button type="button" class="mobile-menu__categories-trigger" data-modal="mobile-menu-categories" aria-label="<?php esc_attr_e( 'Open categories', 'steel-eshop' ); ?>">
                    <div class="mobile-menu__categories-trigger-title">
                        <span class="material-symbols" translate="no">widgets</span>
                        <span><?php _e( 'Categories', 'steel-eshop' ); ?></span>
                    </div>
                    <span class="material-symbols" translate="no">keyboard_arrow_right</span>
                </button>
            </div>

            <!-- Standard pages navigation -->
            <nav class="mobile-menu__nav" aria-label="<?php esc_attr_e( 'Mobile Menu Navigation', 'steel-eshop' ); ?>">
                <?php
                wp_nav_menu( [
                    'theme_location' => 'header-pages-menu',
                    'menu_class'     => 'header__menu menu hidden-tablet mobile-menu__menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'walker'         => new Steel_Nav_Menu(),
                ] );
                ?>
            </nav>

            <!-- Contacts -->
            <div class="mobile-menu__contact">
                <div class="mobile-menu__contact-title"><?php _e( 'Contacts', 'steel-eshop' ); ?></div>
                <div class="mobile-menu__contact-list">

                    <?php if ( $store_address || $store_city ) : ?>
                        <div class="mobile-menu__contact-item">
                            <div class="mobile-menu__contact-label">
                                <span class="mobile-menu__contact-icon material-symbols" translate="no">location_on</span>
                                <?php _e( 'Store address', 'steel-eshop' ); ?>
                            </div>
                            <span class="mobile-menu__contact-value">
                                <?php echo esc_html( trim( $store_address . ', ' . $store_city, ', ' ) ); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $store_email ) : ?>
                        <div class="mobile-menu__contact-item">
                            <div class="mobile-menu__contact-label">
                                <span class="mobile-menu__contact-icon material-symbols" translate="no">mail</span>
                                Email
                            </div>
                            <span class="mobile-menu__contact-value">
                                <a href="mailto:<?php echo esc_attr( $store_email ); ?>" class="mobile-menu__contact-link">
                                    <?php echo esc_html( $store_email ); ?>
                                </a>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $store_phone ) : ?>
                        <div class="mobile-menu__contact-item">
                            <div class="mobile-menu__contact-label">
                                <span class="mobile-menu__contact-icon material-symbols" translate="no">call</span>
                                <?php _e( 'Phone numbers', 'steel-eshop' ); ?>
                            </div>
                            <span class="mobile-menu__contact-value">
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $store_phone ) ); ?>" class="mobile-menu__contact-link">
                                    <?php echo esc_html( $store_phone ); ?>
                                </a>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $steel_theme_settings['footer_work_schedule'] ) ) : ?>
                        <div class="mobile-menu__contact-item">
                            <div class="mobile-menu__contact-label">
                                <span class="mobile-menu__contact-icon material-symbols" translate="no">schedule</span>
                                <?php _e( 'Work schedule', 'steel-eshop' ); ?>
                            </div>
                            <span class="mobile-menu__contact-value">
                                <?php echo wp_kses_post( $steel_theme_settings['footer_work_schedule'] ); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <!-- SIBLING LAYER: Categories Layer -->
    <div class="modal__layer" id="mobile-menu-categories" role="region" aria-label="<?php esc_attr_e( 'Categories', 'steel-eshop' ); ?>" aria-hidden="true">
        <div class="modal__header">
            <span class="modal__title"><?php _e( 'Categories', 'steel-eshop' ); ?></span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Close category menu', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled" translate="no">close</span>
            </button>
        </div>

        <div class="mobile-menu__content">
            <?php if ( ! empty( $parents ) ) : ?>
                <ul class="mobile-menu__categories-list">
                    <?php foreach ( $parents as $parent_id => $parent ) :
                        $has_children = ! empty( $children[ $parent_id ] );
                    ?>
                        <li class="mobile-menu__category-item<?php echo $has_children ? ' has-children' : ''; ?>">
                            <div class="mobile-menu__category-row">
                                <a href="<?php echo esc_url( get_term_link( $parent ) ); ?>" class="mobile-menu__category-link">
                                    <?php echo esc_html( $parent->name ); ?>
                                </a>
                                <?php if ( $has_children ) : ?>
                                    <button type="button" class="mobile-menu__category-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle subcategories', 'steel-eshop' ); ?>">
                                        <span class="material-symbols" translate="no">keyboard_arrow_down</span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if ( $has_children ) : ?>
                                <ul class="mobile-menu__subcategories-list">
                                    <?php foreach ( $children[ $parent_id ] as $child ) : ?>
                                        <li class="mobile-menu__subcategory-item">
                                            <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="mobile-menu__subcategory-link">
                                                <?php echo esc_html( $child->name ); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="mobile-menu__empty"><?php _e( 'No categories found', 'steel-eshop' ); ?></p>
            <?php endif; ?>
        </div>
    </div>

</div>