<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<div class="account-edit-address">
	<?php if ( ! $load_address ) : ?>
		<?php wc_get_template( 'myaccount/my-address.php' ); ?>
	<?php else : ?>

		<div class="account-edit-address__top">
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="button button--transparent button--small account-edit-address__back">
				<span class="material-symbols" aria-hidden="true">arrow_back</span>
				<span><?php esc_html_e( 'Back to addresses', 'steel-eshop' ); ?></span>
			</a>
		</div>

		<form method="post" class="account-edit-address__form form" novalidate>

			<h2 class="account-edit-address__title"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h2>

			<div class="woocommerce-address-fields">
				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

				<div class="account-edit-address__fields woocommerce-address-fields__field-wrapper">
					<?php
					foreach ( $address as $key => $field ) {
						woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					}
					?>
				</div>

				<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

				<div class="account-edit-address__actions">
					<button type="submit" class="button account-edit-address__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Save address', 'woocommerce' ); ?>
					</button>
					<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
					<input type="hidden" name="action" value="edit_address" />
				</div>
			</div>

		</form>

	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>

