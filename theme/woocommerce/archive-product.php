<?php
/**
 * Product archive template
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'woocommerce_before_main_content' );
?>

<main class="archive-product">
    <div class="container">
        <?php woocommerce_breadcrumb(); ?>

        <!-- category carousel -->
        <?php
            $categories = steel_get_sub_categories();
            if ( $categories && !is_search() ):
        ?>
            <div class="category-carousel swiper">

                <ul class="swiper-wrapper">
                    <?php foreach ($categories as $cat): ?>
                        <li class="swiper-slide">
                            <?php get_template_part('template-parts/product-cat', null, ['category' => $cat, 'class' => 'category-card--small']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Navigation -->
                <button class="swiper__button swiper__button--absolute swiper__button--prev button button--rounded" aria-label="<?php esc_attr_e( 'Previous categories', 'steel-eshop' ); ?>">
                    <span class="material-symbols" translate="no">keyboard_arrow_left</span>
                </button>
                <button class="swiper__button swiper__button--absolute swiper__button--next button button--rounded" aria-label="<?php esc_attr_e( 'Next categories', 'steel-eshop' ); ?>">
                    <span class="material-symbols" translate="no">keyboard_arrow_right</span>
                </button>

            </div>
        <?php endif; ?>
    </div>

    <div class="container <?php echo ( is_search() || is_shop() ) ? '': 'sidebar-layout'; ?>">

        <!-- sidebar with filters -->
        <?php
            if ( !is_search() && !is_shop() ) {
                echo '<aside class="sidebar-layout__sidebar" aria-label="' . esc_attr__( 'Product Filters', 'steel-eshop' ) . '">';
                get_template_part('template-parts/product-filters');
                echo '</aside>';
            }
        ?>
        
        <!-- products -->
        <section class="products <?php echo ( is_search() || is_shop() ) ? '': 'sidebar-layout__content'; ?>">

            <!-- title and ordering -->
            <header class="products__header">
                <h1 class="products__title"><?php woocommerce_page_title(); ?></h1>
            </header>

            <div class="products__ordering-wrap">
                <?php if ( !is_search() ): ?>
                    <button class="products__filters-button button visible-tablet"
                        data-modal="product-filters" data-lock-class="no-scroll-tablet"
                        aria-label="<?php esc_attr_e( 'Filter products', 'steel-eshop' ); ?>"
                    >
                        <span class="material-symbols" translate="no">tune</span>
                    </button>
                <?php endif; ?>
                <?php woocommerce_catalog_ordering(); ?>
            </div>

            <?php do_action( 'woocommerce_before_shop_loop' ); ?>

            <!-- product list -->
            <?php if ( have_posts() ) : ?>

                <ul
                    class="products__grid"
                    data-infinite-scroll="pagination"
                    data-infinite-scroll-item=".product-grid__item"
                >
                    <?php while ( have_posts() ) : the_post(); ?>

                        <li class="product-grid__item">
                            <?php wc_get_template_part( 'content', 'product' ); ?>
                        </li>

                    <?php endwhile; ?>
                </ul>

            <?php else : ?>

                <div class="products__empty">
                    <span class="material-symbols" translate="no">search_off</span>
                    <?php _e( 'Unfortunately, nothing was found.', 'steel-eshop' ); ?>
                </div>

            <?php endif; ?>

            <?php
                $next_url = steel_get_next_page_url();

                if ( $next_url ) :
            ?>
                <div
                    class="infinite-scroll-loader loader"
                    data-infinite-scroll-loader
                    data-next-url="<?php echo esc_url( $next_url ); ?>"
                    aria-hidden="true"
                ></div>
            <?php endif; ?>
            
        </section>
    </div> 

</main>

<?php
do_action( 'woocommerce_after_main_content' );
get_footer(); ?>