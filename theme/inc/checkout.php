<?php
/**
 * Checkout customization and hooks for Steel Eshop
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove default coupon form and duplicate notices wrapper from top of checkout,
 * and remove default core order details table from thankyou page (rendered custom in thankyou.php).
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

/**
 * Customize checkout fields for Ukrainian retail standards
 */
add_filter( 'woocommerce_checkout_fields', 'steel_customize_checkout_fields', 99 );
function steel_customize_checkout_fields( $fields ) {
	// --- Billing fields (Customer contacts) ---
	$fields['billing']['billing_first_name']['priority']    = 10;
	$fields['billing']['billing_first_name']['label']       = __( "Ім'я", 'steel-eshop' );
	$fields['billing']['billing_first_name']['placeholder'] = __( "Введіть ваше ім'я", 'steel-eshop' );
	$fields['billing']['billing_first_name']['class']       = array( 'form-row', 'form-row-first' );
	$fields['billing']['billing_first_name']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_first_name']['required']    = true;

	$fields['billing']['billing_last_name']['priority']    = 20;
	$fields['billing']['billing_last_name']['label']       = __( 'Прізвище', 'steel-eshop' );
	$fields['billing']['billing_last_name']['placeholder'] = __( 'Введіть ваше прізвище', 'steel-eshop' );
	$fields['billing']['billing_last_name']['class']       = array( 'form-row', 'form-row-last' );
	$fields['billing']['billing_last_name']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_last_name']['required']    = true;

	$fields['billing']['billing_phone']['priority']    = 30;
	$fields['billing']['billing_phone']['label']       = __( 'Номер телефону', 'steel-eshop' );
	$fields['billing']['billing_phone']['placeholder'] = '+380 (__) ___-__-__';
	$fields['billing']['billing_phone']['type']        = 'tel';
	$fields['billing']['billing_phone']['class']       = array( 'form-row', 'form-row-first' );
	$fields['billing']['billing_phone']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_phone']['required']    = true;

	$fields['billing']['billing_email']['priority']    = 40;
	$fields['billing']['billing_email']['label']       = __( 'E-mail', 'steel-eshop' );
	$fields['billing']['billing_email']['placeholder'] = 'name@example.com';
	$fields['billing']['billing_email']['type']        = 'email';
	$fields['billing']['billing_email']['class']       = array( 'form-row', 'form-row-last' );
	$fields['billing']['billing_email']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_email']['required']    = true;

	// --- Address fields (For courier / address delivery) ---
	// Keep required false in WC config so hidden address fields don't block Nova Poshta or Pickup checkout.
	// Custom validation is enforced in steel_validate_checkout_address().
	$fields['billing']['billing_city']['priority']    = 50;
	$fields['billing']['billing_city']['label']       = __( 'Місто', 'steel-eshop' );
	$fields['billing']['billing_city']['placeholder'] = __( 'Введіть місто доставки', 'steel-eshop' );
	$fields['billing']['billing_city']['class']       = array( 'form-row', 'form-row-wide' );
	$fields['billing']['billing_city']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_city']['required']    = false;

	$fields['billing']['billing_address_1']['priority']    = 60;
	$fields['billing']['billing_address_1']['label']       = __( 'Вулиця та номер будинку', 'steel-eshop' );
	$fields['billing']['billing_address_1']['placeholder'] = __( 'вул. Хрещатик, буд. 1', 'steel-eshop' );
	$fields['billing']['billing_address_1']['class']       = array( 'form-row', 'form-row-first' );
	$fields['billing']['billing_address_1']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_address_1']['required']    = false;

	$fields['billing']['billing_address_2']['priority']    = 70;
	$fields['billing']['billing_address_2']['label']       = __( 'Квартира / офіс', 'steel-eshop' );
	$fields['billing']['billing_address_2']['placeholder'] = __( 'кв. 10 (необовʼязково)', 'steel-eshop' );
	$fields['billing']['billing_address_2']['class']       = array( 'form-row', 'form-row-last' );
	$fields['billing']['billing_address_2']['input_class'] = array( 'form__input' );
	$fields['billing']['billing_address_2']['required']    = false;

	// Unset unnecessary fields for Ukrainian retail
	unset(
		$fields['billing']['billing_company'],
		$fields['billing']['billing_country'],
		$fields['billing']['billing_state'],
		$fields['billing']['billing_postcode'],
		$fields['shipping']
	);

	// Order comments
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = __( 'Коментар до замовлення', 'steel-eshop' );
		$fields['order']['order_comments']['placeholder'] = __( 'Додаткові побажання чи уточнення до замовлення...', 'steel-eshop' );
		$fields['order']['order_comments']['class']       = array( 'form-row', 'form-row-wide' );
		$fields['order']['order_comments']['input_class'] = array( 'form__textarea' );
	}

	return $fields;
}

