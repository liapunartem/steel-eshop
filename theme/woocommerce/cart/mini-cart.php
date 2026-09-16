<?php
/**
 * Mini-cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="mini-cart">
<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="mini-cart__list woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			$visible = apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key );

			if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'mini-cart__thumbnail' ) ), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				
				?>
				<li class="mini-cart__item woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					
					<!-- Delete button -->
					<?php
					echo apply_filters(
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a role="button" href="%s" class="mini-cart__remove-button remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s"><div class="material-symbols" translate="no">delete</div></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() ),
							esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
						),
						$cart_item_key
					);
					?>

					<!-- Product image -->
					<?php if ( empty( $product_permalink ) ) : ?>
						<div class="mini-cart__image-link">
							<?php echo $thumbnail; ?>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $product_permalink ); ?>" class="mini-cart__image-link">
							<?php echo $thumbnail; ?>
						</a>
					<?php endif; ?>

					<!-- Information part -->
					<div class="mini-cart__item-info">
						
						<!-- Product name -->
						<?php if ( empty( $product_permalink ) ) : ?>
							<span class="mini-cart__product-title"><?php echo wp_kses_post( $product_name ); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="mini-cart__product-title">
								<?php echo wp_kses_post( $product_name ); ?>
							</a>
						<?php endif; ?>

						<!-- Variations (size, color, etc.) -->
						<?php if ( ! empty( wc_get_formatted_cart_item_data( $cart_item ) ) ) : ?>
							<div class="mini-cart__variation">
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
							</div>
						<?php endif; ?>

						<!-- Prices and quantity -->
						<div class="mini-cart__price-inner">
							<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

							<div class="mini-cart__price">
								<?php echo $product_price; ?>
							</div>
						</div>

					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="mini-cart__total woocommerce-mini-cart__total total">
		<?php do_action( 'woocommerce_widget_shopping_cart_total' ); ?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<div class="mini-cart__buttons">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button button--transparent"><?php _e( 'View cart', 'steel-eshop' ); ?></a>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button"><?php _e( 'Ordering', 'steel-eshop' ); ?></a>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="mini-cart__empty-message woocommerce-mini-cart__empty-message">
		<?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?>
	</p>

<?php endif; ?>

<?php if ( ! is_user_logged_in() ) : 
	$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?>
	<div class="mini-cart__notice">
		<span class="material-symbols" translate="no" aria-hidden="true">info</span>
		<p>
			<?php
			printf(
				wp_kses(
					__( 'Якщо ви хочете зберігати товари між іншими пристроями, будь ласка, <a href="%1$s">увійдіть</a> або <a href="%2$s">зареєструйтесь</a>.', 'steel-eshop' ),
					array(
						'a' => array(
							'href'  => array(),
							'class' => array(),
						),
					)
				),
				esc_url( $account_url . '#tab-login' ),
				esc_url( $account_url . '#tab-register' )
			);
			?>
		</p>
	</div>
<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>