<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="modal modal--offcanvas" id="modal-cart" role="dialog" aria-modal="true" aria-labelledby="modal-cart-title" aria-hidden="true">
    <div class="modal__backdrop"></div>
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-cart-title">
                <span class="material-symbols material-symbols--filled">shopping_cart</span>
                <?php _e( 'Cart', 'steel-eshop' ); ?>
            </span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Close cart', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled">close</span>
            </button>
        </div>
        <?php the_widget( 'WC_Widget_Cart', array( 'title' => '' ) ); ?>
    </div>
</div>