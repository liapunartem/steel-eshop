<?php
/**
 * Custom template for ordering: reset to default if clicking active item
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$id_suffix = wp_unique_id();
?>

<form class="ordering scroll-shadow" method="get" id="sorting-form-<?php echo esc_attr( $id_suffix ); ?>">
    
    <div class="scroll-shadow__left"></div>

    <ul class="ordering__list" data-check-scroll="horizontal">
        <?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
            <?php 
                $is_active = ( $orderby === $id );
            ?>
            <li class="ordering__item">
                <button type="submit" 
                        name="orderby" 
                        value="<?php echo esc_attr( $id ); ?>"
                        onclick="<?php
                            echo $is_active ? "window.location.href='".
                            esc_url(remove_query_arg('orderby'))."'; return false;" : "";
                        ?>"
                        class="ordering__button button button--rounded button--transparent <?php
                            echo $is_active ? 'is-current' : ''; 
                        ?>"
                        aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
                    <span><?php echo esc_html( $name ); ?></span>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="scroll-shadow__right"></div>

    <input type="hidden" name="paged" value="1" />
    <?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
</form>