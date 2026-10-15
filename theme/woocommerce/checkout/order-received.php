<?php
/**
 * "Order received" message template part
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;
?>
<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
	<?php
	if ( $order ) {
		echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Дякуємо! Ваше замовлення успішно отримано.', 'steel-eshop' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Дякуємо! Ваше замовлення отримано.', 'steel-eshop' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</p>

