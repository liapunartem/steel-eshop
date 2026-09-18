<?php
/**
 * The template for displaying product content within loops
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) {
    return;
}

$product_id     = $product->get_id();
$product_link   = get_permalink( $product_id );
$is_in_wishlist = steel_is_in_wishlist( $product_id );
$is_on_sale     = $product->is_on_sale();
$average_rating = (float) $product->get_average_rating();
$rating_count   = $product->get_rating_count();
$rating_percent = ( $average_rating / 5 ) * 100;
?>

<article <?php wc_product_class( 'product-card', $product ); ?>>
    <?php if ( $is_on_sale ) : ?>
        <span class="product-card__badge product-card__badge--sale">
            <?php esc_html_e( 'Акція', 'steel-eshop' ); ?>
        </span>
    <?php endif; ?>

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

    <div class="product-card__spacer" aria-hidden="true">
        <div class="product-card__title"><span>&nbsp;<br>&nbsp;</span></div>
        <div class="product-card__rating">
            <div class="product-card__stars star-rating"><span></span></div>
            <span class="product-card__rating-count">(0)</span>
        </div>
        <div class="product-card__price"><span class="price">0 ₴</span></div>
        <div class="product-card__actions"><div class="button">&nbsp;</div></div>
    </div>

    <div class="product-card__body">
        <h3 class="product-card__title">
            <a href="<?php echo esc_url( $product_link ); ?>">
                <?php echo esc_html( $product->get_name() ); ?>
            </a>
        </h3>

        <div class="product-card__rating">
            <div class="product-card__stars" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %s out of 5', 'steel-eshop' ), $average_rating ) ); ?>">
                <span style="width: <?php echo esc_attr( $rating_percent ); ?>%;"></span>
            </div>
            <span class="product-card__rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
        </div>

        <div class="product-card__price">
            <?php woocommerce_template_loop_price(); ?>
        </div>
        
        <div class="product-card__actions">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
    </div>

</article>