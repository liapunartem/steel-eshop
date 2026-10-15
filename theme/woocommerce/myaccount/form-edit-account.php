<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<div class="account-edit-account">
	<form class="account-edit-account__form form woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<div class="form__row form__row--two-columns">
			<p class="form__field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label class="form__label" for="account_first_name"><?php esc_html_e( 'Ім\'я', 'steel-eshop' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" aria-required="true" />
			</p>
			<p class="form__field woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label class="form__label" for="account_last_name"><?php esc_html_e( 'Прізвище', 'steel-eshop' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" aria-required="true" />
			</p>
		</div>

		<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label class="form__label" for="account_display_name"><?php esc_html_e( 'Відображуване ім\'я', 'steel-eshop' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<input type="text" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" aria-describedby="account_display_name_description" value="<?php echo esc_attr( $user->display_name ); ?>" aria-required="true" />
			<span id="account_display_name_description" class="form__help">
				<em><?php echo wc_reviews_enabled() ? esc_html__( 'Так ваше ім\'я відображатиметься в особистому кабінеті та у відгуках', 'steel-eshop' ) : esc_html__( 'Так ваше ім\'я відображатиметься в особистому кабінеті', 'steel-eshop' ); ?></em>
			</span>
		</p>

		<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label class="form__label" for="account_email"><?php esc_html_e( 'Електронна пошта', 'steel-eshop' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<input type="email" class="form__input woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" aria-required="true" />
		</p>

		<?php
		/**
		 * Hook where additional fields should be rendered.
		 *
		 * @since 8.7.0
		 */
		do_action( 'woocommerce_edit_account_form_fields' );
		?>

		<fieldset class="account-edit-account__fieldset form__fieldset">
			<legend class="form__legend"><?php esc_html_e( 'Зміна пароля', 'steel-eshop' ); ?></legend>

			<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label class="form__label" for="password_current"><?php esc_html_e( 'Поточний пароль (залиште порожнім, щоб не змінювати)', 'steel-eshop' ); ?></label>
				<input type="password" class="form__input woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="current-password" />
			</p>
			<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label class="form__label" for="password_1"><?php esc_html_e( 'Новий пароль (залиште порожнім, щоб не змінювати)', 'steel-eshop' ); ?></label>
				<input type="password" class="form__input woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="new-password" />
			</p>
			<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label class="form__label" for="password_2"><?php esc_html_e( 'Підтвердження нового пароля', 'steel-eshop' ); ?></label>
				<input type="password" class="form__input woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="new-password" />
			</p>
		</fieldset>

		<?php
		/**
		 * My Account edit account form.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_edit_account_form' );
		?>

		<div class="account-edit-account__actions">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="button account-edit-account__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Зберегти зміни', 'steel-eshop' ); ?>">
				<?php esc_html_e( 'Зберегти зміни', 'steel-eshop' ); ?>
			</button>
			<input type="hidden" name="action" value="save_account_details" />
		</div>

		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
	</form>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

