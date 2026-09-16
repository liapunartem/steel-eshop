<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$is_register_active      = $is_registration_enabled && ( ! empty( $_POST['register'] ) || ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) );
$login_tab_active        = ! $is_register_active;
$register_tab_active     = $is_register_active;
?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="account-auth-card account-auth-card--login">

<?php if ( $is_registration_enabled ) : ?>

	<div class="tabs login-tabs">
		<!-- Tab List (Navigation) -->
		<ul class="tabs__list login-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Вхід та реєстрація', 'steel-eshop' ); ?>">
			<li class="tabs__item login-tabs__item <?php echo $login_tab_active ? 'is-active' : ''; ?>" role="presentation" data-target="tab-login">
				<a href="#tab-login" class="tabs__link login-tabs__link" id="tab-link-login" role="tab" aria-selected="<?php echo $login_tab_active ? 'true' : 'false'; ?>" aria-controls="tab-login" tabindex="<?php echo $login_tab_active ? '0' : '-1'; ?>">
					<span class="material-symbols login-tabs__icon" translate="no" aria-hidden="true">login</span>
					<span class="login-tabs__text"><?php esc_html_e( 'Login', 'woocommerce' ); ?></span>
				</a>
			</li>
			<li class="tabs__item login-tabs__item <?php echo $register_tab_active ? 'is-active' : ''; ?>" role="presentation" data-target="tab-register">
				<a href="#tab-register" class="tabs__link login-tabs__link" id="tab-link-register" role="tab" aria-selected="<?php echo $register_tab_active ? 'true' : 'false'; ?>" aria-controls="tab-register" tabindex="<?php echo $register_tab_active ? '0' : '-1'; ?>">
					<span class="material-symbols login-tabs__icon" translate="no" aria-hidden="true">person_add</span>
					<span class="login-tabs__text"><?php esc_html_e( 'Register', 'woocommerce' ); ?></span>
				</a>
			</li>
		</ul>

		<!-- Content panels -->
		<div class="tabs__content login-tabs__content">
			<div class="tabs__panel login-tabs__panel <?php echo $login_tab_active ? 'is-active' : ''; ?>" id="tab-login" role="tabpanel" aria-labelledby="tab-link-login" tabindex="0">

<?php else : ?>

	<div class="account-auth-card__header">
		<div class="account-auth-card__icon">
			<span class="material-symbols" translate="no" aria-hidden="true">lock</span>
		</div>
		<h2 class="account-auth-card__title"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
	</div>

<?php endif; ?>

				<h2 class="visually-hidden"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

				<form class="login-form form woocommerce-form woocommerce-form-login login" method="post" novalidate>

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label class="form__label" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="text" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
					</p>

					<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label class="form__label" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input class="form__input woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<div class="form__row form__row--remember-lost">
						<label class="form__label form__label--checkbox woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="form__checkbox woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
							<span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
						</label>

						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="form__lost-password-link">
							<?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?>
						</a>
					</div>

					<div class="form__actions">
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<button type="submit" class="form__submit-button button woocommerce-button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
					</div>

					<?php if ( $is_registration_enabled ) : ?>
						<div class="account-auth-card__switch">
							<p>
								<?php esc_html_e( 'Немає акаунту?', 'steel-eshop' ); ?>
								<a href="#tab-register" class="account-auth-card__switch-link" data-switch-to="tab-register"><?php esc_html_e( 'Register', 'woocommerce' ); ?></a>
							</p>
						</div>
					<?php endif; ?>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

				</form>

<?php if ( $is_registration_enabled ) : ?>

			</div>

			<div class="tabs__panel login-tabs__panel <?php echo $register_tab_active ? 'is-active' : ''; ?>" id="tab-register" role="tabpanel" aria-labelledby="tab-link-register" tabindex="0">

				<h2 class="visually-hidden"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

				<form method="post" class="register-form form woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="form__label" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="text" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
						</p>

					<?php endif; ?>

					<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label class="form__label" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="email" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="form__field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="form__label" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="password" class="form__input woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
						</p>

					<?php else : ?>

						<p class="form__help"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<div class="form__actions">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="form__submit-button button woocommerce-Button woocommerce-button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
					</div>

					<div class="account-auth-card__switch">
						<p>
							<?php esc_html_e( 'Вже маєте акаунт?', 'steel-eshop' ); ?>
							<a href="#tab-login" class="account-auth-card__switch-link" data-switch-to="tab-login"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></a>
						</p>
					</div>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>

			</div>
		</div>

	</div>

<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
