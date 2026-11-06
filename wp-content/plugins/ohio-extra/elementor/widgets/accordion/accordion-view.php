<div class="ohio-accordion-sс ohio-widget accordion <?php echo $this->getWrapperClasses(); ?>" data-ohio-accordion="true">

    <?php foreach ($settings['tabs'] as $item) : ?>
        <div class="accordion-item elementor-repeater-item-<?php echo $item['_id'];
            if (!empty($item['is_active'])) { echo ' active'; }
            if (!empty($item['custom_class'])) { echo ' ' . $item['custom_class']; }
            ?>">

            <div class="accordion-button">
                <div class="h6 accordion-header">

                    <?php if ( !empty( $item['use_icon'] ) ) {
                        $icon_type = !empty( $item['icon_type'] ) ? $item['icon_type'] : 'icon';

                        if ( $icon_type == 'image' && !empty( $item['icon_image']['url'] ) ) {
                            $image_id = !empty( $item['icon_image']['id'] ) ? $item['icon_image']['id'] : 0;
                            echo '<img class="icon" src="' . esc_url( $item['icon_image']['url'] ) . '"';
                            if ( $image_id ) {
                                echo ' srcset="' . esc_attr( wp_get_attachment_image_srcset( $image_id, 'large' ) ) . '"';
                                echo ' sizes="' . esc_attr( wp_get_attachment_image_sizes( $image_id, 'large' ) ) . '"';
                            }
                            echo ' alt="' . esc_attr__( 'Icon', 'ohio-extra' ) . '">';
                        } elseif ( $icon_type == 'html' && !empty( $item['icon_html'] ) ) {
                            echo OhioExtraParser::VC_icon_html( $item['icon_html'], 'icon' );
                        } elseif ( $icon_type == 'icon' && !empty( $item['icon_icon'] ) ) {
                            \Elementor\Icons_Manager::render_icon( $item['icon_icon'], [ 'class' => 'icon' ] );
                        }
                    } ?>

                    <?php echo $item['list_title']; ?>

                </div>
                <button class="icon-button -extra-small" aria-label="<?php esc_html_e( 'Toggle', 'ohio-extra' ); ?>">
                    <i class="icon"></i>
                </button>
            </div>
            <div class="accordion-collapse <?php if (!empty($item['is_active'])) echo 'visible'; ?>">
                <div class="accordion-body">
                <?php
                    if ($item['list_content_type'] == 'editor') {
                        echo do_shortcode( $item['list_content_editor'] );
                    } else {
                        if ( !empty($item['list_content_template']) && $item['list_content_template'] != 0 ) {
                            // Template
                            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $this->getLocalizedTemplate( $item['list_content_template'] ) );
                        } else {
                            // No template message
                            $link = add_query_arg(
                                array(
                                    'post_type'     => 'elementor_library',
                                    'action'        => 'elementor_new_post',
                                    '_wpnonce'      => wp_create_nonce( 'elementor_action_new_post' ),
                                    'template_type' => 'section',
                                ),
                                esc_url( admin_url( '/edit.php' ) )
                            );

                            echo '<div style="text-align:center;">';
                            echo __( 'Template is not defined. Select an existing template or create a', 'ohio-extra' );
                            echo ' <a class="new-template-link elementor-clickable brand-color" target="_blank" href="' . $link . '">' . esc_html__( 'new one', 'ohio-extra' ) . '</a>.';
                            echo '</div>';
                        }
                    }
                ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>