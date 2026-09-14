<?php

/**
* WPBakery Page Builder Ohio Social Networks shortcode params
*/

vc_lean_map( 'ohio_social_networks', 'ohio_social_networks_sc_map' );

function ohio_social_networks_sc_map() {
	return array(
		'name' => __( 'Social Networks', 'ohio-extra' ),
		'description' => __( 'Social media accounts', 'ohio-extra' ),
		'base' => 'ohio_social_networks',
		'category' => __( 'Ohio', 'ohio-extra' ),
		'icon' => OHIO_EXTRA_DIR_URL . 'assets/images/shortcodes/social_networks_icon.svg',
		'params' => array(

			// General.
			array(
				'type' => 'ohio_choose_box',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Type', 'ohio-extra' ),
				'param_name' => 'icon_layout',
				'value' => array(
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_054.svg',
						'key' => 'flat',
						'title' => __( 'Default', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_052.svg',
						'key' => 'outline',
						'title' => __( 'Outlined', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_053.svg',
						'key' => 'fill',
						'title' => __( 'Filled', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_056.svg',
						'key' => 'text',
						'title' => __( 'Text', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_055.svg',
						'key' => 'inline',
						'title' => __( 'Text with Icon', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_057.svg',
						'key' => 'boxed',
						'title' => __( 'Contained', 'ohio-extra' ),
					),
				)
			),
			array(
				'type' => 'ohio_choose_box',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Alignment', 'ohio-extra' ),
				'param_name' => 'block_alignment',
				'value' => array(
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_035.svg',
						'key' => 'left',
						'title' => __( 'Left', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_036.svg',
						'key' => 'center',
						'title' => __( 'Center', 'ohio-extra' ),
					),
					array(
						'icon' => plugin_dir_url( __FILE__ ) . 'images/wpb_params_037.svg',
						'key' => 'right',
						'title' => __( 'Right', 'ohio-extra' ),
					)
				),
				'default' => 'center',
			),
			array(
				'type' => 'dropdown',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Size', 'ohio-extra' ),
				'param_name' => 'icons_size',
				'value' => array(
					__( 'Default', 'ohio-extra' ) => 'default',
					__( 'Small', 'ohio-extra' ) => 'small',
					__( 'Large', 'ohio-extra' ) => 'large'
				),
			),
			array(
				'type' => 'dropdown',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Type', 'ohio-extra' ),
				'param_name' => 'type_links',
				'value' => array(
					__( 'Subscribe links', 'ohio-extra' ) => 'custom',
					__( 'Share links', 'ohio-extra' ) => 'share',
				),
			),
			array(
				'type' => 'ohio_range',
				'holder' => 'em',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Shape Border', 'ohio-extra' ),
				'param_name' => 'border_width',
				'description' => __( '<a target="_blank" href="https://www.w3schools.com/cssref/css_units.asp">Use px units&nbsp;<i title="Use CSS unit value." class="far fa-question-circle"></i></a>', 'ohio-extra' ),
				'value' => '0',
				'dependency' => array(
					'element' => 'icon_layout',
					'value' => array(
						'flat',
						'outline',
						'fill',
						'boxed'
					),
				),
			),
			array(
				'type' => 'ohio_range',
				'holder' => 'em',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Shape Corners', 'ohio-extra' ),
				'param_name' => 'border_radius',
				'description' => __( '<a target="_blank" href="https://www.w3schools.com/cssref/css_units.asp">Use px units&nbsp;<i title="Use CSS unit value." class="far fa-question-circle"></i></a>', 'ohio-extra' ),
				'value' => '5',
				'dependency' => array(
					'element' => 'icon_layout',
					'value' => array(
						'flat',
						'outline',
						'fill',
						'boxed'
					),
				),
			),
			array(
				'type' => 'ohio_check',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Use Brand Colors?', 'ohio-extra' ),
				'param_name' => 'default_colors',
				'value' => array(
					__( 'Yes', 'ohio-extra' ) => '0'
				),
			),
			array(
				'type' => 'ohio_check',
				'group' => __( 'General', 'ohio-extra' ),
				'heading' => __( 'Use Brand Colors (Hover)?', 'ohio-extra' ),
				'param_name' => 'hover_default_colors',
				'value' => array(
					__( 'Yes', 'ohio-extra' ) => '0'
				),
				'dependency' => array(
					'element' => 'default_colors',
					'value' => '0',
				),
			),

			// Links.
			array(
				'type' => 'ohio_check',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-facebook"></i> ' . __( 'Facebook Share', 'ohio-extra' ),
				'param_name' => 'facebook',
				'value' => array(
					__( 'Enable', 'ohio-extra' ) => '1'
				),
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'share',
				),
			),
			array(
				'type' => 'ohio_check',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-x-twitter"></i> ' . __( 'X Share', 'ohio-extra' ),
				'param_name' => 'twitter',
				'value' => array(
					__( 'Enable', 'ohio-extra' ) => '1'
				),
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'share',
				),
			),
			array(
				'type' => 'ohio_check',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-linkedin"></i> ' . __( 'LinkedIn Share', 'ohio-extra' ),
				'param_name' => 'linkedin',
				'value' => array(
					__( 'Enable', 'ohio-extra' ) => '1'
				),
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'share',
				),
			),
			array(
				'type' => 'ohio_check',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-pinterest"></i> ' . __( 'Pinterest Share', 'ohio-extra' ),
				'param_name' => 'pinterest',
				'value' => array(
					__( 'Enable', 'ohio-extra' ) => '1'
				),
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'share',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-500px"></i> ' . __( '500px', 'ohio-extra' ),
				'param_name' => 'fivehundredpx_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-amazon"></i> ' . __( 'Amazon', 'ohio-extra' ),
				'param_name' => 'amazon_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-angellist"></i> ' . __( 'AngelList', 'ohio-extra' ),
				'param_name' => 'angellist_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-apple"></i> ' . __( 'Apple', 'ohio-extra' ),
				'param_name' => 'apple_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-artstation"></i> ' . __( 'ArtStation', 'ohio-extra' ),
				'param_name' => 'artstation_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-behance"></i> ' . __( 'Behance', 'ohio-extra' ),
				'param_name' => 'behance_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-bitbucket"></i> ' . __( 'Bitbucket', 'ohio-extra' ),
				'param_name' => 'bitbucket_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-bluesky"></i> ' . __( 'BlueSky', 'ohio-extra' ),
				'param_name' => 'bluesky_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-blogger"></i> ' . __( 'Blogger', 'ohio-extra' ),
				'param_name' => 'blogger_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-codepen"></i> ' . __( 'CodePen', 'ohio-extra' ),
				'param_name' => 'codepen_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-deviantart"></i> ' . __( 'DeviantArt', 'ohio-extra' ),
				'param_name' => 'deviantart_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-diaspora"></i> ' . __( 'Diaspora', 'ohio-extra' ),
				'param_name' => 'diaspora_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-digg"></i> ' . __( 'Digg', 'ohio-extra' ),
				'param_name' => 'digg_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-discord"></i> ' . __( 'Discord', 'ohio-extra' ),
				'param_name' => 'discord_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-dribbble"></i> ' . __( 'Dribbble', 'ohio-extra' ),
				'param_name' => 'dribbble_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-dropbox"></i> ' . __( 'Dropbox', 'ohio-extra' ),
				'param_name' => 'dropbox_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-drupal"></i> ' . __( 'Drupal', 'ohio-extra' ),
				'param_name' => 'drupal_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-etsy"></i> ' . __( 'Etsy', 'ohio-extra' ),
				'param_name' => 'etsy_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-facebook-f"></i> ' . __( 'Facebook', 'ohio-extra' ),
				'param_name' => 'facebook_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-fediverse"></i> ' . __( 'Fediverse', 'ohio-extra' ),
				'param_name' => 'fediverse_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-figma"></i> ' . __( 'Figma', 'ohio-extra' ),
				'param_name' => 'figma_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-flickr"></i> ' . __( 'Flickr', 'ohio-extra' ),
				'param_name' => 'flickr_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-foursquare"></i> ' . __( 'Foursquare', 'ohio-extra' ),
				'param_name' => 'foursquare_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-git"></i> ' . __( 'Git', 'ohio-extra' ),
				'param_name' => 'git_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-github"></i> ' . __( 'GitHub', 'ohio-extra' ),
				'param_name' => 'github_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-gitlab"></i> ' . __( 'GitLab', 'ohio-extra' ),
				'param_name' => 'gitlab_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-goodreads"></i> ' . __( 'Goodreads', 'ohio-extra' ),
				'param_name' => 'goodreads_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-google"></i> ' . __( 'Google', 'ohio-extra' ),
				'param_name' => 'google_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-google-play"></i> ' . __( 'Google Play', 'ohio-extra' ),
				'param_name' => 'googleplay_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-hacker-news"></i> ' . __( 'Hacker News', 'ohio-extra' ),
				'param_name' => 'hackernews_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-houzz"></i> ' . __( 'Houzz', 'ohio-extra' ),
				'param_name' => 'houzz_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-instagram"></i> ' . __( 'Instagram', 'ohio-extra' ),
				'param_name' => 'instagram_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-jsfiddle"></i> ' . __( 'JSFiddle', 'ohio-extra' ),
				'param_name' => 'jsfiddle_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-kaggle"></i> ' . __( 'Kaggle', 'ohio-extra' ),
				'param_name' => 'kaggle_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-kickstarter"></i> ' . __( 'Kickstarter', 'ohio-extra' ),
				'param_name' => 'kickstarter_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-lastfm"></i> ' . __( 'Last.fm', 'ohio-extra' ),
				'param_name' => 'lastfm_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-line"></i> ' . __( 'Line', 'ohio-extra' ),
				'param_name' => 'line_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-linkedin"></i> ' . __( 'LinkedIn', 'ohio-extra' ),
				'param_name' => 'linkedin_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-mastodon"></i> ' . __( 'Mastodon', 'ohio-extra' ),
				'param_name' => 'mastodon_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-medium-m"></i> ' . __( 'Medium', 'ohio-extra' ),
				'param_name' => 'medium_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-meetup"></i> ' . __( 'Meetup', 'ohio-extra' ),
				'param_name' => 'meetup_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-messenger"></i> ' . __( 'Messenger', 'ohio-extra' ),
				'param_name' => 'messenger_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-nextdoor"></i> ' . __( 'Nextdoor', 'ohio-extra' ),
				'param_name' => 'nextdoor_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-npm"></i> ' . __( 'npm', 'ohio-extra' ),
				'param_name' => 'npm_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-orcid"></i> ' . __( 'ORCID', 'ohio-extra' ),
				'param_name' => 'orcid_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-patreon"></i> ' . __( 'Patreon', 'ohio-extra' ),
				'param_name' => 'patreon_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-paypal"></i> ' . __( 'PayPal', 'ohio-extra' ),
				'param_name' => 'paypal_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-peertube"></i> ' . __( 'PeerTube', 'ohio-extra' ),
				'param_name' => 'peertube_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-pinterest"></i> ' . __( 'Pinterest', 'ohio-extra' ),
				'param_name' => 'pinterest_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-product-hunt"></i> ' . __( 'Product Hunt', 'ohio-extra' ),
				'param_name' => 'producthunt_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-python"></i> ' . __( 'Python', 'ohio-extra' ),
				'param_name' => 'python_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-qq"></i> ' . __( 'QQ', 'ohio-extra' ),
				'param_name' => 'qq_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-quora"></i> ' . __( 'Quora', 'ohio-extra' ),
				'param_name' => 'quora_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-reddit"></i> ' . __( 'Reddit', 'ohio-extra' ),
				'param_name' => 'reddit_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-researchgate"></i> ' . __( 'ResearchGate', 'ohio-extra' ),
				'param_name' => 'researchgate_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-reverbnation"></i> ' . __( 'ReverbNation', 'ohio-extra' ),
				'param_name' => 'reverbnation_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-rss"></i> ' . __( 'RSS', 'ohio-extra' ),
				'param_name' => 'rss_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-salesforce"></i> ' . __( 'Salesforce', 'ohio-extra' ),
				'param_name' => 'salesforce_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-sass"></i> ' . __( 'Sass', 'ohio-extra' ),
				'param_name' => 'sass_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-shopify"></i> ' . __( 'Shopify', 'ohio-extra' ),
				'param_name' => 'shopify_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-skype"></i> ' . __( 'Skype', 'ohio-extra' ),
				'param_name' => 'skype_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-slack"></i> ' . __( 'Slack', 'ohio-extra' ),
				'param_name' => 'slack_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-snapchat"></i> ' . __( 'Snapchat', 'ohio-extra' ),
				'param_name' => 'snapchat_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-soundcloud"></i> ' . __( 'SoundCloud', 'ohio-extra' ),
				'param_name' => 'soundcloud_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-sourcetree"></i> ' . __( 'SourceTree', 'ohio-extra' ),
				'param_name' => 'sourcetree_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-spotify"></i> ' . __( 'Spotify', 'ohio-extra' ),
				'param_name' => 'spotify_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-stack-exchange"></i> ' . __( 'Stack Exchange', 'ohio-extra' ),
				'param_name' => 'stackexchange_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-stack-overflow"></i> ' . __( 'Stack Overflow', 'ohio-extra' ),
				'param_name' => 'stackoverflow_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-strava"></i> ' . __( 'Strava', 'ohio-extra' ),
				'param_name' => 'strava_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-stripe"></i> ' . __( 'Stripe', 'ohio-extra' ),
				'param_name' => 'stripe_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-teamspeak"></i> ' . __( 'TeamSpeak', 'ohio-extra' ),
				'param_name' => 'teamspeak_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-telegram"></i> ' . __( 'Telegram', 'ohio-extra' ),
				'param_name' => 'telegram_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-threads"></i> ' . __( 'Threads', 'ohio-extra' ),
				'param_name' => 'threads_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-tiktok"></i> ' . __( 'TikTok', 'ohio-extra' ),
				'param_name' => 'tiktok_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-trello"></i> ' . __( 'Trello', 'ohio-extra' ),
				'param_name' => 'trello_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-tripadvisor"></i> ' . __( 'TripAdvisor', 'ohio-extra' ),
				'param_name' => 'tripadvisor_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-tumblr"></i> ' . __( 'Tumblr', 'ohio-extra' ),
				'param_name' => 'tumblr_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-twitch"></i> ' . __( 'Twitch', 'ohio-extra' ),
				'param_name' => 'twitch_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-x-twitter"></i> ' . __( 'X', 'ohio-extra' ),
				'param_name' => 'twitter_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-ubuntu"></i> ' . __( 'Ubuntu', 'ohio-extra' ),
				'param_name' => 'ubuntu_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-untappd"></i> ' . __( 'Untappd', 'ohio-extra' ),
				'param_name' => 'untappd_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-usps"></i> ' . __( 'USPS', 'ohio-extra' ),
				'param_name' => 'usps_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-viadeo"></i> ' . __( 'Viadeo', 'ohio-extra' ),
				'param_name' => 'viadeo_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-vimeo"></i> ' . __( 'Vimeo', 'ohio-extra' ),
				'param_name' => 'vimeo_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-waze"></i> ' . __( 'Waze', 'ohio-extra' ),
				'param_name' => 'waze_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-weibo"></i> ' . __( 'Weibo', 'ohio-extra' ),
				'param_name' => 'weibo_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-weixin"></i> ' . __( 'WeChat', 'ohio-extra' ),
				'param_name' => 'wechat_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-wikipedia-w"></i> ' . __( 'Wikipedia', 'ohio-extra' ),
				'param_name' => 'wikipedia_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-wordpress"></i> ' . __( 'WordPress', 'ohio-extra' ),
				'param_name' => 'wordpress_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-xing"></i> ' . __( 'Xing', 'ohio-extra' ),
				'param_name' => 'xing_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-yahoo"></i> ' . __( 'Yahoo', 'ohio-extra' ),
				'param_name' => 'yahoo_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-yelp"></i> ' . __( 'Yelp', 'ohio-extra' ),
				'param_name' => 'yelp_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-youtube"></i> ' . __( 'YouTube', 'ohio-extra' ),
				'param_name' => 'youtube_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Links', 'ohio-extra' ),
				'heading' => '<i class="fa-brands fa-zhihu"></i> ' . __( 'Zhihu', 'ohio-extra' ),
				'param_name' => 'zhihu_link_custom',
				'dependency' => array(
					'element' => 'type_links',
					'value' => 'custom',
				),
			),
		
			// Styles.
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Icon Color', 'ohio-extra' ),
				'param_name' => 'icon_color',
				'dependency' => array(
					'element' => 'default_colors',
					'value' => array(
						'0',
					)
				)
			),
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Icon Color (Hover)', 'ohio-extra' ),
				'param_name' => 'icon_hover_color',
				'dependency' => array(
					'element' => 'hover_default_colors',
					'value' => array(
						'0'
					)
				)
			),
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Shape Color', 'ohio-extra' ),
				'param_name' => 'color',
				'dependency' => array(
					'element' => 'default_colors',
					'value' => array(
						'0',
					)
				)
			),
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Shape Color (Hover)', 'ohio-extra' ),
				'param_name' => 'hover_color',
				'dependency' => array(
					'element' => 'hover_default_colors',
					'value' => array(
						'0'
					)
				)
			),
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Shape Border Color', 'ohio-extra' ),
				'param_name' => 'border_color',
				'dependency' => array(
					'element' => 'icon_layout',
					'value' => array(
						'fill',
						'boxed'
					),
				),
			),
			array(
				'type' => 'ohio_colorpicker',
				'group' => __( 'Styles', 'ohio-extra' ),
				'heading' => __( 'Shape Border Color (Hover)', 'ohio-extra' ),
				'param_name' => 'border_hover_color',
				'default_colors' => array(

				),
				'dependency' => array(
					'element' => 'icon_layout',
					'value' => array(
						'flat',
						'fill',
						'boxed'
					),
				),
			),
			
			// Design Options.
            array(
                'type' => 'css_editor',
                'heading' => __( 'CSS', 'ohio-extra' ),
                'param_name' => 'content_styles',
                'group' => __( 'Design Options', 'ohio-extra' ),
            ),
            array(
                'type' => 'ohio_divider',
                'group' => __( 'Design Options', 'ohio-extra' ),
                'param_name' => 'other_settings_title',
                'value' => __( 'Other', 'ohio-extra' ),
            ),
            array(
                'type' => 'textfield',
                'group' => __( 'Design Options', 'ohio-extra' ),
                'heading' => __( 'CSS Class', 'ohio-extra' ),
                'param_name' => 'css_class',
                'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ohio-extra' ),
            ),

			// Appear Effect.
			array(
				'type' => 'dropdown',
				'group' => __( 'Appear Effect', 'ohio-extra' ),
				'heading' => __( 'Appear Effect', 'ohio-extra' ),
				'param_name' => 'appearance_effect',
				'value' => array(
					__( 'None', 'ohio-extra' ) => 'none',
					__( 'Fade Up', 'ohio-extra' ) => 'fade-up',
					__( 'Fade Down', 'ohio-extra' ) => 'fade-down',
					__( 'Fade Left', 'ohio-extra' ) => 'fade-left',
					__( 'Fade Right', 'ohio-extra' ) => 'fade-right',
					__( 'Flip Up', 'ohio-extra' ) => 'flip-up',
					__( 'Flip Down', 'ohio-extra' ) => 'flip-down',
					__( 'Zoom In', 'ohio-extra' ) => 'zoom-in',
					__( 'Zoom Out', 'ohio-extra' ) => 'zoom-out'
				)
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Appear Effect', 'ohio-extra' ),
				'heading' => __( 'Animation Duration', 'ohio-extra' ),
				'param_name' => 'appearance_duration',
				'description' => __( 'Duration accept values from 50 to 3000 (ms), with step 50.', 'ohio-extra' ),
			),
			array(
				'type' => 'textfield',
				'group' => __( 'Appear Effect', 'ohio-extra' ),
				'heading' => __( 'Animation Delay', 'ohio-extra' ),
				'param_name' => 'appearance_delay',
				'description' => __( 'A delay before animation, accepted values are in range from 50 to 3000 (ms), with a step of 50.', 'ohio-extra' ),
			),

			array(
				'type' => 'ohio_check',
				'group' => __( 'Appear Effect', 'ohio-extra' ),
				'heading' => __( 'Animation Repeat', 'ohio-extra' ),
				'description' => 'Repeat animation while scrolling page up and down',
				'param_name' => 'appearance_once',
				'value' => array(
					__( 'Yes', 'ohio-extra' ) => '1'
				)
			),
		)
	);
}