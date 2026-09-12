<?php
defined( 'ABSPATH' ) || exit;

$categories = steel_get_footer_categories( $args ?? [] );

if ( empty( $categories ) ) {
    return;
}

$current_cat_id = ( function_exists( 'is_product_category' ) && is_product_category() ) ? (int) get_queried_object_id() : 0;
?>

<ul id="menu-footer-categories-menu" class="footer__categories-list footer__menu menu">
    <?php foreach ( $categories as $cat ) :
        $is_current = ( $current_cat_id === (int) $cat->term_id );
        $item_class = 'menu__item' . ( $is_current ? ' is-current' : '' );
    ?>
        <li class="<?php echo esc_attr( $item_class ); ?>">
            <a class="menu__link" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                <?php echo esc_html( $cat->name ); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