/**
 * Set default country to Ukraine
 */
add_filter( 'default_checkout_billing_country', static function () {
	return 'UA';
} );

/**
 * Disable "ship to different address" check since all delivery info is in billing/delivery block
 */
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

/**
 * Conditional validation of address fields:
 * Only validate city and street if Courier / Address delivery is selected.
 * When Nova Poshta or Pickup is selected, address fields are not required.
 */
add_action( 'woocommerce_checkout_process', 'steel_validate_checkout_address' );
function steel_validate_checkout_address() {
	if ( ! WC()->cart || ! WC()->cart->needs_shipping() ) {
		return;
	}

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );
	$chosen         = isset( $chosen_methods[0] ) ? (string) $chosen_methods[0] : '';

	$is_novaposhta = ( 0 === strpos( $chosen, 'novaposhta' ) );
	$is_pickup     = ( 0 === strpos( $chosen, 'local_pickup' ) );

	// If courier or other address delivery is chosen, require city and address_1
	if ( ! $is_novaposhta && ! $is_pickup ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$city      = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
		$address_1 = isset( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '';
		// phpcs:enable

		if ( '' === trim( $city ) ) {
			wc_add_notice( __( 'Будь ласка, вкажіть місто для адресної доставки.', 'steel-eshop' ), 'error' );
		}
		if ( '' === trim( $address_1 ) ) {
			wc_add_notice( __( 'Будь ласка, вкажіть вулицю та номер будинку для адресної доставки.', 'steel-eshop' ), 'error' );
		}
	}
}

/**
 * Render custom delivery methods radio cards
 */
function steel_checkout_shipping_methods_html() {
	if ( ! WC()->cart || ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
		return;
	}

	$packages = WC()->shipping()->get_packages();

	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
		$rates         = $package['rates'] ?? array();

		if ( empty( $rates ) ) {
			echo '<p class="checkout-delivery__empty">' . esc_html__( 'Немає доступних способів доставки.', 'steel-eshop' ) . '</p>';
			continue;
		}
		?>
		<div class="checkout-delivery-list" role="radiogroup" aria-label="<?php esc_attr_e( 'Спосіб доставки', 'steel-eshop' ); ?>">
			<?php
			foreach ( $rates as $method ) {
				$method_id = $method->id;
				$checked   = checked( $method_id, $chosen_method, false );
				$cost      = (float) $method->cost;

				// Cost label badge
				if ( 0 === strpos( $method_id, 'novaposhta' ) && 0.0 === $cost ) {
					$cost_html = '<span class="checkout-delivery-card__cost">' . esc_html__( 'за тарифами перевізника', 'steel-eshop' ) . '</span>';
				} elseif ( 0.0 === $cost ) {
					$cost_html = '<span class="checkout-delivery-card__cost checkout-delivery-card__cost--free">' . esc_html__( 'Безкоштовно', 'steel-eshop' ) . '</span>';
				} else {
					$cost_html = '<span class="checkout-delivery-card__cost">' . wc_price( $cost ) . '</span>';
				}

				// Carrier icon
				$icon = 'local_shipping';
				if ( 0 === strpos( $method_id, 'local_pickup' ) ) {
					$icon = 'storefront';
				} elseif ( 0 === strpos( $method_id, 'novaposhta' ) ) {
					$icon = 'package_2';
				}
				?>
				<label class="checkout-delivery-card <?php echo $checked ? 'is-selected' : ''; ?>" for="shipping_method_<?php echo esc_attr( $i . '_' . sanitize_title( $method_id ) ); ?>">
					<input
						type="radio"
						name="shipping_method[<?php echo esc_attr( $i ); ?>]"
						data-index="<?php echo esc_attr( $i ); ?>"
						id="shipping_method_<?php echo esc_attr( $i . '_' . sanitize_title( $method_id ) ); ?>"
						value="<?php echo esc_attr( $method_id ); ?>"
						class="shipping_method checkout-delivery-card__input"
						<?php echo $checked; ?>
					/>
					<span class="checkout-delivery-card__indicator"></span>
					<span class="material-symbols checkout-delivery-card__icon" translate="no" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
					<span class="checkout-delivery-card__content">
						<span class="checkout-delivery-card__title"><?php echo esc_html( $method->get_label() ); ?></span>
						<?php echo $cost_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</label>
				<?php
			}
			?>
		</div>
		<?php
	}
}

/**
 * Register AJAX fragment to refresh delivery methods in Block 2
 */
add_filter( 'woocommerce_update_order_review_fragments', 'steel_checkout_order_review_fragments' );
function steel_checkout_order_review_fragments( $fragments ) {
	ob_start();
	?>
	<div class="checkout-delivery-methods">
		<?php steel_checkout_shipping_methods_html(); ?>
	</div>
	<?php
	$fragments['.checkout-delivery-methods'] = ob_get_clean();

	return $fragments;
}

