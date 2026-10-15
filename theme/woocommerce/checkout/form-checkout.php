<?php
/**
 * Checkout Form Template
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Вам потрібно увійти в обліковий запис для оформлення замовлення.', 'steel-eshop' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Оформлення замовлення', 'steel-eshop' ); ?>">

	<div class="sidebar-layout sidebar-layout--right checkout-layout">

		<div class="sidebar-layout__content checkout-main">

			<section class="checkout-block checkout-contact-block" aria-labelledby="checkout-block-contact-title">
				<h2 class="checkout-block__title" id="checkout-block-contact-title">
					<span class="checkout-block__number">1</span>
					<?php esc_html_e( 'Контактні дані', 'steel-eshop' ); ?>
				</h2>

				<div class="checkout-block__content">
					<div class="checkout-fields-grid checkout-fields-grid--2col">
						<?php
						$fields       = $checkout->get_checkout_fields( 'billing' );
						$contact_keys = array( 'billing_first_name', 'billing_last_name', 'billing_phone', 'billing_email' );

						foreach ( $contact_keys as $key ) {
							if ( isset( $fields[ $key ] ) ) {
								woocommerce_form_field( $key, $fields[ $key ], $checkout->get_value( $key ) );
							}
						}
						?>
					</div>
				</div>
			</section>

			<section class="checkout-block checkout-delivery-block" aria-labelledby="checkout-block-delivery-title">
				<h2 class="checkout-block__title" id="checkout-block-delivery-title">
					<span class="checkout-block__number">2</span>
					<?php esc_html_e( 'Доставка', 'steel-eshop' ); ?>
				</h2>

				<div class="checkout-block__content">
					<div class="checkout-delivery-methods">
						<?php steel_checkout_shipping_methods_html(); ?>
					</div>

					<div class="checkout-np-widget-wrap" id="checkout-np-widget-wrap">
						<?php
						if ( ! has_action( 'woocommerce_after_checkout_shipping_form' ) ) {
							echo '<div id="npwc-picker-root"></div>';
						} else {
							do_action( 'woocommerce_after_checkout_shipping_form' );
						}
						?>
					</div>

					<div class="checkout-address-fields is-hidden" id="checkout-address-fields">
						<h3 class="checkout-address-fields__title">
							<span class="material-symbols" translate="no" aria-hidden="true">home_pin</span>
							<?php esc_html_e( 'Адреса доставки курʼєром', 'steel-eshop' ); ?>
						</h3>
						<div class="checkout-fields-grid">
							<?php
							if ( isset( $fields['billing_city'] ) ) {
								woocommerce_form_field( 'billing_city', $fields['billing_city'], $checkout->get_value( 'billing_city' ) );
							}
							?>
							<div class="checkout-fields-grid--2col">
								<?php
								if ( isset( $fields['billing_address_1'] ) ) {
									woocommerce_form_field( 'billing_address_1', $fields['billing_address_1'], $checkout->get_value( 'billing_address_1' ) );
								}
								if ( isset( $fields['billing_address_2'] ) ) {
									woocommerce_form_field( 'billing_address_2', $fields['billing_address_2'], $checkout->get_value( 'billing_address_2' ) );
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="checkout-block checkout-payment-block" aria-labelledby="checkout-block-payment-title">
				<h2 class="checkout-block__title" id="checkout-block-payment-title">
					<span class="checkout-block__number">3</span>
					<?php esc_html_e( 'Спосіб оплати', 'steel-eshop' ); ?>
				</h2>

				<div class="checkout-block__content">
					<?php woocommerce_checkout_payment(); ?>
				</div>
			</section>

			<section class="checkout-block checkout-comment-block" aria-labelledby="checkout-block-comment-title">
				<div class="checkout-accordion" data-accordion>
					<button type="button" class="checkout-accordion__trigger" data-accordion-trigger aria-expanded="false" id="checkout-block-comment-title">
						<span class="checkout-accordion__title">
							<span class="material-symbols" translate="no" aria-hidden="true">notes</span>
							<?php esc_html_e( 'Додати коментар до замовлення', 'steel-eshop' ); ?>
						</span>
						<span class="material-symbols checkout-accordion__icon" translate="no" aria-hidden="true">expand_more</span>
					</button>
					<div class="checkout-accordion__content" data-accordion-content hidden>
						<?php
						$order_fields = $checkout->get_checkout_fields( 'order' );
						if ( isset( $order_fields['order_comments'] ) ) {
							woocommerce_form_field( 'order_comments', $order_fields['order_comments'], $checkout->get_value( 'order_comments' ) );
						}
						?>
					</div>
				</div>
			</section>

		</div>

		<aside class="sidebar-layout__sidebar checkout-sidebar">
			<div class="checkout-sidebar__inner">

				<div class="checkout-sidebar-box checkout-coupon-box">
					<div class="checkout-accordion" data-accordion>
						<button type="button" class="checkout-accordion__trigger" data-accordion-trigger aria-expanded="false">
							<span class="checkout-accordion__title">
								<span class="material-symbols" translate="no" aria-hidden="true">confirmation_number</span>
								<?php esc_html_e( 'Є промокод?', 'steel-eshop' ); ?>
							</span>
							<span class="material-symbols checkout-accordion__icon" translate="no" aria-hidden="true">expand_more</span>
						</button>
						<div class="checkout-accordion__content" data-accordion-content hidden>
							<div class="checkout-coupon-form">
								<input
									type="text"
									name="checkout_coupon_code"
									id="checkout_coupon_code"
									class="form__input checkout-coupon-form__input"
									placeholder="<?php esc_attr_e( 'Введіть код купона', 'steel-eshop' ); ?>"
									autocomplete="off"
								/>
								<button type="button" class="button button--transparent checkout-coupon-form__btn" id="apply_checkout_coupon">
									<?php esc_html_e( 'Застосувати', 'steel-eshop' ); ?>
								</button>
							</div>
							<div class="checkout-coupon-notice" id="checkout_coupon_notice"></div>
						</div>
					</div>
				</div>

				<div class="checkout-sidebar-box checkout-order-review-box">
					<h3 class="checkout-order-review-box__title">
						<?php esc_html_e( 'Ваше замовлення', 'steel-eshop' ); ?>
					</h3>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php woocommerce_order_review(); ?>
					</div>
				</div>

			</div>
		</aside>

	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
