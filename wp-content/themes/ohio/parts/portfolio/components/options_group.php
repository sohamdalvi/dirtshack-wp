<?php
    $project = OhioHelper::get_storage_item_data();
?>

<ul class="options-group -unlist">
    <?php if ( !empty( $project['strategy'] ) ) : ?>
        <li>
            <div class="h6 title"><?php esc_html_e( 'Strategy', 'ohio' ); ?></div>
            <p><?php echo wp_kses( $project['strategy'], 'default' ); ?></p>
        </li>
    <?php endif; ?>

    <?php if ( !empty( $project['design'] ) ) : ?>
        <li>
            <div class="h6 title"><?php esc_html_e( 'Design', 'ohio' ); ?></div>
            <p><?php echo wp_kses( $project['design'], 'default' ); ?></p>
        </li>
    <?php endif; ?>

    <?php if ( !empty( $project['client'] ) ) : ?>
        <li>
            <div class="h6 title"><?php esc_html_e( 'Client', 'ohio' ); ?></div>
            <p><?php echo wp_kses( $project['client'], 'default' ); ?></p>
        </li>
    <?php endif; ?>

    <?php if ( !empty( $project['custom_fields'] ) ) : ?>

        <?php foreach ( $project['custom_fields'] as $custom_field ) : ?>
        <li>
            <div class="h6 title"><?php echo esc_html( $custom_field['title'] ); ?></div>
            <p><?php echo esc_html( $custom_field['value'] ); ?></p>
        </li>
        <?php endforeach; ?>

    <?php endif; ?>

    <?php if ( !empty( $project['tags'] ) ) : ?>
        <li>
            <div class="h6 title"><?php esc_html_e( 'Tags', 'ohio' ); ?></div>
            <p>
				<?php if ( $project['raw_tags'] ) : ?>

					<?php foreach ( $project['raw_tags'] as $i => $tag ) : ?>
						<a href="<?php echo esc_url( get_term_link( $tag->term_id ) ); ?>"><?php echo esc_html( $tag->name ); ?></a><?php
							if ( $i < count( $project['raw_tags']) - 1 ) echo ', ';
						?>
					<?php endforeach; ?>
                    
				<?php endif; ?>
			</p>
        </li>
    <?php endif; ?>
</ul>