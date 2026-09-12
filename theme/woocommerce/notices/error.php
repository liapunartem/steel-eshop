<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<div class="notices notices--error" role="alert">
	<div class="notices__content">
		<?php foreach ( $notices as $notice ) : ?>
			<div><?php echo wc_kses_notice( $notice['notice'] ); ?></div>
		<?php endforeach; ?>
	</div>
	<button type="button" class="close-notice-btn" aria-label="<?php esc_attr_e( 'Close notice', 'steel-eshop' ); ?>">
		<span class="material-symbols" aria-hidden="true">close</span>
	</button>
</div>