/**
 * Remove default COD and BACS thankyou output from bottom hook (rendered custom within the thankyou layout).
 */
add_action( 'init', 'steel_remove_offline_thankyou_outputs', 20 );
function steel_remove_offline_thankyou_outputs() {
	if ( function_exists( 'WC' ) && WC()->payment_gateways() ) {
		$gateways = WC()->payment_gateways()->payment_gateways();
		if ( isset( $gateways['cod'] ) ) {
			remove_action( 'woocommerce_thankyou_cod', array( $gateways['cod'], 'thankyou_page' ) );
		}
		if ( isset( $gateways['bacs'] ) ) {
			remove_action( 'woocommerce_thankyou_bacs', array( $gateways['bacs'], 'thankyou_page' ) );
		}
	}
}

/**
 * Synchronize and clean Nova Poshta session when non-NP shipping method is selected.
 */
add_action( 'woocommerce_checkout_update_order_review', 'steel_sync_np_shipping_session' );
function steel_sync_np_shipping_session( $post_data ) {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return;
	}

	parse_str( (string) $post_data, $data );
	$chosen_methods = isset( $data['shipping_method'] ) ? (array) $data['shipping_method'] : array();
	$chosen         = isset( $chosen_methods[0] ) ? (string) $chosen_methods[0] : '';
	$is_novaposhta   = ( 0 === strpos( $chosen, 'novaposhta' ) );

	if ( ! $is_novaposhta ) {
		WC()->session->set( 'npwc_warehouse_ref', '' );
		WC()->session->set( 'npwc_warehouse_name', '' );
	}
}

/**
 * Clean up any Nova Poshta order metadata and draft shipments if customer placed an order with non-NP method.
 */
add_action( 'woocommerce_checkout_update_order_meta', 'steel_cleanup_non_np_order_meta', 99, 2 );
function steel_cleanup_non_np_order_meta( $order_id, $data = array() ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$is_novaposhta = false;
	$is_pickup     = false;

	foreach ( $order->get_shipping_methods() as $shipping_item ) {
		$method_id = (string) $shipping_item->get_method_id();
		if ( 0 === strpos( $method_id, 'novaposhta' ) ) {
			$is_novaposhta = true;
		} elseif ( 0 === strpos( $method_id, 'local_pickup' ) ) {
			$is_pickup = true;
		}
	}

	if ( ! $is_novaposhta ) {
		$order->delete_meta_data( '_npwc_warehouse_ref' );
		$order->delete_meta_data( '_npwc_warehouse' );
		$order->delete_meta_data( '_npwc_city_ref' );
		$order->delete_meta_data( '_npwc_city' );
		$order->delete_meta_data( '_npwc_city_area' );

		// If pickup, ensure no warehouse string leaked into address_1
		if ( $is_pickup ) {
			$address_1 = (string) $order->get_billing_address_1();
			if ( preg_match( '/відділення|поштомат|пункт/iu', $address_1 ) ) {
				$order->set_billing_address_1( '' );
			}
		}

		$order->save();

		// Remove draft shipment created by CatCode plugin if any
		global $wpdb;
		$table = $wpdb->prefix . 'npwc_shipment';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
			$wpdb->delete( $table, array( 'order_id' => $order_id ), array( '%d' ) );
		}
	}
}

/**
 * Remove Nova Poshta branch from order item totals if the order did not use Nova Poshta.
 */
add_filter( 'woocommerce_get_order_item_totals', 'steel_filter_order_item_totals_np', 20, 2 );
function steel_filter_order_item_totals_np( $total_rows, $order ) {
	if ( ! $order instanceof \WC_Order ) {
		return $total_rows;
	}

	$is_novaposhta = false;
	foreach ( $order->get_shipping_methods() as $shipping_item ) {
		if ( 0 === strpos( (string) $shipping_item->get_method_id(), 'novaposhta' ) ) {
			$is_novaposhta = true;
			break;
		}
	}

	if ( ! $is_novaposhta && isset( $total_rows['npwc_branch'] ) ) {
		unset( $total_rows['npwc_branch'] );
	}

	return $total_rows;
}

/**
 * Localize checkout privacy policy text.
 *
 * @param string $text Privacy policy text.
 * @param string $type Context ('checkout' or 'registration').
 * @return string
 */
