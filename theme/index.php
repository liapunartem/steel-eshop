<?php
/**
 * Main fallback template file
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="container default-layout">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1><?php the_title(); ?></h1>
                </header>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e( 'Nothing found.', 'steel-eshop' ); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
