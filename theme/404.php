<?php
/**
 * 404 Error template
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="container not-found-page">
    <div class="products__empty">
        <span class="material-symbols" style="font-size: 4rem;">error</span>
        <h1><?php esc_html_e( '404 - Page Not Found', 'steel-eshop' ); ?></h1>
        <p><?php esc_html_e( 'The page you are looking for does not exist or has been moved.', 'steel-eshop' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button">
            <?php esc_html_e( 'Go to Homepage', 'steel-eshop' ); ?>
        </a>
    </div>
</main>

<?php get_footer(); ?>

