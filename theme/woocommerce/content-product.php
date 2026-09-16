<?php
/**
 * The template for displaying product content within loops
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) {
    return;
}

$product_id   = $product->get_id();
$product_link = get_permalink( $product_id );
$is_in_wishlist = steel_is_in_wishlist( $product_id );
?>

<article <?php wc_product_class( 'product-card', $product ); ?>>
    <!-- <?php woocommerce_show_product_loop_sale_flash() ?> -->

    <button type="button" class="product-card__wishlist-button wishlist-button <?php echo $is_in_wishlist ? 'is-active' : ''; ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" aria-label="<?php echo esc_attr( sprintf( $is_in_wishlist ? __( 'Remove %s from wishlist', 'steel-eshop' ) : __( 'Add %s to wishlist', 'steel-eshop' ), $product->get_name() ) ); ?>">
        <span class="material-symbols" translate="no">favorite</span>
    </button>

    <div class="product-card__image-wrap">
        <a href="<?php echo esc_url( $product_link ); ?>" class="product-card__image-link">
            <?php echo woocommerce_get_product_thumbnail(
                'woocommerce_thumbnail', 
                ['class' => 'product-card__image', 'loading' => 'lazy',]
            ); ?>
        </a>

    </div>

    <div class="product-card__body">
        <h3 class="product-card__title">
            <a href="<?php echo esc_url( $product_link ); ?>">
                <?php echo esc_html( $product->get_name() ); ?>
            </a>
        </h3>

        <div class="product-card__price">
            <?php woocommerce_template_loop_price(); ?>
        </div>
        
        <?php woocommerce_template_loop_add_to_cart(); ?>
    </div>

</article>