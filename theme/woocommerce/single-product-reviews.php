<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div class="reviews">
	<div class="reviews__list-wrapper">
		<h2 class="reviews__title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'woocommerce' );
			}
			?>
		</h2>

		<?php if ( have_comments() ) : ?>

			<?php 
				$comments_per_page = (int) get_option( 'comments_per_page' );

				$total_comments = (int) get_comments( array(
					'post_id' => $product->get_id(),
					'status'  => 'approve',
					'type'    => 'review',
					'count'   => true,
				) );

				$max_pages = (int) ceil( $total_comments / $comments_per_page );

				$first_page_comments = get_comments( array(
					'post_id' => $product->get_id(),
					'status'  => 'approve',
					'type'    => 'review',
					'number'  => $comments_per_page,
					'offset'  => 0,
					'order'   => 'DESC',
					'orderby' => 'comment_date_gmt',
				) );
			?>

			<ol
				class="reviews__list"
				data-infinite-scroll="ajax"
				data-type="reviews"
				data-product-id="<?php echo esc_attr( get_the_ID() ); ?>"
				data-page="1"
				data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
			>
				<?php
					wp_list_comments(
						array(
							'callback' => 'woocommerce_comments',
							'type'     => 'review',
							'per_page' => $comments_per_page,
						),
						$first_page_comments
					); 
				?>
			</ol>

			<?php if ( $max_pages > 1 ) : ?>
				<div
					class="infinite-scroll-loader loader"
					data-infinite-scroll-loader
					aria-hidden="true"
				></div>
			<?php endif; ?>

		<?php else : ?>
			<p class="reviews__noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<button data-modal="modal-reviews-form" class="button"><?php _e( 'Leave a review', 'steel-eshop' ); ?></button>
	<?php else : ?>
		<p class="reviews__verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
	<?php endif; ?>

	<div class="clear"></div>
</div>
