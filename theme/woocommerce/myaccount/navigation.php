<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$nav_icons = [
	'dashboard'       => 'dashboard',
	'orders'          => 'package_2',
	'downloads'       => 'download',
	'edit-address'    => 'location_on',
	'payment-methods' => 'credit_card',
	'edit-account'    => 'manage_accounts',
	'customer-logout' => 'logout',
];
?>

<nav class="account-nav scroll-shadow woocommerce-MyAccount-navigation" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
	<div class="scroll-shadow__left"></div>
	<ul class="account-nav__list" data-check-scroll="horizontal">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			$is_active = wc_is_current_account_menu_item( $endpoint );
			$item_classes = 'account-nav__item account-nav__item--' . sanitize_html_class( $endpoint ) . ' ' . wc_get_account_menu_item_classes( $endpoint );
			if ( $is_active ) {
				$item_classes .= ' is-active account-nav__item--active';
			}
			$icon = $nav_icons[ $endpoint ] ?? 'arrow_forward';
		?>
			<li class="<?php echo esc_attr( $item_classes ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="account-nav__link" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<span class="account-nav__icon material-symbols" translate="no" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
					<span class="account-nav__label"><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="scroll-shadow__right"></div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
