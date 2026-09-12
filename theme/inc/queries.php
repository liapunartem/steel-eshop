<?php
defined( 'ABSPATH' ) || exit;

function steel_get_hero_slides() {
    return new WP_Query([
        'post_type'      => 'slide',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
}

function steel_get_main_categories() {
    return get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => 0,
    ]);
}

function steel_get_sub_categories() {
    $term_id = get_queried_object_id();

    return get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => $term_id,
    ]);
}

function steel_get_categories_by_ids( $ids = [] ) {
    return get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'include'    => $ids,
        'orderby'    => 'include',
    ]);
}

function steel_get_products_by_ids( $ids = [] ) {
    return new WP_Query([
        'post_type'      => 'product',
        'post__in'       => $ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
}

function steel_get_new_products( $count = 10 ) {
    $args = [
        'limit'        => $count,
        'status'       => 'publish',
        'visibility'   => 'visible',
        'paginate'     => false,
        'stock_status' => 'instock',
        'orderby' => 'date',
         
    ];

    return wc_get_products( $args );
}

function steel_get_featured_products( $count = 10 ) {
    $args = [
        'limit'        => $count,
        'status'       => 'publish',
        'visibility'   => 'visible',
        'paginate'     => false,
        'stock_status' => 'instock',
        'featured'     => true,
    ];

    return wc_get_products( $args );
}

/**
 * Get popular products by sales or views
 * @param string $type  Sort type: 'sales' or 'views'
 * @param int    $count Number of products
 */
function steel_get_popular_products( $type = 'sales', $count = 10 ) {
    $args = [
        'limit'        => $count,
        'status'       => 'publish',
        'visibility'   => 'visible',
        'paginate'     => false,
        'stock_status' => 'instock',
    ];

    if ( $type === 'sales' ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'total_sales';
        $args['order']    = 'DESC';
    } elseif ( $type === 'views' ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'total_views';
        $args['order']    = 'DESC';
    }

    return wc_get_products( $args );
}

/**
 * Retrieve product categories organized in a hierarchical tree (parents and children)
 * for the dropdown catalog menu.
 *
 * @param array $args Additional arguments for get_terms().
 * @return array Array with 'parents' and 'children' keys, or empty array on failure.
 */
function steel_get_dropdown_categories_hierarchy( $args = [] ) {
    $defaults = [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'exclude'    => [],
    ];

    $query_args = wp_parse_args( $args, $defaults );

    // Automatically exclude default 'Uncategorized' category if not explicitly specified
    if ( empty( $query_args['exclude'] ) ) {
        $default_cat_id = (int) get_option( 'default_product_cat' );
        if ( $default_cat_id ) {
            $default_term = get_term( $default_cat_id, 'product_cat' );
            if ( $default_term && ! is_wp_error( $default_term ) ) {
                if ( 'uncategorized' === $default_term->slug || 0 === (int) $default_term->count ) {
                    $query_args['exclude'] = [ $default_cat_id ];
                }
            }
        }
    }

    $terms = get_terms( $query_args );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return [];
    }

    $parents  = [];
    $children = [];

    foreach ( $terms as $term ) {
        if ( 0 === (int) $term->parent ) {
            $parents[ $term->term_id ] = $term;
        } else {
            $children[ (int) $term->parent ][] = $term;
        }
    }

    return [
        'parents'  => $parents,
        'children' => $children,
    ];
}

/**
 * Retrieve top-level product categories for the footer menu.
 *
 * @param array $args Additional arguments for get_terms().
 * @return array List of top-level WP_Term objects.
 */
function steel_get_footer_categories( $args = [] ) {
    $defaults = [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'exclude'    => [],
    ];

    $query_args = wp_parse_args( $args, $defaults );

    // Automatically exclude default 'Uncategorized' category if not explicitly specified
    if ( empty( $query_args['exclude'] ) ) {
        $default_cat_id = (int) get_option( 'default_product_cat' );
        if ( $default_cat_id ) {
            $default_term = get_term( $default_cat_id, 'product_cat' );
            if ( $default_term && ! is_wp_error( $default_term ) ) {
                if ( 'uncategorized' === $default_term->slug || 0 === (int) $default_term->count ) {
                    $query_args['exclude'] = [ $default_cat_id ];
                }
            }
        }
    }

    $terms = get_terms( $query_args );

    return ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms : [];
}