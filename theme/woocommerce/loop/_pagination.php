<?php
/**
 * Custom Pagination Template
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

$links = paginate_links(
	[
		'base'         => $base,
		'format'       => $format,
		'add_args'     => false,
		'current'      => max( 1, $current ),
		'total'        => $total,
		'prev_text'    => '<span class="material-symbols" translate="no">keyboard_arrow_left</span>',
		'next_text'    => '<span class="material-symbols" translate="no">keyboard_arrow_right</span>',
		'type'         => 'array',
		'end_size'     => 1,
		'mid_size'     => 1,
	]
);

if ( is_array( $links ) ) {
	$prev_link = '';
	$next_link = '';
	$page_links = array();

	foreach ( $links as $link ) {
		if ( str_contains( $link, 'prev' ) ) {
			$prev_link = $link;
		} elseif ( str_contains( $link, 'next' ) ) {
			$next_link = $link;
		} else {
			$page_links[] = $link;
		}
	}

	echo '<nav class="pagination">';

	if ( $prev_link ) {
		echo str_replace( 'class="prev page-numbers', 'class="pagination__link button', $prev_link );
	}

	if ( ! empty( $page_links ) ) {
		echo '<div class="pagination__page-numbers scroll-shadow">';
		echo '<div class="scroll-shadow__left"></div>';
		echo '<ul class="pagination__list" data-check-scroll="horizontal">';

		foreach ( $page_links as $page ) {
			if ( str_contains( $page, 'current' ) ) {
				$page = str_replace( 'page-numbers current', 'pagination__link button is-current', $page );
			} else {
				$page = str_replace( 'page-numbers', 'pagination__link button button--transparent', $page );
			};				

			echo '<li>' . $page . '</li>';
		}

		echo '</ul>';
		echo '<div class="scroll-shadow__right"></div>';
		echo '</div>';
	}

	if ( $next_link ) {
		echo str_replace( 'class="next page-numbers', 'class="pagination__link button', $next_link );
	}
	
	echo '</nav>';
}
