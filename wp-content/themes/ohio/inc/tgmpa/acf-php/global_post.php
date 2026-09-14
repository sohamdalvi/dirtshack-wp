<?php if ( function_exists( 'acf_add_local_field_group' ) ) :

    acf_add_local_field_group( [
        "key" => "group_592d60af343cfp",
        "title" => __( 'Single Post Settings', 'ohio' ),
        "private" => true,
        "fields" => [
            [
                "key" => "field_592d60ah8a4137",
                "label" => __( 'General', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "placement" => "top",
                "endpoint" => 0
            ],
            [
                "key" => "field_592h4k6gf7kld9",
                "label" => __( 'Layout', 'ohio' ),
                "name" => "global_page_post_layout_type",
                "type" => "image_option",
                "instructions" => __( 'Choose a single post layout for the entire site.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "image_option_value" => [
                    [
                        "name" => "type_1",
                        "description" => __( 'Standard', 'ohio' ),
                        "src" => "acf__image_45.svg"
                    ],
                    [
                        "name" => "type_2",
                        "description" => __( 'Split Screen', 'ohio' ),
                        "src" => "acf__image_46.svg"
                    ]
                ],
                "default_value" => "type_1"
            ],
            [
                "key" => "field_592h4k6gf7kld93746",
                "label" => __( 'Wrap Container Width', 'ohio' ),
                "name" => "global_page_post_layout_width",
                "type" => "text",
                "instructions" => __( 'Set the width of the post wrap container.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_591ac509d31f3",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "default_value" => "",
                "placeholder" => "1144px by default",
                "prepend" => __( 'Use CSS units', 'ohio' ),
                "append" => "",
                "maxlength" => 200
            ],
            [
                "key" => "field_593j324f324l",
                "label" => '<h4>' . __( 'Display', 'ohio' ) . '</h4>',
                "name" => "",
                "type" => "message"
            ],
            [
                "key" => "field_59374a3443d8361523f",
                "label" => __( 'Tags', 'ohio' ),
                "name" => "global_post_tags_visibility",
                "type" => "true_false",
                "instructions" => __( 'Show a tags section after the blog post content area.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],
            [
                "key" => "field_59374a3fdf523f",
                "label" => __( 'Comments', 'ohio' ),
                "name" => "global_post_comments_visibility",
                "type" => "true_false",
                "instructions" => __( 'Show a comments section at the bottom of a single post.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],
            [
                "key" => "field_592d714deeb14",
                "label" => __( 'Next/Prev Navigation', 'ohio' ),
                "name" => "global_post_previous_n_next_visibility",
                "type" => "true_false",
                "instructions" => __( 'Show a sticky navigation bar with the next/previous blog post links.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],




            [
                "key" => "field_59374a3443d83234",
                "label" => '<h4>' . __( 'Styles', 'ohio' ) . '</h4>',
                "name" => "",
                "conditional_logic" => [
                    [   
                        [
                            "field" => "field_592fd8902e284",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "type" => "message"
            ],
            [
                "key" => "field_59374a3443d83231",
                "label" => __( 'Post Typography', 'ohio' ),
                "name" => "global_page_post_typo",
                "type" => "ohio_typo",
                "instructions" => __( 'Set the typography and color for a single post\'s content.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [   
                        [
                            "field" => "field_592fd8902e284",
                            "operator" => "==",
                            "value" => "1"
                        ],
                        [
                            "field" => "field_59229bda3374d",
                            "operator" => "!=",
                            "value" => "hamburger"
                        ]
                    ]
                ]
            ],
            [
                "key" => "field_592d6023057235",
                "label" => __( 'About Author', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "placement" => "top",
                "endpoint" => 0
            ],

            [
                "key" => "field_5937e0a52b48c",
                "label" => "",
                "name" => "",
                "type" => "message",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "message" => '<p class="message">' . '<i class="bi bi-question-circle"></i>' . __( 'You can find more about author settings in <a target="_blank" href="profile.php">Personal Options</a> WordPress menu.', 'ohio' ),
                "new_lines" => "",
                "esc_html" => 0
            ],
            [
                "key" => "field_592d716deeb14",
                "label" => __( 'Author', 'ohio' ),
                "name" => "global_post_author_widget_visibility",
                "type" => "true_false",
                "instructions" => __( 'Show an author widget after the blog post content area.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],
            [
                "key" => "field_5937e0a52b873",
                "label" => __( 'Social Accounts', 'ohio' ),
                "name" => "global_author_social_links",
                "type" => "repeater",
                "instructions" => __( 'Choose social accounts to be displayed in the author widget.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_592d716deeb14",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "collapsed" => "",
                "min" => 0,
                "max" => 0,
                "layout" => "table",
                "button_label" => __( '+ Add Author', 'ohio' ),
                "sub_fields" => [
                    [
                        "key" => "field_5937e0a52f6f4",
                        "label" => __( 'Author', 'ohio' ),
                        "name" => "author",
                        "type" => "user",
                        "instructions" => "",
                        "required" => 0,
                        "conditional_logic" => 0,
                        "role" => "",
                        "allow_null" => 0,
                        "multiple" => 0
                    ],
                    [
                        "key" => "field_5937e0a52fac9",
                        "label" => __( 'Links', 'ohio' ),
                        "name" => "links",
                        "type" => "repeater",
                        "instructions" => "",
                        "required" => 0,
                        "conditional_logic" => 0,
                        "collapsed" => "",
                        "min" => 0,
                        "max" => 0,
                        "layout" => "table",
                        "button_label" => __( '+ Add Link', 'ohio' ),
                        "sub_fields" => [
                            [
                                "key" => "field_5937e0a534d96",
                                "label" => __( 'Social Accounts', 'ohio' ),
                                "name" => "social_networks",
                                "type" => "select",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => 0,
                                "choices" => [
                                    "fivehundredpx" => __( '500px', 'ohio' ),
                                    "amazon" => __( 'Amazon', 'ohio' ),
                                    "angellist" => __( 'AngelList', 'ohio' ),
                                    "apple" => __( 'Apple', 'ohio' ),
                                    "artstation" => __( 'Artstation', 'ohio' ),
                                    "behance" => __( 'Behance', 'ohio' ),
                                    "bitbucket" => __( 'Bitbucket', 'ohio' ),
                                    "bluesky" => __( 'Bluesky', 'ohio' ),
                                    "blogger" => __( 'Blogger', 'ohio' ),
                                    "codepen" => __( 'Codepen', 'ohio' ),
                                    "deviantart" => __( 'DeviantArt', 'ohio' ),
                                    "diaspora" => __( 'Diaspora', 'ohio' ),
                                    "digg" => __( 'Digg', 'ohio' ),
                                    "discord" => __( 'Discord', 'ohio' ),
                                    "dribbble" => __( 'Dribbble', 'ohio' ),
                                    "dropbox" => __( 'Dropbox', 'ohio' ),
                                    "drupal" => __( 'Drupal', 'ohio' ),
                                    "etsy" => __( 'Etsy', 'ohio' ),
                                    "facebook" => __( 'Facebook', 'ohio' ),
                                    "fediverse" => __( 'Fediverse (Mastodon)', 'ohio' ),
                                    "figma" => __( 'Figma', 'ohio' ),
                                    "flickr" => __( 'Flickr', 'ohio' ),
                                    "foursquare" => __( 'Foursquare', 'ohio' ),
                                    "git" => __( 'Git', 'ohio' ),
                                    "github" => __( 'GitHub', 'ohio' ),
                                    "gitlab" => __( 'GitLab', 'ohio' ),
                                    "goodreads" => __( 'Goodreads', 'ohio' ),
                                    "google" => __( 'Google', 'ohio' ),
                                    "google-play" => __( 'Google Play', 'ohio' ),
                                    "hacker-news" => __( 'Hacker News', 'ohio' ),
                                    "houzz" => __( 'Houzz', 'ohio' ),
                                    "instagram" => __( 'Instagram', 'ohio' ),
                                    "jsfiddle" => __( 'JSFiddle', 'ohio' ),
                                    "kaggle" => __( 'Kaggle', 'ohio' ),
                                    "kickstarter" => __( 'Kickstarter', 'ohio' ),
                                    "lastfm" => __( 'Last.fm', 'ohio' ),
                                    "line" => __( 'LINE', 'ohio' ),
                                    "linkedin" => __( 'LinkedIn', 'ohio' ),
                                    "mastodon" => __( 'Mastodon', 'ohio' ),
                                    "medium" => __( 'Medium', 'ohio' ),
                                    "meetup" => __( 'Meetup', 'ohio' ),
                                    "messenger" => __( 'Messenger', 'ohio' ),
                                    "nextdoor" => __( 'Nextdoor', 'ohio' ),
                                    "npm" => __( 'NPM', 'ohio' ),
                                    "orcid" => __( 'ORCID', 'ohio' ),
                                    "patreon" => __( 'Patreon', 'ohio' ),
                                    "paypal" => __( 'PayPal', 'ohio' ),
                                    "peertube" => __( 'PeerTube', 'ohio' ),
                                    "pinterest" => __( 'Pinterest', 'ohio' ),
                                    "producthunt" => __( 'Product Hunt', 'ohio' ),
                                    "python" => __( 'Python', 'ohio' ),
                                    "qq" => __( 'QQ', 'ohio' ),
                                    "quora" => __( 'Quora', 'ohio' ),
                                    "reddit" => __( 'Reddit', 'ohio' ),
                                    "researchgate" => __( 'ResearchGate', 'ohio' ),
                                    "reverbnation" => __( 'ReverbNation', 'ohio' ),
                                    "rss" => __( 'RSS', 'ohio' ),
                                    "salesforce" => __( 'Salesforce', 'ohio' ),
                                    "sass" => __( 'Sass', 'ohio' ),
                                    "sharp" => __( 'Sharp', 'ohio' ),
                                    "shopify" => __( 'Shopify', 'ohio' ),
                                    "skype" => __( 'Skype', 'ohio' ),
                                    "slack" => __( 'Slack', 'ohio' ),
                                    "snapchat" => __( 'Snapchat', 'ohio' ),
                                    "soundcloud" => __( 'SoundCloud', 'ohio' ),
                                    "sourcetree" => __( 'Sourcetree', 'ohio' ),
                                    "spotify" => __( 'Spotify', 'ohio' ),
                                    "stackexchange" => __( 'Stack Exchange', 'ohio' ),
                                    "stackoverflow" => __( 'Stack Overflow', 'ohio' ),
                                    "strava" => __( 'Strava', 'ohio' ),
                                    "stripe" => __( 'Stripe', 'ohio' ),
                                    "t2" => __( 'T2', 'ohio' ),
                                    "teamspeak" => __( 'Teamspeak', 'ohio' ),
                                    "telegram" => __( 'Telegram', 'ohio' ),
                                    "threads" => __( 'Threads', 'ohio' ),
                                    "tiktok" => __( 'TikTok', 'ohio' ),
                                    "trello" => __( 'Trello', 'ohio' ),
                                    "tripadvisor" => __( 'TripAdvisor', 'ohio' ),
                                    "tumblr" => __( 'Tumblr', 'ohio' ),
                                    "twitch" => __( 'Twitch', 'ohio' ),
                                    "twitter" => __( 'X', 'ohio' ),
                                    "ubuntu" => __( 'Ubuntu', 'ohio' ),
                                    "untappd" => __( 'Untappd', 'ohio' ),
                                    "usps" => __( 'USPS', 'ohio' ),
                                    "viadeo" => __( 'Viadeo', 'ohio' ),
                                    "vimeo" => __( 'Vimeo', 'ohio' ),
                                    "waze" => __( 'Waze', 'ohio' ),
                                    "weibo" => __( 'Weibo', 'ohio' ),
                                    "wechat" => __( 'WeChat', 'ohio' ),
                                    "weixin" => __( 'Weixin', 'ohio' ),
                                    "whatsapp" => __( 'WhatsApp', 'ohio' ),
                                    "wikipedia" => __( 'Wikipedia', 'ohio' ),
                                    "wordpress" => __( 'WordPress', 'ohio' ),
                                    "wpexplorer" => __( 'WPExplorer', 'ohio' ),
                                    "xing" => __( 'Xing', 'ohio' ),
                                    "yahoo" => __( 'Yahoo', 'ohio' ),
                                    "yelp" => __( 'Yelp', 'ohio' ),
                                    "youtube" => __( 'YouTube', 'ohio' ),
                                    "zhihu" => __( 'Zhihu', 'ohio' ),
                                ],
                                "default_value" => [
                                    "facebook"
                                ],
                                "allow_null" => 0,
                                "multiple" => 0,
                                "ui" => 0,
                                "ajax" => 0,
                                "return_format" => "value",
                                "placeholder" => ""
                            ],
                            [
                                "key" => "field_5937e0a535178",
                                "label" => "URL",
                                "name" => "url",
                                "type" => "url",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => 0,
                                "default_value" => "",
                                "placeholder" => ""
                            ]
                        ]
                    ]
                ]
            ],
            [
                "key" => "field_592d60jh8a4137",
                "label" => __( 'Related Posts', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "placement" => "top",
                "endpoint" => 0
            ],
            [
                "key" => "field_592d716deeb15",
                "label" => __( 'Related Posts', 'ohio' ),
                "name" => "global_post_related_posts_visibility",
                "type" => "true_false",
                "instructions" => __( 'Show a related posts section below the post content area.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],
            [
                "key" => "field_592d60af8ac18",
                "label" => __( 'Items Per Row', 'ohio' ),
                "name" => "global_post_columns_in_row",
                "type" => "ohio_columns",
                "instructions" => __( 'Set a number of blog post items per row.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_592d716deeb15",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "default_value" => "3-2-1",
                "allow_null" => 0,
                "multiple" => 0,
                "ui" => 0,
                "ajax" => 0,
                "return_format" => "value",
                "placeholder" => "",
                "use_inherit" => false
            ],
            [
                "key" => "field_592d60af8ac66",
                "label" => __( 'Number of Posts', 'ohio' ),
                "name" => "global_related_posts_amount",
                "type" => "number",
                "instructions" => __( 'Set a number of blog post items per section.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_592d716deeb15",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "default_value" => "2",
                "allow_null" => 0,
                "multiple" => 0,
                "min" => 1,
                "max" => 12,
                "ui" => 0,
                "ajax" => 0,
                "placeholder" => "",
                "append" => __( 'posts', 'ohio' ),
            ],
            [
                "key" => "field_592d60ah8a413k",
                "label" => __( 'Sharing', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "placement" => "top",
                "endpoint" => 0
            ],
            [
                "key" => "field_5937433fdf523f",
                "label" => __( 'Sharing', 'ohio' ),
                "name" => "global_post_social_visibility",
                "type" => "true_false",
                "instructions" => __( 'Enable sharing feature for single posts.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => "",
                "ui_off_text" => ""
            ],
            [
                "key" => "field_5937fd3fdf523f",
                "label" => __( 'Sharing Networks', 'ohio' ),
                "name" => "global_post_sharing_networks",
                "type" => "select",
                "instructions" => __( 'Choose social networks to be available for sharing through.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_5937433fdf523f",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ],
                "choices" => [
                    "facebook" => __( 'Facebook', 'ohio' ),
                    "twitter" => __( 'X', 'ohio' ),
                    "linkedin" => __( 'LinkedIn', 'ohio' ),
                    "pinterest" => __( 'Pinterest', 'ohio' )
                ],
                "default_value" => [],
                "allow_null" => 0,
                "multiple" => 1,
                "ui" => 1,
                "ajax" => 1,
                "return_format" => "value",
                "placeholder" => ""
            ],
            [
                "key" => "field_5937fd3fdf523f5",
                "label" => __( 'Open in New Tab', 'ohio' ),
                "name" => "global_post_sharing_networks_target_blank",
                "type" => "true_false",
                "instructions" => __( 'Open a sharing dialog window in a new browser tab.', 'ohio' ),
                "default_value" => 1,
                "ui" => 1,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_5937433fdf523f",
                            "operator" => "==",
                            "value" => "1"
                        ]
                    ]
                ]
            ],
        ],
        "location" => [
            [
                [
                    "param" => "options_page",
                    "operator" => "==",
                    "value" => "theme-general-post"
                ]
            ]
        ],
        "menu_order" => 0,
        "position" => "normal",
        "style" => "default",
        "label_placement" => "left",
        "instruction_placement" => "label",
        "hide_on_screen" => "",
        "active" => 1,
        "description" => ""
    ] );

endif;
