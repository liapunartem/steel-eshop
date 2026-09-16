<?php
defined( 'ABSPATH' ) || exit;

/**
 * Walker class for rendering dropdown categories via wp_nav_menu.
 *
 * @deprecated Use steel_dropdown_categories() or get_template_part('template-parts/dropdown-categories') instead,
 *             which automatically queries product_cat taxonomy hierarchy.
 */
class Steel_Dropdown_Categories extends Walker_Nav_Menu {

    // Start submenu <ul>
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-categories__sublist\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    // Start <li>
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        $indent = str_repeat("\t", $depth);

        // Level-based classes
        $item_class = $depth === 0 
            ? 'dropdown-categories__item' 
            : 'dropdown-categories__subitem';

        $output .= "$indent<li class=\"$item_class\">";

        // Link
        $output .= '<a href="' . esc_url($item->url) . '" class="dropdown-categories__link">';

        $output .= '<span class="dropdown-categories__title">' . esc_html($item->title) . '</span>';

        // Icon only if has children AND only for top level (як у твоєму HTML)
        if ( ! empty( $args->walker->has_children ) && $depth === 0 ) {
            $output .= '<span class="dropdown-categories__icon material-symbols" translate="no">keyboard_arrow_right</span>';
        }

        $output .= '</a>';
    }

    // Close </li>
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}