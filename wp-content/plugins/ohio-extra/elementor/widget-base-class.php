<?php
class Ohio_Elementor_Widget_Base extends \Elementor\Widget_Base {

    protected $inlineStyles = [];
    protected $wrapperClasses = [];


    public function get_name()
    {
        return 'Ohio Widget base';
    }

    public function get_categories()
    {
        return [ 'ohio-theme' ];
    }

    /**
     * Parse Elementor link control data to <a> attributes string
     * 
     * @param array $params Elementor Link control params
     * 
     * @return string Attributes
     */
    protected function getLinkAttributesString($params) {
        $attrString = 'href="' . $params['url'] . '" ';

        if ( $params['is_external'] ) {
            $attrString .= 'target="_blank" ';
        }

        if ( $params['nofollow'] ) {
            $attrString .= 'rel="nofollow" ';
        }

        if ( !empty( $params['custom_attributes'] ) ) {
            $attributes = explode( ',', $params['custom_attributes'] );
            foreach ( $attributes as $attr ) {
                $attr = explode( '|', $attr );
                if ( count( $attr ) == 2 ) {
                    $attrString .= $attr[0] . '="' . $attr[1] . '" ';
                } elseif ( count( $attr ) == 1 ) {
                    $attrString .= $attr[0] . '="" ';
                }
            }
        }

        return $attrString;
    }

    protected function addInlineStyle($slug, $settings_name, $style)
    {
        $settings = $this->get_settings_for_display();

        if ( !isset( $this->inlineStyles[$slug] ) ) {
            $this->inlineStyles[$slug] = [];
        }

        if ( $settings_name && !empty( $settings[$settings_name] ) ) {
            $style = str_replace( '{{VALUE}}', $settings[$settings_name], $style );
        }

        $this->inlineStyles[$slug][] = rtrim( trim( $style ), ';' );
    }

    /**
     * Returns collected inline styles as string
     * 
     * @param string $slug Unique slug
     * 
     * @return string CSS styles
     */
    protected function getInlineStyle($slug)
    {
        if ( !empty( $this->inlineStyles[$slug] ) ) {
            return implode( ';', $this->inlineStyles[$slug] );
        }

        return '';
    }

    protected function getInlineStyleAttr($slug)
    {
        $style = $this->getInlineStyle($slug);

        if ( !empty( $style ) ) {
            return 'style="' . esc_attr( $style ) . '"';
        }

        return '';
    }

    /**
     * Adds CSS class string to collection
     * 
     * @param string|array $class CSS class
     */
    protected function addWrapperClass($class)
    {
        if ( is_string( $class ) ) {
            $this->wrapperClasses[] = $class;
        }

        if ( is_array( $class ) ) {
            $this->wrapperClasses = array_merge( $this->wrapperClasses, $class );
        }
    }

    /**
     * Returns wrapper classes collection as string for "class" HTML attribute
     * 
     * @return string CSS classes
     */
    protected function getWrapperClasses()
    {
        return implode( ' ', array_unique( $this->wrapperClasses ) );
    }

