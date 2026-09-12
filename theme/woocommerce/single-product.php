<?php
/**
 * The Template for displaying all single products
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'woocommerce_before_main_content' );
?>

<main class="single-product-main">
	<div class="container">
        <?php woocommerce_output_all_notices(); ?>
        <?php woocommerce_breadcrumb(); ?>
    </div>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<?php wc_get_template_part( 'content', 'single-product' ); ?>

	<?php endwhile; ?>
</main>

<?php
do_action( 'woocommerce_after_main_content' );
get_footer();

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
