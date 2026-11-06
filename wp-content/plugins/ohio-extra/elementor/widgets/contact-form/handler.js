(function ($) {
    'use strict';

    // Adds the icon set in the widget's "Button Icon" section to the submit button.
    // The theme replaces the CF7 submit input with the [data-button-contact] button
    // and sets its text, so the icon is added once that replacement has happened.
    function addButtonIcon($scope, attempt) {
        var $forms = $scope.find('.contact-form');
        var pending = false;

        $forms.each(function () {
            var $form = $(this);
            var $template = $form.find('[data-button-contact-icon]');

            if (!$template.length) {
                return;
            }

            var $button = $form.find('form [data-button-loading]');

            if (!$button.length) {
                pending = true;
                return;
            }

            if ($button.attr('data-button-icon') === 'true') {
                return;
            }

            var $icon = $template.children().first().clone();

            if ($template.attr('data-button-contact-icon') === 'left') {
                $button.prepend($icon, ' ');
            } else {
                $button.append(' ', $icon);
            }

            $button.attr('data-button-icon', 'true');
        });

        if (pending && attempt < 30) {
            setTimeout(function () {
                addButtonIcon($scope, attempt + 1);
            }, 100);
        }
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/ohio_contact_form.default', function ($element) {
            if (elementorFrontend.isEditMode()) {
                $(window).trigger('ohio:handle_contact_forms');
            }

            addButtonIcon($element, 0);
        });
    });
})(jQuery);
