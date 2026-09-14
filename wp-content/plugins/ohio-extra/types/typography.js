!function($) {

    function ohioTypographySerialize($block, $hidden_input) {
        let result = {};

        result.font_size = $block.find('input[data-target="font-size"]').val();
        result.font_size_tablet = $block.find('input[data-target="font-size-tablet"]').val();
        result.font_size_mobile = $block.find('input[data-target="font-size-mobile"]').val();
        result.line_height = $block.find('input[data-target="line-height"]').val();
        result.line_height_tablet = $block.find('input[data-target="line-height-tablet"]').val();
        result.line_height_mobile = $block.find('input[data-target="line-height-mobile"]').val();
        result.letter_spacing = $block.find('input[data-target="letter-spacing"]').val();
        result.letter_spacing_tablet = $block.find('input[data-target="letter-spacing-tablet"]').val();
        result.letter_spacing_mobile = $block.find('input[data-target="letter-spacing-mobile"]').val();
        result.color = $block.find('input[data-target="color"]').val();
        result.weight = $block.find('select[data-target="weight"]').val();
        result.style = $block.find('select[data-target="font-style"]').val();
        result.transform = $block.find('select[data-target="font-transform"]').val();
        result.decoration = $block.find('select[data-target="font-decoration"]').val();
        result.use_custom_font = $block.find('input[data-target="use-custom-font"]').prop('checked');
        if ( result.use_custom_font ) {
            result.custom_font = $block.find('select[data-target="custom-font"]').val();
        }

        $hidden_input.val( JSON.stringify( result ) );
    }

    $('#vc_ui-panel-edit-element').on('change', '.ohio_extra_typography_block input, .ohio_extra_typography_block select', function(e){
        var $closest = $(this).closest('.ohio_extra_typography_block');
        var $value_hidden_input = $closest.find('.wpb_vc_param_value');
        ohioTypographySerialize( $closest, $value_hidden_input );
    });


    $('#vc_ui-panel-edit-element').on('change', '.ohio_extra_typography_block input[data-target="use-custom-font"]', function(e){
        if ($(this).prop('checked')) {
            $(this).closest('.ohio_extra_typography_block').find('.custom-font-panel').show();
        } else {
            $(this).closest('.ohio_extra_typography_block').find('.custom-font-panel').hide();
        }
    });

    // A shortcode can carry several ohio_typography params at once (e.g. the
    // Heading element has one per tab: Title Typography, Subtitle Typography...).
    // All of their blocks exist in the DOM as soon as the edit panel opens, but
    // only the active tab's block is actually visible - the others sit inside
    // display:none tab panes until the user clicks over to them. Widgets are
    // only initialized once a block is actually visible: eagerly for whichever
    // tab is open by default, and lazily - on tab click - for the rest.
    function ohioInitTypographyBlock($block) {
        if ($block.data('ohio-typography-inited') || !$block.is(':visible')) {
            return;
        }
        $block.data('ohio-typography-inited', true);

        // vc.atts.colorpicker.init(settings, $holder) does two things with
        // $holder: it scans it for ".color-group" to build the Pickr widget,
        // and - separately - it runs $holder.find('label').on('click', ...) to
        // focus the picker's button whenever any label inside $holder is
        // clicked. That second part assumes $holder contains only the color
        // field's own label. Our typography block wraps every field (Font
        // Weight, Font Size, Text Transform, ...) in its own bare <label> for
        // layout purposes, so passing the whole block made WPBakery bind that
        // "click label -> steal focus into the color picker" handler to every
        // field's label, not just the color field's - freezing every other
        // input in the block. Scoping the call to just the color field's own
        // label keeps that focus-assist working for the color field while
        // leaving every other label alone.
        var $colorLabel = $block.find('.color-group').closest('label');
        if ($colorLabel.length) {
            vc.atts.colorpicker.init({}, $colorLabel);
        }

        // Turn the plain <select> fields into WPBakery's own select2 dropdowns.
        // vc.atts.dropdown.init() expects the immediate parent of a single
        // "select.dropdown" element - not the select itself, and not a holder
        // with several selects inside it - so each field gets its own
        // .edit_form_line wrapper and its own init() call.
        $block.find('.edit_form_line').each(function() {
            vc.atts.dropdown.init({}, $(this));
        });
    }

    function ohioInitVisibleTypographyBlocks() {
        $('.ohio_extra_typography_block').each(function() {
            ohioInitTypographyBlock($(this));
        });
    }

    // Initialize whatever is visible right away (covers the default/active tab).
    ohioInitVisibleTypographyBlocks();

    // Re-scan after every WPBakery edit-panel tab switch so a block gets
    // initialized the first time its own tab becomes visible. A short delay
    // gives the panel time to actually flip display before we check :visible.
    $(document).on('click', '[data-vc-ui-element="panel-tab-control"]', function() {
        setTimeout(ohioInitVisibleTypographyBlocks, 50);
    });

    $('#vc_ui-panel-edit-element .ohio_extra_typography_block .devices-select li').on('click', function() {
        let elclass = $(this).attr('class');
        $(this).closest('.devices-select').attr('class', 'devices-select ' + elclass);

        $(this).closest('ul').css({'opacity': 0, 'height': 0});
        setTimeout(() => {
            $(this).closest('ul').css({'opacity': 1, 'height': 'auto'});
        }, 500);

        let type = elclass.replace('device-', '');
        $(this).closest('label').find('input').hide();
        if (type === 'desktop') {
            $(this).closest('label').find('input:first').show();
        } else {
            $(this).closest('label').find('input[data-target$="' + type + '"]').show();
        }

        return false;
    });

}(window.jQuery);