<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.6.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>

<div class="account-view-order">
	<div class="account-view-order__top">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="button button--transparent button--small account-view-order__back">
			<span class="material-symbols" translate="no" aria-hidden="true">arrow_back</span>
			<span><?php esc_html_e( 'Назад до замовлень', 'steel-eshop' ); ?></span>
		</a>
	</div>

	<div class="account-view-order__banner">
		<div class="account-view-order__banner-info">
			<h2 class="account-view-order__title">
				<?php printf( esc_html__( 'Замовлення #%s', 'steel-eshop' ), esc_html( $order->get_order_number() ) ); ?>
			</h2>
			<p class="account-view-order__status-text">
				<?php
				echo wp_kses_post(
					apply_filters(
						'woocommerce_order_details_status',
						sprintf(
							/* translators: 1: order number 2: order date 3: order status */
							esc_html__( 'Замовлення #%1$s було оформлено %2$s і зараз має статус: %3$s.', 'steel-eshop' ),
							'<mark class="order-number">#' . esc_html( $order->get_order_number() ) . '</mark>',
							'<mark class="order-date">' . esc_html( wc_format_datetime( $order->get_date_created() ) ) . '</mark>',
							'<mark class="order-status">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</mark>'
						),
						$order
					)
				);
				?>
			</p>
		</div>
		<span class="account-badge account-badge--<?php echo esc_attr( $order->get_status() ); ?>">
			<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
		</span>
	</div>

	<?php if ( $notes ) : ?>
		<section class="account-notes">
			<h3 class="account-notes__title"><?php esc_html_e( 'Оновлення замовлення', 'steel-eshop' ); ?></h3>
			<ol class="account-notes__list woocommerce-OrderUpdates commentlist notes">
				<?php foreach ( $notes as $note ) : ?>
					<li class="account-notes__item woocommerce-OrderUpdate comment note">
						<div class="account-notes__inner woocommerce-OrderUpdate-inner comment_container">
							<time class="account-notes__date woocommerce-OrderUpdate-meta meta">
								<?php echo date_i18n( esc_html__( 'j F Y, H:i', 'steel-eshop' ), strtotime( $note->comment_date ) ); ?>
							</time>
							<div class="account-notes__description woocommerce-OrderUpdate-description description">
								<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>

	<div class="account-view-order__details">
		<?php do_action( 'woocommerce_view_order', $order_id ); ?>
	</div>
</div>

