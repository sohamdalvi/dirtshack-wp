<?php

/*
* Marks the theme's ".project-page" template markup on single project pages
* (the structure the theme renders outside the_content(), so it can't be
* edited with Elementor) and links back to the project's edit screen.
*
* Shown only inside the Elementor editor's preview iframe for that post -
* never on a normal front-end visit, even for a logged-in editor.
*
* Hooks into two generic extension points the theme exposes on single
* project pages: the `ohio_project_page_class` filter (extra classes on the
* ".project-page" wrapper) and the `ohio_project_page_notice` action
* (extra markup printed as the first child of that wrapper).
*/

/*
* Whether the current request is rendering inside the Elementor editor's
* preview iframe for the given post (not a normal front-end visit, even
* for a logged-in user who can edit the post).
*/
function ohio_extra_is_elementor_editor( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) || empty( \Elementor\Plugin::$instance->preview ) ) {
		return false;
	}

	return (bool) \Elementor\Plugin::$instance->preview->is_preview_mode( $post_id );
}

// Load the notice/highlight styles on the front-end, but only inside the
// Elementor preview iframe - never on a normal page load.
add_action( 'wp_enqueue_scripts', 'ohio_extra_project_page_notice_styles' );
function ohio_extra_project_page_notice_styles() {
	if ( ! ohio_extra_is_elementor_editor() ) {
		return;
	}

	wp_enqueue_style( 'ohio-extra-styles', OHIO_EXTRA_DIR_URL . 'assets/css/ohio-extra.css', [], '3.7.7' );
}

// Append the highlight class to the theme's ".project-page" wrapper.
add_filter( 'ohio_project_page_class', 'ohio_extra_project_page_class' );
function ohio_extra_project_page_class( $classes ) {
	if ( ohio_extra_is_elementor_editor() ) {
		$classes .= ' clb-theme-template-highlight';
	}

	return $classes;
}

// Print the notice banner as the first child of the ".project-page" wrapper.
add_action( 'ohio_project_page_notice', 'ohio_extra_project_page_notice' );
function ohio_extra_project_page_notice() {
	$post_id = get_the_ID();

	if ( ! $post_id || ! ohio_extra_is_elementor_editor( $post_id ) ) {
		return;
	}

	$edit_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$link_id = 'clb-theme-template-notice-link-' . $post_id;
	?>
	<div class="clb-theme-template-notice">
		<span><?php esc_html_e( 'This area is part of the theme template and can\'t be edited with Elementor.', 'ohio-extra' ); ?></span>
		<a id="<?php echo esc_attr( $link_id ); ?>" href="<?php echo esc_url( $edit_url ); ?>" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Edit with Project Settings', 'ohio-extra' ); ?>
		</a>
	</div>
	<script>
	// Elementor's preview iframe swallows normal link clicks (it prevents the
	// default action so editors don't accidentally navigate away while editing).
	// Open the edit screen ourselves on mousedown, which fires before - and
	// independently of - whatever Elementor does to the click event, then
	// suppress the click's own default/bubling so we never open it twice.
	(function () {
		var link = document.getElementById( <?php echo wp_json_encode( $link_id ); ?> );
		if ( ! link ) {
			return;
		}
		var opened = false;
		var openEditScreen = function () {
			if ( opened ) {
				return;
			}
			opened = true;
			window.open( link.href, '_blank', 'noopener,noreferrer' );
			setTimeout( function () { opened = false; }, 300 );
		};
		link.addEventListener( 'mousedown', function ( e ) {
			if ( e.button !== 0 ) {
				return;
			}
			e.preventDefault();
			openEditScreen();
		} );
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openEditScreen();
		} );
	})();
	</script>
	<?php
}
