<div class="ohio-widget progress <?php echo $this->getWrapperClasses(); ?>" data-ohio-progress-bar="<?php echo esc_attr( $settings['progress_value']['size'] ); ?>">
    <div class="progress-heading">
        <?php if (!empty($settings['label'])): ?>
            <div class="h6 label"><?php echo $settings['label']; ?></div>
        <?php endif; ?>

        <?php if ( empty($settings['show_percents_tooltip']) ): ?>
            <span class="progress-percent">
                <span class="percent">0</span>%
            </span>
        <?php endif; ?>
    </div>
    <div class="progress-holder <?php echo $settings['inner_classes'] ?> <?php echo $settings['size_classes'] ?>">
        <div class="progress-bar" role="progressbar" aria-label="Progress bar">

            <?php if ( !empty($settings['show_percents_tooltip']) ) : ?>
                <span class="progress-percent has-tooltip -visible -small" data-tooltip="0%" tabindex="0"></span>
            <?php endif; ?>
            
        </div>
    </div>
</div>