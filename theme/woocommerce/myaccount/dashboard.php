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
			<span class="material-symbols" translate="no">account_circle</span>
		</div>
		<div class="account-dashboard__user">
			<h2 class="account-dashboard__greeting">
				<?php
				printf(
					/* translators: 1: user display name */
					esc_html__( 'Привіт, %s', 'steel-eshop' ),
					'<strong>' . esc_html( $current_user->display_name ) . '</strong>'
				);
				?>
			</h2>
			<p class="account-dashboard__logout">
				<?php
				printf(
					/* translators: 1: logout url */
					wp_kses( __( 'Не ваш обліковий запис? <a href="%s" class="account-dashboard__logout-link">Вийти</a>', 'steel-eshop' ), $allowed_html ),
					esc_url( wc_logout_url() )
				);
				?>
			</p>
		</div>
	</header>

	<p class="account-dashboard__intro">
		<?php
		$dashboard_desc = __( 'З панелі керування вашого облікового запису ви можете переглядати свої <a href="%1$s">останні замовлення</a>, керувати <a href="%2$s">адресою доставки</a> та <a href="%3$s">змінювати пароль і деталі облікового запису</a>.', 'steel-eshop' );
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
				<span class="material-symbols" translate="no">package_2</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Замовлення', 'steel-eshop' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'Історія замовлень, статус та квитанції', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" translate="no" aria-hidden="true">arrow_forward</span>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="account-card">
			<div class="account-card__icon" aria-hidden="true">
				<span class="material-symbols" translate="no">location_on</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Адреса доставки', 'steel-eshop' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'Керування адресою доставки', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" translate="no" aria-hidden="true">arrow_forward</span>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="account-card">
			<div class="account-card__icon" aria-hidden="true">
				<span class="material-symbols" translate="no">manage_accounts</span>
			</div>
			<div class="account-card__info">
				<span class="account-card__title"><?php esc_html_e( 'Деталі облікового запису', 'steel-eshop' ); ?></span>
				<span class="account-card__desc"><?php esc_html_e( 'Редагування імені, email та зміна пароля', 'steel-eshop' ); ?></span>
			</div>
			<span class="account-card__arrow material-symbols" translate="no" aria-hidden="true">arrow_forward</span>
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

