<?php

/**
* WPBakery Page Builder Ohio Team Member shortcode view
*/

?>
<div class="ohio-widget team-member banner card<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo esc_attr( $animation_attrs ); ?>>

    <div class="image-holder" <?php echo esc_attr($tilt_attrs); ?>>
            
        <?php if ( $use_link ) : ?>
			<a data-cursor-class="cursor-link" href="<?php echo $member_link['url']; ?>"<?php if ( $member_link['blank'] ) { echo ' target="_blank" rel="nofollow"'; } ?>>
		<?php endif; ?>

            <?php if ( $photo ) : ?>
				<img <?php echo $photo_image_atts; ?>>
			<?php endif; ?>

        <?php if ( $use_link ) : ?>
			</a>
		<?php endif; ?>

		<?php if ( $block_type_layout != 'inner' ) : ?>

	        <div class="overlay-details -fade-up dark-scheme">

	        	<?php if ( $description ) : ?>
					<p><?php echo $description; ?></p>
				<?php endif; ?>

	            <div class="social-networks -outlined -small">

		        	<?php if ( $fivehundred_link ) : ?>
						<a href="<?php echo $fivehundred_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( '500px', 'ohio-extra' ); ?>" class="network -unlink fivehundredpx">
							<i class="fa-brands fa-500px"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $amazon_link ) : ?>
						<a href="<?php echo $amazon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'amazon', 'ohio-extra' ); ?>" class="network -unlink amazon">
							<i class="fa-brands fa-amazon"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $angellist_link ) : ?>
						<a href="<?php echo $angellist_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'angellist', 'ohio-extra' ); ?>" class="network -unlink angellist">
							<i class="fa-brands fa-angellist"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $apple_link ) : ?>
						<a href="<?php echo $apple_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'apple', 'ohio-extra' ); ?>" class="network -unlink apple">
							<i class="fa-brands fa-apple"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $artstation_link ) : ?>
						<a href="<?php echo $artstation_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'artstation', 'ohio-extra' ); ?>" class="network -unlink artstation">
							<i class="fa-brands fa-artstation"></i>
						</a>
					<?php endif; ?>

	            	<?php if ( $behance_link ) : ?>
						<a href="<?php echo $behance_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'behance', 'ohio-extra' ); ?>" class="network -unlink behance">
							<i class="fa-brands fa-behance"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $bitbucket_link ) : ?>
						<a href="<?php echo $bitbucket_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'bitbucket', 'ohio-extra' ); ?>" class="network -unlink bitbucket">
							<i class="fa-brands fa-bitbucket"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $bluesky_link ) : ?>
						<a href="<?php echo $bluesky_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'bluesky', 'ohio-extra' ); ?>" class="network -unlink bluesky">
							<i class="fa-brands fa-bluesky"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $blogger_link ) : ?>
						<a href="<?php echo $blogger_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'blogger', 'ohio-extra' ); ?>" class="network -unlink blogger">
							<i class="fa-brands fa-blogger"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $codepen_link ) : ?>
						<a href="<?php echo $codepen_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'codepen', 'ohio-extra' ); ?>" class="network -unlink codepen">
							<i class="fa-brands fa-codepen"></i>
						</a>
					<?php endif; ?>

					<?php if ( $deviantart_link ) : ?>
						<a href="<?php echo $deviantart_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'deviantart', 'ohio-extra' ); ?>" class="network -unlink deviantart">
							<i class="fa-brands fa-deviantart"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $diaspora_link ) : ?>
						<a href="<?php echo $diaspora_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'diaspora', 'ohio-extra' ); ?>" class="network -unlink diaspora">
							<i class="fa-brands fa-diaspora"></i>
						</a>
					<?php endif; ?>

					<?php if ( $digg_link ) : ?>
						<a href="<?php echo $digg_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'digg', 'ohio-extra' ); ?>" class="network -unlink digg">
							<i class="fa-brands fa-digg"></i>
						</a>
					<?php endif; ?>

					<?php if ( $discord_link ) : ?>
						<a href="<?php echo $discord_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'discord', 'ohio-extra' ); ?>" class="network -unlink discord">
							<i class="fa-brands fa-discord"></i>
						</a>
					<?php endif; ?>

					<?php if ( $dribbble_link ) : ?>
						<a href="<?php echo $dribbble_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'dribbble', 'ohio-extra' ); ?>" class="network -unlink dribbble">
							<i class="fa-brands fa-dribbble"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $dropbox_link ) : ?>
						<a href="<?php echo $dropbox_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'dropbox', 'ohio-extra' ); ?>" class="network -unlink dropbox">
							<i class="fa-brands fa-dropbox"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $drupal_link ) : ?>
						<a href="<?php echo $drupal_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'drupal', 'ohio-extra' ); ?>" class="network -unlink drupal">
							<i class="fa-brands fa-drupal"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $etsy_link ) : ?>
						<a href="<?php echo $etsy_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'etsy', 'ohio-extra' ); ?>" class="network -unlink etsy">
							<i class="fa-brands fa-etsy"></i>
						</a>
					<?php endif; ?>

					<?php if ( $facebook_link ) : ?>
						<a href="<?php echo $facebook_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'facebook', 'ohio-extra' ); ?>" class="network -unlink facebook">
							<i class="fa-brands fa-facebook-f"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $fediverse_link ) : ?>
						<a href="<?php echo $fediverse_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'fediverse', 'ohio-extra' ); ?>" class="network -unlink fediverse">
							<i class="fa-brands fa-fediverse"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $figma_link ) : ?>
						<a href="<?php echo $figma_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'figma', 'ohio-extra' ); ?>" class="network -unlink figma">
							<i class="fa-brands fa-figma"></i>
						</a>
					<?php endif; ?>

					<?php if ( $flickr_link ) : ?>
						<a href="<?php echo $flickr_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'flickr', 'ohio-extra' ); ?>" class="network -unlink flickr">
							<i class="fa-brands fa-flickr"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $foursquare_link ) : ?>
						<a href="<?php echo $foursquare_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'foursquare', 'ohio-extra' ); ?>" class="network -unlink foursquare">
							<i class="fa-brands fa-foursquare"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $git_link ) : ?>
						<a href="<?php echo $git_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'git', 'ohio-extra' ); ?>" class="network -unlink git">
							<i class="fa-brands fa-git"></i>
						</a>
					<?php endif; ?>

					<?php if ( $github_link ) : ?>
						<a href="<?php echo $github_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'github', 'ohio-extra' ); ?>" class="network -unlink github">
							<i class="fa-brands fa-github"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $gitlab_link ) : ?>
						<a href="<?php echo $gitlab_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'gitlab', 'ohio-extra' ); ?>" class="network -unlink gitlab">
							<i class="fa-brands fa-gitlab"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $goodreads_link ) : ?>
						<a href="<?php echo $goodreads_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'goodreads', 'ohio-extra' ); ?>" class="network -unlink goodreads">
							<i class="fa-brands fa-goodreads"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $google_link ) : ?>
						<a href="<?php echo $google_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'google', 'ohio-extra' ); ?>" class="network -unlink google">
							<i class="fa-brands fa-google"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $googleplay_link ) : ?>
						<a href="<?php echo $googleplay_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'google-play', 'ohio-extra' ); ?>" class="network -unlink googleplay">
							<i class="fa-brands fa-google-play"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $hackernews_link ) : ?>
						<a href="<?php echo $hackernews_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'hacker-news', 'ohio-extra' ); ?>" class="network -unlink hackernews">
							<i class="fa-brands fa-hacker-news"></i>
						</a>
					<?php endif; ?>

					<?php if ( $houzz_link ) : ?>
						<a href="<?php echo $houzz_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'houzz', 'ohio-extra' ); ?>" class="network -unlink houzz">
							<i class="fa-brands fa-houzz"></i>
						</a>
					<?php endif; ?>

					<?php if ( $instagram_link ) : ?>
						<a href="<?php echo $instagram_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'instagram', 'ohio-extra' ); ?>" class="network -unlink instagram">
							<i class="fa-brands fa-instagram"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $jsfiddle_link ) : ?>
						<a href="<?php echo $jsfiddle_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'jsfiddle', 'ohio-extra' ); ?>" class="network -unlink jsfiddle">
							<i class="fa-brands fa-jsfiddle"></i>
						</a>
					<?php endif; ?>

					<?php if ( $kaggle_link ) : ?>
						<a href="<?php echo $kaggle_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'kaggle', 'ohio-extra' ); ?>" class="network -unlink kaggle">
							<i class="fa-brands fa-kaggle"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $kickstarter_link ) : ?>
						<a href="<?php echo $kickstarter_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'kickstarter', 'ohio-extra' ); ?>" class="network -unlink kickstarter">
							<i class="fa-brands fa-kickstarter"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $lastfm_link ) : ?>
						<a href="<?php echo $lastfm_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'lastfm', 'ohio-extra' ); ?>" class="network -unlink lastfm">
							<i class="fa-brands fa-lastfm"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $line_link ) : ?>
						<a href="<?php echo $line_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'line', 'ohio-extra' ); ?>" class="network -unlink line">
							<i class="fa-brands fa-line"></i>
						</a>
					<?php endif; ?>

					<?php if ( $linkedin_link ) : ?>
						<a href="<?php echo $linkedin_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'linkedin', 'ohio-extra' ); ?>" class="network -unlink linkedin">
							<i class="fa-brands fa-linkedin"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $mastodon_link ) : ?>
						<a href="<?php echo $mastodon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'mastodon', 'ohio-extra' ); ?>" class="network -unlink mastodon">
							<i class="fa-brands fa-mastodon"></i>
						</a>
					<?php endif; ?>

					<?php if ( $medium_link ) : ?>
						<a href="<?php echo $medium_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'medium', 'ohio-extra' ); ?>" class="network -unlink medium">
							<i class="fa-brands fa-medium-m"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $meetup_link ) : ?>
						<a href="<?php echo $meetup_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'meetup', 'ohio-extra' ); ?>" class="network -unlink meetup">
							<i class="fa-brands fa-meetup"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $messenger_link ) : ?>
						<a href="<?php echo $messenger_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'messenger', 'ohio-extra' ); ?>" class="network -unlink messenger">
							<i class="fa-brands fa-messenger"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $nextdoor_link ) : ?>
						<a href="<?php echo $nextdoor_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'nextdoor', 'ohio-extra' ); ?>" class="network -unlink nextdoor">
							<i class="fa-brands fa-nextdoor"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $npm_link ) : ?>
						<a href="<?php echo $npm_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'npm', 'ohio-extra' ); ?>" class="network -unlink npm">
							<i class="fa-brands fa-npm"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $orcid_link ) : ?>
						<a href="<?php echo $orcid_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'orcid', 'ohio-extra' ); ?>" class="network -unlink orcid">
							<i class="fa-brands fa-orcid"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $patreon_link ) : ?>
						<a href="<?php echo $patreon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'patreon', 'ohio-extra' ); ?>" class="network -unlink patreon">
							<i class="fa-brands fa-patreon"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $paypal_link ) : ?>
						<a href="<?php echo $paypal_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'paypal', 'ohio-extra' ); ?>" class="network -unlink paypal">
							<i class="fa-brands fa-paypal"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $peertube_link ) : ?>
						<a href="<?php echo $peertube_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'peertube', 'ohio-extra' ); ?>" class="network -unlink peertube">
							<i class="fa-brands fa-peertube"></i>
						</a>
					<?php endif; ?>

					<?php if ( $pinterest_link ) : ?>
						<a href="<?php echo $pinterest_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'pinterest', 'ohio-extra' ); ?>" class="network -unlink pinterest">
							<i class="fa-brands fa-pinterest"></i>
						</a>
					<?php endif; ?>

					<?php if ( $producthunt_link ) : ?>
						<a href="<?php echo $producthunt_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'producthunt', 'ohio-extra' ); ?>" class="network -unlink producthunt">
							<i class="fa-brands fa-product-hunt"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $python_link ) : ?>
						<a href="<?php echo $python_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'python', 'ohio-extra' ); ?>" class="network -unlink python">
							<i class="fa-brands fa-python"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $qq_link ) : ?>
						<a href="<?php echo $qq_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'qq', 'ohio-extra' ); ?>" class="network -unlink qq">
							<i class="fa-brands fa-qq"></i>
						</a>
					<?php endif; ?>

					<?php if ( $quora_link ) : ?>
						<a href="<?php echo $quora_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'quora', 'ohio-extra' ); ?>" class="network -unlink quora">
							<i class="fa-brands fa-quora"></i>
						</a>
					<?php endif; ?>

					<?php if ( $reddit_link ) : ?>
						<a href="<?php echo $reddit_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'reddit', 'ohio-extra' ); ?>" class="network -unlink reddit">
							<i class="fa-brands fa-reddit"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $researchgate_link ) : ?>
						<a href="<?php echo $researchgate_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'researchgate', 'ohio-extra' ); ?>" class="network -unlink researchgate">
							<i class="fa-brands fa-researchgate"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $reverbnation_link ) : ?>
						<a href="<?php echo $reverbnation_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'reverbnation', 'ohio-extra' ); ?>" class="network -unlink reverbnation">
							<i class="fa-brands fa-reverbnation"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $rss_link ) : ?>
						<a href="<?php echo $rss_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'rss', 'ohio-extra' ); ?>" class="network -unlink rss">
							<i class="fa-brands fa-rss"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $salesforce_link ) : ?>
						<a href="<?php echo $salesforce_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'salesforce', 'ohio-extra' ); ?>" class="network -unlink salesforce">
							<i class="fa-brands fa-salesforce"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $sass_link ) : ?>
						<a href="<?php echo $sass_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sass', 'ohio-extra' ); ?>" class="network -unlink sass">
							<i class="fa-brands fa-sass"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $sharp_link ) : ?>
						<a href="<?php echo $sharp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sharp', 'ohio-extra' ); ?>" class="network -unlink sharp">
							<i class="fa-brands fa-sharp"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $shopify_link ) : ?>
						<a href="<?php echo $shopify_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'shopify', 'ohio-extra' ); ?>" class="network -unlink shopify">
							<i class="fa-brands fa-shopify"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $skype_link ) : ?>
						<a href="<?php echo $skype_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'skype', 'ohio-extra' ); ?>" class="network -unlink skype">
							<i class="fa-brands fa-skype"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $slack_link ) : ?>
						<a href="<?php echo $slack_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'slack', 'ohio-extra' ); ?>" class="network -unlink slack">
							<i class="fa-brands fa-slack"></i>
						</a>
					<?php endif; ?>

					<?php if ( $snapchat_link ) : ?>
						<a href="<?php echo $snapchat_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'snapchat', 'ohio-extra' ); ?>" class="network -unlink snapchat">
							<i class="fa-brands fa-snapchat"></i>
						</a>
					<?php endif; ?>

					<?php if ( $soundcloud_link ) : ?>
						<a href="<?php echo $soundcloud_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'soundcloud', 'ohio-extra' ); ?>" class="network -unlink soundcloud">
							<i class="fa-brands fa-soundcloud"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $sourcetree_link ) : ?>
						<a href="<?php echo $sourcetree_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sourcetree', 'ohio-extra' ); ?>" class="network -unlink sourcetree">
							<i class="fa-brands fa-sourcetree"></i>
						</a>
					<?php endif; ?>

					<?php if ( $spotify_link ) : ?>
						<a href="<?php echo $spotify_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'spotify', 'ohio-extra' ); ?>" class="network -unlink spotify">
							<i class="fa-brands fa-spotify"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $stackexchange_link ) : ?>
						<a href="<?php echo $stackexchange_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stackexchange', 'ohio-extra' ); ?>" class="network -unlink stackexchange">
							<i class="fa-brands fa-stack-exchange"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $stackoverflow_link ) : ?>
						<a href="<?php echo $stackoverflow_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stackoverflow', 'ohio-extra' ); ?>" class="network -unlink stackoverflow">
							<i class="fa-brands fa-stack-overflow"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $strava_link ) : ?>
						<a href="<?php echo $strava_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'strava', 'ohio-extra' ); ?>" class="network -unlink strava">
							<i class="fa-brands fa-strava"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $stripe_link ) : ?>
						<a href="<?php echo $stripe_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stripe', 'ohio-extra' ); ?>" class="network -unlink stripe">
							<i class="fa-brands fa-stripe"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $t2_link ) : ?>
						<a href="<?php echo $t2_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 't2', 'ohio-extra' ); ?>" class="network -unlink t2">
							<i class="fa-brands fa-t2"></i>
						</a>
					<?php endif; ?>

					<?php if ( $teamspeak_link ) : ?>
						<a href="<?php echo $teamspeak_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'teamspeak', 'ohio-extra' ); ?>" class="network -unlink teamspeak">
							<i class="fa-brands fa-teamspeak"></i>
						</a>
					<?php endif; ?>

					<?php if ( $telegram_link ) : ?>
						<a href="<?php echo $telegram_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'telegram', 'ohio-extra' ); ?>" class="network -unlink telegram">
							<i class="fa-brands fa-telegram"></i>
						</a>
					<?php endif; ?>

					<?php if ( $threads_link ) : ?>
						<a href="<?php echo $threads_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'threads', 'ohio-extra' ); ?>" class="network -unlink threads">
							<i class="fa-brands fa-threads"></i>
						</a>
					<?php endif; ?>

					<?php if ( $tiktok_link ) : ?>
						<a href="<?php echo $tiktok_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tiktok', 'ohio-extra' ); ?>" class="network -unlink tiktok">
							<i class="fa-brands fa-tiktok"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $trello_link ) : ?>
						<a href="<?php echo $trello_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'trello', 'ohio-extra' ); ?>" class="network -unlink trello">
							<i class="fa-brands fa-trello"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $tripadvisor_link ) : ?>
						<a href="<?php echo $tripadvisor_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tripadvisor', 'ohio-extra' ); ?>" class="network -unlink tripadvisor">
							<i class="fa-brands fa-tripadvisor"></i>
						</a>
					<?php endif; ?>

					<?php if ( $tumblr_link ) : ?>
						<a href="<?php echo $tumblr_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tumblr', 'ohio-extra' ); ?>" class="network -unlink tumblr">
							<i class="fa-brands fa-tumblr"></i>
						</a>
					<?php endif; ?>

					<?php if ( $twitch_link ) : ?>
						<a href="<?php echo $twitch_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'twitch', 'ohio-extra' ); ?>" class="network -unlink twitch">
							<i class="fa-brands fa-twitch"></i>
						</a>
					<?php endif; ?>

					<?php if ( $twitter_link ) : ?>
						<a href="<?php echo $twitter_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'twitter', 'ohio-extra' ); ?>" class="network -unlink twitter">
							<i class="fa-brands fa-x-twitter"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $ubuntu_link ) : ?>
						<a href="<?php echo $ubuntu_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ubuntu', 'ohio-extra' ); ?>" class="network -unlink ubuntu">
							<i class="fa-brands fa-ubuntu"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $untappd_link ) : ?>
						<a href="<?php echo $untappd_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'untappd', 'ohio-extra' ); ?>" class="network -unlink untappd">
							<i class="fa-brands fa-untappd"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $usps_link ) : ?>
						<a href="<?php echo $usps_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'usps', 'ohio-extra' ); ?>" class="network -unlink usps">
							<i class="fa-brands fa-usps"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $viadeo_link ) : ?>
						<a href="<?php echo $viadeo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'viadeo', 'ohio-extra' ); ?>" class="network -unlink viadeo">
							<i class="fa-brands fa-viadeo"></i>
						</a>
					<?php endif; ?>

					<?php if ( $vimeo_link ) : ?>
						<a href="<?php echo $vimeo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'vimeo', 'ohio-extra' ); ?>" class="network -unlink vimeo">
							<i class="fa-brands fa-vimeo"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $waze_link ) : ?>
						<a href="<?php echo $waze_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'waze', 'ohio-extra' ); ?>" class="network -unlink waze">
							<i class="fa-brands fa-waze"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $weibo_link ) : ?>
						<a href="<?php echo $weibo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'weibo', 'ohio-extra' ); ?>" class="network -unlink weibo">
							<i class="fa-brands fa-weibo"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $wechat_link ) : ?>
						<a href="<?php echo $wechat_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wechat', 'ohio-extra' ); ?>" class="network -unlink wechat">
							<i class="fa-brands fa-wechat"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $weixin_link ) : ?>
						<a href="<?php echo $weixin_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'weixin', 'ohio-extra' ); ?>" class="network -unlink weixin">
							<i class="fa-brands fa-weixin"></i>
						</a>
					<?php endif; ?>

					<?php if ( $whatsapp_link ) : ?>
						<a href="<?php echo $whatsapp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'whatsapp', 'ohio-extra' ); ?>" class="network -unlink whatsapp">
							<i class="fa-brands fa-whatsapp"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $wikipedia_link ) : ?>
						<a href="<?php echo $wikipedia_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wikipedia', 'ohio-extra' ); ?>" class="network -unlink wikipedia">
							<i class="fa-brands fa-wikipedia-w"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $wordpress_link ) : ?>
						<a href="<?php echo $wordpress_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wordpress', 'ohio-extra' ); ?>" class="network -unlink wordpress">
							<i class="fa-brands fa-wordpress"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $wpexplorer_link ) : ?>
						<a href="<?php echo $wpexplorer_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wpexplorer', 'ohio-extra' ); ?>" class="network -unlink wpexplorer">
							<i class="fa-brands fa-wpexplorer"></i>
						</a>
					<?php endif; ?>

					<?php if ( $xing_link ) : ?>
						<a href="<?php echo $xing_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'xing', 'ohio-extra' ); ?>" class="network -unlink xing">
							<i class="fa-brands fa-xing"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $yahoo_link ) : ?>
						<a href="<?php echo $yahoo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'yahoo', 'ohio-extra' ); ?>" class="network -unlink yahoo">
							<i class="fa-brands fa-yahoo"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $yelp_link ) : ?>
						<a href="<?php echo $yelp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'yelp', 'ohio-extra' ); ?>" class="network -unlink yelp">
							<i class="fa-brands fa-yelp"></i>
						</a>
					<?php endif; ?>

					<?php if ( $youtube_link ) : ?>
						<a href="<?php echo $youtube_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'youtube', 'ohio-extra' ); ?>" class="network -unlink youtube">
							<i class="fa-brands fa-youtube"></i>
						</a>
					<?php endif; ?>

		        	<?php if ( $zhihu_link ) : ?>
						<a href="<?php echo $zhihu_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'zhihu', 'ohio-extra' ); ?>" class="network -unlink zhihu">
							<i class="fa-brands fa-zhihu"></i>
						</a>
					<?php endif; ?>

	            </div>
	        </div>

        <?php else : ?>

	        <div class="overlay-details dark-scheme">
			    <div class="author">
			        <?php if ( $name ) : ?>
		            	<h5 class="title"><?php echo $name; ?></h5>
					<?php endif; ?>

					<?php if ( $position ) : ?>
						<p class="author-details -unspace"><?php echo $position; ?></p>
					<?php endif; ?>
			    </div>
			    <div class="extra-details">

			        <?php if ( $description ) : ?>
						<p><?php echo $description; ?></p>
					<?php endif; ?>

			        <div class="social-networks -outlined -small">

			        	<?php if ( $fivehundred_link ) : ?>
							<a href="<?php echo $fivehundred_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( '500px', 'ohio-extra' ); ?>" class="network -unlink fivehundredpx">
								<i class="fa-brands fa-500px"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $amazon_link ) : ?>
							<a href="<?php echo $amazon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'amazon', 'ohio-extra' ); ?>" class="network -unlink amazon">
								<i class="fa-brands fa-amazon"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $angellist_link ) : ?>
							<a href="<?php echo $angellist_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'angellist', 'ohio-extra' ); ?>" class="network -unlink angellist">
								<i class="fa-brands fa-angellist"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $apple_link ) : ?>
							<a href="<?php echo $apple_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'apple', 'ohio-extra' ); ?>" class="network -unlink apple">
								<i class="fa-brands fa-apple"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $artstation_link ) : ?>
							<a href="<?php echo $artstation_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'artstation', 'ohio-extra' ); ?>" class="network -unlink artstation">
								<i class="fa-brands fa-artstation"></i>
							</a>
						<?php endif; ?>

		            	<?php if ( $behance_link ) : ?>
							<a href="<?php echo $behance_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'behance', 'ohio-extra' ); ?>" class="network -unlink behance">
								<i class="fa-brands fa-behance"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $bitbucket_link ) : ?>
							<a href="<?php echo $bitbucket_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'bitbucket', 'ohio-extra' ); ?>" class="network -unlink bitbucket">
								<i class="fa-brands fa-bitbucket"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $bluesky_link ) : ?>
							<a href="<?php echo $bluesky_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'bluesky', 'ohio-extra' ); ?>" class="network -unlink bluesky">
								<i class="fa-brands fa-bluesky"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $blogger_link ) : ?>
							<a href="<?php echo $blogger_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'blogger', 'ohio-extra' ); ?>" class="network -unlink blogger">
								<i class="fa-brands fa-blogger"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $codepen_link ) : ?>
							<a href="<?php echo $codepen_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'codepen', 'ohio-extra' ); ?>" class="network -unlink codepen">
								<i class="fa-brands fa-codepen"></i>
							</a>
						<?php endif; ?>

						<?php if ( $deviantart_link ) : ?>
							<a href="<?php echo $deviantart_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'deviantart', 'ohio-extra' ); ?>" class="network -unlink deviantart">
								<i class="fa-brands fa-deviantart"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $diaspora_link ) : ?>
							<a href="<?php echo $diaspora_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'diaspora', 'ohio-extra' ); ?>" class="network -unlink diaspora">
								<i class="fa-brands fa-diaspora"></i>
							</a>
						<?php endif; ?>

						<?php if ( $digg_link ) : ?>
							<a href="<?php echo $digg_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'digg', 'ohio-extra' ); ?>" class="network -unlink digg">
								<i class="fa-brands fa-digg"></i>
							</a>
						<?php endif; ?>

						<?php if ( $discord_link ) : ?>
							<a href="<?php echo $discord_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'discord', 'ohio-extra' ); ?>" class="network -unlink discord">
								<i class="fa-brands fa-discord"></i>
							</a>
						<?php endif; ?>

						<?php if ( $dribbble_link ) : ?>
							<a href="<?php echo $dribbble_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'dribbble', 'ohio-extra' ); ?>" class="network -unlink dribbble">
								<i class="fa-brands fa-dribbble"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $dropbox_link ) : ?>
							<a href="<?php echo $dropbox_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'dropbox', 'ohio-extra' ); ?>" class="network -unlink dropbox">
								<i class="fa-brands fa-dropbox"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $drupal_link ) : ?>
							<a href="<?php echo $drupal_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'drupal', 'ohio-extra' ); ?>" class="network -unlink drupal">
								<i class="fa-brands fa-drupal"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $etsy_link ) : ?>
							<a href="<?php echo $etsy_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'etsy', 'ohio-extra' ); ?>" class="network -unlink etsy">
								<i class="fa-brands fa-etsy"></i>
							</a>
						<?php endif; ?>

						<?php if ( $facebook_link ) : ?>
							<a href="<?php echo $facebook_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'facebook', 'ohio-extra' ); ?>" class="network -unlink facebook">
								<i class="fa-brands fa-facebook-f"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $fediverse_link ) : ?>
							<a href="<?php echo $fediverse_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'fediverse', 'ohio-extra' ); ?>" class="network -unlink fediverse">
								<i class="fa-brands fa-fediverse"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $figma_link ) : ?>
							<a href="<?php echo $figma_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'figma', 'ohio-extra' ); ?>" class="network -unlink figma">
								<i class="fa-brands fa-figma"></i>
							</a>
						<?php endif; ?>

						<?php if ( $flickr_link ) : ?>
							<a href="<?php echo $flickr_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'flickr', 'ohio-extra' ); ?>" class="network -unlink flickr">
								<i class="fa-brands fa-flickr"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $foursquare_link ) : ?>
							<a href="<?php echo $foursquare_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'foursquare', 'ohio-extra' ); ?>" class="network -unlink foursquare">
								<i class="fa-brands fa-foursquare"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $git_link ) : ?>
							<a href="<?php echo $git_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'git', 'ohio-extra' ); ?>" class="network -unlink git">
								<i class="fa-brands fa-git"></i>
							</a>
						<?php endif; ?>

						<?php if ( $github_link ) : ?>
							<a href="<?php echo $github_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'github', 'ohio-extra' ); ?>" class="network -unlink github">
								<i class="fa-brands fa-github"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $gitlab_link ) : ?>
							<a href="<?php echo $gitlab_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'gitlab', 'ohio-extra' ); ?>" class="network -unlink gitlab">
								<i class="fa-brands fa-gitlab"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $goodreads_link ) : ?>
							<a href="<?php echo $goodreads_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'goodreads', 'ohio-extra' ); ?>" class="network -unlink goodreads">
								<i class="fa-brands fa-goodreads"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $google_link ) : ?>
							<a href="<?php echo $google_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'google', 'ohio-extra' ); ?>" class="network -unlink google">
								<i class="fa-brands fa-google"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $googleplay_link ) : ?>
							<a href="<?php echo $googleplay_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'google-play', 'ohio-extra' ); ?>" class="network -unlink googleplay">
								<i class="fa-brands fa-google-play"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $hackernews_link ) : ?>
							<a href="<?php echo $hackernews_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'hacker-news', 'ohio-extra' ); ?>" class="network -unlink hackernews">
								<i class="fa-brands fa-hacker-news"></i>
							</a>
						<?php endif; ?>

						<?php if ( $houzz_link ) : ?>
							<a href="<?php echo $houzz_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'houzz', 'ohio-extra' ); ?>" class="network -unlink houzz">
								<i class="fa-brands fa-houzz"></i>
							</a>
						<?php endif; ?>

						<?php if ( $instagram_link ) : ?>
							<a href="<?php echo $instagram_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'instagram', 'ohio-extra' ); ?>" class="network -unlink instagram">
								<i class="fa-brands fa-instagram"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $jsfiddle_link ) : ?>
							<a href="<?php echo $jsfiddle_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'jsfiddle', 'ohio-extra' ); ?>" class="network -unlink jsfiddle">
								<i class="fa-brands fa-jsfiddle"></i>
							</a>
						<?php endif; ?>

						<?php if ( $kaggle_link ) : ?>
							<a href="<?php echo $kaggle_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'kaggle', 'ohio-extra' ); ?>" class="network -unlink kaggle">
								<i class="fa-brands fa-kaggle"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $kickstarter_link ) : ?>
							<a href="<?php echo $kickstarter_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'kickstarter', 'ohio-extra' ); ?>" class="network -unlink kickstarter">
								<i class="fa-brands fa-kickstarter"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $lastfm_link ) : ?>
							<a href="<?php echo $lastfm_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'lastfm', 'ohio-extra' ); ?>" class="network -unlink lastfm">
								<i class="fa-brands fa-lastfm"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $line_link ) : ?>
							<a href="<?php echo $line_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'line', 'ohio-extra' ); ?>" class="network -unlink line">
								<i class="fa-brands fa-line"></i>
							</a>
						<?php endif; ?>

						<?php if ( $linkedin_link ) : ?>
							<a href="<?php echo $linkedin_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'linkedin', 'ohio-extra' ); ?>" class="network -unlink linkedin">
								<i class="fa-brands fa-linkedin"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $mastodon_link ) : ?>
							<a href="<?php echo $mastodon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'mastodon', 'ohio-extra' ); ?>" class="network -unlink mastodon">
								<i class="fa-brands fa-mastodon"></i>
							</a>
						<?php endif; ?>

						<?php if ( $medium_link ) : ?>
							<a href="<?php echo $medium_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'medium', 'ohio-extra' ); ?>" class="network -unlink medium">
								<i class="fa-brands fa-medium-m"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $meetup_link ) : ?>
							<a href="<?php echo $meetup_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'meetup', 'ohio-extra' ); ?>" class="network -unlink meetup">
								<i class="fa-brands fa-meetup"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $messenger_link ) : ?>
							<a href="<?php echo $messenger_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'messenger', 'ohio-extra' ); ?>" class="network -unlink messenger">
								<i class="fa-brands fa-messenger"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $nextdoor_link ) : ?>
							<a href="<?php echo $nextdoor_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'nextdoor', 'ohio-extra' ); ?>" class="network -unlink nextdoor">
								<i class="fa-brands fa-nextdoor"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $npm_link ) : ?>
							<a href="<?php echo $npm_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'npm', 'ohio-extra' ); ?>" class="network -unlink npm">
								<i class="fa-brands fa-npm"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $orcid_link ) : ?>
							<a href="<?php echo $orcid_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'orcid', 'ohio-extra' ); ?>" class="network -unlink orcid">
								<i class="fa-brands fa-orcid"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $patreon_link ) : ?>
							<a href="<?php echo $patreon_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'patreon', 'ohio-extra' ); ?>" class="network -unlink patreon">
								<i class="fa-brands fa-patreon"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $paypal_link ) : ?>
							<a href="<?php echo $paypal_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'paypal', 'ohio-extra' ); ?>" class="network -unlink paypal">
								<i class="fa-brands fa-paypal"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $peertube_link ) : ?>
							<a href="<?php echo $peertube_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'peertube', 'ohio-extra' ); ?>" class="network -unlink peertube">
								<i class="fa-brands fa-peertube"></i>
							</a>
						<?php endif; ?>

						<?php if ( $pinterest_link ) : ?>
							<a href="<?php echo $pinterest_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'pinterest', 'ohio-extra' ); ?>" class="network -unlink pinterest">
								<i class="fa-brands fa-pinterest"></i>
							</a>
						<?php endif; ?>

						<?php if ( $producthunt_link ) : ?>
							<a href="<?php echo $producthunt_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'producthunt', 'ohio-extra' ); ?>" class="network -unlink producthunt">
								<i class="fa-brands fa-product-hunt"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $python_link ) : ?>
							<a href="<?php echo $python_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'python', 'ohio-extra' ); ?>" class="network -unlink python">
								<i class="fa-brands fa-python"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $qq_link ) : ?>
							<a href="<?php echo $qq_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'qq', 'ohio-extra' ); ?>" class="network -unlink qq">
								<i class="fa-brands fa-qq"></i>
							</a>
						<?php endif; ?>

						<?php if ( $quora_link ) : ?>
							<a href="<?php echo $quora_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'quora', 'ohio-extra' ); ?>" class="network -unlink quora">
								<i class="fa-brands fa-quora"></i>
							</a>
						<?php endif; ?>

						<?php if ( $reddit_link ) : ?>
							<a href="<?php echo $reddit_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'reddit', 'ohio-extra' ); ?>" class="network -unlink reddit">
								<i class="fa-brands fa-reddit"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $researchgate_link ) : ?>
							<a href="<?php echo $researchgate_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'researchgate', 'ohio-extra' ); ?>" class="network -unlink researchgate">
								<i class="fa-brands fa-researchgate"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $reverbnation_link ) : ?>
							<a href="<?php echo $reverbnation_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'reverbnation', 'ohio-extra' ); ?>" class="network -unlink reverbnation">
								<i class="fa-brands fa-reverbnation"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $rss_link ) : ?>
							<a href="<?php echo $rss_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'rss', 'ohio-extra' ); ?>" class="network -unlink rss">
								<i class="fa-brands fa-rss"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $salesforce_link ) : ?>
							<a href="<?php echo $salesforce_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'salesforce', 'ohio-extra' ); ?>" class="network -unlink salesforce">
								<i class="fa-brands fa-salesforce"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $sass_link ) : ?>
							<a href="<?php echo $sass_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sass', 'ohio-extra' ); ?>" class="network -unlink sass">
								<i class="fa-brands fa-sass"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $sharp_link ) : ?>
							<a href="<?php echo $sharp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sharp', 'ohio-extra' ); ?>" class="network -unlink sharp">
								<i class="fa-brands fa-sharp"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $shopify_link ) : ?>
							<a href="<?php echo $shopify_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'shopify', 'ohio-extra' ); ?>" class="network -unlink shopify">
								<i class="fa-brands fa-shopify"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $skype_link ) : ?>
							<a href="<?php echo $skype_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'skype', 'ohio-extra' ); ?>" class="network -unlink skype">
								<i class="fa-brands fa-skype"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $slack_link ) : ?>
							<a href="<?php echo $slack_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'slack', 'ohio-extra' ); ?>" class="network -unlink slack">
								<i class="fa-brands fa-slack"></i>
							</a>
						<?php endif; ?>

						<?php if ( $snapchat_link ) : ?>
							<a href="<?php echo $snapchat_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'snapchat', 'ohio-extra' ); ?>" class="network -unlink snapchat">
								<i class="fa-brands fa-snapchat"></i>
							</a>
						<?php endif; ?>

						<?php if ( $soundcloud_link ) : ?>
							<a href="<?php echo $soundcloud_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'soundcloud', 'ohio-extra' ); ?>" class="network -unlink soundcloud">
								<i class="fa-brands fa-soundcloud"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $sourcetree_link ) : ?>
							<a href="<?php echo $sourcetree_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'sourcetree', 'ohio-extra' ); ?>" class="network -unlink sourcetree">
								<i class="fa-brands fa-sourcetree"></i>
							</a>
						<?php endif; ?>

						<?php if ( $spotify_link ) : ?>
							<a href="<?php echo $spotify_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'spotify', 'ohio-extra' ); ?>" class="network -unlink spotify">
								<i class="fa-brands fa-spotify"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $stackexchange_link ) : ?>
							<a href="<?php echo $stackexchange_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stackexchange', 'ohio-extra' ); ?>" class="network -unlink stackexchange">
								<i class="fa-brands fa-stack-exchange"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $stackoverflow_link ) : ?>
							<a href="<?php echo $stackoverflow_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stackoverflow', 'ohio-extra' ); ?>" class="network -unlink stackoverflow">
								<i class="fa-brands fa-stack-overflow"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $strava_link ) : ?>
							<a href="<?php echo $strava_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'strava', 'ohio-extra' ); ?>" class="network -unlink strava">
								<i class="fa-brands fa-strava"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $stripe_link ) : ?>
							<a href="<?php echo $stripe_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'stripe', 'ohio-extra' ); ?>" class="network -unlink stripe">
								<i class="fa-brands fa-stripe"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $t2_link ) : ?>
							<a href="<?php echo $t2_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 't2', 'ohio-extra' ); ?>" class="network -unlink t2">
								<i class="fa-brands fa-t2"></i>
							</a>
						<?php endif; ?>

						<?php if ( $teamspeak_link ) : ?>
							<a href="<?php echo $teamspeak_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'teamspeak', 'ohio-extra' ); ?>" class="network -unlink teamspeak">
								<i class="fa-brands fa-teamspeak"></i>
							</a>
						<?php endif; ?>

						<?php if ( $telegram_link ) : ?>
							<a href="<?php echo $telegram_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'telegram', 'ohio-extra' ); ?>" class="network -unlink telegram">
								<i class="fa-brands fa-telegram"></i>
							</a>
						<?php endif; ?>

						<?php if ( $threads_link ) : ?>
							<a href="<?php echo $threads_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'threads', 'ohio-extra' ); ?>" class="network -unlink threads">
								<i class="fa-brands fa-threads"></i>
							</a>
						<?php endif; ?>

						<?php if ( $tiktok_link ) : ?>
							<a href="<?php echo $tiktok_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tiktok', 'ohio-extra' ); ?>" class="network -unlink tiktok">
								<i class="fa-brands fa-tiktok"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $trello_link ) : ?>
							<a href="<?php echo $trello_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'trello', 'ohio-extra' ); ?>" class="network -unlink trello">
								<i class="fa-brands fa-trello"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $tripadvisor_link ) : ?>
							<a href="<?php echo $tripadvisor_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tripadvisor', 'ohio-extra' ); ?>" class="network -unlink tripadvisor">
								<i class="fa-brands fa-tripadvisor"></i>
							</a>
						<?php endif; ?>

						<?php if ( $tumblr_link ) : ?>
							<a href="<?php echo $tumblr_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'tumblr', 'ohio-extra' ); ?>" class="network -unlink tumblr">
								<i class="fa-brands fa-tumblr"></i>
							</a>
						<?php endif; ?>

						<?php if ( $twitch_link ) : ?>
							<a href="<?php echo $twitch_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'twitch', 'ohio-extra' ); ?>" class="network -unlink twitch">
								<i class="fa-brands fa-twitch"></i>
							</a>
						<?php endif; ?>

						<?php if ( $twitter_link ) : ?>
							<a href="<?php echo $twitter_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'twitter', 'ohio-extra' ); ?>" class="network -unlink twitter">
								<i class="fa-brands fa-x-twitter"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $ubuntu_link ) : ?>
							<a href="<?php echo $ubuntu_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ubuntu', 'ohio-extra' ); ?>" class="network -unlink ubuntu">
								<i class="fa-brands fa-ubuntu"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $untappd_link ) : ?>
							<a href="<?php echo $untappd_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'untappd', 'ohio-extra' ); ?>" class="network -unlink untappd">
								<i class="fa-brands fa-untappd"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $usps_link ) : ?>
							<a href="<?php echo $usps_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'usps', 'ohio-extra' ); ?>" class="network -unlink usps">
								<i class="fa-brands fa-usps"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $viadeo_link ) : ?>
							<a href="<?php echo $viadeo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'viadeo', 'ohio-extra' ); ?>" class="network -unlink viadeo">
								<i class="fa-brands fa-viadeo"></i>
							</a>
						<?php endif; ?>

						<?php if ( $vimeo_link ) : ?>
							<a href="<?php echo $vimeo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'vimeo', 'ohio-extra' ); ?>" class="network -unlink vimeo">
								<i class="fa-brands fa-vimeo"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $waze_link ) : ?>
							<a href="<?php echo $waze_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'waze', 'ohio-extra' ); ?>" class="network -unlink waze">
								<i class="fa-brands fa-waze"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $weibo_link ) : ?>
							<a href="<?php echo $weibo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'weibo', 'ohio-extra' ); ?>" class="network -unlink weibo">
								<i class="fa-brands fa-weibo"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $wechat_link ) : ?>
							<a href="<?php echo $wechat_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wechat', 'ohio-extra' ); ?>" class="network -unlink wechat">
								<i class="fa-brands fa-wechat"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $weixin_link ) : ?>
							<a href="<?php echo $weixin_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'weixin', 'ohio-extra' ); ?>" class="network -unlink weixin">
								<i class="fa-brands fa-weixin"></i>
							</a>
						<?php endif; ?>

						<?php if ( $whatsapp_link ) : ?>
							<a href="<?php echo $whatsapp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'whatsapp', 'ohio-extra' ); ?>" class="network -unlink whatsapp">
								<i class="fa-brands fa-whatsapp"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $wikipedia_link ) : ?>
							<a href="<?php echo $wikipedia_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wikipedia', 'ohio-extra' ); ?>" class="network -unlink wikipedia">
								<i class="fa-brands fa-wikipedia-w"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $wordpress_link ) : ?>
							<a href="<?php echo $wordpress_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wordpress', 'ohio-extra' ); ?>" class="network -unlink wordpress">
								<i class="fa-brands fa-wordpress"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $wpexplorer_link ) : ?>
							<a href="<?php echo $wpexplorer_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'wpexplorer', 'ohio-extra' ); ?>" class="network -unlink wpexplorer">
								<i class="fa-brands fa-wpexplorer"></i>
							</a>
						<?php endif; ?>

						<?php if ( $xing_link ) : ?>
							<a href="<?php echo $xing_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'xing', 'ohio-extra' ); ?>" class="network -unlink xing">
								<i class="fa-brands fa-xing"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $yahoo_link ) : ?>
							<a href="<?php echo $yahoo_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'yahoo', 'ohio-extra' ); ?>" class="network -unlink yahoo">
								<i class="fa-brands fa-yahoo"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $yelp_link ) : ?>
							<a href="<?php echo $yelp_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'yelp', 'ohio-extra' ); ?>" class="network -unlink yelp">
								<i class="fa-brands fa-yelp"></i>
							</a>
						<?php endif; ?>

						<?php if ( $youtube_link ) : ?>
							<a href="<?php echo $youtube_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'youtube', 'ohio-extra' ); ?>" class="network -unlink youtube">
								<i class="fa-brands fa-youtube"></i>
							</a>
						<?php endif; ?>

			        	<?php if ( $zhihu_link ) : ?>
							<a href="<?php echo $zhihu_link; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'zhihu', 'ohio-extra' ); ?>" class="network -unlink zhihu">
								<i class="fa-brands fa-zhihu"></i>
							</a>
						<?php endif; ?>

		            </div>
			    </div>
	        </div>

        <?php endif; ?>

    </div>

    <?php if ( $block_type_layout != 'inner' ) : ?>

	    <div class="card-details">
	        <div class="heading author">

	            <?php if ( $name ) : ?>
	            	<h5 class="title"><?php echo $name; ?></h5>
				<?php endif; ?>

				<?php if ( $position ) : ?>
					<p class="author-details -unspace"><?php echo $position; ?></p>
				<?php endif; ?>

	        </div>
	    </div>

    <?php endif; ?>

</div>