<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="account-auth-card">
	<form method="post" class="account-auth-card__form form woocommerce-ResetPassword lost_reset_password">

		<h2 class="account-auth-card__title"><?php esc_html_e( 'Lost password', 'steel-eshop' ); ?></h2>

		<p class="account-auth-card__desc">
			<?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?>
		</p>

		<p class="form__field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label class="form__label" for="user_login"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
			<input class="form__input woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
		</p>

		<div class="clear"></div>

		<?php do_action( 'woocommerce_lostpassword_form' ); ?>

		<div class="account-auth-card__actions">
			<input type="hidden" name="wc_reset_password" value="true" />
			<button type="submit" class="button form__submit-button woocommerce-Button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
				<?php esc_html_e( 'Reset password', 'woocommerce' ); ?>
			</button>
		</div>

		<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		<p class="account-auth-card__footer">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="account-auth-card__back-link">
				&larr; <?php esc_html_e( 'Back to login', 'steel-eshop' ); ?>
			</a>
		</p>

	</form>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );

