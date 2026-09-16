	<!-- END CONTENT -->

	<?php global $steel_theme_settings; ?>

	<!-- START FOOTER -->
	<footer class="footer">
		<div class="footer__main">

			<!-- mobile footer logo -->
			<div class="footer__logo-wrapper visible-mobile">
				<div class="container">
					<a class="footer__logo logo" href="<?php echo esc_url( home_url() ); ?>">
						<?php echo wp_get_attachment_image(
							get_theme_mod('custom_logo'), 'medium', false,
							['loading' => 'lazy', 'alt' => 'logo',]
						); ?>
					</a>
				</div>
			</div>

			<div class="footer__grid container">

				<!-- page navigation -->
            	<div class="footer__page-nav hidden-mobile">
					<a class="footer__logo logo" href="<?php echo esc_url( home_url() ); ?>">
						<?php echo wp_get_attachment_image(
							get_theme_mod('custom_logo'), 'medium', false,
							['loading' => 'lazy', 'alt' => 'logo',]
						); ?>
					</a>
					<nav aria-label="<?php esc_attr_e( 'Footer Pages Navigation', 'steel-eshop' ); ?>">
						<?php
							wp_nav_menu( [
								'theme_location' => 'footer-pages-menu',
								'menu_class'     => 'footer__menu menu',
								'container'      => false,
								'fallback_cb'    => false,
								'walker'         => new Steel_Nav_Menu(),
							] );
						?>
					</nav>
				</div>
				
				<!-- category navigation -->
				<div class="footer__categories">
					<div class="footer__categories-title footer__title">
						<?php _e( 'Categories', 'steel-eshop' ) ?>
						<span class="footer__categories-title-icon visible-mobile material-symbols" translate="no">stat_minus_1</span>
					</div>
					<?php steel_footer_categories(); ?>
				</div>
				
				<!-- contacts -->
				<div class="footer__contact">
					<div class="footer__title"><?php _e( 'Contacts', 'steel-eshop' ) ?></div>
					<div class="footer__contact-grid">

						<div class="footer__contact-item">
							<div class="footer__contact-title">
								<span class="footer__contact-icon material-symbols" translate="no">location_on</span>
								<?php _e( 'Store address', 'steel-eshop' ) ?>
							</div>
							<span class="footer__contact-text">
								<?php
									echo esc_html( get_option('woocommerce_store_address') ) .
									', ' . esc_html( get_option('woocommerce_store_city') );
								?>
							</span>
						</div>

						<div class="footer__contact-item">
							<div class="footer__contact-title">
								<span class="footer__contact-icon material-symbols" translate="no">mail</span>
								Email
							</div>
							<span class="footer__contact-text">
								<?php
									$store_email = get_option('woocommerce_pos_store_email') ?: get_option('admin_email');
									if ( $store_email ) :
								?>
									<a href="mailto:<?php echo esc_attr( $store_email ); ?>" class="footer__contact-link">
										<?php echo esc_html( $store_email ); ?>
									</a>
								<?php endif; ?>
							</span>
						</div>

						<div class="footer__contact-item">
							<div class="footer__contact-title">
								<span class="footer__contact-icon material-symbols" translate="no">call</span>
								<?php _e( 'Phone numbers', 'steel-eshop' ) ?>
							</div>
							<span class="footer__contact-text">
								<?php
									$store_phone = get_option('woocommerce_pos_store_phone');
									if ( $store_phone ) :
								?>
									<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $store_phone ) ); ?>" class="footer__contact-link">
										<?php echo esc_html( $store_phone ); ?>
									</a>
								<?php endif; ?>
							</span>
						</div>

						<div class="footer__contact-item">
							<div class="footer__contact-title">
								<span class="footer__contact-icon material-symbols" translate="no">schedule</span>
								<?php _e( 'Work schedule', 'steel-eshop' ) ?>
							</div>
							<span class="footer__contact-text">
								<?php echo wp_kses_post($steel_theme_settings['footer_work_schedule']); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- copyright -->
		<div class="footer__copyright">
			<div class="container">
				<?php echo esc_html( $steel_theme_settings['footer_copyright'] ); ?>
			</div>
		</div>
	</footer>
	<!-- END FOOTER -->

	<!-- START OFFCANVAS -->
	<?php
		get_template_part( 'template-parts/modal/mini-cart' );
		get_template_part( 'template-parts/modal/mobile-menu' );
		if ( function_exists( 'is_product' ) && is_product() ) {
			get_template_part( 'template-parts/modal/reviews-form' );
		}
		get_template_part( 'template-parts/modal/wishlist' );
	?>
	<!-- END OFFCANVAS -->

	<!-- START WP_FOOTER -->
	<?php wp_footer(); ?>
	<!-- END WP_FOOTER -->

</body>
</html>