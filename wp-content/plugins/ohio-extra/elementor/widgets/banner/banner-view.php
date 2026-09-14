<?php

/**
* WPBakery Page Builder Ohio Banner shortcode view
*/

?>
<div class="ohio-banner-sc ohio-widget banner card <?php echo $this->getWrapperClasses(); ?>">

    <?php if ( in_array( $settings['block_type_layout'], [ 'inner' ] ) ) : ?>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) ) : ?>
            <a class="-unlink" data-cursor-class="cursor-link" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
        <?php endif; ?>

            <div class="image-holder" <?php if ( $settings['tilt_effect'] ) { echo esc_attr( $tilt_attrs ); } ?>>
                <?php if ( !empty( $settings['background_image']['url'] ) ) : ?>
                    <img src="<?php echo $settings['background_image']['url']; ?>"
                        srcset="<?php echo wp_get_attachment_image_srcset( $settings['background_image']['id'], 'large' ) ?>"
                        sizes="<?php echo wp_get_attachment_image_sizes( $settings['background_image']['id'], 'large' ) ?>"
                        alt="<?php echo esc_attr( get_post_meta( $settings['background_image']['id'], '_wp_attachment_image_alt', true ) ); ?>">
                <?php endif; ?>
                <div class="overlay-details">
                    <div class="card-details">
                        <div class="heading">

                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'before_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>

                            <<?php echo $settings['heading_tag']; ?> class="title"><?php echo $settings['title']; ?></<?php echo $settings['heading_tag']; ?>>

                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'after_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="description -flex -flex-column -flex-align-start">
                        
                        <?php if ( !empty( $settings['description'] ) ) : ?>
                            <p>
                                <?php echo $settings['description']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $settings['show_button'] ) :
                            // Determine if the title is empty
                            $no_title_class = empty( $settings['button_title'] ) ? ' -without-text' : '';
                        ?>
                            <button class="button <?php echo esc_attr( $this->getButtonClasses() ) . $no_title_class; ?>">
                                <?php echo $settings['button_title']; ?>
                                <i class="icon">
                                    <svg class="default" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z"></path>
                                    </svg>
                                </i>
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) ) : ?>
            </a>
        <?php endif; ?>

    <?php elseif ( in_array( $settings['block_type_layout'], [ 'inner_hover' ] ) ) : ?>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            <a class="-unlink" data-cursor-class="cursor-link" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
        <?php endif; ?>

            <div class="image-holder" <?php if ( $settings['tilt_effect'] ) { echo esc_attr( $tilt_attrs ); } ?>>
                <?php if ( !empty( $settings['background_image']['url'] ) ) : ?>
                    <img src="<?php echo $settings['background_image']['url']; ?>"
                        srcset="<?php echo wp_get_attachment_image_srcset( $settings['background_image']['id'], 'large' ) ?>"
                        sizes="<?php echo wp_get_attachment_image_sizes( $settings['background_image']['id'], 'large' ) ?>"
                        alt="<?php echo esc_attr( get_post_meta( $settings['background_image']['id'], '_wp_attachment_image_alt', true ) ); ?>">
                <?php endif; ?>
                <div class="overlay-details">
                    <div class="card-details -fade-down">
                        <div class="heading">
                            
                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'before_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>

                            <<?php echo $settings['heading_tag']; ?> class="title"><?php echo $settings['title']; ?></<?php echo $settings['heading_tag']; ?>>

                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'after_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="description -fade-up -flex -flex-column -flex-align-start">

                        <?php if ( !empty( $settings['description'] ) ) : ?>
                            <p class="-fade-up">
                                <?php echo $settings['description']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $settings['show_button'] ) :
                            // Determine if the title is empty
                            $no_title_class = empty( $settings['button_title'] ) ? ' -without-text' : '';
                        ?>
                            <a class="button <?php echo esc_attr( $this->getButtonClasses() ) . $no_title_class; ?>" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
                                <?php echo $settings['button_title']; ?>
                                <i class="icon">
                                    <svg class="default" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z"></path>
                                    </svg>
                                </i>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            </a>
        <?php endif; ?>

    <?php elseif ( in_array( $settings['block_type_layout'], [ 'overlay_image' ] ) ) : ?>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            <a class="-unlink" data-cursor-class="cursor-link" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
        <?php endif; ?>

            <div class="image-holder" <?php if ( $settings['tilt_effect'] ) { echo esc_attr( $tilt_attrs ); } ?>>
                <?php if ( !empty( $settings['background_image']['url'] ) ) : ?>
                    <img src="<?php echo $settings['background_image']['url']; ?>"
                        srcset="<?php echo wp_get_attachment_image_srcset( $settings['background_image']['id'], 'large' ) ?>"
                        sizes="<?php echo wp_get_attachment_image_sizes( $settings['background_image']['id'], 'large' ) ?>"
                        alt="<?php echo esc_attr( get_post_meta( $settings['background_image']['id'], '_wp_attachment_image_alt', true ) ); ?>">
                <?php endif; ?>

                <div class="overlay-details -flex-column -flex-just-space-between">
                    <div class="card-details">
                        <div class="heading">
                            
                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'before_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>

                            <<?php echo $settings['heading_tag']; ?> class="title"><?php echo $settings['title']; ?></<?php echo $settings['heading_tag']; ?>>

                            <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'after_title' ) : ?>
                                <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="description -flex -flex-column -flex-align-start">

                        <?php if ( !empty( $settings['description'] ) ) : ?>
                            <p>
                                <?php echo $settings['description']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $settings['show_button'] ) :
                            // Determine if the title is empty
                            $no_title_class = empty( $settings['button_title'] ) ? ' -without-text' : '';
                        ?>
                            <a class="button <?php echo esc_attr( $this->getButtonClasses() ) . $no_title_class; ?>" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
                                <?php echo $settings['button_title']; ?>
                                <i class="icon">
                                    <svg class="default" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z"></path>
                                    </svg>
                                </i>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            </a>
        <?php endif; ?>


    <?php else : ?>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            <a class="-unlink" data-cursor-class="cursor-link" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
        <?php endif; ?>

            <div class="image-holder" <?php if ( $settings['tilt_effect'] ) { echo esc_attr( $tilt_attrs ); } ?>>
                <?php if ( !empty( $settings['background_image']['url'] ) ) : ?>
                    <img src="<?php echo $settings['background_image']['url']; ?>"
                        srcset="<?php echo wp_get_attachment_image_srcset( $settings['background_image']['id'], 'large' ) ?>"
                        sizes="<?php echo wp_get_attachment_image_sizes( $settings['background_image']['id'], 'large' ) ?>"
                        alt="<?php echo esc_attr( get_post_meta( $settings['background_image']['id'], '_wp_attachment_image_alt', true ) ); ?>">
                <?php endif; ?>

                <?php if ( !empty( $settings['description'] ) || $settings['show_button'] ) : ?>
                    <div class="overlay-details description -fade-up -flex-column -flex-align-start">

                        <?php if ( !empty( $settings['description'] ) ) : ?>
                            <p>
                                <?php echo $settings['description']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $settings['show_button'] ) :
                            // Determine if the title is empty
                            $no_title_class = empty( $settings['button_title'] ) ? ' -without-text' : '';
                        ?>
                            <a class="button <?php echo esc_attr( $this->getButtonClasses() ) . $no_title_class; ?>" <?php echo $this->getLinkAttributesString( $settings['link'] ); ?>>
                                <?php echo $settings['button_title']; ?>
                                <i class="icon">
                                    <svg class="default" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 0L6.59 1.41L12.17 7H0V9H12.17L6.59 14.59L8 16L16 8L8 0Z"></path>
                                    </svg>
                                </i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

        <?php if ( !empty( $settings['use_link'] ) && !empty( $settings['link']['url'] ) && !$settings['show_button'] ) : ?>
            </a>
        <?php endif; ?>

        <div class="card-details">
            <div class="heading">
                
                <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'before_title' ) : ?>
                    <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                <?php endif; ?>

                <<?php echo $settings['heading_tag']; ?> class="title"><?php echo $settings['title']; ?></<?php echo $settings['heading_tag']; ?>>

                <?php if ( !empty( $settings['subtitle'] ) && $settings['subtitle_position'] === 'after_title' ) : ?>
                    <div class="subtitle"><?php echo $settings['subtitle']; ?></div>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

</div>