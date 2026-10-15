<?php
/**
 * Output a single payment method
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<label class="checkout-payment-card <?php echo $gateway->chosen ? 'is-selected' : ''; ?>" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<input
			id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
			type="radio"
			class="input-radio checkout-payment-card__input"
			name="payment_method"
			value="<?php echo esc_attr( $gateway->id ); ?>"
			<?php checked( $gateway->chosen, true ); ?>
			data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"
		/>
		<span class="checkout-payment-card__indicator"></span>
		<span class="checkout-payment-card__content">
			<span class="checkout-payment-card__title"><?php echo $gateway->get_title(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></span>
			<?php if ( $gateway->get_icon() ) : ?>
				<span class="checkout-payment-card__icon"><?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></span>
			<?php endif; ?>
		</span>
	</label>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>

