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
					<?php if ( 'bacs' === $order->get_payment_method() ) : ?>
						<?php
						$bacs_accounts     = array();
						$bacs_instructions = '';

						if ( function_exists( 'WC' ) && WC()->payment_gateways() ) {
							$gateways = WC()->payment_gateways()->payment_gateways();
							if ( isset( $gateways['bacs'] ) ) {
								$bacs_gateway      = $gateways['bacs'];
								$bacs_instructions = (string) $bacs_gateway->instructions;
								$bacs_accounts     = apply_filters( 'woocommerce_bacs_accounts', $bacs_gateway->account_details, $order->get_id() );
							}
						}

						if ( empty( $bacs_accounts ) ) {
							$raw_accounts  = get_option( 'woocommerce_bacs_accounts', array() );
							$bacs_accounts = is_array( $raw_accounts ) ? $raw_accounts : array();
						}
						?>
						<?php if ( ! empty( $bacs_accounts ) || ! empty( $bacs_instructions ) ) : ?>
							<section class="order-received__block order-received-bacs">
								<h3 class="order-received__block-title">
									<span class="material-symbols" translate="no" aria-hidden="true">account_balance</span>
									<?php esc_html_e( 'Реквізити для оплати', 'steel-eshop' ); ?>
								</h3>

								<?php if ( $bacs_instructions ) : ?>
									<div class="order-received-bacs__instructions">
										<?php echo wp_kses_post( wpautop( wptexturize( $bacs_instructions ) ) ); ?>
									</div>
								<?php else : ?>
									<p class="order-received-bacs__desc">
										<?php esc_html_e( 'Переведіть кошти за вказаними реквізитами. Замовлення буде відправлено після зарахування коштів.', 'steel-eshop' ); ?>
									</p>
								<?php endif; ?>

								<?php if ( ! empty( $bacs_accounts ) ) : ?>
									<div class="order-received-bacs__accounts">
										<?php foreach ( $bacs_accounts as $account ) : ?>
											<?php
											$account      = (array) $account;
											$account_name = $account['account_name'] ?? '';
											$bank_name    = $account['bank_name'] ?? '';
											$iban         = $account['iban'] ?? '';
											$account_num  = $account['account_number'] ?? '';
											$sort_code    = $account['sort_code'] ?? '';
											$bic          = $account['bic'] ?? '';
											?>
											<div class="order-received-bacs__card">
												<?php if ( $account_name ) : ?>
													<h4 class="order-received-bacs__card-title">
														<span class="material-symbols" translate="no" aria-hidden="true">badge</span>
														<?php echo esc_html( $account_name ); ?>
													</h4>
												<?php endif; ?>

												<div class="order-received-bacs__fields">
													<?php if ( $bank_name ) : ?>
														<div class="order-received-bacs__field">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'Банк:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val"><?php echo esc_html( $bank_name ); ?></span>
														</div>
													<?php endif; ?>

													<?php if ( $iban ) : ?>
														<div class="order-received-bacs__field order-received-bacs__field--copyable">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'IBAN:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val order-received-bacs__field-val--code">
																<code><?php echo esc_html( $iban ); ?></code>
																<button type="button" class="order-received-bacs__copy-btn" data-copy="<?php echo esc_attr( preg_replace( '/\s+/', '', $iban ) ); ?>" aria-label="<?php esc_attr_e( 'Скопіювати IBAN', 'steel-eshop' ); ?>">
																	<span class="material-symbols" translate="no" aria-hidden="true">content_copy</span>
																	<span class="order-received-bacs__copy-text"><?php esc_html_e( 'Скопіювати', 'steel-eshop' ); ?></span>
																</button>
															</span>
														</div>
													<?php endif; ?>

													<?php if ( $account_num && ! $iban ) : ?>
														<div class="order-received-bacs__field order-received-bacs__field--copyable">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'Рахунок:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val order-received-bacs__field-val--code">
																<code><?php echo esc_html( $account_num ); ?></code>
																<button type="button" class="order-received-bacs__copy-btn" data-copy="<?php echo esc_attr( preg_replace( '/\s+/', '', $account_num ) ); ?>" aria-label="<?php esc_attr_e( 'Скопіювати рахунок', 'steel-eshop' ); ?>">
																	<span class="material-symbols" translate="no" aria-hidden="true">content_copy</span>
																	<span class="order-received-bacs__copy-text"><?php esc_html_e( 'Скопіювати', 'steel-eshop' ); ?></span>
																</button>
															</span>
														</div>
													<?php elseif ( $account_num && $iban && $account_num !== $iban ) : ?>
														<div class="order-received-bacs__field">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'Номер рахунку:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val"><?php echo esc_html( $account_num ); ?></span>
														</div>
													<?php endif; ?>

													<?php if ( $sort_code ) : ?>
														<div class="order-received-bacs__field">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'МФО / Код банку:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val"><?php echo esc_html( $sort_code ); ?></span>
														</div>
													<?php endif; ?>

													<?php if ( $bic ) : ?>
														<div class="order-received-bacs__field">
															<span class="order-received-bacs__field-label"><?php esc_html_e( 'BIC / SWIFT:', 'steel-eshop' ); ?></span>
															<span class="order-received-bacs__field-val"><?php echo esc_html( $bic ); ?></span>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<div class="order-received-bacs__summary">
									<div class="order-received-bacs__summary-row">
										<span class="order-received-bacs__summary-label"><?php esc_html_e( 'Призначення платежу:', 'steel-eshop' ); ?></span>
										<span class="order-received-bacs__summary-val">
											<code><?php echo esc_html( sprintf( __( 'Оплата замовлення #%s', 'steel-eshop' ), $order->get_order_number() ) ); ?></code>
											<button type="button" class="order-received-bacs__copy-btn" data-copy="<?php echo esc_attr( sprintf( __( 'Оплата замовлення #%s', 'steel-eshop' ), $order->get_order_number() ) ); ?>" aria-label="<?php esc_attr_e( 'Скопіювати призначення платежу', 'steel-eshop' ); ?>">
												<span class="material-symbols" translate="no" aria-hidden="true">content_copy</span>
												<span class="order-received-bacs__copy-text"><?php esc_html_e( 'Скопіювати', 'steel-eshop' ); ?></span>
											</button>
										</span>
									</div>
									<div class="order-received-bacs__summary-row">
										<span class="order-received-bacs__summary-label"><?php esc_html_e( 'Сума до сплати:', 'steel-eshop' ); ?></span>
										<span class="order-received-bacs__summary-val order-received-bacs__summary-val--highlight">
											<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
										</span>
									</div>
								</div>

								<script>
									(function() {
										document.addEventListener('click', function(e) {
											var btn = e.target.closest('.order-received-bacs__copy-btn');
											if (!btn) return;
											e.preventDefault();
											var text = btn.getAttribute('data-copy');
											if (!text) return;

											var copy = function(t) {
												if (navigator.clipboard && window.isSecureContext) {
													return navigator.clipboard.writeText(t);
												}
												var ta = document.createElement('textarea');
												ta.value = t;
												ta.style.position = 'fixed';
												ta.style.left = '-9999px';
												document.body.appendChild(ta);
												ta.select();
												try {
													document.execCommand('copy');
													ta.remove();
													return Promise.resolve();
												} catch (err) {
													ta.remove();
													return Promise.reject(err);
												}
											};

											copy(text).then(function() {
												var textSpan = btn.querySelector('.order-received-bacs__copy-text');
												var iconSpan = btn.querySelector('.material-symbols');
												var origText = textSpan ? textSpan.textContent : '';
												var origIcon = iconSpan ? iconSpan.textContent : '';

												btn.classList.add('is-copied');
												if (textSpan) textSpan.textContent = '<?php echo esc_js( __( 'Скопійовано', 'steel-eshop' ) ); ?>';
												if (iconSpan) iconSpan.textContent = 'check';

												setTimeout(function() {
													btn.classList.remove('is-copied');
													if (textSpan) textSpan.textContent = origText;
													if (iconSpan) iconSpan.textContent = origIcon;
												}, 2000);
											});
										});
									})();
								</script>
							</section>
						<?php endif; ?>
					<?php endif; ?>

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
		if ( ! in_array( $order->get_payment_method(), array( 'cod', 'bacs' ), true ) ) {
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

