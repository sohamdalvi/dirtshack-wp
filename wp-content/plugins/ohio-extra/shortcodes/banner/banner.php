<?php 

/**
* WPBakery Page Builder Ohio Banner shortcode
*/

add_shortcode( 'ohio_banner', 'ohio_banner_func' );

function ohio_banner_func( $atts ) {
	if ( isset( $atts ) && is_array( $atts ) ) extract( $atts );

	// Default values, parsing and filtering
	$block_type_layout = isset( $block_type_layout ) ? OhioExtraFilter::string( $block_type_layout, 'string', 'full' ) : 'full';
	$block_type_full_align = isset( $block_type_full_align ) ? OhioExtraFilter::string( $block_type_full_align, 'string', 'left' ) : 'left';
	$card_effect = isset( $card_effect ) ? OhioExtraFilter::string( $card_effect, 'string', 'none' ) : 'none';
	$equal_height = isset( $equal_height ) ? OhioExtraFilter::boolean( $equal_height ) : true;
	$stretch_to_fit = isset( $stretch_to_fit ) ? OhioExtraFilter::boolean( $stretch_to_fit ) : true;
	$border_width = isset( $border_width ) ? OhioExtraFilter::string( $border_width, 'string', '') : '';
	$border_color = isset( $border_color ) ? OhioExtraFilter::string( $border_color, 'string', '') : '';

	$fill_color = isset( $fill_color ) ? OhioExtraFilter::string( $fill_color, 'string', '') : '';
	$dark_mode_scheme = isset( $dark_mode_scheme ) ? OhioExtraFilter::string( $dark_mode_scheme, 'string', 'none' ) : 'none';
	$icon_color = isset( $icon_color ) ? OhioExtraFilter::string( $icon_color, 'string', '') : '';
	$icon_button_color = isset( $icon_button_color ) ? OhioExtraFilter::string( $icon_button_color, 'string', '') : '';

	$border_radius = isset( $border_radius ) ? OhioExtraFilter::string( $border_radius, 'string', '') : '';
	$tilt_effect = isset( $tilt_effect ) ? OhioExtraFilter::boolean( $tilt_effect ) : true;
	$drop_shadow = isset( $drop_shadow ) ? OhioExtraFilter::boolean( $drop_shadow ) : false;
	$drop_shadow_intensity = isset( $drop_shadow_intensity ) ? OhioExtraFilter::string( $drop_shadow_intensity, 'string', '') : '';
	
	$use_link = isset( $use_link ) ? OhioExtraFilter::boolean( $use_link ) : true;
	$link_url = OhioExtraParser::VC_link_params( ( isset( $link_url ) ? $link_url : '' ), array( 'caption' => esc_html__( '', 'ohio-extra' ) ) );
	$banner_button = isset( $banner_button ) ? OhioExtraFilter::string( $banner_button, 'string', false ) : false;
	$banner_button = preg_replace( '/\&amp\;/', '&', $banner_button );
	parse_str( $banner_button, $button_settings );
	$show_button = isset( $show_button ) ? OhioExtraFilter::boolean( $show_button ) : false;
	$button_animation = isset( $button_animation ) ? OhioExtraFilter::boolean( $button_animation ) : false;

	$title = isset( $title ) ? OhioExtraFilter::string( $title ) : false;
	$heading_tag = isset( $heading_tag ) ? OhioExtraFilter::headingTag( $heading_tag ) : 'h3';
	$title_typo = isset( $title_typo ) ? OhioExtraFilter::string( $title_typo ) : false;
	$background_image = isset( $background_image ) ? OhioExtraFilter::string( $background_image ) : false;
	$image = OhioExtraParser::generateImageAttsById( OhioExtraFilter::string( $background_image ), $title );
	$subtitle = isset( $subtitle ) ? OhioExtraFilter::string( $subtitle ) : false;
	$subtitle_position = ( isset( $subtitle_position ) ) ? OhioExtraFilter::string( $subtitle_position, 'string', 'before_title' ) : 'before_title';
	$description = isset( $description ) ? rawurldecode( base64_decode( $description ) ) : '';
	$description = OhioExtraFilter::string( $description, 'textarea', '' );
	$description_typo = isset( $description_typo ) ? OhioExtraFilter::string( $description_typo ) : false;
	$subtitle_typo = isset( $subtitle_typo ) ? OhioExtraFilter::string( $subtitle_typo ) : false;
	$overlay_color = isset( $overlay_color ) ? OhioExtraFilter::string( $overlay_color ) : false;

	// Design options
    $content_styles = isset( $content_styles ) ? OhioExtraFilter::string( $content_styles ) : false;
    $content_styles_str = strpos($content_styles, "{");
    $content_styles_css = substr($content_styles, $content_styles_str);

	// Appear effect
	$appearance_effect = isset( $appearance_effect ) ? OhioExtraFilter::string( $appearance_effect, 'attr', 'none' )  : 'none';
	$appearance_once = isset( $appearance_once ) ? OhioExtraFilter::boolean( $appearance_once ) : true;
	$appearance_duration = isset( $appearance_duration ) ? OhioExtraFilter::string( $appearance_duration, 'attr', false )  : false;
	$appearance_delay = isset( $appearance_delay ) ? OhioExtraFilter::string( $appearance_delay, 'attr', false ) : false;
	
	$animation_attrs = '';
	if ( $appearance_effect != 'none' ) {
		OhioHelper::add_required_script( 'aos' );
	}
	if ( $appearance_effect != 'none' ) {
		$animation_attrs .= ' data-aos=' . esc_attr( $appearance_effect ) . '';
	}
	if ( !$appearance_once ) {
		$animation_attrs .= ' data-aos-once=true';
	}
	if ( !empty( $appearance_duration ) ) {
		$animation_attrs .= ' data-aos-duration=' . intval( $appearance_duration ) . '';
	}
	if ( !empty( $appearance_delay ) ) {
		$animation_attrs .= ' data-aos-delay=' . intval( $appearance_delay ) . '';
	}

	// Tilt effect
	$tilt_attrs = '';
	if ( !$tilt_effect ) {
		$tilt_attrs .= ' data-tilt=true data-tilt-perspective=6000';
	}

	// Wrapper ID
	$wrapper_id = uniqid( 'ohio-custom-' );

	// Wrapper classes
	$wrapper_classes = '';

	$layout_classes = '';
	switch ( $block_type_layout ) {
		case 'full':
			$layout_classes .= '';
			break;
		case 'inner':
			$layout_classes .= ' -with-overlay';
			break;
		case 'overlay_image':
			$layout_classes .= ' -with-overlay-image';
			break;
		case 'inner_hover':
			$layout_classes .= ' -image-only';
			break;
	}

	$hover_effect = '';
	switch ( $card_effect ) {
		case 'scale':
			$hover_effect .= ' -img-scale';
			break;
		case 'overlay':
			$hover_effect .= ' -img-overlay';
			break;
		case 'greyscale':
			$hover_effect .= ' -img-greyscale';
			break;
	}

	$content_classes = '';
	switch ( $block_type_full_align ) {
		case 'center':
			$content_classes .= ' -center';
			break;
		case 'right':
			$content_classes .= ' -right';
			break;
	}

	// Drop shadow
	if ( $drop_shadow ) {
		$wrapper_classes .= ' -with-shadow';
	}

	// Icon Button Hover Animation
	if ( $button_animation ) {
		$wrapper_classes .= ' -with-icon-button-animation';
	}

	$wrapper_classes .= isset( $css_class ) ? ' ' . OhioExtraFilter::string( $css_class, 'attr', '' )  : '';
	$wrapper_classes .= $layout_classes;
	$wrapper_classes .= $hover_effect;
	$wrapper_classes .= $content_classes;

	if ( !$equal_height ) {
		$wrapper_classes .= ' -metro';
	}

	if ( !$stretch_to_fit ) {
		$wrapper_classes .= ' -stretch';
	}

	if ( 'light' === $dark_mode_scheme ) {
		$wrapper_classes .= ' clb__dark_mode_light';
	} elseif ( 'dark' === $dark_mode_scheme ) {
		$wrapper_classes .= ' clb__dark_mode_black';
	}
	
	/**
	* Assembling styles
	*/

	$_style_block = '';

	$banner_title_typo = OhioExtraParser::VC_typo_to_CSS( $title_typo );
	OhioExtraParser::VC_typo_custom_font( $title_typo );

	if ( $banner_title_typo ) {
		$_selector = '';
		$_selector .= '#' . $wrapper_id . ':not(.-with-overlay-image) .heading .title,';
		$_selector .= '#' . $wrapper_id . '.-with-overlay-image .heading .title{';
		$_block_typo = $banner_title_typo;
		if ( !empty( $_block_typo['desktop'] ) ) {
			$_style_block .= $_selector . $_block_typo['desktop'] . '}';
		}
		if ( !empty( $_block_typo['tablet'] ) ) {
		    $_style_block .= '@media screen and (min-width: 769px) and (max-width: 1180px){';
		    $_style_block .= $_selector . $_block_typo['tablet'] . '}';
		    $_style_block .= '}';
		}
		if ( !empty( $_block_typo['mobile'] ) ) {
		    $_style_block .= '@media screen and (max-width: 768px){';
		    $_style_block .= $_selector . $_block_typo['mobile'] . '}';
		    $_style_block .= '}';
		}
	}

	$banner_subtitle_typo = OhioExtraParser::VC_typo_to_CSS( $subtitle_typo );
	OhioExtraParser::VC_typo_custom_font( $subtitle_typo );

	if ( $banner_subtitle_typo ) {
		$_selector = '';
		$_selector .= '#' . $wrapper_id . ':not(.-with-overlay-image) .heading .subtitle,';
		$_selector .= '#' . $wrapper_id . '.-with-overlay-image .heading .subtitle{';
		$_block_typo = $banner_subtitle_typo;
		if ( !empty( $_block_typo['desktop'] ) ) {
			$_style_block .= $_selector . $_block_typo['desktop'] . '}';
		}
		if ( !empty( $_block_typo['tablet'] ) ) {
		    $_style_block .= '@media screen and (min-width: 769px) and (max-width: 1180px){';
		    $_style_block .= $_selector . $_block_typo['tablet'] . '}';
		    $_style_block .= '}';
		}
		if ( !empty( $_block_typo['mobile'] ) ) {
		    $_style_block .= '@media screen and (max-width: 768px){';
		    $_style_block .= $_selector . $_block_typo['mobile'] . '}';
		    $_style_block .= '}';
		}
	}

	$banner_description_typo = OhioExtraParser::VC_typo_to_CSS( $description_typo );
	OhioExtraParser::VC_typo_custom_font( $description_typo );

	if ( $banner_description_typo ) {
		$_selector = '';
		$_selector .= '#' . $wrapper_id . ':not(.-with-overlay-image) .overlay-details p,';
		$_selector .= '#' . $wrapper_id . '.-with-overlay-image .overlay-details p{';
		$_block_typo = $banner_description_typo;
		if ( !empty( $_block_typo['desktop'] ) ) {
			$_style_block .= $_selector . $_block_typo['desktop'] . '}';
		}
		if ( !empty( $_block_typo['tablet'] ) ) {
		    $_style_block .= '@media screen and (min-width: 769px) and (max-width: 1180px){';
		    $_style_block .= $_selector . $_block_typo['tablet'] . '}';
		    $_style_block .= '}';
		}
		if ( !empty( $_block_typo['mobile'] ) ) {
		    $_style_block .= '@media screen and (max-width: 768px){';
		    $_style_block .= $_selector . $_block_typo['mobile'] . '}';
		    $_style_block .= '}';
		}
	}

	if ( $block_type_layout == 'full' ) {
		$overlay_css = OhioExtraParser::VC_color_to_CSS( $overlay_color, 'background:linear-gradient(rgba(0, 0, 0, 0), {{color}});' );
	} else {
		$overlay_css = OhioExtraParser::VC_color_to_CSS( $overlay_color, 'background-color:{{color}};' );
	}

	if ( $overlay_css ) {
		if ( $block_type_layout == 'overlay_image' ) {
			$_style_block .= '#' . $wrapper_id . '.-with-overlay-image:hover .overlay-details{';
		} else {
			$_style_block .= '#' . $wrapper_id . ' .overlay-details{';
		}
		$_style_block .= $overlay_css;
		$_style_block .= '}';
	}

	$_fill_color = OhioExtraParser::VC_color_to_CSS( $fill_color, 'background-color:{{color}};' );
	if ( $_fill_color ) {
		$_style_block .= '#' . $wrapper_id . ' .image-holder{';
		$_style_block .= $_fill_color;
		$_style_block .= '}';
	}

	$_icon_button_color = OhioExtraParser::VC_color_to_CSS( $icon_button_color, 'background-color:{{color}};' );
	if ( $_icon_button_color ) {
		$_style_block .= '#' . $wrapper_id . ' .overlay-details .icon-button{';
		$_style_block .= $_icon_button_color;
		$_style_block .= '}';
	}

	$_icon_color = OhioExtraParser::VC_color_to_CSS( $icon_color, 'color:{{color}};' );
	if ( $_icon_color ) {
		$_style_block .= '#' . $wrapper_id . ' .overlay-details .icon-button{';
		$_style_block .= $_icon_color;
		$_style_block .= '}';
	}

	$_border_color = OhioExtraParser::VC_color_to_CSS( $border_color, 'border-color:{{color}};' );
	if ( $_border_color ) {
		$_style_block .= '#' . $wrapper_id . '.card .image-holder{';
		$_style_block .= $_border_color;
		$_style_block .= '}';
	}

	if ( isset( $border_width ) && $border_width != '' ) {
		$_style_block .= '#' . $wrapper_id . '.card .image-holder{';
		$_style_block .= 'border-width:' . $border_width . 'px;';
		$_style_block .= 'border-style: solid';
		$_style_block .= '}';
	}

	if ( isset( $border_radius ) && $border_radius != '' ) {
		$_style_block .= '#' . $wrapper_id . '.card:not(.-contained) .image-holder{';
		$_style_block .= 'border-radius:' . $border_radius . 'px;';
		$_style_block .= '}';
	}

	if ( isset( $drop_shadow_intensity ) && $drop_shadow_intensity != '' ) {
		$_style_block .= '#' . $wrapper_id . '.-with-shadow .image-holder{';
		$_style_block .= 'box-shadow: 0px 5px 15px 0px rgba(0, 0, 0,' . $drop_shadow_intensity . '%);';
		$_style_block .= '}';
	}

	// Button

	$button_css = OhioExtraParser::VC_button_to_css( $button_settings );
	
	if ( $button_css['css'] ) {
		$_style_block .= '#' . $wrapper_id . ' .button{';
		$_style_block .= $button_css['css'];
		$_style_block .= '}';
	}

	if ( $button_css['hover-css'] ) {
		$_style_block .= '#' . $wrapper_id . ' .button:hover{';
		$_style_block .= $button_css['hover-css'];
		$_style_block .= '}';
	}
	
	if ( $content_styles_css ) {
	    $_style_block .= '#' . $wrapper_id . $content_styles_css;
	}

	OhioLayout::append_to_shortcodes_css_buffer( $_style_block );

	ob_start();
	include( plugin_dir_path( __FILE__ ) . 'banner__view.php' );
	return ob_get_clean();
}