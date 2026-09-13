<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.9.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<div class="account-payment-methods">
	<?php if ( $has_methods ) : ?>

		<div class="account-table-wrapper">
			<table class="account-table account-payment-methods__table woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table">
				<thead class="account-table__head">
					<tr class="account-table__row account-table__row--head">
						<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
							<th scope="col" class="account-table__header account-table__header--<?php echo esc_attr( $column_id ); ?> woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?>">
								<span class="nobr"><?php echo esc_html( $column_name ); ?></span>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody class="account-table__body">
					<?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
						<?php foreach ( $methods as $method ) : ?>
							<tr class="account-table__row payment-method<?php echo ! empty( $method['is_default'] ) ? ' default-payment-method' : ''; ?>">
								<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
									<td class="account-table__cell account-table__cell--<?php echo esc_attr( $column_id ); ?> woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
										<?php
										if ( has_action( 'woocommerce_account_payment_methods_column_' . $column_id ) ) {
											do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
										} elseif ( 'method' === $column_id ) {
											if ( ! empty( $method['method']['last4'] ) ) {
												/* translators: 1: credit card type 2: last 4 digits */
												echo sprintf( esc_html__( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
											} else {
												echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
											}
										} elseif ( 'expires' === $column_id ) {
											echo esc_html( $method['expires'] );
										} elseif ( 'actions' === $column_id ) {
											echo '<div class="account-table__actions">';
											foreach ( $method['actions'] as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
												echo '<a href="' . esc_url( $action['url'] ) . '" class="button button--transparent button--small account-table__action ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
											}
											echo '</div>';
										}
										?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	<?php else : ?>

		<div class="account-empty">
			<div class="account-empty__icon" aria-hidden="true">
				<span class="material-symbols">credit_card</span>
			</div>
			<h3 class="account-empty__title"><?php esc_html_e( 'No saved methods found.', 'woocommerce' ); ?></h3>
			<p class="account-empty__text"><?php esc_html_e( 'You can save payment methods for faster checkout next time.', 'steel-eshop' ); ?></p>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>

	<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
		<div class="account-payment-methods__add">
			<a class="button" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>">
				<span class="material-symbols" aria-hidden="true">add</span>
				<span><?php esc_html_e( 'Add payment method', 'woocommerce' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
</div>

