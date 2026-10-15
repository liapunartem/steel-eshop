<?php
/**
 * 404 Error template
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="container not-found-page">
    <div class="products__empty">
        <span class="material-symbols" translate="no" style="font-size: 4rem;">error</span>
        <h1><?php esc_html_e( '404 — Сторінку не знайдено', 'steel-eshop' ); ?></h1>
        <p><?php esc_html_e( 'Сторінка, яку ви шукаєте, не існує або була переміщена.', 'steel-eshop' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button">
            <?php esc_html_e( 'Перейти на головну', 'steel-eshop' ); ?>
        </a>
    </div>
</main>

<?php get_footer(); ?>

