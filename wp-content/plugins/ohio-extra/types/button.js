!function($) {

	function ohioButtonSerialize($block) {
		var $hidden_input =  $block.find('.wpb_vc_param_value');
		var serialize_string = '';

		var type = $block.find('select.type').val();
		var size = $block.find('select.size').val();
		var fullwidth = $block.find('input[name="fullwidth"]')[0].checked;

		var color = $block.find('input[name="color"]').val();
		var brandColor = $block.find('input[name="brand-color"]')[0].checked;

		var hoverColor = $block.find('input[name="hover-color"]').val();
		var brandHoverColor = $block.find('input[name="brand-hover-color"]')[0].checked;

		var textColor = $block.find('input[name="text-color"]').val();
		var brandTextColor = $block.find('input[name="brand-text-color"]')[0].checked;

		var textHoverColor = $block.find('input[name="text-hover-color"]').val();
		var brandTextHoverColor = $block.find('input[name="brand-text-hover-color"]')[0].checked;

		var borderColor = $block.find('input[name="border-color"]').val();
		var brandBorderColor = $block.find('input[name="brand-border-color"]')[0].checked;

		var borderHoverColor = $block.find('input[name="border-hover-color"]').val();
		var brandBorderHoverColor = $block.find('input[name="brand-border-hover-color"]')[0].checked;

		if ( type ) {
			serialize_string += 'type=' + type;
		}
		if ( size ) {
			serialize_string += '&size=' + size;
		}
		if ( fullwidth ) {
			serialize_string += '&fullwidth=true';
		}
		if ( color || brandColor ) {
			serialize_string += '&color=' + ( ( brandColor ) ? 'brand' : color );
		}
		if ( hoverColor || brandHoverColor ) {
			serialize_string += '&hover-color=' + ( ( brandHoverColor ) ? 'brand' : hoverColor );
		}
		if ( textColor || brandTextColor ) {
			serialize_string += '&text-color=' + ( ( brandTextColor ) ? 'brand' : textColor );
		}
		if ( textHoverColor || brandTextHoverColor ) {
			serialize_string += '&text-hover-color=' + ( ( brandTextHoverColor ) ? 'brand' : textHoverColor );
		}
		if ( borderColor || brandBorderColor ) {
			serialize_string += '&border-color=' + ( ( brandBorderColor ) ? 'brand' : borderColor );
		}
		if ( borderHoverColor || brandBorderHoverColor ) {
			serialize_string += '&border-hover-color=' + ( ( brandBorderHoverColor ) ? 'brand' : borderHoverColor );
		}

		$hidden_input.val( serialize_string );
	}

	function ohioHideFields(){
		var buttonBlock = $('.ohio_extra_button_block');

		var buttonType = buttonBlock.find('select.type').val(); 
		var size = buttonBlock.find('.size')[0];
		var fullwidth = buttonBlock.find('.fullwidth')[0];

		var color = buttonBlock.find('.button-color')[0];
		var hoverColor = buttonBlock.find('.button-hover-color')[0];

		if ( buttonType == 'arrow_link' ){
			$([size, fullwidth, color, hoverColor]).addClass('disabled');
		} else {
			$([size, fullwidth, color, hoverColor]).each(function(){
				if ( !$(this).attr('data-disabled') ){
					$(this).removeClass('disabled');
				}
			});
		}

		// Brand colors
		buttonBlock.find('.brand-color').each(function(){
			var color = $(this).parent().find('.color-group');

			if ( $(this).find('input')[0].checked ){
				color.addClass('disabled');
			} else {
				color.removeClass('disabled');
			}
		});
	}

	$('.ohio_extra_button_block').each(function(_, el) {
		vc.atts.colorpicker.init({}, $(el));
	});

	// Turn the plain Type/Size <select> fields into WPBakery's own select2
	// dropdowns. A button field can sit inside a tab that isn't active yet
	// when the panel first opens (select2 doesn't size itself correctly if
	// initialized while its container is display:none), so init is skipped
	// until the block is actually visible, and retried after every tab
	// switch - see typography.js for the same pattern in more detail.
	function ohioInitButtonBlock($block) {
		if ($block.data('ohio-button-inited') || !$block.is(':visible')) {
			return;
		}
		$block.data('ohio-button-inited', true);

		$block.find('.edit_form_line').each(function() {
			vc.atts.dropdown.init({}, $(this));
		});
	}

	function ohioInitVisibleButtonBlocks() {
		$('.ohio_extra_button_block').each(function() {
			ohioInitButtonBlock($(this));
		});
	}

	ohioInitVisibleButtonBlocks();

	$(document).on('click', '[data-vc-ui-element="panel-tab-control"]', function() {
		setTimeout(ohioInitVisibleButtonBlocks, 50);
	});

	$('#vc_ui-panel-edit-element').on(
		'change',
		'.ohio_extra_button_block input, .ohio_extra_button_block select',
		function(e){
			var $closest = $(this).closest('.ohio_extra_button_block');
			ohioButtonSerialize( $closest );
			ohioHideFields();
		}
	);

	$('.ohio_extra_button_block .wp-picker-clear').on('click', function(e){
		var $closest = $(this).closest('.ohio_extra_button_block');
		ohioButtonSerialize( $closest );
		ohioHideFields();
	});


	$('.ohio_extra_button_block .wp-picker-container').on('click', function(){
		var holder = $(this).find('.wp-picker-holder');

		$(this).css('left', '');
		var diff = (holder.outerWidth() + $(this).parent().parent().position().left) - $('.ohio_extra_button_block').outerWidth();

		if ( diff > 0 ){
			$(this).addClass('invert-position');
			$(this).find('.wp-picker-input-wrap, .wp-picker-holder').css('left', -diff + 'px');
		} else {
			$(this).removeClass('invert-position');
		}
	});


	ohioHideFields();

}(window.jQuery);