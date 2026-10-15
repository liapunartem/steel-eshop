<?php
/**
 * Thankyou / Order Received Template
 *
 * @package SteelEshop
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-order order-received">

	<?php if ( $order ) : ?>
		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
			<div class="order-received__hero order-received__hero--failed">
				<div class="order-received__hero-icon">
					<span class="material-symbols" translate="no" aria-hidden="true">error</span>
				</div>
				<h2 class="order-received__hero-title"><?php esc_html_e( 'Оплата не відбулася', 'steel-eshop' ); ?></h2>
				<p class="order-received__hero-desc">
					<?php esc_html_e( 'На жаль, платіж було відхилено або сталася помилка під час обробки. Ви можете спробувати оплатити замовлення знову або звʼязатися з нами для допомоги.', 'steel-eshop' ); ?>
				</p>
				<div class="order-received__hero-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button">
						<span class="material-symbols" translate="no" aria-hidden="true">refresh</span>
						<?php esc_html_e( 'Спробувати оплатити знову', 'steel-eshop' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button button--transparent">
						<?php esc_html_e( 'Повернутися до магазину', 'steel-eshop' ); ?>
					</a>
				</div>
			</div>

		<?php else : ?>
			<div class="order-received__hero">
				<div class="order-received__hero-icon">
					<span class="material-symbols" translate="no" aria-hidden="true">check_circle</span>
				</div>
				<h2 class="order-received__hero-title"><?php esc_html_e( 'Дякуємо за ваше замовлення!', 'steel-eshop' ); ?></h2>
				<p class="order-received__hero-desc">
					<?php
					printf(
						/* translators: %s: customer email */
						esc_html__( 'Ваше замовлення успішно прийнято в обробку. Деталі надіслано на %s.', 'steel-eshop' ),
						'<strong>' . esc_html( $order->get_billing_email() ) . '</strong>'
					);
					?>
				</p>
			</div>

			<div class="order-received__metrics">
				<div class="order-received__metric-card">
					<span class="order-received__metric-label"><?php esc_html_e( 'Номер замовлення', 'steel-eshop' ); ?></span>
					<strong class="order-received__metric-value">#<?php echo esc_html( $order->get_order_number() ); ?></strong>
				</div>
				<div class="order-received__metric-card">
					<span class="order-received__metric-label"><?php esc_html_e( 'Дата замовлення', 'steel-eshop' ); ?></span>
					<strong class="order-received__metric-value"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
				</div>
				<div class="order-received__metric-card">
					<span class="order-received__metric-label"><?php esc_html_e( 'Спосіб оплати', 'steel-eshop' ); ?></span>
					<strong class="order-received__metric-value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
				</div>
				<div class="order-received__metric-card">
					<span class="order-received__metric-label"><?php esc_html_e( 'Статус', 'steel-eshop' ); ?></span>
					<div>
						<span class="account-badge account-badge--<?php echo esc_attr( $order->get_status() ); ?>">
							<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
						</span>
					</div>
				</div>
			</div>

			<div class="order-received__layout">
				<div class="order-received__main">
					<section class="order-received__block">
						<h3 class="order-received__block-title">
							<span class="material-symbols" translate="no" aria-hidden="true">shopping_bag</span>
							<?php esc_html_e( 'Товари в замовленні', 'steel-eshop' ); ?>
						</h3>

						<div class="order-received__items" role="list">
							<?php
							foreach ( $order->get_items() as $item_id => $item ) :
								$product           = $item->get_product();
								$product_permalink = $product && $product->is_visible() ? $product->get_permalink( $item ) : '';
								$thumbnail         = $product ? $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'order-received-item__img' ) ) : '';
								?>
								<div class="order-received-item" role="listitem">
									<div class="order-received-item__thumb-wrap">
										<?php if ( $thumbnail ) : ?>
											<?php if ( $product_permalink ) : ?>
												<a href="<?php echo esc_url( $product_permalink ); ?>" tabindex="-1"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
											<?php else : ?>
												<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<?php endif; ?>
										<?php endif; ?>
										<span class="order-received-item__qty-badge">
											<?php echo esc_html( $item->get_quantity() ); ?>
										</span>
									</div>

									<div class="order-received-item__details">
										<div class="order-received-item__title-wrap">
											<?php if ( $product_permalink ) : ?>
												<a href="<?php echo esc_url( $product_permalink ); ?>" class="order-received-item__title">
													<?php echo esc_html( $item->get_name() ); ?>
												</a>
											<?php else : ?>
												<span class="order-received-item__title">
													<?php echo esc_html( $item->get_name() ); ?>
												</span>
											<?php endif; ?>

											<?php wc_display_item_meta( $item ); ?>
										</div>

										<div class="order-received-item__price">
											<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="order-received__totals">
							<?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
								<div class="order-received__totals-row order-received__totals-row--<?php echo esc_attr( $key ); ?>">
									<span class="order-received__totals-label"><?php echo esc_html( $total['label'] ); ?></span>
									<span class="order-received__totals-value"><?php echo wp_kses_post( $total['value'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				</div>

				<aside class="order-received__sidebar">
					<section class="order-received__block">
						<h3 class="order-received__block-title">
							<span class="material-symbols" translate="no" aria-hidden="true">person</span>
							<?php esc_html_e( 'Дані отримувача', 'steel-eshop' ); ?>
						</h3>

						<div class="order-received__info-list">
							<div class="order-received__info-row">
								<span class="order-received__info-label"><?php esc_html_e( 'Одержувач:', 'steel-eshop' ); ?></span>
								<span class="order-received__info-val"><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></span>
							</div>
							<?php if ( $order->get_billing_phone() ) : ?>
								<div class="order-received__info-row">
									<span class="order-received__info-label"><?php esc_html_e( 'Телефон:', 'steel-eshop' ); ?></span>
									<span class="order-received__info-val">
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $order->get_billing_phone() ) ); ?>">
											<?php echo esc_html( $order->get_billing_phone() ); ?>
										</a>
									</span>
								</div>
							<?php endif; ?>
							<?php if ( $order->get_billing_email() ) : ?>
								<div class="order-received__info-row">
									<span class="order-received__info-label"><?php esc_html_e( 'E-mail:', 'steel-eshop' ); ?></span>
									<span class="order-received__info-val">
										<a href="mailto:<?php echo esc_attr( $order->get_billing_email() ); ?>">
											<?php echo esc_html( $order->get_billing_email() ); ?>
										</a>
									</span>
								</div>
							<?php endif; ?>
						</div>
					</section>

					<section class="order-received__block">
						<h3 class="order-received__block-title">
							<span class="material-symbols" translate="no" aria-hidden="true">local_shipping</span>
							<?php esc_html_e( 'Доставка', 'steel-eshop' ); ?>
						</h3>

						<div class="order-received__info-list">
							<div class="order-received__info-row">
								<span class="order-received__info-label"><?php esc_html_e( 'Спосіб:', 'steel-eshop' ); ?></span>
								<span class="order-received__info-val"><?php echo esc_html( $order->get_shipping_method() ?: __( 'Самовивіз', 'steel-eshop' ) ); ?></span>
							</div>

							<?php
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

							// Fallback check on shipping method title
							if ( ! $is_novaposhta && ! $is_pickup ) {
								$shipping_name = mb_strtolower( (string) $order->get_shipping_method() );
								if ( false !== mb_strpos( $shipping_name, 'пошт' ) ) {
									$is_novaposhta = true;
								} elseif ( false !== mb_strpos( $shipping_name, 'самовивіз' ) ) {
									$is_pickup = true;
								}
							}

							$np_city      = (string) $order->get_meta( '_npwc_city' );
							$np_warehouse = (string) $order->get_meta( '_npwc_warehouse' );
							$ttn          = (string) $order->get_meta( '_npwc_ttn' );

							if ( $is_novaposhta && $np_warehouse ) :
								?>
								<div class="order-received__info-row">
									<span class="order-received__info-label"><?php esc_html_e( 'Відділення:', 'steel-eshop' ); ?></span>
									<span class="order-received__info-val"><?php echo esc_html( trim( $np_city . ', ' . $np_warehouse, ', ' ) ); ?></span>
								</div>
							<?php elseif ( ! $is_pickup && ! $is_novaposhta && $order->get_billing_address_1() ) : ?>
								<div class="order-received__info-row">
									<span class="order-received__info-label"><?php esc_html_e( 'Адреса:', 'steel-eshop' ); ?></span>
									<span class="order-received__info-val">
										<?php echo esc_html( trim( $order->get_billing_city() . ', ' . $order->get_billing_address_1() . ( $order->get_billing_address_2() ? ', ' . $order->get_billing_address_2() : '' ), ', ' ) ); ?>
									</span>
								</div>
							<?php endif; ?>

							<?php if ( $is_novaposhta && $ttn ) : ?>
								<div class="order-received__info-row order-received__info-row--ttn">
									<span class="order-received__info-label"><?php esc_html_e( 'Номер ТТН:', 'steel-eshop' ); ?></span>
									<span class="order-received__info-val">
										<a href="https://novaposhta.ua/tracking/?cargo_number=<?php echo esc_attr( $ttn ); ?>" target="_blank" rel="noopener noreferrer">
											<?php echo esc_html( $ttn ); ?>
											<span class="material-symbols" translate="no" aria-hidden="true">open_in_new</span>
										</a>
									</span>
								</div>
							<?php endif; ?>
						</div>
					</section>

					<?php if ( $order->get_customer_note() ) : ?>
						<section class="order-received__block">
							<h3 class="order-received__block-title">
								<span class="material-symbols" translate="no" aria-hidden="true">notes</span>
								<?php esc_html_e( 'Коментар до замовлення', 'steel-eshop' ); ?>
							</h3>
							<p class="order-received__note-text">
								<?php echo nl2br( esc_html( $order->get_customer_note() ) ); ?>
							</p>
						</section>
					<?php endif; ?>
				</aside>
			</div>

			<div class="order-received__actions">
				<a href="<?php echo esc_url( wc_get_page_permalink( 'home' ) ); ?>" class="button">
					<span class="material-symbols" translate="no" aria-hidden="true">shopping_cart</span>
					<?php esc_html_e( 'Продовжити покупки', 'steel-eshop' ); ?>
				</a>

				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="button button--transparent">
						<span class="material-symbols" translate="no" aria-hidden="true">receipt_long</span>
						<?php esc_html_e( 'Мої замовлення', 'steel-eshop' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( 'cod' !== $order->get_payment_method() ) {
			do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
		}
		?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>
		<div class="order-received__hero">
			<div class="order-received__hero-icon">
				<span class="material-symbols" translate="no" aria-hidden="true">check_circle</span>
			</div>
			<h2 class="order-received__hero-title"><?php esc_html_e( 'Дякуємо за замовлення!', 'steel-eshop' ); ?></h2>
			<p class="order-received__hero-desc"><?php esc_html_e( 'Ваше замовлення успішно отримано.', 'steel-eshop' ); ?></p>
			<div class="order-received__actions">
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button">
					<?php esc_html_e( 'Перейти до каталогу', 'steel-eshop' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>

</div>

