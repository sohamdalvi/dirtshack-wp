<?php

/**
* WPBakery Page Builder Ohio Call To Action shortcode view
*/

?>
<div class="ohio-widget call-to-action<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo esc_attr( $animation_attrs ); ?>>
	<div class="heading title">
		<?php if ( $subtitle_type_layout == 'top_subtitle' ) : ?>
			<div class="subtitle">
				<?php echo $subtitle; ?>
			</div>
		<?php endif; ?>

		<<?php echo $heading_tag; ?> class="title">
			<?php echo $title; ?>
		</<?php echo $heading_tag; ?>>

		<?php if ( $subtitle_type_layout == 'bottom_subtitle' ) : ?>
			<div class="subtitle">
				<?php echo $subtitle; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="holder">
		<?php if ( $add_link ) : ?>
			<a class="button<?php echo $button_css['classes']; ?>" href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( $link['blank'] ) echo ' target="_blank"'; ?>>
				<?php if ( $icon_use && $icon_position == 'left' ) : ?>
					<?php if ( $icon_type == 'user_image' && $icon_image_atts ) : ?>
						<img class="icon -left -image" <?php echo $icon_image_atts; ?> />
					<?php elseif ( $icon_type == 'custom_html' && $icon_as_html ) : ?>
						<?php echo OhioExtraParser::VC_icon_html( $icon_as_html, 'icon -left' ); ?>
					<?php elseif ( $icon_type == 'font_icon' && $icon_as_icon ) : ?>
						<i class="icon -left <?php echo $icon_as_icon; ?>"></i>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo $link['caption']; ?>
				<?php if ( $icon_use && $icon_position == 'right' ) : ?>
					<?php if ( $icon_type == 'user_image' && $icon_image_atts ) : ?>
						<img class="icon -right -image" <?php echo $icon_image_atts; ?> />
					<?php elseif ( $icon_type == 'custom_html' && $icon_as_html ) : ?>
						<?php echo OhioExtraParser::VC_icon_html( $icon_as_html, 'icon -right' ); ?>
					<?php elseif ( $icon_type == 'font_icon' && $icon_as_icon ) : ?>
						<i class="icon -right <?php echo $icon_as_icon; ?>"></i>
					<?php endif; ?>
				<?php endif; ?>
			</a>
		<?php endif; ?>
	</div>
</div>