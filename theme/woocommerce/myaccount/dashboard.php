<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_html = [
	'a' => [
		'href'  => [],
		'class' => [],
	],
	'strong' => [],
];
?>

<div class="account-dashboard">
	<header class="account-dashboard__header">
		<div class="account-dashboard__avatar" aria-hidden="true">
			<span class="material-symbols">account_circle</span>
		</div>
		<div class="account-dashboard__user">
			<h2 class="account-dashboard__greeting">
				<?php
				printf(
					/* translators: 1: user display name */
					esc_html__( 'Hello, %s', 'steel-eshop' ),
					'<strong>' . esc_html( $current_user->display_name ) . '</strong>'
				);
				?>
			</h2>
			<p class="account-dashboard__logout">
				<?php
				printf(
					/* translators: 1: logout url */
					wp_kses( __( 'Not your account? <a href="%s" class="account-dashboard__logout-link">Log out</a>', 'steel-eshop' ), $allowed_html ),
					esc_url( wc_logout_url() )
				);
				?>
			</p>
		</div>
	</header>

	<p class="account-dashboard__intro">
		<?php
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
		if ( wc_shipping_enabled() ) {
			$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
		}
		printf(
			wp_kses( $dashboard_desc, $allowed_html ),
			esc_url( wc_get_endpoint_url( 'orders' ) ),
			esc_url( wc_get_endpoint_url( 'edit-address' ) ),
			esc_url( wc_get_endpoint_url( 'edit-account' ) )
		);
		?>
	</p>

	<div class="account-dashboard__cards">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="account-card">
			<div class="account-card__icon" aria-hidden="true">
				<span class="material-symbols">package_2</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Orders', 'woocommerce' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'View order history, status and receipts', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" aria-hidden="true">arrow_forward</span>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="account-card">
			<div class="account-card__icon" aria-hidden="true">
				<span class="material-symbols">location_on</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Addresses', 'woocommerce' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'Manage shipping and billing addresses', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" aria-hidden="true">arrow_forward</span>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="account-card">
			<div class="account-card__icon" aria-hidden="true">
				<span class="material-symbols">manage_accounts</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Account details', 'woocommerce' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'Edit name, email and change password', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" aria-hidden="true">arrow_forward</span>
		</a>
	</div>

	<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );
	?>
</div>

