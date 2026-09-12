<?php
/**
 * Single Product tabs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

    <div class="product-tabs tabs">
        <!-- Tab List (Navigation) -->
        <ul class="product-tabs__list tabs__list" role="tablist">
            <?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ) : ?>
                <li class="product-tabs__item tabs__item <?php echo $is_first ? 'is-active' : ''; ?>" 
                    id="tab-title-<?php echo esc_attr( $key ); ?>" 
                    role="presentation" 
                    data-target="tab-<?php echo esc_attr( $key ); ?>">
                    
                    <a href="#tab-<?php echo esc_attr( $key ); ?>" 
                       class="product-tabs__link tabs__link"
                       role="tab" 
                       aria-controls="tab-<?php echo esc_attr( $key ); ?>">
                        <?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
                    </a>
                </li>
            <?php $is_first = false; endforeach; ?>
        </ul>

        <!-- Content panels -->
        <div class="product-tabs__content tabs__content">
            <?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ) : ?>
                <section class="product-tabs__panel tabs__panel <?php echo $is_first ? 'is-active' : ''; ?>" 
                     id="tab-<?php echo esc_attr( $key ); ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
                    
                    <div class="product-tabs__inner tabs__inner">
                        <?php
                        if ( isset( $product_tab['callback'] ) ) {
                            call_user_func( $product_tab['callback'], $key, $product_tab );
                        }
                        ?>
                    </div>
                </section>
            <?php $is_first = false; endforeach; ?>
        </div>

        <?php do_action( 'woocommerce_product_after_tabs' ); ?>
    </div>

<?php endif; ?>
