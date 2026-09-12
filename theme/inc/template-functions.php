<?php
defined( 'ABSPATH' ) || exit;


function steel_the_popular_products_carousel( $type = 'sales', $count = 10 ) {
    get_template_part(
        'template-parts/product-carousel',
        null,
        [
            'products' => steel_get_popular_products( $type, $count ),
            'section_class' => 'popular-products',
            'section_title' => __( 'Popular products', 'steel-eshop' ),
        ]
    );
}

function steel_the_new_product_carousel( $count = 10 ) {
    get_template_part(
        'template-parts/product-carousel',
        null,
        [
            'products' => steel_get_new_products( $count ),
            'section_class' => 'new-products',
            'section_title' => __( 'New products', 'steel-eshop' ),
        ]
    );
}

function steel_the_featured_products_carousel( $count = 10 ) {
    get_template_part(
        'template-parts/product-carousel',
        null,
        [
            'products' => steel_get_featured_products( $count ),
            'section_class' => 'featured-products',
            'section_title' => __( 'Featured products', 'steel-eshop' ),
        ]
    );
}

/**
 * Displays the opening tag of the add to cart form depending on the product type.
 */
function steel_the_product_add_to_cart_form_open() {
    $product = $GLOBALS['product'];

    if ( ! $product ) {
        return;
    }

    $product_type = $product->get_type();

    if ( $product_type === 'simple' ) {
        ?>
        <form
            class="cart"
            action="<?php echo esc_url( $product->get_permalink() ); ?>"
            method="post"
            enctype='multipart/form-data'
        >
        <?php
    }
    elseif ( $product_type === 'variable' ) {
        $variations_json = wp_json_encode( $product->get_available_variations() );
        $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

        ?>
        <form
            class="variations_form cart"
            action="<?php echo esc_url( $product->get_permalink() ); ?>"
            method="post" enctype='multipart/form-data'
            data-product_id="<?php echo absint( $product->get_id() ); ?>"
            data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>"
        >
        <?php
    }
    elseif ( $product_type === 'grouped' ) {
        ?>
        <form
            class="cart grouped_form"
            action="<?php echo esc_url( $product->get_permalink() ); ?>"
            method="post"
            enctype='multipart/form-data'
        >
        <?php
    }
    elseif ( $product_type === 'external' ) {
        ?><form class="cart" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="get"><?php
    }

    do_action( 'woocommerce_before_add_to_cart_button' );
}

/**
 * Returns the link of the next page from the pagination
 */
function steel_get_next_page_url(): string {
    $current = wc_get_loop_prop( 'current_page' );
    $total   = wc_get_loop_prop( 'total_pages' );

    if ( $current >= $total ) {
        return '';
    }

    return get_pagenum_link( $current + 1 );
}


add_action( 'wp_ajax_st_load_more', 'st_load_more' );
add_action( 'wp_ajax_nopriv_st_load_more', 'st_load_more' );

/**
 * Load the next batch of content via AJAX.
 *
 * @return void
 */
function st_load_more() {
    check_ajax_referer( 'st_load_more', 'nonce' );

    $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'posts';
    $page = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;

    switch ( $type ) {
        case 'reviews':
            st_load_more_reviews( $page );
            break;

        case 'posts':
            st_load_more_posts( $page );
            break;

        default:
            wp_send_json_error( array( 'message' => 'Invalid content type.' ), 400 );
    }
}

/**
 * Load CPT or Posts via WP_Query.
 */
function st_load_more_posts( int $page ): void {
    $allowed_post_types = [ 'post', 'product' ];
    $allowed_templates  = [ 'product', 'product-cat' ];

    $raw_post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'post';
    $post_type     = in_array( $raw_post_type, $allowed_post_types, true ) ? $raw_post_type : 'post';

    $raw_template  = isset( $_POST['template'] ) ? sanitize_file_name( $_POST['template'] ) : '';
    $template      = in_array( $raw_template, $allowed_templates, true ) ? $raw_template : '';

    $posts_per_page = isset( $_POST['posts_per_page'] ) ? min( 50, max( 1, absint( $_POST['posts_per_page'] ) ) ) : 12;

    $args = array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
    );

    // Додаємо специфічне сортування WooCommerce, якщо підвантажуємо товари
    if ( 'product' === $post_type && class_exists( 'WooCommerce' ) ) {
        $ordering = WC()->query->get_catalog_ordering_args();
        $args['orderby']  = $ordering['orderby'];
        $args['order']    = $ordering['order'];
        if ( isset( $ordering['meta_key'] ) ) {
            $args['meta_key'] = $ordering['meta_key'];
        }
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            if ( 'product' === $post_type ) {
                echo '<li class="product-grid__item">';
                wc_get_template_part( 'content', 'product' );
                echo '</li>';
            } else {
                get_template_part( 'template-parts/' . $template );
            }
        }
        wp_reset_postdata();
    }

    wp_send_json_success(
        array(
            'html'      => ob_get_clean(),
            'current'   => $page,
            'max_pages' => $query->max_num_pages,
            'has_more'  => $page < $query->max_num_pages,
        )
    );
}

