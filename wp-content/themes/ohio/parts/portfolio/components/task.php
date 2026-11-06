<?php
    $project = OhioHelper::get_storage_item_data();
?>

<?php if ( !empty( $project['task'] ) ) : ?>

    <div class="project-task heading">
        <div class="subtitle">
            <?php esc_html_e( 'Task', 'ohio' ); ?>
        </div>
        <p class="title h4 -unspace">
            <?php echo wp_kses( $project['task'], 'basic_html' ); ?>
        </p>
    </div>

<?php endif; ?>