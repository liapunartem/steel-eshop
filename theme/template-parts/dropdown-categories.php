<?php
defined( 'ABSPATH' ) || exit;

$category_tree = steel_get_dropdown_categories_hierarchy( $args ?? [] );

if ( empty( $category_tree['parents'] ) ) {
    return;
}

$parents  = $category_tree['parents'];
$children = $category_tree['children'];
?>

<ul class="dropdown-categories">
    <?php foreach ( $parents as $parent_id => $parent ) :
        $has_children = ! empty( $children[ $parent_id ] );
    ?>
        <li class="dropdown-categories__item">
            <a href="<?php echo esc_url( get_term_link( $parent ) ); ?>" class="dropdown-categories__link">
                <span class="dropdown-categories__title"><?php echo esc_html( $parent->name ); ?></span>
                <?php if ( $has_children ) : ?>
                    <span class="dropdown-categories__icon material-symbols" aria-hidden="true">keyboard_arrow_right</span>
                <?php endif; ?>
            </a>

            <?php if ( $has_children ) : ?>
                <ul class="dropdown-categories__sublist">
                    <?php foreach ( $children[ $parent_id ] as $child ) : ?>
                        <li class="dropdown-categories__subitem">
                            <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="dropdown-categories__link">
                                <span class="dropdown-categories__title"><?php echo esc_html( $child->name ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

