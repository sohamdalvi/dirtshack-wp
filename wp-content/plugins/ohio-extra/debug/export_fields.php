<?php

// Registered on 'acf/init' (fires during 'init') so the translated labels
// don't trigger WP 6.7+ "translation loading triggered too early" notices.
add_action( 'acf/init', 'ohio_extra_register_demo_export_fields' );
function ohio_extra_register_demo_export_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_5f3a642c40bc1',
        'title' => __( 'Demo Export', 'ohio-extra' ),
        'fields' => array(
            array(
                'key' => 'field_5f3a645f21047',
                'label' => __( 'Mark this page to be exported for a specific builder only:', 'ohio-extra' ),
                'name' => 'ohio_for_wpbakery_version_only',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'for_wpbakery_only' => 'WPBAKERY',
                    'elementor:sections' => 'ELEMENTOR SECTIONS',
                    'elementor:containers' => 'ELEMENTOR CONTAINERS',
                ),
                'allow_custom' => 0,
                'default_value' => null,
                'layout' => 'vertical',
                'toggle' => 0,
                'return_format' => 'value',
                'save_custom' => 0,
                'allow_null' => true,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'ohio_portfolio',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
}
