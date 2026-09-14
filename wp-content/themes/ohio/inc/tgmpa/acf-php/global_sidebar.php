<?php if ( function_exists( 'acf_add_local_field_group' ) ) :

    acf_add_local_field_group( [
        "key" => "group_591b0f20ed84sidebar",
        "title" => __( 'Sidebar Settings', 'ohio' ),
        "private" => true,
        "fields" => [
            [
                "key" => "field_591b002d481fcsidebar",
                "label" => __( 'General', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "wrapper" => [
                    "width" => "",
                    "class" => "",
                    "id" => ""
                ],
                "placement" => "top",
                "endpoint" => 0
            ],
            [
                "key" => "field_5937e0a52b48cexmod153sidebar",
                "label" => "",
                "name" => "",
                "type" => "message",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => 0,
                "message" => '<p class="message">' . '<i class="bi bi-question-circle"></i>' . __( 'When you edit Theme Settings, the changes apply to your entire site. Use local Page Settings to override options for individual pages.', 'ohio' ) . '</p>',
                "new_lines" => "",
                "esc_html" => 0
            ],
            [
                "key" => "field_59390deaf218c",
                "label" => __( 'Sidebar', 'ohio' ),
                "name" => "global_page_sidebar_position",
                "type" => "radio",
                "instructions" => __( 'Show a sidebar on the entire site.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => 0,
                "choices" => [
                    "without" => __( 'Disable', 'ohio' ),
                    "left" => __( 'Left', 'ohio' ),
                    "right" => __( 'Right', 'ohio' )
                ],
                "default_value" => [
                    "left"
                ],
                "allow_null" => 0,
                "multiple" => 0,
                "ui" => 0,
                "layout" => "horizontal",
                "ajax" => 0,
                "return_format" => "value",
                "placeholder" => ""
            ],
            [
                "key" => "field_59392223423434234c",
                "label" => __( 'Layout', 'ohio' ),
                "name" => "global_page_sidebar_layout",
                "type" => "select",
                "instructions" => __( 'Choose the layout type for the sidebar.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
                "choices" => [
                    "default" => __( 'Default', 'ohio' ),
                    "boxed" => __( 'Boxed', 'ohio' )
                ],
                "default_value" => [
                    "default"
                ],
                "return_format" => "value"
            ],
            [
                "key" => "field_5939272395862334",
                "label" => __( 'Gaps', 'ohio' ),
                "name" => "global_page_sidebar_gaps",
                "type" => "text",
                "instructions" => __( 'Set the sidebar gaps.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ],
                        [
                            "field" => "field_59392223423434234c",
                            "operator" => "!=",
                            "value" => "default"
                        ]
                    ]
                ],
                "default_value" => "",
                "placeholder" => "",
                "prepend" => __( 'Use CSS units', 'ohio' ),
                "append" => "",
                "maxlength" => 200
            ],
            [
                "key" => "field_5939222395862334",
                "label" => __( 'Width', 'ohio' ),
                "name" => "global_page_sidebar_width",
                "type" => "ohio_responsive_height",
                "instructions" => __( 'Set the sidebar width.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
                "default_value" => ""
            ],
            [
                "key" => "field_593s45s15af5g9t",
                "label" => '<h4>' . __( 'Styles', 'ohio' ) . '</h4>',
                "name" => "",
                "type" => "message",
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
            ],
            [
                "key" => "field_5922203568723",
                "label" => __( 'Background', 'ohio' ),
                "name" => "global_page_sidebar",
                "type" => "clone",
                "instructions" => __( 'Customize the background of the sidebar with a boxed layout.', 'ohio' ),
                "required" => false,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59392223423434234c",
                            "operator" => "==",
                            "value" => "boxed"
                        ]
                    ]
                ],
                "clone" => [
                    "group_982e082a3bcfcf81b766eaa1ec2df4f11e0f5cd3"
                ],
                "display" => "group",
                "layout" => "block",
                "prefix_label" => false,
                "prefix_name" => true
            ],
            [
                "key" => "field_591b0f20ed84swidgets",
                "label" => __( 'Widgets', 'ohio' ),
                "name" => "",
                "type" => "tab",
                "instructions" => "",
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
                "wrapper" => [
                    "width" => "",
                    "class" => "",
                    "id" => ""
                ],
                "placement" => "top",
                "endpoint" => 0
            ],
            [
                "key" => "field_5939222395862323",
                "label" => __( 'Widgets Divider', 'ohio' ),
                "name" => "global_page_widgets_divider",
                "type" => "true_false",
                "instructions" => __( 'Show widgets divider on the sidebar.', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
                "message" => "",
                "default_value" => 1,
                "ui" => 1,
                "ui_on_text" => __( 'Yes', 'ohio' ),
                "ui_off_text" => __( 'No', 'ohio' )
            ],
            [
                "key" => "field_593s45s15af5g9twidgets",
                "label" => '<h4>' . __( 'Styles', 'ohio' ) . '</h4>',
                "name" => "",
                "type" => "message",
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
            ],
            [
                "key" => "field_5937e3a33b3exmod15",
                "label" => __( 'Widget Titles Typography', 'ohio' ),
                "name" => "global_widgets_heading_typo",
                "type" => "ohio_typo",
                "instructions" => __( 'Set up typography styles for widget heading', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
            ],
            [
                "key" => "field_5937e3a33f4exmod15",
                "label" => __( 'Widget Content Typography', 'ohio' ),
                "name" => "global_widgets_content_typo",
                "type" => "ohio_typo",
                "instructions" => __( 'Set up typography styles for widget content', 'ohio' ),
                "required" => 0,
                "conditional_logic" => [
                    [
                        [
                            "field" => "field_59390deaf218c",
                            "operator" => "!=",
                            "value" => "without"
                        ]
                    ]
                ],
            ]
        ],
        "location" => [
            [
                [
                    "param" => "options_page",
                    "operator" => "==",
                    "value" => "theme-general-sidebar"
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
