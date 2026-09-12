<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="modal modal--offcanvas" id="mobile-menu" role="dialog" aria-modal="true" aria-labelledby="modal-mobile-menu-title" aria-hidden="true">
    <div class="modal__backdrop"></div>
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-mobile-menu-title"><?php _e( 'Menu', 'steel-eshop' ); ?></span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Close mobile menu', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled">close</span>
            </button>
        </div>

        <nav aria-label="<?php esc_attr_e( 'Mobile Menu Navigation', 'steel-eshop' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'header-pages-menu',
                'menu_class'     => 'mobile-menu__list menu',
                'container'      => false,
                'fallback_cb'    => false,
                'walker'         => new Steel_Nav_Menu(),
            ] );
            ?>
        </nav>
    </div>
</div>