!function($){

	function ohioColumnsSerialize( $block ){
		var $hidden_input =  $block.find('.wpb_vc_param_value');
		var serialize_string = '';

		var large = $block.find('.nor-col-large').val();
		var medium = $block.find('.nor-col-medium').val();
		var small = $block.find('.nor-col-small').val();
		var extraSmall = $block.find('.nor-col-extra-small').val();
		
		serialize_string = large + '-' + small + '-' + extraSmall;

		$hidden_input.val( serialize_string );
	}


	$(document).on( 'change', '.nor-col-large, .nor-col-medium, .nor-col-small, .nor-col-extra-small', function(){
		ohioColumnsSerialize( $(this).closest('.ohio_extra_columns_block') );
	});

	// Turn the plain <select> fields into WPBakery's own select2 dropdowns,
	// matching the look of every other dropdown in the edit panel. A block
	// can sit inside a tab that isn't active yet when the panel first opens,
	// so init is skipped until the block is actually visible, and retried
	// after every tab switch.
	function ohioInitColumnsBlock( $block ){
		if ( $block.data('ohio-columns-inited') || !$block.is(':visible') ){
			return;
		}
		$block.data('ohio-columns-inited', true);

		$block.find('.edit_form_line').each(function() {
			vc.atts.dropdown.init({}, $(this));
		});
	}

	function ohioInitVisibleColumnsBlocks(){
		$('.ohio_extra_columns_block').each(function(){
			ohioInitColumnsBlock( $(this) );
		});
	}

	ohioInitVisibleColumnsBlocks();

	$(document).on('click', '[data-vc-ui-element="panel-tab-control"]', function() {
		setTimeout(ohioInitVisibleColumnsBlocks, 50);
	});

}(window.jQuery);