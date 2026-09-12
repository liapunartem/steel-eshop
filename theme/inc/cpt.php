<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

	register_post_type( 'slide', [
		'labels'             => [
			'name'          => __( 'Slide', 'steel-eshop' ),
			'singular_name' => __( 'Slide', 'steel-eshop' ),
			'add_new'       => __( 'Add new slide', 'steel-eshop' ),
			'add_new_item'  => __( 'New slide', 'steel-eshop' ),
			'edit_item'     => __( 'Edit', 'steel-eshop' ),
			'new_item'      => __( 'New slide', 'steel-eshop' ),
			'view_item'     => __( 'View', 'steel-eshop' ),
			'menu_name'     => __( 'Slides', 'steel-eshop' ),
			'all_items'     => __( 'All slides', 'steel-eshop' ),
        ],
		'public'             => false,
		'show_ui'            => true,
		'publicly_queryable' => false,
		'exclude_from_search'=> true,
		'show_in_menu'       => true,
		'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
		'menu_icon'          => 'dashicons-format-gallery',
		'show_in_rest'       => true,
	] );

} );

add_action('add_meta_boxes', function() {
    add_meta_box(
        'slide_fields',
        'Slide Settings',
        function($post){
            wp_nonce_field( 'steel_save_slide_meta', 'steel_slide_meta_nonce' );

            $is_custom    = get_post_meta($post->ID, '_is_custom', true);
            $btn_text     = get_post_meta($post->ID, '_btn_text', true);
            $btn_link     = get_post_meta($post->ID, '_btn_link', true);
            $custom_class = get_post_meta($post->ID, '_custom_class', true);
            ?>

            <p>
                <label>
                    <input type="checkbox" name="is_custom" value="1" <?php checked($is_custom, '1'); ?>>
                    <?php _e( 'Custom content', 'steel-eshop' ) ?>
                </label>
            </p>

            <div class="steel-default-fields" style="<?php echo $is_custom ? 'display:none;' : ''; ?>">
                <p>
                    <label><?php _e( 'Button Text', 'steel-eshop' ) ?></label><br>
                    <input type="text" name="btn_text" value="<?php echo esc_attr($btn_text); ?>" class="widefat">
                </p>

                <p>
                    <label><?php _e( 'Button Link', 'steel-eshop' ) ?></label><br>
                    <input type="text" name="btn_link" value="<?php echo esc_attr($btn_link); ?>" class="widefat">
                </p>
            </div>

            <div class="steel-custom-fields" style="<?php echo $is_custom ? '' : 'display:none;'; ?>">
                <p>
                    <label><?php _e( 'Custom Class', 'steel-eshop' ) ?></label><br>
                    <input type="text" name="custom_class" value="<?php echo esc_attr($custom_class); ?>" class="widefat">
                </p>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const checkbox = document.querySelector('[name="is_custom"]');
                    const defaultFields = document.querySelector('.steel-default-fields');
                    const customFields = document.querySelector('.steel-custom-fields');

                    if (!checkbox || !defaultFields || !customFields) return;

                    function toggleFields() {
                        if (checkbox.checked) {
                            defaultFields.style.display = 'none';
                            customFields.style.display = 'block';
                        } else {
                            defaultFields.style.display = 'block';
                            customFields.style.display = 'none';
                        }
                    }

                    checkbox.addEventListener('change', toggleFields);
                    toggleFields();
                });
            </script>

            <?php
        },
        'slide'
    );
});

add_action( 'save_post_slide', function( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['steel_slide_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['steel_slide_meta_nonce'] ) ), 'steel_save_slide_meta' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $is_custom = isset( $_POST['is_custom'] ) ? '1' : '0';
    update_post_meta( $post_id, '_is_custom', $is_custom );

    if ( isset( $_POST['btn_text'] ) ) {
        update_post_meta( $post_id, '_btn_text', sanitize_text_field( wp_unslash( $_POST['btn_text'] ) ) );
    }

    if ( isset( $_POST['btn_link'] ) ) {
        update_post_meta( $post_id, '_btn_link', esc_url_raw( wp_unslash( $_POST['btn_link'] ) ) );
    }

    if ( isset( $_POST['custom_class'] ) ) {
        update_post_meta( $post_id, '_custom_class', sanitize_html_class( wp_unslash( $_POST['custom_class'] ) ) );
    }
} );