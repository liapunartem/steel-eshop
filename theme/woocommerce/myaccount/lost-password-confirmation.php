<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="account-auth-card">
	<div class="account-auth-card__icon" aria-hidden="true">
		<span class="material-symbols">mark_email_read</span>
	</div>

	<h2 class="account-auth-card__title"><?php esc_html_e( 'Check your email', 'steel-eshop' ); ?></h2>

	<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

	<p class="account-auth-card__desc">
		<?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'woocommerce' ) ) ); ?>
	</p>

	<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>

	<div class="account-auth-card__actions">
		<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button account-auth-card__button">
			<?php esc_html_e( 'Return to login', 'steel-eshop' ); ?>
		</a>
	</div>
</div>

