<?php
/**
 * The template for displaying [vc_column_inner] shortcode.
 *
 * This overrides js_composer/include/templates/shortcodes/vc_column_inner.php to add
 * support for the "Dark Mode Background" option (mirrors the same feature on vc_row,
 * registered in ohio-extra.php and implemented in vc_templates/vc_row.php and
 * vc_templates/vc_column.php).
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_column_inner.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var array $atts
 * @var string $el_class
 * @var string $el_id
 * @var string $width
 * @var string $css
 * @var string $offset
 * @var string $content - shortcode content
 * @var string $dark_mode_scheme
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Column_Inner $this
 */
$el_class = $width = $el_id = $css = $offset = $css_animation = $dark_mode_scheme = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );

$css_classes = [
	$this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation ),
	'wpb_column',
	'vc_column_container',
	$width,
];

if ( vc_shortcode_custom_css_has_property( $css, [
	'border',
	'background',
] ) ) {
	$css_classes[] = 'vc_col-has-fill';
}

if ( 'light' === $dark_mode_scheme ) {
	$css_classes[] = 'clb__dark_mode_light';
} elseif ( 'dark' === $dark_mode_scheme ) {
	$css_classes[] = 'clb__dark_mode_black';
}

$wrapper_attributes = [];

$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->getSettings()['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
$inner_column_class = 'vc_column-inner ' . esc_attr( trim( vc_shortcode_custom_css_class( $css ) ) );
$output .= '<div class="' . trim( $inner_column_class ) . '">';
$output .= '<div class="wpb_wrapper">';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
