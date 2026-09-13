<?php
/**
 * Add payment method form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-add-payment-method.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
?>

<div class="account-add-payment-method">
	<div class="account-add-payment-method__top">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'payment-methods' ) ); ?>" class="button button--transparent button--small account-add-payment-method__back">
			<span class="material-symbols" aria-hidden="true">arrow_back</span>
			<span><?php esc_html_e( 'Back to payment methods', 'steel-eshop' ); ?></span>
		</a>
	</div>

	<?php if ( $available_gateways ) : ?>
		<form id="add_payment_method" class="account-add-payment-method__form form" method="post">
			<div id="payment" class="woocommerce-Payment">
				<ul class="woocommerce-PaymentMethods payment_methods methods" aria-label="<?php esc_attr_e( 'Payment methods', 'woocommerce' ); ?>">
					<?php
					if ( count( $available_gateways ) ) {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $gateway->id ); ?> payment_method_<?php echo esc_attr( $gateway->id ); ?>">
							<label class="form__label woocommerce-form__label-for-radio" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
								<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
								<span><?php echo wp_kses_post( $gateway->get_title() ); ?> <?php echo wp_kses_post( $gateway->get_icon() ); ?></span>
							</label>
							<?php
							if ( $gateway->has_fields() || $gateway->get_description() ) {
								echo '<div class="woocommerce-PaymentBox woocommerce-PaymentBox--' . esc_attr( $gateway->id ) . ' payment_box payment_method_' . esc_attr( $gateway->id ) . '" style="display: none;">';
								$gateway->payment_fields();
								echo '</div>';
							}
							?>
						</li>
						<?php
					}
					?>
				</ul>

				<?php do_action( 'woocommerce_add_payment_method_form_bottom' ); ?>

				<div class="account-add-payment-method__actions form-row">
					<?php wp_nonce_field( 'woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce' ); ?>
					<button type="submit" class="button account-add-payment-method__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" id="place_order" value="<?php esc_attr_e( 'Add payment method', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Add payment method', 'woocommerce' ); ?>
					</button>
					<input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
				</div>
			</div>
		</form>
	<?php else : ?>
		<div class="account-empty">
			<p><?php esc_html_e( 'New payment methods can only be added during checkout. Please contact us if you require assistance.', 'woocommerce' ); ?></p>
		</div>
	<?php endif; ?>
</div>

