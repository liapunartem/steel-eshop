<?php
/**
 * Template Name: Catalog
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="page-catalog-main">
    <div class="container">
        <?php woocommerce_breadcrumb(); ?>
    </div>

    <section class="container">
        <header><h1><?php _e( 'Catalog', 'steel-eshop' ); ?></h1></header>
        <ul class="category-grid">
            <?php
                $categories = steel_get_main_categories();
                if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                    foreach ( $categories as $cat ) {
                        echo '<li>';
                        get_template_part( 'template-parts/product-cat', null, [ 'category' => $cat ] );
                        echo '</li>';
                    }
                }
            ?>
        </ul>
    </section>

</main>

<?php get_footer(); ?>