    /**
     * Adds button style controlls to widget Style tab
     * 
     * @param bool $withCondition Additional visibility condition for "use_link" control
     */
    protected function addButtonStyleSection( $withCondition = 'use_link' )
    {
        $this->start_controls_section(
            'button_styles_section',
            [
                'label' => __( 'Button', 'ohio-extra' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ($withCondition) ? [ $withCondition => 'yes' ] : [],
            ]
        );

        $this->add_control(
            'button_style_type',
            [
                'label' => __( 'Type', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Filled', 'ohio-extra' ),
                    'outline' => __( 'Outlined', 'ohio-extra' ),
                    'flat' => __( 'Flat', 'ohio-extra' ),
                    'link' => __( 'Text', 'ohio-extra' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => __( 'Size', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Default', 'ohio-extra' ),
                    'small' => __( 'Small', 'ohio-extra' ),
                    'large' => __( 'Large', 'ohio-extra' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'button_with_is_full',
            [
                'label' => __( 'Set Block?', 'ohio-extra' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'ohio-extra' ),
                'label_off' => __( 'No', 'ohio-extra' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );


        $this->start_controls_tabs( 'tab_colors_style', ['separator' => 'before'] );

        $this->start_controls_tab(
            'tab_button_colors_normal',
            [
                'label' => __( 'Normal', 'ohio-extra' ),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __( 'Text Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button:not(:hover)' => 'color: {{VALUE}}'
                ]
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __( 'Fill Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button.-default:not(:hover)' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_style_type' => [ 'default' ],
                ],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label' => __( 'Border Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_style_type' => [ 'default', 'outline' ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_colors_hover',
            [
                'label' => __( 'Hover', 'ohio-extra' ),
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __( 'Text Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button:hover' => 'color: {{VALUE}}'
                ]
            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => __( 'Fill Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button.-default:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_style_type' => [ 'default' ],
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __( 'Border Color', 'ohio-extra' ),
                'type' =>  \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .button:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_style_type' => [ 'default', 'outline' ],
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
			'button_typography_separator',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_text_typography',
                'label' => __( 'Button Typography', 'ohio-extra' ),
                'selector' => '{{WRAPPER}} .button'
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Collect all button classes from settings
     * 
     * Recommend use with $this->addButtonStyleSection()
     * 
     * @return string Concatenated CSS classes string
     */
    protected function getButtonClasses(): string
    {
        $settings = $this->get_settings_for_display();

        $button_classes = [];
        switch ( $settings['button_style_type'] ) {
            case 'default':
                $button_classes[] = '-default';
                break;
            case 'outline':
                $button_classes[] = '-outlined';
                break;
            case 'flat':
                $button_classes[] = '-flat';
                break;
            case 'link':
                $button_classes[] = '-text';
                break;
        }

        switch ( $settings['button_size'] ) {
            case 'small':
                $button_classes[] = '-small';
                break;
            case 'large':
                $button_classes[] = '-large';
                break;
        }

        if ( $settings['button_with_is_full'] ) {
            $button_classes[] = '-block';
        }

        if ( !empty( $settings['button_color'] ) ) {
            $button_classes[] = 'btn-elementor-colored';
        }

        return implode( ' ', $button_classes );
    }

    /**
     * Options list for social networks selects
     *
     * @return array Social networks list in slug=>title format
     */
    protected function getSocialNetworksOptionsList(): array
    {
        return [
            'fivehundredpx' => __( '500px', 'ohio-extra' ),
            '500px'       => __( '500px', 'ohio-extra' ), // Backward compatibility
            'amazon'      => __( 'Amazon', 'ohio-extra' ),
            'angellist'   => __( 'AngelList', 'ohio-extra' ),
            'apple'       => __( 'Apple', 'ohio-extra' ),
            'artstation'  => __( 'ArtStation', 'ohio-extra' ),
            'behance'     => __( 'Behance', 'ohio-extra' ),
            'bitbucket'   => __( 'Bitbucket', 'ohio-extra' ),
            'bluesky'     => __( 'Bluesky', 'ohio-extra' ),
            'blogger'     => __( 'Blogger', 'ohio-extra' ),
            'codepen'     => __( 'CodePen', 'ohio-extra' ),
            'deviantart'  => __( 'DeviantArt', 'ohio-extra' ),
            'diaspora'    => __( 'Diaspora', 'ohio-extra' ),
            'digg'        => __( 'Digg', 'ohio-extra' ),
            'discord'     => __( 'Discord', 'ohio-extra' ),
            'dribbble'    => __( 'Dribbble', 'ohio-extra' ),
            'dropbox'     => __( 'Dropbox', 'ohio-extra' ),
            'drupal'      => __( 'Drupal', 'ohio-extra' ),
            'etsy'        => __( 'Etsy', 'ohio-extra' ),
            'facebook'    => __( 'Facebook', 'ohio-extra' ),
            'fediverse'   => __( 'Fediverse (Mastodon)', 'ohio-extra' ),
            'figma'       => __( 'Figma', 'ohio-extra' ),
            'flickr'      => __( 'Flickr', 'ohio-extra' ),
            'foursquare'  => __( 'Foursquare', 'ohio-extra' ),
            'git'         => __( 'Git', 'ohio-extra' ),
            'github'      => __( 'GitHub', 'ohio-extra' ),
            'gitlab'      => __( 'GitLab', 'ohio-extra' ),
            'goodreads'   => __( 'Goodreads', 'ohio-extra' ),
            'google'      => __( 'Google', 'ohio-extra' ),
            'google-play' => __( 'Google Play', 'ohio-extra' ),
            'hacker-news' => __( 'Hacker News', 'ohio-extra' ),
            'houzz'       => __( 'Houzz', 'ohio-extra' ),
            'instagram'   => __( 'Instagram', 'ohio-extra' ),
            'jsfiddle'    => __( 'JSFiddle', 'ohio-extra' ),
            'kaggle'      => __( 'Kaggle', 'ohio-extra' ),
            'kickstarter' => __( 'Kickstarter', 'ohio-extra' ),
            'lastfm'      => __( 'Last.fm', 'ohio-extra' ),
            'line'        => __( 'LINE', 'ohio-extra' ),
            'linkedin'    => __( 'LinkedIn', 'ohio-extra' ),
            'mastodon'    => __( 'Mastodon', 'ohio-extra' ),
            'medium'      => __( 'Medium', 'ohio-extra' ),
            'meetup'      => __( 'Meetup', 'ohio-extra' ),
            'messenger'   => __( 'Messenger', 'ohio-extra' ),
            'nextdoor'    => __( 'Nextdoor', 'ohio-extra' ),
            'npm'         => __( 'NPM', 'ohio-extra' ),
            'orcid'       => __( 'ORCID', 'ohio-extra' ),
            'patreon'     => __( 'Patreon', 'ohio-extra' ),
            'paypal'      => __( 'PayPal', 'ohio-extra' ),
            'peertube'    => __( 'PeerTube', 'ohio-extra' ),
            'pinterest'   => __( 'Pinterest', 'ohio-extra' ),
            'producthunt' => __( 'Product Hunt', 'ohio-extra' ),
            'python'      => __( 'Python', 'ohio-extra' ),
            'qq'          => __( 'QQ', 'ohio-extra' ),
            'quora'       => __( 'Quora', 'ohio-extra' ),
            'reddit'      => __( 'Reddit', 'ohio-extra' ),
            'researchgate' => __( 'ResearchGate', 'ohio-extra' ),
            'reverbnation' => __( 'ReverbNation', 'ohio-extra' ),
            'rss'         => __( 'RSS', 'ohio-extra' ),
            'salesforce'  => __( 'Salesforce', 'ohio-extra' ),
            'sass'        => __( 'Sass', 'ohio-extra' ),
            'sharp'       => __( 'Sharp', 'ohio-extra' ),
            'shopify'     => __( 'Shopify', 'ohio-extra' ),
            'skype'       => __( 'Skype', 'ohio-extra' ),
            'slack'       => __( 'Slack', 'ohio-extra' ),
            'snapchat'    => __( 'Snapchat', 'ohio-extra' ),
            'soundcloud'  => __( 'SoundCloud', 'ohio-extra' ),
            'sourcetree'  => __( 'Sourcetree', 'ohio-extra' ),
            'spotify'     => __( 'Spotify', 'ohio-extra' ),
            'stackexchange' => __( 'Stack Exchange', 'ohio-extra' ),
            'stackoverflow' => __( 'Stack Overflow', 'ohio-extra' ),
            'strava'      => __( 'Strava', 'ohio-extra' ),
            'stripe'      => __( 'Stripe', 'ohio-extra' ),
            't2'          => __( 'T2', 'ohio-extra' ),
            'teamspeak'   => __( 'TeamSpeak', 'ohio-extra' ),
            'telegram'    => __( 'Telegram', 'ohio-extra' ),
            'threads'     => __( 'Threads', 'ohio-extra' ),
            'tiktok'      => __( 'TikTok', 'ohio-extra' ),
            'trello'      => __( 'Trello', 'ohio-extra' ),
            'tripadvisor' => __( 'TripAdvisor', 'ohio-extra' ),
            'tumblr'      => __( 'Tumblr', 'ohio-extra' ),
            'twitch'      => __( 'Twitch', 'ohio-extra' ),
            'twitter'     => __( 'X', 'ohio-extra' ),
            'ubuntu'      => __( 'Ubuntu', 'ohio-extra' ),
            'untappd'     => __( 'Untappd', 'ohio-extra' ),
            'usps'        => __( 'USPS', 'ohio-extra' ),
            'viadeo'      => __( 'Viadeo', 'ohio-extra' ),
            'vimeo'       => __( 'Vimeo', 'ohio-extra' ),
            'waze'        => __( 'Waze', 'ohio-extra' ),
            'weibo'       => __( 'Weibo', 'ohio-extra' ),
            'wechat'      => __( 'WeChat', 'ohio-extra' ),
            'weixin'      => __( 'Weixin', 'ohio-extra' ),
            'whatsapp'    => __( 'WhatsApp', 'ohio-extra' ),
            'wikipedia'   => __( 'Wikipedia', 'ohio-extra' ),
            'wordpress'   => __( 'WordPress', 'ohio-extra' ),
            'wpexplorer'  => __( 'WPExplorer', 'ohio-extra' ),
            'xing'        => __( 'Xing', 'ohio-extra' ),
            'yahoo'       => __( 'Yahoo', 'ohio-extra' ),
            'yelp'        => __( 'Yelp', 'ohio-extra' ),
            'youtube'     => __( 'YouTube', 'ohio-extra' ),
            'zhihu'       => __( 'Zhihu', 'ohio-extra' ),

            'other'       => __( 'Other', 'ohio-extra' ),
        ];
    }

    /**
     * Renders social network i-tag FontAwesome icon
     *
     * @param string $slug Social network name slug
     */
    protected function renderSocialNetworkIcon( $slug )
    {
        $icons = [
            'fivehundredpx' => 'fa-brands fa-500px',
            '500px' => 'fa-brands fa-500px', // Backward compatibility
            'amazon' => 'fa-brands fa-amazon',
            'angellist' => 'fa-brands fa-angellist',
            'apple' => 'fa-brands fa-apple',
            'artstation' => 'fa-brands fa-artstation',
            'behance' => 'fa-brands fa-behance',
            'bitbucket' => 'fa-brands fa-bitbucket',
            'bluesky' => 'fa-brands fa-bluesky',
            'blogger' => 'fa-brands fa-blogger',
            'codepen' => 'fa-brands fa-codepen',
            'deviantart' => 'fa-brands fa-deviantart',
            'diaspora' => 'fa-brands fa-diaspora',
            'digg' => 'fa-brands fa-digg',
            'discord' => 'fa-brands fa-discord',
            'dribbble' => 'fa-brands fa-dribbble',
            'dropbox' => 'fa-brands fa-dropbox',
            'drupal' => 'fa-brands fa-drupal',
            'etsy' => 'fa-brands fa-etsy',
            'facebook' => 'fa-brands fa-facebook-f',
            'fediverse' => 'fa-brands fa-mastodon',
            'figma' => 'fa-brands fa-figma',
            'flickr' => 'fa-brands fa-flickr',
            'foursquare' => 'fa-brands fa-foursquare',
            'git' => 'fa-brands fa-git',
            'github' => 'fa-brands fa-github',
            'gitlab' => 'fa-brands fa-gitlab',
            'goodreads' => 'fa-brands fa-goodreads',
            'google' => 'fa-brands fa-google',
            'google-play' => 'fa-brands fa-google-play',
            'hacker-news' => 'fa-brands fa-hacker-news',
            'houzz' => 'fa-brands fa-houzz',
            'instagram' => 'fa-brands fa-instagram',
            'jsfiddle' => 'fa-brands fa-jsfiddle',
            'kaggle' => 'fa-brands fa-kaggle',
            'kickstarter' => 'fa-brands fa-kickstarter',
            'lastfm' => 'fa-brands fa-lastfm',
            'line' => 'fa-brands fa-line',
            'linkedin' => 'fa-brands fa-linkedin-in',
            'mastodon' => 'fa-brands fa-mastodon',
            'medium' => 'fa-brands fa-medium',
            'meetup' => 'fa-brands fa-meetup',
            'messenger' => 'fa-brands fa-facebook-messenger',
            'nextdoor' => 'fa-brands fa-nextdoor',
            'npm' => 'fa-brands fa-npm',
            'orcid' => 'fa-brands fa-orcid',
            'patreon' => 'fa-brands fa-patreon',
            'paypal' => 'fa-brands fa-paypal',
            'peertube' => 'fa-brands fa-peertube',
            'pinterest' => 'fa-brands fa-pinterest-p',
            'producthunt' => 'fa-brands fa-product-hunt',
            'python' => 'fa-brands fa-python',
            'qq' => 'fa-brands fa-qq',
            'quora' => 'fa-brands fa-quora',
            'reddit' => 'fa-brands fa-reddit',
            'researchgate' => 'fa-brands fa-researchgate',
            'reverbnation' => 'fa-brands fa-reverbnation',
            'rss' => 'fa-brands fa-rss',
            'salesforce' => 'fa-brands fa-salesforce',
            'sass' => 'fa-brands fa-sass',
            'sharp' => 'fa-brands fa-sharp',
            'shopify' => 'fa-brands fa-shopify',
            'skype' => 'fa-brands fa-skype',
            'slack' => 'fa-brands fa-slack',
            'snapchat' => 'fa-brands fa-snapchat',
            'soundcloud' => 'fa-brands fa-soundcloud',
            'sourcetree' => 'fa-brands fa-sourcetree',
            'spotify' => 'fa-brands fa-spotify',
            'stackexchange' => 'fa-brands fa-stack-exchange',
            'stackoverflow' => 'fa-brands fa-stack-overflow',
            'strava' => 'fa-brands fa-strava',
            'stripe' => 'fa-brands fa-stripe',
            't2' => 'fa-brands fa-t2',
            'teamspeak' => 'fa-brands fa-teamspeak',
            'telegram' => 'fa-brands fa-telegram',
            'threads' => 'fa-brands fa-threads',
            'tiktok' => 'fa-brands fa-tiktok',
            'trello' => 'fa-brands fa-trello',
            'tripadvisor' => 'fa-brands fa-tripadvisor',
            'tumblr' => 'fa-brands fa-tumblr',
            'twitch' => 'fa-brands fa-twitch',
            'twitter' => 'fa-brands fa-x-twitter',
            'ubuntu' => 'fa-brands fa-ubuntu',
            'untappd' => 'fa-brands fa-untappd',
            'usps' => 'fa-brands fa-usps',
            'viadeo' => 'fa-brands fa-viadeo',
            'vimeo' => 'fa-brands fa-vimeo-v',
            'waze' => 'fa-brands fa-waze',
            'weibo' => 'fa-brands fa-weibo',
            'wechat' => 'fa-brands fa-wechat',
            'weixin' => 'fa-brands fa-weixin',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'wikipedia' => 'fa-brands fa-wikipedia-w',
            'wordpress' => 'fa-brands fa-wordpress',
            'wpexplorer' => 'fa-brands fa-wpexplorer',
            'xing' => 'fa-brands fa-xing',
            'yahoo' => 'fa-brands fa-yahoo',
            'yelp' => 'fa-brands fa-yelp',
            'youtube' => 'fa-brands fa-youtube',
            'zhihu' => 'fa-brands fa-zhihu',
            'other' => 'fas fa-external-link-alt'
        ];

        if ( !isset( $icons[$slug] ) ) {
            $slug = 'other';
        }

        echo '<i class="icon ' . esc_attr( $icons[$slug] ) . '"></i>';
    }

    /**
     * Show html layout for icon with native render_icon function or custom img tag
     *
     * @param string $class CSS class
     * @return void
     */
    protected function showIconInView( $class = '' )
    {
        $settings = $this->get_settings_for_display();

        if ( $settings['icon_type'] == 'icon' ) {
            \Elementor\Icons_Manager::render_icon( $settings['icon_icon'], [
                'class' => $class,
                'style' => $this->getInlineStyle( 'icon' )
            ] );
        } elseif ( $settings['icon_type'] == 'image' && !empty( $settings['icon_image']['url'] ) ) {
            echo '<img class="' . $class . '" ';
            echo 'src="' . esc_url( $settings['icon_image']['url'] ) . '" ';
            echo 'srcset="' . wp_get_attachment_image_srcset( $settings['icon_image']['id'], 'large' ) . '" ';
            echo 'sizes="' . wp_get_attachment_image_sizes( $settings['icon_image']['id'], 'large' ) . '" ';
            echo 'alt="' . __( 'Icon', 'ohio-extra' ) . '">';
        } elseif ( $settings['icon_type'] == 'html' ) {
            echo $settings['icon_html'];
        }
    }

    /**
     * Get localized Elementor template id (WPML compatibility)
     *
     * @param string $template_id Elementor template ID
     * @return void
     */
    protected function getLocalizedTemplate( $template_id ) {
        $have_wpml = function_exists( 'icl_get_languages' );
        $localized_id = $template_id;
        
        if( $have_wpml ) {
            $localized_id = apply_filters( 'wpml_object_id', $template_id, 'post', true );
        }

        return $localized_id;
    }
}
