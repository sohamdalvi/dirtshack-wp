<?php
/**
 * Ohio WordPress Theme
 *
 * Social bar template
 *
 * @author Colabrio
 * @link   https://ohio.clbthemes.com
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get theme options
$show_social = OhioOptions::get( 'page_social_links_visibility', true );
$show_social_tablet = OhioOptions::get( 'header_menu_social_links_visibility_tablet', false );
$social_icons = OhioOptions::get_global( 'social_network_type', false );
$external_link = OhioOptions::get_global( 'social_network_target_blank', true );
$link_target = ( $external_link ) ? '_blank' : '_self';

if ( !$show_social ) {
    $show_social = OhioSettings::is_coming_soon_page();
}

$social_classes = '';

if ( $social_icons == 'icons' ) {
    $social_classes .= ' icons';
}
if ( !$show_social_tablet ) {
    $social_classes .= ' vc_hidden-md';
}

$social_networks_mapping = [
    'fivehundredpx' => array( 'icon' => 'fa-500px', 'text' => esc_html__( '500px', 'ohio' ) ),
    '500px' => array( 'icon' => 'fa-500px', 'text' => esc_html__( '500px', 'ohio' ) ), // Backward compatibility
    'amazon' => array( 'icon' => 'fa-amazon', 'text' => esc_html__( 'Amz.', 'ohio' ) ),
    'angellist' => array( 'icon' => 'fa-angellist', 'text' => esc_html__( 'Ang.', 'ohio' ) ),
    'apple' => array( 'icon' => 'fa-apple', 'text' => esc_html__( 'Apl.', 'ohio' ) ),
    'artstation' => array( 'icon' => 'fa-artstation', 'text' => esc_html__( 'Art.', 'ohio' ) ),
    'behance' => array( 'icon' => 'fa-behance', 'text' => esc_html__( 'Be.', 'ohio' ) ),
    'bitbucket' => array( 'icon' => 'fa-bitbucket', 'text' => esc_html__( 'Bit.', 'ohio' ) ),
    'bluesky' => array( 'icon' => 'fa-bluesky', 'text' => esc_html__( 'Sky.', 'ohio' ) ),
    'blogger' => array( 'icon' => 'fa-blogger', 'text' => esc_html__( 'Blg.', 'ohio' ) ),
    'codepen' => array( 'icon' => 'fa-codepen', 'text' => esc_html__( 'Pen.', 'ohio' ) ),
    'deviantart' => array( 'icon' => 'fa-deviantart', 'text' => esc_html__( 'Dev.', 'ohio' ) ),
    'diaspora' => array( 'icon' => 'fa-diaspora', 'text' => esc_html__( 'Dis.', 'ohio' ) ),
    'digg' => array( 'icon' => 'fa-digg', 'text' => esc_html__( 'Dg.', 'ohio' ) ),
    'discord' => array( 'icon' => 'fa-discord', 'text' => esc_html__( 'Dc.', 'ohio' ) ),
    'dribbble' => array( 'icon' => 'fa-dribbble', 'text' => esc_html__( 'Dr.', 'ohio' ) ),
    'dropbox' => array( 'icon' => 'fa-dropbox', 'text' => esc_html__( 'Drop.', 'ohio' ) ),
    'drupal' => array( 'icon' => 'fa-drupal', 'text' => esc_html__( 'Dru.', 'ohio' ) ),
    'etsy' => array( 'icon' => 'fa-etsy', 'text' => esc_html__( 'Etsy', 'ohio' ) ),
    'facebook' => array( 'icon' => 'fa-facebook-f', 'text' => esc_html__( 'Fb.', 'ohio' ) ),
    'fediverse' => array( 'icon' => 'fa-mastodon', 'text' => esc_html__( 'Fed.', 'ohio' ) ),
    'figma' => array( 'icon' => 'fa-figma', 'text' => esc_html__( 'Fig.', 'ohio' ) ),
    'flickr' => array( 'icon' => 'fa-flickr', 'text' => esc_html__( 'Fl.', 'ohio' ) ),
    'foursquare' => array( 'icon' => 'fa-foursquare', 'text' => esc_html__( '4sq.', 'ohio' ) ),
    'git' => array( 'icon' => 'fa-git', 'text' => esc_html__( 'Git', 'ohio' ) ),
    'github' => array( 'icon' => 'fa-github', 'text' => esc_html__( 'Gh.', 'ohio') ),
    'gitlab' => array( 'icon' => 'fa-gitlab', 'text' => esc_html__( 'Lab.', 'ohio' ) ),
    'goodreads' => array( 'icon' => 'fa-goodreads', 'text' => esc_html__( 'Gd.', 'ohio' ) ),
    'google' => array( 'icon' => 'fa-google', 'text' => esc_html__( 'Ggl.', 'ohio' ) ),
    'google-play' => array( 'icon' => 'fa-google-play', 'text' => esc_html__( 'Play', 'ohio' ) ),
    'hacker-news' => array( 'icon' => 'fa-hacker-news', 'text' => esc_html__( 'HN.', 'ohio' ) ),
    'houzz' => array( 'icon' => 'fa-houzz', 'text' => esc_html__( 'Hzz.', 'ohio' ) ),
    'instagram' => array( 'icon' => 'fa-instagram', 'text' => esc_html__( 'Ig.', 'ohio' ) ),
    'jsfiddle' => array( 'icon' => 'fa-jsfiddle', 'text' => esc_html__( 'JS.', 'ohio' ) ),
    'kaggle' => array( 'icon' => 'fa-kaggle', 'text' => esc_html__( 'Ka.', 'ohio' ) ),
    'kickstarter' => array( 'icon' => 'fa-kickstarter', 'text' => esc_html__( 'Kick', 'ohio' ) ),
    'lastfm' => array( 'icon' => 'fa-lastfm', 'text' => esc_html__( 'Fm.', 'ohio' ) ),
    'line' => array( 'icon' => 'fa-line', 'text' => esc_html__( 'Line', 'ohio' ) ),
    'linkedin' => array( 'icon' => 'fa-linkedin-in', 'text' => esc_html__( 'Lk.', 'ohio' ) ),
    'mastodon' => array( 'icon' => 'fa-mastodon', 'text' => esc_html__( 'Mas.', 'ohio' ) ),
    'medium' => array( 'icon' => 'fa-medium', 'text' => esc_html__( 'Md.', 'ohio' ) ),
    'meetup' => array( 'icon' => 'fa-meetup', 'text' => esc_html__( 'Meet', 'ohio' ) ),
    'messenger' => array( 'icon' => 'fa-facebook-messenger', 'text' => esc_html__( 'Msg.', 'ohio' ) ),
    'nextdoor' => array( 'icon' => 'fa-nextdoor', 'text' => esc_html__( 'Nd.', 'ohio' ) ),
    'npm' => array( 'icon' => 'fa-npm', 'text' => esc_html__( 'Npm', 'ohio' ) ),
    'orcid' => array( 'icon' => 'fa-orcid', 'text' => esc_html__( 'Orcid', 'ohio' ) ),
    'patreon' => array( 'icon' => 'fa-patreon', 'text' => esc_html__( 'Pat.', 'ohio' ) ),
    'paypal' => array( 'icon' => 'fa-paypal', 'text' => esc_html__( 'Pay.', 'ohio' ) ),
    'peertube' => array( 'icon' => 'fa-peertube', 'text' => esc_html__( 'Peer', 'ohio' ) ),
    'pinterest' => array( 'icon' => 'fa-pinterest-p', 'text' => esc_html__( 'Pt.', 'ohio' ) ),
    'producthunt' => array( 'icon' => 'fa-product-hunt', 'text' => esc_html__( 'Ph.', 'ohio' ) ),
    'python' => array( 'icon' => 'fa-python', 'text' => esc_html__( 'Py.', 'ohio' ) ),
    'qq' => array( 'icon' => 'fa-qq', 'text' => esc_html__( 'QQ', 'ohio' ) ),
    'quora' => array( 'icon' => 'fa-quora', 'text' => esc_html__( 'Qu.', 'ohio' ) ),
    'reddit' => array( 'icon' => 'fa-reddit', 'text' => esc_html__( 'Re.', 'ohio' ) ),
    'researchgate' => array( 'icon' => 'fa-researchgate', 'text' => esc_html__( 'RG.', 'ohio' ) ),
    'reverbnation' => array( 'icon' => 'fa-reverbnation', 'text' => esc_html__( 'Rvn.', 'ohio' ) ),
    'rss' => array( 'icon' => 'fa-rss', 'text' => esc_html__( 'RSS', 'ohio' ) ),
    'salesforce' => array( 'icon' => 'fa-salesforce', 'text' => esc_html__( 'SF.', 'ohio' ) ),
    'sass' => array( 'icon' => 'fa-sass', 'text' => esc_html__( 'Sass', 'ohio' ) ),
    'sharp' => array( 'icon' => 'fa-sharp', 'text' => esc_html__( 'Shrp', 'ohio' ) ),
    'shopify' => array( 'icon' => 'fa-shopify', 'text' => esc_html__( 'Shop', 'ohio' ) ),
    'skype' => array( 'icon' => 'fa-skype', 'text' => esc_html__( 'Sky.', 'ohio' ) ),
    'slack' => array( 'icon' => 'fa-slack', 'text' => esc_html__( 'Slk.', 'ohio' ) ),
    'snapchat' => array( 'icon' => 'fa-snapchat', 'text' => esc_html__( 'Sn.', 'ohio' ) ),
    'soundcloud' => array( 'icon' => 'fa-soundcloud', 'text' => esc_html__( 'Sc.', 'ohio' ) ),
    'sourcetree' => array( 'icon' => 'fa-sourcetree', 'text' => esc_html__( 'ST.', 'ohio' ) ),
    'spotify' => array( 'icon' => 'fa-spotify', 'text' => esc_html__( 'Sp.', 'ohio' ) ),
    'stackexchange' => array( 'icon' => 'fa-stack-exchange', 'text' => esc_html__( 'Exch', 'ohio' ) ),
    'stackoverflow' => array( 'icon' => 'fa-stack-overflow', 'text' => esc_html__( 'SO.', 'ohio' ) ),
    'strava' => array( 'icon' => 'fa-strava', 'text' => esc_html__( 'Str.', 'ohio' ) ),
    'stripe' => array( 'icon' => 'fa-stripe', 'text' => esc_html__( 'Strp', 'ohio' ) ),
    't2' => array( 'icon' => 'fa-t2', 'text' => esc_html__( 'T2', 'ohio' ) ),
    'teamspeak' => array( 'icon' => 'fa-teamspeak', 'text' => esc_html__( 'TS.', 'ohio' ) ),
    'telegram' => array( 'icon' => 'fa-telegram', 'text' => esc_html__( 'Tl.', 'ohio' ) ),
    'threads' => array( 'icon' => 'fa-threads', 'text' => esc_html__( 'Thr.', 'ohio' ) ),
    'tiktok' => array( 'icon' => 'fa-tiktok', 'text' => esc_html__( 'Tk.', 'ohio' ) ),
    'trello' => array( 'icon' => 'fa-trello', 'text' => esc_html__( 'Trl.', 'ohio' ) ),
    'tripadvisor' => array( 'icon' => 'fa-tripadvisor', 'text' => esc_html__( 'TA.', 'ohio' ) ),
    'tumblr' => array( 'icon' => 'fa-tumblr', 'text' => esc_html__( 'Tb.', 'ohio' ) ),
    'twitch' => array( 'icon' => 'fa-twitch', 'text' => esc_html__( 'Tw.', 'ohio' ) ),
    'twitter' => array( 'icon' => 'fa-x-twitter', 'text' => esc_html__( 'X.', 'ohio' ) ),
    'ubuntu' => array( 'icon' => 'fa-ubuntu', 'text' => esc_html__( 'Ubu.', 'ohio' ) ),
    'untappd' => array( 'icon' => 'fa-untappd', 'text' => esc_html__( 'Unt.', 'ohio' ) ),
    'usps' => array( 'icon' => 'fa-usps', 'text' => esc_html__( 'USPS', 'ohio' ) ),
    'viadeo' => array( 'icon' => 'fa-viadeo', 'text' => esc_html__( 'Viad', 'ohio' ) ),
    'vimeo' => array( 'icon' => 'fa-vimeo-v', 'text' => esc_html__( 'Vm.', 'ohio' ) ),
    'waze' => array( 'icon' => 'fa-waze', 'text' => esc_html__( 'Waze', 'ohio' ) ),
    'weibo' => array( 'icon' => 'fa-weibo', 'text' => esc_html__( 'Wb.', 'ohio' ) ),
    'wechat' => array( 'icon' => 'fa-wechat', 'text' => esc_html__( 'WX.', 'ohio' ) ),
    'weixin' => array( 'icon' => 'fa-weixin', 'text' => esc_html__( 'WXN', 'ohio' ) ),
    'whatsapp' => array( 'icon' => 'fa-whatsapp', 'text' => esc_html__( 'Wh.', 'ohio' ) ),
    'wikipedia' => array( 'icon' => 'fa-wikipedia-w', 'text' => esc_html__( 'Wiki', 'ohio' ) ),
    'wordpress' => array( 'icon' => 'fa-wordpress', 'text' => esc_html__( 'WP.', 'ohio' ) ),
    'wpexplorer' => array( 'icon' => 'fa-wpexplorer', 'text' => esc_html__( 'WPX', 'ohio' ) ),
    'xing' => array( 'icon' => 'fa-xing', 'text' => esc_html__( 'Xi.', 'ohio' ) ),
    'yahoo' => array( 'icon' => 'fa-yahoo', 'text' => esc_html__( 'Yh.', 'ohio' ) ),
    'yelp' => array( 'icon' => 'fa-yelp', 'text' => esc_html__( 'Yelp', 'ohio' ) ),
    'youtube' => array( 'icon' => 'fa-youtube', 'text' => esc_html__( 'Yt.', 'ohio' ) ),
    'zhihu' => array( 'icon' => 'fa-zhihu', 'text' => esc_html__( 'Zhi.', 'ohio' ) ),
];
?>

<?php if ( $show_social ) : ?>
    <div class="social-bar">
        <ul class="social-bar-holder titles-typo -small-t -unlist<?php echo esc_attr( $social_classes ); ?>">

            <?php if ( have_rows( 'global_header_menu_social_links', 'option' ) ) : ?>
                <li class="caption"><?php esc_html_e( 'Follow Us', 'ohio' ); ?></li>
            <?php endif; ?>

            <?php while ( have_rows( 'global_header_menu_social_links', 'option' ) ): the_row(); ?>
                <?php
                    $social_network = get_sub_field( 'social_network' );
                    $social_network_url = get_sub_field( 'url' );
                    $social_network_fields = $social_networks_mapping[$social_network];
                    extract( $social_network_fields );
                ?>
                <li>
                    <a class="-unlink<?php if ( $social_icons == 'icons' ) { echo( esc_attr(' -undash') ); } ?> <?php echo $social_network; ?>" href="<?php echo esc_url( $social_network_url ); ?>" target="<?php echo $link_target; ?>" rel="nofollow"><?php echo $social_icons == 'icons' ? '<i class="fa-brands ' . $icon . '"></i>' : $text; ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
<?php endif; ?>
