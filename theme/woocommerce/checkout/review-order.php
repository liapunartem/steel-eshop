<?php
/**
 * Review Order Table / Summary
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table checkout-order-summary">

	<div class="checkout-items-list" role="list" aria-label="<?php esc_attr_e( 'Товари в замовленні', 'steel-eshop' ); ?>">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$thumbnail         = $_product->get_image(
					'woocommerce_thumbnail',
					array(
						'class'   => 'checkout-item__img',
						'loading' => 'lazy',
					)
				);
				?>
				<div class="checkout-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" role="listitem">
					<div class="checkout-item__thumb-wrap">
						<?php if ( ! $product_permalink ) : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" tabindex="-1" aria-hidden="true">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<span class="checkout-item__qty-badge" aria-label="<?php echo esc_attr( sprintf( __( 'Кількість: %s', 'steel-eshop' ), $cart_item['quantity'] ) ); ?>">
							<?php echo esc_html( $cart_item['quantity'] ); ?>
						</span>
					</div>

					<div class="checkout-item__details">
						<div class="checkout-item__title-wrap">
							<?php if ( ! $product_permalink ) : ?>
								<span class="checkout-item__title"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></span>
							<?php else : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>" class="checkout-item__title">
									<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
								</a>
							<?php endif; ?>
							<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>

						<div class="checkout-item__price">
							<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</div>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</div>

	<div class="checkout-totals">
		<div class="checkout-totals__row checkout-totals__row--subtotal">
			<span class="checkout-totals__label"><?php esc_html_e( 'Вартість товарів', 'steel-eshop' ); ?></span>
			<span class="checkout-totals__value"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="checkout-totals__row checkout-totals__row--discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span class="checkout-totals__label">
					<?php wc_cart_totals_coupon_label( $coupon ); ?>
				</span>
				<span class="checkout-totals__value">
					<?php wc_cart_totals_coupon_html( $coupon ); ?>
				</span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="checkout-totals__row checkout-totals__row--shipping">
				<span class="checkout-totals__label"><?php esc_html_e( 'Доставка', 'steel-eshop' ); ?></span>
				<span class="checkout-totals__value">
					<?php
					$packages        = WC()->shipping()->get_packages();
					$chosen_methods  = WC()->session->get( 'chosen_shipping_methods', array() );
					$shipping_labels = array();

					foreach ( $packages as $i => $package ) {
						$chosen = $chosen_methods[ $i ] ?? '';
						if ( isset( $package['rates'][ $chosen ] ) ) {
							$rate = $package['rates'][ $chosen ];
							$cost = (float) $rate->cost;

							if ( 0 === strpos( $rate->id, 'novaposhta' ) && 0.0 === $cost ) {
								$shipping_labels[] = esc_html__( 'за тарифами перевізника', 'steel-eshop' );
							} elseif ( 0.0 === $cost ) {
								$shipping_labels[] = '<span class="checkout-totals__free">' . esc_html__( 'Безкоштовно', 'steel-eshop' ) . '</span>';
							} else {
								$shipping_labels[] = wc_price( $cost );
							}
						}
					}

					if ( ! empty( $shipping_labels ) ) {
						echo implode( ', ', $shipping_labels ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo esc_html__( 'Виберіть спосіб доставки', 'steel-eshop' );
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="checkout-totals__row checkout-totals__row--tax tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span class="checkout-totals__label"><?php echo esc_html( $tax->label ); ?></span>
						<span class="checkout-totals__value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="checkout-totals__row checkout-totals__row--tax">
					<span class="checkout-totals__label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="checkout-totals__value"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<div class="checkout-totals__row checkout-totals__row--total">
			<span class="checkout-totals__label"><?php esc_html_e( 'Загалом до сплати', 'steel-eshop' ); ?></span>
			<span class="checkout-totals__value checkout-totals__value--highlight"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</div>

	<div class="checkout-place-order">
		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php
		$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Оформити замовлення', 'steel-eshop' ) );
		echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button checkout-submit-btn" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine
		?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>

</div>
