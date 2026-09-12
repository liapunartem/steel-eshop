<?php
/**
 * Related Products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) {
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}

	get_template_part(
		'template-parts/product-carousel',
		null,
		[
			'products' => $related_products,
			'section_class' => 'related-products',
			'section_title' => __( 'Related products', 'steel-eshop' ),
		]
	);

}