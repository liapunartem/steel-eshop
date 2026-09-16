<?php
/**
 * The template for displaying product content in the single-product.php template
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_type = $product->get_type();

$product_img_ids = $product->get_gallery_image_ids();
$product_main_img_id = $product->get_image_id();
if ( $product_main_img_id ) {
	array_unshift( $product_img_ids, $product_main_img_id );
} else {
	array_unshift( $product_img_ids, wc_placeholder_img_src( 'woocommerce_full' ) );
}

$stock_text = $product->get_availability()['availability'];
$stock_class = $product->get_availability()['class'];

$is_in_wishlist = steel_is_in_wishlist( $product->get_id() );

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'container', $product ); ?>>

	<!-- gallery -->
	<section class="product-gallery">
    	<h2 class="visually-hidden"><?php _e( 'Product gallery', 'steel-eshop' ) ?></h2>

		<?php if ( count( $product_img_ids ) > 1 ): ?>
		<!-- thumbnail -->
		<div class="product-gallery__thumbs swiper-thumbs swiper">
			<div class="swiper-wrapper">
					<?php foreach ( $product_img_ids as $index => $product_img_id ): ?>
						<div class="swiper-thumbs__slide swiper-slide">
							<?php echo wp_get_attachment_image(
								$product_img_id,
								'thumbnail',
								false,
								[
									'class' => 'swiper-thumbs__img',
									'alt' => esc_attr( $product->get_title()
									. sprintf( __( ' - thumbnail %d', 'steel-eshop' ), $index + 1 ) ),
								]

							); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- main slider -->
		<div class="product-gallery__slider swiper swiper--slider">

			<button class="product-gallery__wishlist-button wishlist-button <?php echo $is_in_wishlist ? 'is-active' : ''; ?>" data-product-id="<?php the_ID(); ?>">
				<span class="material-symbols" translate="no">favorite</span>
			</button>

			<div class="swiper-wrapper">
				<?php if ( $product_img_ids ): ?>
					<?php foreach ( $product_img_ids as $index => $product_img_id ): ?>
						<a
							class="swiper-slide"
							data-fancybox="product-gallery"
							href="<?php echo wp_get_attachment_url( $product_img_id ); ?>"
							product_img_id="<?php echo $product_img_id; ?>"
						>
							<?php echo wp_get_attachment_image(
								$product_img_id,
								'large',
								false,
								[
									'alt' => esc_attr( $product->get_title()
									. sprintf( __( ' - image %d', 'steel-eshop' ), $index + 1 ) ),
								]
							);?>
						</a>

					<?php endforeach; ?>
				<?php endif; ?>
				
			</div>
			
			<?php if ( count( $product_img_ids ) > 1 ): ?>
				<!-- navigation -->
				<button class="swiper__button swiper__button--absolute swiper__button--prev button button--rounded">
					<span class="material-symbols" translate="no">keyboard_arrow_left</span>
				</button>
				<button class="swiper__button swiper__button--absolute swiper__button--next button button--rounded">
					<span class="material-symbols" translate="no">keyboard_arrow_right</span>
				</button>
			<?php endif; ?>
		</div>

	</section>

	<!-- product summary -->
	<section class="product-summary">
		
		<?php steel_the_product_add_to_cart_form_open() ?>

			<div class="product-summary__inner">
				<?php the_title( '<h1 class="product-summary__title">', '</h1>' ); ?>

				<p class="product-summary__sku">
					<?php
						$sku = $product->get_sku() ?: esc_html__( 'N/A', 'steel-eshop' );
					?>

					<?php esc_html_e( 'SKU:', 'steel-eshop' ); ?>
					<span id="js-sku-display"
						data-default-sku="<?php echo $sku; ?>">
						<?php echo $sku; ?>
					</span>
					
				</p>

				<hr>
				
				<div class="product-summary__flex-wrap">
					<div class="product-summary__rating">
						<span class="product-summary__rating-title">
							<?php esc_html_e( 'Reviews:', 'steel-eshop' ); ?>
						</span>

						<span class="material-symbols material-symbols--filled" translate="no">star</span>
						<?php echo $product->get_average_rating(); ?>
						/
						<?php echo $product->get_rating_count(); ?>
						
					</div>
					
					<div id="js-availability-display" class="product-summary__stock">
						<?php echo ($product_type === 'variable') ? '' : wc_get_stock_html($product); ?>
					</div>
				</div>

				<hr>

				<div class="product-summary__flex-wrap">
					<div id="js-price-display" class="product-summary__price">
						<?php echo ($product_type === 'variable') ? '' : $product->get_price_html(); ?>
					</div>
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
			</div>

			<?php if ($product_type === 'variable'): 
				$attributes = $product->get_variation_attributes();
				$attribute_keys  = array_keys( $attributes );
			?>
				<div class="product-summary__inner variation-block">
					<table class="variations" cellspacing="0" role="presentation">
						<tbody>
							<?php foreach ( $attributes as $attribute_name => $options ) : ?>
								<tr>
									<th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></th>
									<td class="value">
										<?php
											wc_dropdown_variation_attribute_options(
												array(
													'options'   => $options,
													'attribute' => $attribute_name,
													'product'   => $product,
												)
											);
											/**
											 * Filters the reset variation button.
											 *
											 * @since 2.5.0
											 *
											 * @param string  $button The reset variation button HTML.
											 */
											echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#" aria-label="' . esc_attr__( 'Clear options', 'woocommerce' ) . '">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

				</div>
			<?php endif; ?>

		</form>
		
	</section>

	<!-- product details -->
	<div class="product__details">
		<?php woocommerce_output_product_data_tabs(); ?>
	</div>

	<!-- related products -->
	<?php woocommerce_output_related_products(); ?>

</div>


