<?php

/**
* WPBakery Page Builder Ohio Icon Box shortcode view
*/

?>
<div class="ohio-widget icon-box<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo esc_attr( $animation_attrs ); ?>>

	<?php if ( $content_full != 'full' ) : ?>
		
		<span class="icon-group<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( $icon_type == 'font_icon' && $icon_as_icon ) : ?>
				<i class="icon <?php echo $icon_as_icon; ?>"></i>
			<?php elseif ( $icon_type == 'custom_html' && $icon_as_html ) : ?>
				<?php echo OhioExtraParser::VC_icon_html( $icon_as_html, 'icon' ); ?>
			<?php elseif ( $icon_as_image ) : ?>
				<img <?php echo $icon_image_atts; ?>>
			<?php endif; ?>
		</span>
		<div class="icon-box-content">
			<<?php echo $heading_tag; ?> class="icon-box-heading"><?php echo $title; ?></<?php echo $heading_tag; ?>>
			<p><?php echo $description; ?></p>
			<?php if ( $use_link ) : ?>
				<a class="button<?php echo $button_css['classes']; ?>" href="<?php echo esc_url( $link_url['url'] ); ?>" <?php if ( $link_url['blank'] ) { echo ' target="_blank"'; } ?>>
					<?php if ( $button_icon_use && $button_icon_position == 'left' ) : ?>
						<?php if ( $button_icon_type == 'user_image' && $button_icon_image_atts ) : ?>
							<img class="icon -left -image" <?php echo $button_icon_image_atts; ?> />
						<?php elseif ( $button_icon_type == 'font_icon' && $button_icon_as_icon ) : ?>
							<i class="icon -left <?php echo $button_icon_as_icon; ?>"></i>
						<?php elseif ( $button_icon_type == 'custom_html' && $button_icon_as_html ) : ?>
							<?php echo OhioExtraParser::VC_icon_html( $button_icon_as_html, 'icon -left' ); ?>
						<?php endif; ?>
					<?php endif; ?>
					<?php echo $link_url['caption']; ?>
					<?php if ( $button_icon_use && $button_icon_position == 'right' ) : ?>
						<?php if ( $button_icon_type == 'user_image' && $button_icon_image_atts ) : ?>
							<img class="icon -right -image" <?php echo $button_icon_image_atts; ?> />
						<?php elseif ( $button_icon_type == 'font_icon' && $button_icon_as_icon ) : ?>
							<i class="icon -right <?php echo $button_icon_as_icon; ?>"></i>
						<?php elseif ( $button_icon_type == 'custom_html' && $button_icon_as_html ) : ?>
							<?php echo OhioExtraParser::VC_icon_html( $button_icon_as_html, 'icon -right' ); ?>
						<?php endif; ?>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>

	<?php else : ?>

		<div class="icon-box-header">
			<span class="icon-group<?php echo esc_attr( $icon_classes ); ?>">
				<?php if ( $icon_type == 'font_icon' && $icon_as_icon ) : ?>
					<i class="icon <?php echo $icon_as_icon; ?>"></i>
				<?php elseif ( $icon_type == 'custom_html' && $icon_as_html ) : ?>
					<?php echo OhioExtraParser::VC_icon_html( $icon_as_html, 'icon' ); ?>
				<?php elseif ( $icon_as_image ) : ?>
					<img <?php echo $icon_image_atts; ?>>
				<?php endif; ?>
			</span>
			<<?php echo $heading_tag; ?> class="icon-box-heading"><?php echo $title; ?></<?php echo $heading_tag; ?>>
		</div>
		<div class="icon-box-content">
			<p><?php echo $description; ?></p>
			<?php if ( $use_link ) : ?>
				<a class="button<?php echo $button_css['classes']; ?>" href="<?php echo esc_url( $link_url['url'] ); ?>" <?php if ( $link_url['blank'] ) { echo ' target="_blank"'; } ?>>
					<?php if ( $button_icon_use && $button_icon_position == 'left' ) : ?>
						<?php if ( $button_icon_type == 'user_image' && $button_icon_image_atts ) : ?>
							<img class="icon -left -image" <?php echo $button_icon_image_atts; ?> />
						<?php elseif ( $button_icon_type == 'font_icon' && $button_icon_as_icon ) : ?>
							<i class="icon -left <?php echo $button_icon_as_icon; ?>"></i>
						<?php elseif ( $button_icon_type == 'custom_html' && $button_icon_as_html ) : ?>
							<?php echo OhioExtraParser::VC_icon_html( $button_icon_as_html, 'icon -left' ); ?>
						<?php endif; ?>
					<?php endif; ?>
					<?php echo $link_url['caption']; ?>
					<?php if ( $button_icon_use && $button_icon_position == 'right' ) : ?>
						<?php if ( $button_icon_type == 'user_image' && $button_icon_image_atts ) : ?>
							<img class="icon -right -image" <?php echo $button_icon_image_atts; ?> />
						<?php elseif ( $button_icon_type == 'font_icon' && $button_icon_as_icon ) : ?>
							<i class="icon -right <?php echo $button_icon_as_icon; ?>"></i>
						<?php elseif ( $button_icon_type == 'custom_html' && $button_icon_as_html ) : ?>
							<?php echo OhioExtraParser::VC_icon_html( $button_icon_as_html, 'icon -right' ); ?>
						<?php endif; ?>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>

	<?php endif; ?>

</div>