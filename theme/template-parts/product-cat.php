<?php
defined( 'ABSPATH' ) || exit;

$cat   = $args['category'] ?? null;
$class = $args['class'] ?? '';

if ( ! $cat || ! isset( $cat->term_id ) ) {
    return;
}

$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
if ( $thumbnail_id ) {
    $image = wp_get_attachment_image( $thumbnail_id, 'medium', false, [
        'class'   => 'category-card__image',
        'alt'     => esc_attr( $cat->name ),
        'loading' => 'lazy',
    ]);
} else {
    $image = function_exists( 'wc_placeholder_img' )
        ? wc_placeholder_img( 'medium', [ 'class' => 'category-card__image', 'alt' => esc_attr( $cat->name ) ] )
        : '';
}
?>

<article class="category-card <?php echo esc_attr( $class ); ?>" data-cat-id="<?php echo esc_attr( $cat->term_id ); ?>">
    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">

        <div class="category-card__wrapper">
            <div class="category-card__overlay"></div>
        </div>

        <div class="category-card__wrapper">
            <span class="category-card__title">
                <?php echo esc_html( $cat->name ); ?>
                <span class="material-symbols">chevron_right</span>
            </span>
        </div>

        <?php echo $image; // WPCS: XSS ok. ?>

    </a>
</article>