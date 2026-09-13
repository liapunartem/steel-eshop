<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>

<div class="account-downloads">
	<?php if ( $has_downloads ) : ?>

		<?php do_action( 'woocommerce_before_available_downloads' ); ?>

		<div class="account-table-wrapper">
			<?php do_action( 'woocommerce_available_downloads', $downloads ); ?>
		</div>

		<?php do_action( 'woocommerce_after_available_downloads' ); ?>

	<?php else : ?>

		<div class="account-empty">
			<div class="account-empty__icon" aria-hidden="true">
				<span class="material-symbols">download</span>
			</div>
			<h3 class="account-empty__title"><?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?></h3>
			<p class="account-empty__text"><?php esc_html_e( 'When you purchase digital or downloadable items, they will appear here.', 'steel-eshop' ); ?></p>
			<a class="button account-empty__button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
			</a>
		</div>

	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>

