<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="account-auth-card">
	<form method="post" class="account-auth-card__form form woocommerce-ResetPassword lost_reset_password">

		<h2 class="account-auth-card__title"><?php esc_html_e( 'Set new password', 'steel-eshop' ); ?></h2>

		<p class="account-auth-card__desc">
			<?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?>
		</p>

		<p class="form__field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label class="form__label" for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
			<input type="password" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" autocomplete="new-password" required aria-required="true" />
		</p>

		<p class="form__field woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
			<label class="form__label" for="password_2"><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
			<input type="password" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" required aria-required="true" />
		</p>

		<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
		<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

		<div class="clear"></div>

		<?php do_action( 'woocommerce_resetpassword_form' ); ?>

		<div class="account-auth-card__actions">
			<input type="hidden" name="wc_reset_password" value="true" />
			<button type="submit" class="button form__submit-button woocommerce-Button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>">
				<?php esc_html_e( 'Save new password', 'steel-eshop' ); ?>
			</button>
		</div>

		<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

	</form>
</div>

<?php
do_action( 'woocommerce_after_reset_password_form' );

