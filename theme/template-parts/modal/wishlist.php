<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="modal modal--offcanvas" id="modal-wishlist" role="dialog" aria-modal="true" aria-labelledby="modal-wishlist-title" aria-hidden="true">
    <div class="modal__backdrop"></div>
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-wishlist-title">
                <span class="material-symbols material-symbols--filled" translate="no">favorite</span>
                <?php esc_html_e( 'Список бажань', 'steel-eshop' ); ?>
            </span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Закрити список бажань', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled" translate="no">close</span>
            </button>
        </div>
        
        <?php echo steel_get_wishlist_html(); ?>
    </div>
</div>