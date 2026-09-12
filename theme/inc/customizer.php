<?php
defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'steel_remove_default_customizer_settings', 10 );
add_action( 'customize_register', 'steel_add_customizer_settings', 20 );


function steel_remove_default_customizer_settings( WP_Customize_Manager $wp_customize ) {
    $wp_customize->remove_setting( 'show_on_front' );
    $wp_customize->remove_control( 'show_on_front' );

    $wp_customize->remove_setting( 'page_on_front' );
    $wp_customize->remove_control( 'page_on_front' );

    $wp_customize->remove_setting( 'page_for_posts' );
    $wp_customize->remove_control( 'page_for_posts' );

    $wp_customize->remove_setting( 'woocommerce_shop_page_display' );
    $wp_customize->remove_control( 'woocommerce_shop_page_display' );

    $wp_customize->remove_setting( 'woocommerce_category_archive_display' );
    $wp_customize->remove_control( 'woocommerce_category_archive_display' );
}

function steel_add_customizer_settings( WP_Customize_Manager $wp_customize ) {
    $wp_customize->add_section( 'header_and_footer',[
        'title' => __( 'Header and footer', 'steel-eshop' ),
        'priority' => 10,
    ]);
    
    $wp_customize->add_setting( 'header_work_schedule', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ] );
    $wp_customize->add_control( 'header_work_schedule', [
        'label'       => __( 'Work schedule in the header', 'steel-eshop' ),
        'section'     => 'header_and_footer',
        'type'        => 'textarea',
        'description' => __( 'Valid text HTML markup', 'steel-eshop' ),
    ]);

    $wp_customize->add_setting( 'footer_work_schedule', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ] );
    $wp_customize->add_control( 'footer_work_schedule', [
        'label'       => __( 'Work schedule in the footer', 'steel-eshop' ),
        'section'     => 'header_and_footer',
        'type'        => 'textarea',
        'description' => __( 'Valid text HTML markup', 'steel-eshop' ),
    ]);

    $wp_customize->add_setting( 'footer_copyright', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'footer_copyright', [
        'label'       => __( 'Copyright text in the footer', 'steel-eshop' ),
        'section'     => 'header_and_footer',
        'type'        => 'textarea',
    ]);


    $front_section = $wp_customize->get_section( 'static_front_page' );
    if ( $front_section ) {
        $front_section->description = __( 'Here you can change some of the content that will be displayed on the home page', 'steel-eshop' );
    }

    $wp_customize->add_setting( 'popular_categories', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'popular_categories', [
        'label'       => __( 'List of popular categories on the home page', 'steel-eshop' ),
        'section'     => 'static_front_page',
        'type'        => 'textarea',
        'description' => __( 'Enter the category IDs separated by commas', 'steel-eshop' ),
    ]);
}

function steel_get_theme_settings() {
    $custom_logo_id = get_theme_mod('custom_logo');

    $popular_categories_raw = get_theme_mod('popular_categories', '');
    $popular_categories = array_filter(array_map('intval', explode(',', $popular_categories_raw)));

    return [
        'header_top_text'      => get_theme_mod( 'header_top_text', '' ),
        'logo_url'             => wp_get_attachment_image_url( $custom_logo_id, 'full' ),
        'popular_categories'   => array_values( $popular_categories ),
        'header_work_schedule' => get_theme_mod( 'header_work_schedule', '' ),
        'footer_work_schedule' => get_theme_mod( 'footer_work_schedule', '' ),
        'footer_copyright' => get_theme_mod( 'footer_copyright', '' ),
    ];
}