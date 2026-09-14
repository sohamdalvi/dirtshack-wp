<?php

/**
* WPBakery Page Builder Ohio Social Networks shortcode view
*/

?>
<div class="ohio-widget social-networks<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo esc_attr( $animation_attrs ); ?>>

	<?php if ( $artstation_link_custom ) : ?>
		<a href="<?php echo $artstation_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ArtStation', 'ohio-extra' ); ?>" class="network -unlink title artstation">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-artstation"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'ArtStation', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $behance_link_custom ) : ?>
		<a href="<?php echo $behance_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Behance', 'ohio-extra' ); ?>" class="network -unlink title behance">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-behance"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Behance', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $deviantart_link_custom ) : ?>
		<a href="<?php echo $deviantart_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'DeviantArt', 'ohio-extra' ); ?>" class="network -unlink title deviantart">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-deviantart"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'DeviantArt', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $digg_link_custom ) : ?>
		<a href="<?php echo $digg_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Digg', 'ohio-extra' ); ?>" class="network -unlink title digg">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-digg"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Digg', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $discord_link_custom ) : ?>
		<a href="<?php echo $discord_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Discord', 'ohio-extra' ); ?>" class="network -unlink title discord">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-discord"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Discord', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $dribbble_link_custom ) : ?>
		<a href="<?php echo $dribbble_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Dribbble', 'ohio-extra' ); ?>" class="network -unlink title dribbble">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-dribbble"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Dribbble', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $facebook_link_custom ) : ?>
		<a href="<?php echo $facebook_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Facebook', 'ohio-extra' ); ?>" class="network -unlink title facebook">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-facebook-f"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Facebook', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $flickr_link_custom ) : ?>
		<a href="<?php echo $flickr_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Flickr', 'ohio-extra' ); ?>" class="network -unlink title flickr">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-flickr"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Flickr', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $github_link_custom ) : ?>
		<a href="<?php echo $github_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'GitHub', 'ohio-extra' ); ?>" class="network -unlink title github">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-github"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'GitHub', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $houzz_link_custom ) : ?>
		<a href="<?php echo $houzz_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Houzz', 'ohio-extra' ); ?>" class="network -unlink title houzz">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-houzz"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Houzz', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $instagram_link_custom ) : ?>
		<a href="<?php echo $instagram_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Instagram', 'ohio-extra' ); ?>" class="network -unlink title instagram">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-instagram"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Instagram', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $kaggle_link_custom ) : ?>
		<a href="<?php echo $kaggle_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Kaggle', 'ohio-extra' ); ?>" class="network -unlink title kaggle">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-kaggle"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Kaggle', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $linkedin_link_custom ) : ?>
		<a href="<?php echo $linkedin_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'LinkedIn', 'ohio-extra' ); ?>" class="network -unlink title linkedin">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-linkedin"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'LinkedIn', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $medium_link_custom ) : ?>
		<a href="<?php echo $medium_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Medium', 'ohio-extra' ); ?>" class="network -unlink title linkedin">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-medium-m"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Medium', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>


	<?php if ( $pinterest_link_custom ) : ?>
		<a href="<?php echo $pinterest_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Pinterest', 'ohio-extra' ); ?>" class="network -unlink title pinterest">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-pinterest"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Pinterest', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $producthunt_link_custom ) : ?>
		<a href="<?php echo $producthunt_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Product Hunt', 'ohio-extra' ); ?>" class="network -unlink title producthunt">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-product-hunt"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Product Hunt', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $quora_link_custom ) : ?>
		<a href="<?php echo $quora_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Quora', 'ohio-extra' ); ?>" class="network -unlink title quora">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-quora"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Quora', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $reddit_link_custom ) : ?>
		<a href="<?php echo $reddit_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Reddit', 'ohio-extra' ); ?>" class="network -unlink title reddit">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-reddit"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Reddit', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $snapchat_link_custom ) : ?>
		<a href="<?php echo $snapchat_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Snapchat', 'ohio-extra' ); ?>" class="network -unlink title snapchat">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-snapchat"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Snapchat', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $soundcloud_link_custom ) : ?>
		<a href="<?php echo $soundcloud_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'SoundCloud', 'ohio-extra' ); ?>" class="network -unlink title soundcloud">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-soundcloud"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'SoundCloud', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $spotify_link_custom ) : ?>
		<a href="<?php echo $spotify_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Spotify', 'ohio-extra' ); ?>" class="network -unlink title spotify">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-spotify"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Spotify', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $teamspeak_link_custom ) : ?>
		<a href="<?php echo $teamspeak_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Spotify', 'ohio-extra' ); ?>" class="network -unlink title teamspeak">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-teamspeak"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Spotify', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $telegram_link_custom ) : ?>
		<a href="<?php echo $telegram_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Telegram', 'ohio-extra' ); ?>" class="network -unlink title telegram">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-telegram"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Telegram', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $threads_link_custom ) : ?>
		<a href="<?php echo $threads_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Threads', 'ohio-extra' ); ?>" class="network -unlink title threads">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-threads"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Threads', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $tiktok_link_custom ) : ?>
		<a href="<?php echo $tiktok_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'TikTok', 'ohio-extra' ); ?>" class="network -unlink title tiktok">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-tiktok"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'TikTok', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $tumblr_link_custom ) : ?>
		<a href="<?php echo $tumblr_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Tumblr', 'ohio-extra' ); ?>" class="network -unlink title tumblr">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-tumblr"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Tumblr', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $twitch_link_custom ) : ?>
		<a href="<?php echo $twitch_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Twitch', 'ohio-extra' ); ?>" class="network -unlink title twitch">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-twitch"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Twitch', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $twitter_link_custom ) : ?>
		<a href="<?php echo $twitter_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'X', 'ohio-extra' ); ?>" class="network -unlink title twitter">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-x-twitter"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'X', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $vimeo_link_custom ) : ?>
		<a href="<?php echo $vimeo_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Vimeo', 'ohio-extra' ); ?>" class="network -unlink title vimeo">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-vimeo"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Vimeo', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>


	<?php if ( $whatsapp_link_custom ) : ?>
		<a href="<?php echo $whatsapp_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'WhatsApp', 'ohio-extra' ); ?>" class="network -unlink title whatsapp">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-whatsapp"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'WhatsApp', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $xing_link_custom ) : ?>
		<a href="<?php echo $xing_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Xing', 'ohio-extra' ); ?>" class="network -unlink title xing">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-xing"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Xing', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $youtube_link_custom ) : ?>
		<a href="<?php echo $youtube_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'YouTube', 'ohio-extra' ); ?>" class="network -unlink title youtube">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-youtube"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'YouTube', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $amazon_link_custom ) : ?>
		<a href="<?php echo $amazon_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Amazon', 'ohio-extra' ); ?>" class="network -unlink title amazon">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-amazon"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Amazon', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $angellist_link_custom ) : ?>
		<a href="<?php echo $angellist_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'AngelList', 'ohio-extra' ); ?>" class="network -unlink title angellist">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-angellist"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'AngelList', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $apple_link_custom ) : ?>
		<a href="<?php echo $apple_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Apple', 'ohio-extra' ); ?>" class="network -unlink title apple">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-apple"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Apple', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $bitbucket_link_custom ) : ?>
		<a href="<?php echo $bitbucket_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Bitbucket', 'ohio-extra' ); ?>" class="network -unlink title bitbucket">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-bitbucket"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Bitbucket', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $bluesky_link_custom ) : ?>
		<a href="<?php echo $bluesky_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Bluesky', 'ohio-extra' ); ?>" class="network -unlink title bluesky">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-bluesky"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Bluesky', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $blogger_link_custom ) : ?>
		<a href="<?php echo $blogger_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Blogger', 'ohio-extra' ); ?>" class="network -unlink title blogger">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-blogger"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Blogger', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $codepen_link_custom ) : ?>
		<a href="<?php echo $codepen_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'CodePen', 'ohio-extra' ); ?>" class="network -unlink title codepen">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-codepen"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'CodePen', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $diaspora_link_custom ) : ?>
		<a href="<?php echo $diaspora_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Diaspora', 'ohio-extra' ); ?>" class="network -unlink title diaspora">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-diaspora"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Diaspora', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $dropbox_link_custom ) : ?>
		<a href="<?php echo $dropbox_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Dropbox', 'ohio-extra' ); ?>" class="network -unlink title dropbox">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-dropbox"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Dropbox', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $drupal_link_custom ) : ?>
		<a href="<?php echo $drupal_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Drupal', 'ohio-extra' ); ?>" class="network -unlink title drupal">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-drupal"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Drupal', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $etsy_link_custom ) : ?>
		<a href="<?php echo $etsy_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Etsy', 'ohio-extra' ); ?>" class="network -unlink title etsy">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-etsy"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Etsy', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $fediverse_link_custom ) : ?>
		<a href="<?php echo $fediverse_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Fediverse', 'ohio-extra' ); ?>" class="network -unlink title fediverse">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-fediverse"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Fediverse', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $figma_link_custom ) : ?>
		<a href="<?php echo $figma_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Figma', 'ohio-extra' ); ?>" class="network -unlink title figma">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-figma"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Figma', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $foursquare_link_custom ) : ?>
		<a href="<?php echo $foursquare_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Foursquare', 'ohio-extra' ); ?>" class="network -unlink title foursquare">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-foursquare"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Foursquare', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $git_link_custom ) : ?>
		<a href="<?php echo $git_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Git', 'ohio-extra' ); ?>" class="network -unlink title git">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-git"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Git', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $gitlab_link_custom ) : ?>
		<a href="<?php echo $gitlab_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'GitLab', 'ohio-extra' ); ?>" class="network -unlink title gitlab">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-gitlab"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'GitLab', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $goodreads_link_custom ) : ?>
		<a href="<?php echo $goodreads_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Goodreads', 'ohio-extra' ); ?>" class="network -unlink title goodreads">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-goodreads"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Goodreads', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $google_link_custom ) : ?>
		<a href="<?php echo $google_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Google', 'ohio-extra' ); ?>" class="network -unlink title google">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-google"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Google', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $googleplay_link_custom ) : ?>
		<a href="<?php echo $googleplay_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Google Play', 'ohio-extra' ); ?>" class="network -unlink title google-play">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-google-play"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Google Play', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $hackernews_link_custom ) : ?>
		<a href="<?php echo $hackernews_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Hacker News', 'ohio-extra' ); ?>" class="network -unlink title hacker-news">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-hacker-news"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Hacker News', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $jsfiddle_link_custom ) : ?>
		<a href="<?php echo $jsfiddle_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'JSFiddle', 'ohio-extra' ); ?>" class="network -unlink title jsfiddle">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-jsfiddle"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'JSFiddle', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $kickstarter_link_custom ) : ?>
		<a href="<?php echo $kickstarter_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Kickstarter', 'ohio-extra' ); ?>" class="network -unlink title kickstarter">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-kickstarter"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Kickstarter', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $lastfm_link_custom ) : ?>
		<a href="<?php echo $lastfm_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Last.fm', 'ohio-extra' ); ?>" class="network -unlink title lastfm">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-lastfm"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Last.fm', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $line_link_custom ) : ?>
		<a href="<?php echo $line_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Line', 'ohio-extra' ); ?>" class="network -unlink title line">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-line"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Line', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $mastodon_link_custom ) : ?>
		<a href="<?php echo $mastodon_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Mastodon', 'ohio-extra' ); ?>" class="network -unlink title mastodon">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-mastodon"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Mastodon', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $meetup_link_custom ) : ?>
		<a href="<?php echo $meetup_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Meetup', 'ohio-extra' ); ?>" class="network -unlink title meetup">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-meetup"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Meetup', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $messenger_link_custom ) : ?>
		<a href="<?php echo $messenger_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Messenger', 'ohio-extra' ); ?>" class="network -unlink title messenger">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-messenger"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Messenger', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $nextdoor_link_custom ) : ?>
		<a href="<?php echo $nextdoor_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Nextdoor', 'ohio-extra' ); ?>" class="network -unlink title nextdoor">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-nextdoor"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Nextdoor', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $npm_link_custom ) : ?>
		<a href="<?php echo $npm_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'npm', 'ohio-extra' ); ?>" class="network -unlink title npm">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-npm"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'npm', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $orcid_link_custom ) : ?>
		<a href="<?php echo $orcid_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ORCID', 'ohio-extra' ); ?>" class="network -unlink title orcid">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-orcid"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'ORCID', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $patreon_link_custom ) : ?>
		<a href="<?php echo $patreon_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Patreon', 'ohio-extra' ); ?>" class="network -unlink title patreon">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-patreon"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Patreon', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $paypal_link_custom ) : ?>
		<a href="<?php echo $paypal_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'PayPal', 'ohio-extra' ); ?>" class="network -unlink title paypal">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-paypal"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'PayPal', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $peertube_link_custom ) : ?>
		<a href="<?php echo $peertube_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'PeerTube', 'ohio-extra' ); ?>" class="network -unlink title peertube">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-peertube"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'PeerTube', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $python_link_custom ) : ?>
		<a href="<?php echo $python_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Python', 'ohio-extra' ); ?>" class="network -unlink title python">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-python"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Python', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $qq_link_custom ) : ?>
		<a href="<?php echo $qq_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'QQ', 'ohio-extra' ); ?>" class="network -unlink title qq">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-qq"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'QQ', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $researchgate_link_custom ) : ?>
		<a href="<?php echo $researchgate_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ResearchGate', 'ohio-extra' ); ?>" class="network -unlink title researchgate">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-researchgate"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'ResearchGate', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $reverbnation_link_custom ) : ?>
		<a href="<?php echo $reverbnation_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'ReverbNation', 'ohio-extra' ); ?>" class="network -unlink title reverbnation">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-reverbnation"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'ReverbNation', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $rss_link_custom ) : ?>
		<a href="<?php echo $rss_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'RSS', 'ohio-extra' ); ?>" class="network -unlink title rss">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-solid fa-rss"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'RSS', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $salesforce_link_custom ) : ?>
		<a href="<?php echo $salesforce_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Salesforce', 'ohio-extra' ); ?>" class="network -unlink title salesforce">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-salesforce"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Salesforce', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $sass_link_custom ) : ?>
		<a href="<?php echo $sass_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Sass', 'ohio-extra' ); ?>" class="network -unlink title sass">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-sass"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Sass', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $shopify_link_custom ) : ?>
		<a href="<?php echo $shopify_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Shopify', 'ohio-extra' ); ?>" class="network -unlink title shopify">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-shopify"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Shopify', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $skype_link_custom ) : ?>
		<a href="<?php echo $skype_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Skype', 'ohio-extra' ); ?>" class="network -unlink title skype">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-skype"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Skype', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $slack_link_custom ) : ?>
		<a href="<?php echo $slack_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Slack', 'ohio-extra' ); ?>" class="network -unlink title slack">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-slack"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Slack', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $sourcetree_link_custom ) : ?>
		<a href="<?php echo $sourcetree_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Sourcetree', 'ohio-extra' ); ?>" class="network -unlink title sourcetree">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-sourcetree"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Sourcetree', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $stackexchange_link_custom ) : ?>
		<a href="<?php echo $stackexchange_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Stack Exchange', 'ohio-extra' ); ?>" class="network -unlink title stack-exchange">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-stack-exchange"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Stack Exchange', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $stackoverflow_link_custom ) : ?>
		<a href="<?php echo $stackoverflow_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Stack Overflow', 'ohio-extra' ); ?>" class="network -unlink title stack-overflow">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-stack-overflow"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Stack Overflow', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $strava_link_custom ) : ?>
		<a href="<?php echo $strava_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Strava', 'ohio-extra' ); ?>" class="network -unlink title strava">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-strava"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Strava', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $stripe_link_custom ) : ?>
		<a href="<?php echo $stripe_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Stripe', 'ohio-extra' ); ?>" class="network -unlink title stripe">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-stripe"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Stripe', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $trello_link_custom ) : ?>
		<a href="<?php echo $trello_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Trello', 'ohio-extra' ); ?>" class="network -unlink title trello">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-trello"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Trello', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $tripadvisor_link_custom ) : ?>
		<a href="<?php echo $tripadvisor_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'TripAdvisor', 'ohio-extra' ); ?>" class="network -unlink title tripadvisor">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-tripadvisor"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'TripAdvisor', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $ubuntu_link_custom ) : ?>
		<a href="<?php echo $ubuntu_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Ubuntu', 'ohio-extra' ); ?>" class="network -unlink title ubuntu">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-ubuntu"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Ubuntu', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $untappd_link_custom ) : ?>
		<a href="<?php echo $untappd_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Untappd', 'ohio-extra' ); ?>" class="network -unlink title untappd">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-untappd"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Untappd', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $usps_link_custom ) : ?>
		<a href="<?php echo $usps_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'USPS', 'ohio-extra' ); ?>" class="network -unlink title usps">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-usps"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'USPS', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $viadeo_link_custom ) : ?>
		<a href="<?php echo $viadeo_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Viadeo', 'ohio-extra' ); ?>" class="network -unlink title viadeo">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-viadeo"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Viadeo', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $waze_link_custom ) : ?>
		<a href="<?php echo $waze_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Waze', 'ohio-extra' ); ?>" class="network -unlink title waze">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-waze"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Waze', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $weibo_link_custom ) : ?>
		<a href="<?php echo $weibo_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Weibo', 'ohio-extra' ); ?>" class="network -unlink title weibo">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-weibo"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Weibo', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $wechat_link_custom ) : ?>
		<a href="<?php echo $wechat_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'WeChat', 'ohio-extra' ); ?>" class="network -unlink title wechat">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-weixin"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'WeChat', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $weixin_link_custom ) : ?>
		<a href="<?php echo $weixin_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'WeChat', 'ohio-extra' ); ?>" class="network -unlink title weixin">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-weixin"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'WeChat', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $wikipedia_link_custom ) : ?>
		<a href="<?php echo $wikipedia_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Wikipedia', 'ohio-extra' ); ?>" class="network -unlink title wikipedia">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-wikipedia-w"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Wikipedia', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $wordpress_link_custom ) : ?>
		<a href="<?php echo $wordpress_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'WordPress', 'ohio-extra' ); ?>" class="network -unlink title wordpress">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-wordpress"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'WordPress', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $yahoo_link_custom ) : ?>
		<a href="<?php echo $yahoo_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Yahoo', 'ohio-extra' ); ?>" class="network -unlink title yahoo">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-yahoo"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Yahoo', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $yelp_link_custom ) : ?>
		<a href="<?php echo $yelp_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Yelp', 'ohio-extra' ); ?>" class="network -unlink title yelp">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-yelp"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Yelp', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $zhihu_link_custom ) : ?>
		<a href="<?php echo $zhihu_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( 'Zhihu', 'ohio-extra' ); ?>" class="network -unlink title zhihu">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-zhihu"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( 'Zhihu', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php if ( $fivehundred_link_custom ) : ?>
		<a href="<?php echo $fivehundred_link_custom; ?>" target="_blank" rel="nofollow" aria-label="<?php esc_html_e( '500px', 'ohio-extra' ); ?>" class="network -unlink title fivehundredpx">
			<?php if ( $show_icon ) : ?>
				<i class="icon fa-brands fa-500px"></i>
			<?php endif; ?>
			<?php if ( $show_text ) : ?>
				<span><?php esc_html_e( '500px', 'ohio-extra' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

</div>