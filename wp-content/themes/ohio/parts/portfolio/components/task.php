<?php
    $project = OhioHelper::get_storage_item_data();
?>

<?php if ( !empty( $project['task'] ) ) : ?>
    <div class="project-task">
        <div class="h6 title"><?php esc_html_e( 'Task', 'ohio' ); ?></div>
        <p class="-unspace"><?php echo wp_kses( $project['task'], 'default' ); ?></p>
    </div>
<?php endif; ?>