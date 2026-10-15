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
		<span class="material-symbols" translate="no">mark_email_read</span>
	</div>

	<h2 class="account-auth-card__title"><?php esc_html_e( 'Перевірте свою пошту', 'steel-eshop' ); ?></h2>

	<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

	<p class="account-auth-card__desc">
		<?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'Лист для скидання пароля надіслано на вашу електронну адресу. Доставка може зайняти кілька хвилин. Будь ласка, зачекайте щонайменше 10 хвилин, перш ніж робити повторну спробу.', 'steel-eshop' ) ) ); ?>
	</p>

	<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>

	<div class="account-auth-card__actions">
		<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button account-auth-card__button">
			<?php esc_html_e( 'Повернутися до входу', 'steel-eshop' ); ?>
		</a>
	</div>
</div>

