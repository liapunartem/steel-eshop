<?php
defined( 'ABSPATH' ) || exit;

$products      = $args['products'] ?? [];
$section_class = $args['section_class'] ?? '';
$section_title = $args['section_title'] ?? '';

if ( empty( $products ) ) {
    return;
}
?>

<section class="section <?php echo esc_attr( $section_class ); ?>">
    <div class="product-carousel swiper swiper--carousel">
        <header class="section__header">
            <h2><?php echo esc_html( $section_title ); ?></h2>

            <!-- navigation -->
            <div class="swiper__buttons-wrap">
                <button class="swiper__button swiper__button--prev button" aria-label="<?php esc_attr_e( 'Previous products', 'steel-eshop' ); ?>">
                    <span class="material-symbols">keyboard_arrow_left</span>
                </button>
                <button class="swiper__button swiper__button--next button" aria-label="<?php esc_attr_e( 'Next products', 'steel-eshop' ); ?>">
                    <span class="material-symbols">keyboard_arrow_right</span>
                </button>
            </div>
        </header>

        <ul class="swiper-wrapper">
            <?php
                global $post;
                foreach ( $products as $product ) {
                    $post = get_post( $product->get_id() );
                    if ( ! $post ) {
                        continue;
                    }
                    setup_postdata( $post );

                    echo '<li class="swiper-slide">';
                        wc_get_template_part( 'content', 'product' );
                    echo '</li>';
                }
                
                wp_reset_postdata();
            ?>
        </ul>
        <!-- pagination -->
        <div class="swiper__pagination"></div>
    </div>
</section>