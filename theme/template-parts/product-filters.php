<?php global $steel_theme_settings; ?>
<div class="product-filters modal modal--offcanvas" id="product-filters">

    <div class="modal__backdrop"></div>

    <div class="product-filters__body modal__body">
        <div class="modal__header visible-tablet">
            <span class="modal__title"><?php _e( 'Filters', 'steel-eshop' ) ?></span>
            <button class="modal__close-btn">
                <span class="material-symbols material-symbols--filled" translate="no">close</span>
            </button>
        </div>

        <div class="product-filters__main modal__main">
            <?php echo do_shortcode( '[fe_chips mobile="yes"]' ); ?>
            <?php echo do_shortcode( '[fe_widget]' ); ?>
        </div>
    </div>

</div>