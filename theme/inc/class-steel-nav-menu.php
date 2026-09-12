<?php
defined( 'ABSPATH' ) || exit;

class Steel_Nav_Menu extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        // Base class
        $classes = ['menu__item'];

        // Active state
        $item_classes = is_array( $item->classes ) ? $item->classes : [];

        if ( in_array('current-menu-item', $item_classes) || in_array('current_page_item', $item_classes) ) {
            $classes[] = 'is-current';
        }


        $class_names = implode(' ', $classes);

        $output .= '<li id="menu-item-' . $item->ID . '" class="' . esc_attr($class_names) . '">';

        $output .= '<a class="menu__link" href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }

}