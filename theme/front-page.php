<?php
get_header();
global $steel_theme_settings;
?>

<main class="front-page">
    <div class="sidebar-layout container">

        <!-- sidebar -->
        <aside class="sidebar-layout__sidebar hidden-tablet">
            <?php steel_dropdown_categories(); ?>
        </aside>

        <!-- content near the sidebar -->
        <div class="sidebar-layout__content">
            
            <!-- hero -->
            <section class="hero-slider swiper swiper--slider">
                <div class="swiper-wrapper">

                    <?php
                    $slides = steel_get_hero_slides();
                    $slide_index = 0;
                    while( $slides->have_posts() ) : $slides->the_post();
                        $id = get_the_ID();
                        $is_custom  = get_post_meta($id, '_is_custom', true);
                        $custom_class = get_post_meta($id, '_custom_class', true);
                        $btn_text = get_post_meta($id, '_btn_text', true);
                        $btn_link = get_post_meta($id, '_btn_link', true);
                    ?>

                        <div class="swiper-slide<?php if ( $is_custom && $custom_class ) { echo ' ' . esc_attr($custom_class); } ?>"
                        >

                            <?php the_post_thumbnail( 'full', [
                                'class'         => 'hero-slider__img',
                                'loading'       => $slide_index === 0 ? 'eager' : 'lazy',
                                'fetchpriority' => $slide_index === 0 ? 'high' : 'auto',
                            ] ); ?>

                            <div class="hero-slider__content">

                                <h2 class="hero-slider__title"><?php the_title(); ?></h2>

                                <?php if ( $is_custom ): ?>
                                    <?php echo apply_filters( 'the_content', get_post_field( 'post_content', get_the_ID() ) ); ?>
                                <?php else: ?>
                                    <div class="hero-slider__text">
                                        <?php the_content() ?>
                                    </div>

                                    <a href="<?php echo esc_url($btn_link); ?>" class="button hero-slider__button">
                                        <?php echo esc_html($btn_text); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    <?php $slide_index++; endwhile; wp_reset_postdata(); ?>
                </div>

                <!-- navigation -->
                <button class="swiper__button swiper__button--absolute swiper__button--prev button button--rounded" aria-label="<?php esc_attr_e( 'Previous slide', 'steel-eshop' ); ?>">
                    <span class="material-symbols" translate="no">keyboard_arrow_left</span>
                </button>
                <button class="swiper__button swiper__button--absolute swiper__button--next button button--rounded" aria-label="<?php esc_attr_e( 'Next slide', 'steel-eshop' ); ?>">
                    <span class="material-symbols" translate="no">keyboard_arrow_right</span>
                </button>
                <!-- pagination -->
                <div class="swiper__pagination"></div>
                
            </section>

            <!-- popular categories -->
            <section class="section">
                <header class="section__header">
                    <h2><?php _e( 'Popular categories', 'steel-eshop' ) ?></h2>
                </header>

                <ul class="category-grid category-grid--front-page">
                    <?php
                        $ids = $steel_theme_settings['popular_categories'];
                        $categories = steel_get_categories_by_ids( $ids );

                        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                            foreach ($categories as $cat) {
                                echo '<li>';
                                get_template_part(
                                    'template-parts/product-cat', null, ['category' => $cat]
                                );
                                echo '</li>';
                            }
                        }
                    ?>
                </ul>

                <footer class="section__footer">
                    <?php
                        $catalog_page = get_page_by_path( 'catalog' );
                        $catalog_url  = $catalog_page ? get_permalink( $catalog_page ) : wc_get_page_permalink( 'shop' );
                    ?>
                    <a href="<?php echo esc_url( $catalog_url ); ?>">
                        <?php _e( 'All categories', 'steel-eshop' ) ?>
                        <span class="material-symbols" translate="no">stat_minus_1</span>
                    </a>
                </footer>
            </section>

            <!-- popular products -->
            <?php steel_the_popular_products_carousel(); ?>

            <!-- new products -->
            <?php steel_the_new_product_carousel(); ?>
        </div>  
        
    </div>
    
    <section class="products section container">
        <?php
            $products_per_page = wc_get_default_product_rows_per_page() * wc_get_default_products_per_row();
            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => $products_per_page,
                'paged'          => 1,
            );

            $ordering = WC()->query->get_catalog_ordering_args();
            $args['orderby'] = $ordering['orderby'];
            $args['order']   = $ordering['order'];
            if ( isset( $ordering['meta_key'] ) ) {
                $args['meta_key'] = $ordering['meta_key'];
            }

            $shop_query = new WP_Query( $args );
        ?>

        <?php if ( $shop_query->have_posts() ) : ?>

            <header class="section__header">
                <h2><?php _e( 'All products', 'steel-eshop' ) ?></h2>
            </header>

            <!-- Контейнер для товарів -->
            <ul
                class="products__grid"
                data-infinite-scroll="ajax"
                data-type="posts"
                data-post-type="product"
                data-template="product"
                data-page="1"
                data-posts-per-page="<?php echo $products_per_page; ?>"
                data-max-pages="<?php echo esc_attr( $shop_query->max_num_pages ); ?>"
            >
                
                <?php while ( $shop_query->have_posts() ) : $shop_query->the_post(); ?>
                    <li class="product-grid__item">
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                    </li>
                <?php endwhile; ?>
                
            </ul>

            <?php if ( $shop_query->max_num_pages > 1 ) : ?>
                <div
                    class="infinite-scroll-loader loader"
                    data-infinite-scroll-loader
                    aria-hidden="true"
                ></div>
            <?php endif; ?>

        <?php endif; ?>
        <?php wp_reset_postdata(); ?>

    </section>

</main>

<?php get_footer(); ?>