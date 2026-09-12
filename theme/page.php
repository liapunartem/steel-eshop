<?php get_header() ?>

<main>
	<div class="container">
        <?php woocommerce_breadcrumb(); ?>
    </div>
	
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title() ?></h1>
			<?php the_content(); ?>
		<?php endwhile; else: ?>
			<h1><?php _e( 'Nothing Found', 'steel-eshop' ) ?></h1>
		<?php endif; ?>
	</div>
</main>

<?php get_footer() ?>