add_filter( 'woocommerce_get_privacy_policy_text', 'steel_translate_privacy_policy_text', 20, 2 );
function steel_translate_privacy_policy_text( $text, $type ) {
	if ( 'checkout' === $type ) {
		$privacy_page_id = function_exists( 'wc_privacy_policy_page_id' ) ? wc_privacy_policy_page_id() : (int) get_option( 'wp_page_for_privacy_policy' );
		$privacy_link    = $privacy_page_id
			? '<a href="' . esc_url( get_permalink( $privacy_page_id ) ) . '" class="woocommerce-privacy-policy-link" target="_blank">' . esc_html__( 'політиці конфіденційності', 'steel-eshop' ) . '</a>'
			: esc_html__( 'політиці конфіденційності', 'steel-eshop' );

		if ( empty( $text ) || false !== stripos( $text, 'personal data' ) || ! preg_match( '/[\x{0400}-\x{04FF}]/u', $text ) ) {
			$text = sprintf(
				/* translators: %s: privacy policy page link */
				__( 'Ваші персональні дані будуть використовуватися для обробки вашого замовлення, підтримки вашого досвіду на цьому сайті та для інших цілей, описаних у нашій %s.', 'steel-eshop' ),
				$privacy_link
			);
		} elseif ( false !== strpos( $text, '[privacy_policy]' ) ) {
			$text = str_replace( '[privacy_policy]', $privacy_link, $text );
		}
	}

	return $text;
}

/**
 * Ensure the default option value for checkout privacy policy text is in Ukrainian.
 *
 * @param string $value Option value.
 * @return string
 */
add_filter( 'option_woocommerce_checkout_privacy_policy_text', 'steel_filter_checkout_privacy_policy_text_option', 20 );
add_filter( 'default_option_woocommerce_checkout_privacy_policy_text', 'steel_filter_checkout_privacy_policy_text_option', 20 );
function steel_filter_checkout_privacy_policy_text_option( $value ) {
	if ( empty( $value ) || false !== stripos( $value, 'personal data' ) || ! preg_match( '/[\x{0400}-\x{04FF}]/u', $value ) ) {
		return __( 'Ваші персональні дані будуть використовуватися для обробки вашого замовлення, підтримки вашого досвіду на цьому сайті та для інших цілей, описаних у нашій [privacy_policy].', 'steel-eshop' );
	}
	return $value;
}

/**
 * Localize checkout terms & conditions checkbox text if enabled.
 *
 * @param string $text Checkbox text.
 * @return string
 */
add_filter( 'woocommerce_terms_and_conditions_checkbox_text', 'steel_translate_terms_checkbox_text', 20 );
function steel_translate_terms_checkbox_text( $text ) {
	if ( empty( $text ) || false !== stripos( $text, 'I have read and agree' ) || ! preg_match( '/[\x{0400}-\x{04FF}]/u', $text ) ) {
		$terms_page_id = function_exists( 'wc_terms_and_conditions_page_id' ) ? wc_terms_and_conditions_page_id() : 0;
		$terms_link    = $terms_page_id
			? '<a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . esc_html__( 'правилами та умовами сайту', 'steel-eshop' ) . '</a>'
			: esc_html__( 'правилами та умовами сайту', 'steel-eshop' );

		$text = sprintf(
			/* translators: %s: terms and conditions link */
			__( 'Я прочитав(-ла) та погоджуюся з %s', 'steel-eshop' ),
			$terms_link
		);
	} elseif ( false !== strpos( $text, '[terms]' ) ) {
		$terms_page_id = function_exists( 'wc_terms_and_conditions_page_id' ) ? wc_terms_and_conditions_page_id() : 0;
		$terms_link    = $terms_page_id
			? '<a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . esc_html__( 'правилами та умовами сайту', 'steel-eshop' ) . '</a>'
			: esc_html__( 'правилами та умовами сайту', 'steel-eshop' );

		$text = str_replace( '[terms]', $terms_link, $text );
	}

	return $text;
}

/**
 * Fallback gettext filter for privacy policy link text in case core helper is invoked directly.
 */
add_filter( 'gettext', 'steel_filter_wc_privacy_gettext', 10, 3 );
function steel_filter_wc_privacy_gettext( $translation, $text, $domain ) {
	if ( 'woocommerce' === $domain ) {
		if ( 'privacy policy' === $text ) {
			return __( 'політиці конфіденційності', 'steel-eshop' );
		}
		if ( 'terms and conditions' === $text ) {
			return __( 'правилами та умовами сайту', 'steel-eshop' );
		}
		if ( 'required' === $text ) {
			return __( 'обов’язково', 'steel-eshop' );
		}
	}
	return $translation;
}

/**
 * Fallback privacy policy page ID if not explicitly configured in WP/WC settings.
 *
 * @param int $page_id Configured privacy policy page ID.
 * @return int
 */
add_filter( 'woocommerce_privacy_policy_page_id', 'steel_fallback_privacy_policy_page_id', 10, 1 );
function steel_fallback_privacy_policy_page_id( $page_id ) {
	if ( ! $page_id ) {
		$candidates = array( 'privacy-policy', 'politika-konfidencijnosti', 'privacy' );
		foreach ( $candidates as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'publish' === $page->post_status ) {
				return (int) $page->ID;
			}
		}
	}
	return $page_id;
}