/**
 * Load product reviews via AJAX independently of WP Discussion settings.
 *
 * @param int $page Current page.
 * @return void
 */
function st_load_more_reviews( int $page ): void {
    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

    if ( ! $product_id ) {
        wp_send_json_error( array( 'message' => 'Invalid product ID.' ), 400 );
    }

    $comments_per_page = (int) get_option( 'comments_per_page' );

    $total_comments = (int) get_comments( array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'type'    => 'review',
        'count'   => true,
    ) );

    $max_pages = (int) ceil( $total_comments / $comments_per_page );

    $comments = get_comments( array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'type'    => 'review',
        'number'  => $comments_per_page,
        'offset'  => ( $page - 1 ) * $comments_per_page,
        'order'   => 'DESC',
        'orderby' => 'comment_date_gmt',
    ) );

    ob_start();

    wp_list_comments(
        array(
            'callback' => 'woocommerce_comments',
            'type'     => 'review',
        ),
        $comments
    );

    wp_send_json_success(
        array(
            'html'      => ob_get_clean(),
            'current'   => $page,
            'max_pages' => $max_pages,
            'has_more'  => $page < $max_pages,
        )
    );
}




/**
 * Допоміжні функції для перевірки та отримання списку обраного
 */
// function steel_get_user_wishlist( $user_id = 0 ) {
// 	if ( ! $user_id ) {
// 		$user_id = get_current_user_id();
// 	}
// 	if ( ! $user_id ) {
// 		return [];
// 	}

// 	$wishlist = get_user_meta( $user_id, 'steel_wishlist', true );
// 	return is_array( $wishlist ) ? array_map( 'absint', $wishlist ) : [];
// }

// function steel_is_in_wishlist( $product_id, $user_id = 0 ) {
// 	$wishlist = steel_get_user_wishlist( $user_id );
// 	return in_array( (int) $product_id, $wishlist, true );
// }

// /**
//  * 3. Обробник AJAX-запиту (Тільки для авторизованих)
//  */
// add_action( 'wp_ajax_steel_toggle_wishlist', 'steel_toggle_wishlist_cb' );
// function steel_toggle_wishlist_cb() {
// 	// Перевірка nonce
// 	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'steel_wishlist_nonce' ) ) {
// 		wp_send_json_error( [ 'message' => __( 'Невірно передано токен безпеки.', 'steel-eshop' ), ] );
// 	}

// 	// Перевірка авторизації
// 	if ( ! is_user_logged_in() ) {
// 		wp_send_json_error( [ 'message' => __( 'Авторизація обовʼязкова.', 'steel-eshop' ), ] );
// 	}

// 	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
// 	$product    = wc_get_product( $product_id );

// 	if ( ! $product || 'publish' !== $product->get_status() ) {
// 		wp_send_json_error( [ 'message' => __( 'Товар не знайдено або він недоступний.', 'steel-eshop' ), ] );
// 	}

// 	$user_id  = get_current_user_id();
// 	$wishlist = steel_get_user_wishlist( $user_id );

// 	if ( in_array( $product_id, $wishlist, true ) ) {
// 		// Видаляємо з обраного
// 		$wishlist = array_diff( $wishlist, [ $product_id, ] );
// 		$action   = 'removed';
// 		$message  = __( 'Товар видалено з обраного.', 'steel-eshop' );
// 	} else {
// 		// Додаємо в обране
// 		$wishlist[] = $product_id;
// 		$action     = 'added';
// 		$message    = __( 'Товар додано в обране.', 'steel-eshop' );
// 	}

// 	// Оновлюємо метадані користувача
// 	update_user_meta( $user_id, 'steel_wishlist', array_values( array_unique( $wishlist ) ) );

// 	wp_send_json_success( [
// 		'action'     => $action,
// 		'product_id' => $product_id,
// 		'count'      => count( $wishlist ),
// 		'message'    => $message,
// 	] );
// }




/**
 * Retrieve wishlist product IDs from cookies.
 */
function steel_get_wishlist_ids() {
	if ( ! isset( $_COOKIE['steel_wishlist'] ) || empty( $_COOKIE['steel_wishlist'] ) ) {
		return array();
	}
	$ids = explode( ',', $_COOKIE['steel_wishlist'] );
	return array_map( 'absint', array_filter( $ids ) );
}

/**
 * Save wishlist product IDs to cookie.
 */
function steel_set_wishlist( array $wishlist ) {
	$wishlist_string = implode( ',', array_unique( array_map( 'absint', $wishlist ) ) );
	$is_ssl          = is_ssl();
	setcookie( 'steel_wishlist', $wishlist_string, [
		'expires'  => time() + ( 3600 * 24 * 30 ),
		'path'     => '/',
		'secure'   => $is_ssl,
		'httponly' => false,
		'samesite' => 'Lax',
	] );
}

/**
 * Check if product is in wishlist.
 */
function steel_is_in_wishlist( $product_id ) {
	$wishlist = steel_get_wishlist_ids();
	return in_array( (int) $product_id, $wishlist, true );
}

/**
 * AJAX handler for toggling wishlist items.
 */
add_action( 'wp_ajax_steel_toggle_wishlist', 'steel_toggle_wishlist_cb' );
add_action( 'wp_ajax_nopriv_steel_toggle_wishlist', 'steel_toggle_wishlist_cb' );

function steel_toggle_wishlist_cb() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'steel_wishlist_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'steel-eshop' ) ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = wc_get_product( $product_id );

	if ( ! $product || 'publish' !== $product->get_status() ) {
		wp_send_json_error( array( 'message' => __( 'Invalid or unavailable product.', 'steel-eshop' ) ) );
	}

	$wishlist = steel_get_wishlist_ids();

	if ( in_array( $product_id, $wishlist, true ) ) {
		$wishlist = array_diff( $wishlist, array( $product_id ) );
		$action   = 'removed';
		$message  = __( 'Product removed from wishlist', 'steel-eshop' );
	} else {
		$wishlist[] = $product_id;
		$action     = 'added';
		$message    = __( 'Product added to wishlist', 'steel-eshop' );
	}

	$wishlist = array_values( array_unique( $wishlist ) );
	steel_set_wishlist( $wishlist );

	wp_send_json_success( array(
		'action'     => $action,
		'product_id' => $product_id,
		'count'      => count( $wishlist ),
		'message'    => $message,
		'html'       => steel_get_wishlist_html(),
	) );
}

add_action( 'wp_ajax_steel_get_wishlist_html_ajax', 'steel_get_wishlist_html_ajax_cb' );
add_action( 'wp_ajax_nopriv_steel_get_wishlist_html_ajax', 'steel_get_wishlist_html_ajax_cb' );

function steel_get_wishlist_html_ajax_cb() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'steel_wishlist_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security error', 'steel-eshop' ) ) );
	}

	wp_send_json_success( array(
		'html'  => steel_get_wishlist_html(),
		'count' => count( steel_get_wishlist_ids() ),
	) );
}

/**
 * Render wishlist HTML list based on saved product IDs.
 */
function steel_get_wishlist_html() {
	$ids = steel_get_wishlist_ids();

	ob_start();
	?>
	<div class="wishlist">
		<?php if ( ! empty( $ids ) ) : ?>

			<ul class="wishlist__list">
				<?php
				foreach ( $ids as $product_id ) {
					$_product = wc_get_product( $product_id );

					if ( ! $_product || ! $_product->exists() || 'publish' !== $_product->get_status() ) {
						continue;
					}

					$product_name      = $_product->get_name();
					$thumbnail         = $_product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'wishlist__thumbnail' ) );
					$product_price     = $_product->get_price_html();
					$product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';
					?>
					<li class="wishlist__item">
						
						<!-- Delete button -->
						<a 
							role="button" 
							href="#" 
							class="wishlist__remove-button remove" 
							aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from wishlist', 'steel-eshop' ), wp_strip_all_tags( $product_name ) ) ); ?>" 
							data-product_id="<?php echo esc_attr( $product_id ); ?>"
						>
                            <div class="material-symbols">delete</div>
                        </a>

						<!-- Product image -->
						<?php if ( empty( $product_permalink ) ) : ?>
							<div class="wishlist__image-link">
								<?php echo $thumbnail; ?>
							</div>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="wishlist__image-link">
								<?php echo $thumbnail; ?>
							</a>
						<?php endif; ?>

						<!-- Information part -->
						<div class="wishlist__item-info">
							
							<!-- Product name -->
							<?php if ( empty( $product_permalink ) ) : ?>
								<span class="wishlist__product-title"><?php echo wp_kses_post( $product_name ); ?></span>
							<?php else : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>" class="wishlist__product-title">
									<?php echo wp_kses_post( $product_name ); ?>
								</a>
							<?php endif; ?>

							<!-- Price -->
                            <div class="wishlist__price">
                                <?php echo $product_price; ?>
                            </div>

						</div>
					</li>
					<?php
				}
				?>
			</ul>

		<?php else : ?>

			<p class="wishlist__empty-message">
				<?php esc_html_e( 'No products in the wishlist.', 'steel-eshop' ); ?>
			</p>

		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Render the dropdown categories menu.
 *
 * @param array $args Optional arguments for category query.
 * @return void
 */
function steel_dropdown_categories( $args = [] ) {
	get_template_part( 'template-parts/dropdown-categories', null, $args );
}

/**
 * Render the footer categories menu.
 *
 * @param array $args Optional arguments for category query.
 * @return void
 */
function steel_footer_categories( $args = [] ) {
	get_template_part( 'template-parts/footer-categories', null, $args );
}