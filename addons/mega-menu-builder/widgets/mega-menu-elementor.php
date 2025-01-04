<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
if( class_exists('\Elementor\Widget_Base')){
    class MegaMenuBuilder_Elementor extends \Elementor\Widget_Base
    {
    public function get_name()
    {
        return 'whd_mega_menu_builder';
    }
    public function get_title()
    {
        return __('Mega Side Menu Builder', 'whd_mega_menu_builder');
    }
    public function get_icon()
    {
        return 'eicon-nav-menu';
    }
    public function get_categories()
    {
        return array('basic');
    }
    public function get_help_url()
    {
        return 'https://github.com/KaifTaufiq';
    }
    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            $this->prepare_content_control('content', 'Items', '', '')
        );
        $this->add_control(
            'is_rtl',
            $this->prepare_content_control('switcher', 'Enable RTL', '', '')

        );
        $this->add_control(
            'je_active',
            [
                'label' => esc_html__('View', 'textdomain'),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => $this->is_jet_engine_active(),
            ]
        );
        $this->add_control(
            'je_profile_active',
            [
                'label' => esc_html__('View', 'textdomain'),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => $this->is_jet_engine_profile_builder_active(),
            ]
        );
        $this->add_control(
            'jet_engine_options',
            $this->prepare_content_control('switcher', 'Enable Jet Engine Profile Builder Links Support', 'yes', ['je_active' => 'yes', 'je_profile_active' => 'yes'])
        );
        $this->add_control(
            'menu_items',
            [
                'label' => __('Menu Items', 'text-domain'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    $this->prepare_repeater_control('add_icon', 'Add Icon', 'switcher', '', ''), // Add Icon Switch
                    $this->prepare_repeater_control('icon', 'Icon', 'icon', '', ['add_icon' => 'yes']), // Icon Selector
                    $this->prepare_repeater_control('is_link', 'Link item', 'switcher', 'yes', ''), // Is Link Switch
                    $this->prepare_repeater_control('link_label', 'Label', 'text', 'Dropdown', ['is_link' => '']), // Dropdown Title
                    $this->prepare_repeater_control('jet_url', 'Enable Jet Engine Links', 'switcher', '', ['is_link' => 'yes']), // Switch For Enable Jet Engine Links
                    $this->prepare_repeater_control('url_link', 'Link', 'url', '', ['is_link' => 'yes', 'jet_url' => '']), // Default Link When Jet Engine Links are disabled
                    $this->prepare_repeater_control('url_label', 'Title', 'text', 'Link Title', ['is_link' => 'yes', 'jet_url' => '']), // Default Link Title For URL
                    $this->prepare_repeater_control('menu_context', 'Context', 'select', '', ['jet_url' => 'yes']), // Jet Engine Context
                    $this->prepare_repeater_control('profile_page', 'Profile Page', 'select', 'profile', ['jet_url' => 'yes', 'menu_context' => 'account_page',]), // Profile Page
                    $this->prepare_repeater_control('user_page', 'User Page', 'select', 'user', ['jet_url' => 'yes', 'menu_context' => 'user_page',]), // User Page
                    $this->prepare_repeater_control('jet_label_switch', 'Custom Label', 'switcher', '', ['jet_url' => 'yes']), // Custom Label Switch For Jet Engine Links
                    $this->prepare_repeater_control('jet_label', 'Label', 'text', '', ['jet_label_switch' => 'yes', 'jet_url' => 'yes']), // Custom Label For Jet Engine Links
                    [
                        'name' => 'parent_select_role',
                        'label' => __('Hide Menu From Role', 'text-domain'),
                        'description' => __('Select the roles you want to hide this menu item from (If Dropdown, All Sub Menu with Dropdown will be hidden)', 'text-domain'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'options' => $this->get_roles(),
                        'multiple' => true,
                        'label_block' => true,
                        'default' => [],
                    ],
                    [
                        'name' => 'sub_menu',
                        'label' => __('Sub Menu', 'text-domain'),
                        'type' => \Elementor\Controls_Manager::REPEATER,
                        'fields' => [
                            $this->prepare_repeater_control('add_icon', 'Add Icon', 'switcher', '', ''), // Add Icon Switch
                            $this->prepare_repeater_control('icon', 'Icon', 'icon', '', ['add_icon' => 'yes']), // Icon Selector
                            $this->prepare_repeater_control('jet_url', 'Enable Jet Engine Links', 'switcher', '', ''), // Switch For Enable Jet Engine Links
                            $this->prepare_repeater_control('url_link', 'Link', 'url', '', ['jet_url' => '']), // Default Link When Jet Engine Links are disabled
                            $this->prepare_repeater_control('url_label', 'Title', 'text', 'Link Title', ['jet_url' => '']), // Default Link Title For URL
                            $this->prepare_repeater_control('menu_context', 'Context', 'select', '', ['jet_url' => 'yes']), // Jet Engine Context
                            $this->prepare_repeater_control('profile_page', 'Profile Page', 'select', 'profile', ['jet_url' => 'yes', 'menu_context' => 'account_page',]), // Profile Page
                            $this->prepare_repeater_control('user_page', 'User Page', 'select', 'user', ['jet_url' => 'yes', 'menu_context' => 'user_page',]), // User Page
                            $this->prepare_repeater_control('jet_label_switch', 'Custom Label', 'switcher', '', ['jet_url' => 'yes']), // Custom Label Switch For Jet Engine Links
                            $this->prepare_repeater_control('jet_label', 'Label', 'text', '', ['jet_label_switch' => 'yes', 'jet_url' => 'yes']), // Custom Label For Jet Engine Links
                            [
                                'name' => 'submenu_select_role',
                                'label' => __('Hide Menu From Role', 'text-domain'),
                                'description' => __('Select the roles you want to hide this sub menu item from', 'text-domain'),
                                'type' => \Elementor\Controls_Manager::SELECT2,
                                'options' => $this->get_roles(),
                                'multiple' => true,
                                'label_block' => true,
                                'default' => [],
                            ],
                        ],
                        'condition' => [
                            'is_link' => '',
                        ],
                    ],
                ],
                'default' => [
                    [
                        'add_icon' => 'yes',
                        'is_link' => '',
                        'link_label' => 'Dropdown',
                        'sub_menu' => [
                            [
                                'add_icon' => 'yes',
                                'url_link' => [
                                    'url' => 'https://whitehatdevs.com/community/mega-menu-builder/',
                                ],
                                'url_label' => 'Guide',

                            ],
                            [
                                'add_icon' => 'yes',
                                'url_link' => [
                                    'url' => 'https://whitehatdevs.com/community/mega-menu-builder/',
                                ],
                                'url_label' => 'Sub Menu Items',

                            ],
                        ],
                    ],
                    [
                        'add_icon' => 'yes',
                        'is_link' => 'yes',
                        'url_link' => [
                            'url' => 'https://whitehatdevs.com',
                        ],
                        'url_label' => 'Built By White Hat Devs',
                    ],
                ],
            ]
        );
        $this->end_controls_section();
        // Icons Section Starts Here
        $this->start_controls_section(
            'icons_section',
            $this->prepare_content_control('content', 'Collapse Icons', '', '')
        );
        $this->add_control(
            'mega_open_icon',
            $this->prepare_content_control('icon', 'Mega Open Icon', 'fa fa-chevron-down', '')
        );
        $this->add_control(
            'mega_close_icon',
            $this->prepare_content_control('icon', 'Mega Close Icon', 'fas fa-chevron-up', '')
        );
        $this->end_controls_section();
        // Logout Section Starts Here
        $this->start_controls_section(
            'logout_section',
            $this->prepare_content_control('content', 'Logout', '', '')
        );
        $this->add_control(
            'logout_switch',
            $this->prepare_content_control('switcher', 'Enable Logout Item', '', '')
        );
        $this->add_control(
            'logout_label',
            [
                'label' => __('Logout', 'text-domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Logout',
                'condition' => [
                    'logout_switch' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'logout_custom_style_switch',
            $this->prepare_content_control('switcher', 'Custom Style For Logout', '', ['logout_switch' => 'yes'])
        );
        $this->add_control(
            'logout_icon_switch',
            $this->prepare_content_control('switcher', 'Add Icon', '', ['logout_switch' => 'yes'])
        );
        $this->add_control(
            'logout_icon',
            $this->prepare_content_control('icon', 'Logout Icon', 'fas fa-sign-out-alt', ['logout_switch' => 'yes', 'logout_icon_switch' => 'yes'])
        );
        $this->add_control(
            'logout_redirect',
            [
                'label' => __('Redirect URL', 'text-domain'),
                'type' => \Elementor\Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'logout_switch' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'active_item_section',
            $this->prepare_content_control('content', 'Active Items', '', '')
        );
        $this->add_control(
            'active_items',
            $this->prepare_content_control('switcher', 'Enable Active Items', 'yes', '')
        );
        $this->add_control(
            'active_item_paramter_switch',
            $this->prepare_content_control('switcher', 'Compare Query Arguments Too?', '', ['active_items' => 'yes'])
        );
        $this->end_controls_section();
        // Style Section Starts Here
        $this->start_controls_section(
            'parent_item_style',
            $this->prepare_content_control('style', 'Parent Item Style', '', '')
        );
        $this->add_control(
            'parent_padding',
            $this->prepare_style_control('dimension', 'Padding', '.jet-menu__parent', '5,10,5,10', 'padding')
        );
        $this->add_control(
            'parent_gap',
            $this->prepare_style_control('slider', 'Row Gap', '.jet-menu__item', ['unit' => 'px', 'size' => 10], 'margin-bottom')
        );
        $this->add_control(
            'parent_radius',
            $this->prepare_style_control('dimension', 'Border Raduis', '.jet-menu__parent', '10,10,10,10', 'border-radius')
        );
        $this->add_control(
            'parent_icon__gap',
            $this->prepare_style_control('slider', 'Parent Icon Gap', '.jet-menu__icon', ['unit' => 'px', 'size' => 10], 'margin-right')
        );
        $this->add_control(
            'parent_icon_width',
            $this->prepare_style_control('slider', 'Parent Icon Size', '.jet-menu__icon', ['unit' => 'px', 'size' => 15], 'height:{{SIZE}}{{UNIT}};width')
        );
        $this->add_control(
            'parent_toggle_icon_width',
            $this->prepare_style_control('slider', 'Parent Toggle Icon Size', '.jet-menu__toggle', ['unit' => 'px', 'size' => 15], 'height:{{SIZE}}{{UNIT}};width')
        );
        $this->start_controls_tabs(
            'parent_style_tabs'
        );
        $this->start_controls_tab(
            'parent_normal_tab',
            ['label' => esc_html__('Normal', 'textdomain'),]
        );
        $this->add_control(
            'parent_normal_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent', '', 'background-color')
        );
        $this->add_control(
            'parent_icon_color',
            $this->prepare_style_control('color', 'Parent Icon Color', '.jet-menu__icon svg', '', 'fill')
        );
        $this->add_control(
            'parent_toggle_icon_normal_color',
            $this->prepare_style_control('color', 'Toggle Icon Color', '.jet-menu__toggle svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'parent_typography_normal',
                'selector' => '{{WRAPPER}} .jet-menu__title',
            ]
        );
        $this->add_control(
            'parent_normal_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'parent_border_normal',
                'selector' => '{{WRAPPER}} .jet-menu__parent',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'parent_box_shadow_normal',
                'selector' => '{{WRAPPER}} .jet-menu__parent',
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'parent_hover_tab',
            ['label' => esc_html__('Hover', 'textdomain'),]
        );
        $this->add_control(
            'parent_hover_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent:hover', '', 'background-color')
        );
        $this->add_control(
            'parent_icon_hover_color',
            $this->prepare_style_control('color', 'Parent Icon Color', '.jet-menu__parent:hover .jet-menu__icon svg', '', 'fill')
        );
        $this->add_control(
            'parent_toggle_icon_hover_color',
            $this->prepare_style_control('color', 'Toggle Icon Color', '.jet-menu__parent:hover .jet-menu__toggle svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'parent_typography_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent:hover .jet-menu__title',
            ]
        );
        $this->add_control(
            'parent_hover_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__parent:hover .jet-menu__title', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'parent_border_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent:hover',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'parent_box_shadow_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent:hover',
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'parent_active_tab',
            [
                'label' => esc_html__('Active', 'textdomain'),
                'condition' => [
                    'active_items' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'link_item_active_style_heading',
            [
                'label' => esc_html__('Link Item Active', 'textdomain'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'link_item_active_style_popover',
            [
                'label' => esc_html__('Active Link Item Style', 'textdomain'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            ]
        );
        $this->start_popover();
        $this->add_control(
            'parent_active_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent--active', '', 'background-color')
        );
        $this->add_control(
            'parent__active_icon_color',
            $this->prepare_style_control('color', 'Parent Icon Color', '.jet-menu__icon--child--active svg', '', 'fill')
        );
        $this->add_control(
            'parent_active_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title--active', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'parent_border_active',
                'selector' => '{{WRAPPER}} .jet-menu__parent--active',
            ]
        );
        $this->end_popover();
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'parent_typography_active',
                'selector' => '{{WRAPPER}} .jet-menu__title--active',
                'label' => esc_html__('Link Item Typography', 'your-text-domain'),
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'parent_box_shadow_active',
                'selector' => '{{WRAPPER}} .jet-menu__parent--active',
            )
        );
        $this->add_control(
            'dropdown_item_active_style_heading',
            [
                'label' => esc_html__('Active Dropdown Styling', 'textdomain'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'dropdown_item_active_style_popover',
            [
                'label' => esc_html__('Active Dropdown', 'textdomain'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            ]
        );
        $this->start_popover();
        $this->add_control(
            'dropdown_active_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent--dropdown', '', 'background-color')
        );
        $this->add_control(
            'dropdown_toggle_icon_hover_color',
            $this->prepare_style_control('color', 'Toggle Icon Color', '.jet-menu__toggle--dropdown svg', '', 'fill')
        );
        $this->add_control(
            'dropdown__active_icon_color',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__icon--dropdown svg', '', 'fill')
        );
        $this->add_control(
            'dropdown_active_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title--dropdown', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'dropdown_border_active',
                'selector' => '{{WRAPPER}} .jet-menu__parent--dropdown',
            ]
        );
        $this->end_popover();
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'dropdown_typography_active',
                'selector' => '{{WRAPPER}} .jet-menu__title--dropdown',
                'label' => esc_html__('Active Dropdown Typography', 'your-text-domain'),
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'dropdown_box_shadow_active',
                'selector' => '{{WRAPPER}} .jet-menu__parent--dropdown',
            )
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section(
            'child_section_wrapper_style',
            $this->prepare_content_control('style', 'Child Items Wrapper', '', '')
        );
        $this->add_control(
            'child_wrapper_padding',
            $this->prepare_style_control('dimension', 'Padding', '.jet-menu__sub-menu', '0,0,0,0', 'padding')
        );
        $this->add_control(
            'child_wrapper_margin',
            $this->prepare_style_control('dimension', 'Margin', '.jet-menu__sub-menu', '5,0,5,0', 'margin')
        );
        $this->add_control(
            'child_wrapper_radius',
            $this->prepare_style_control('dimension', 'Border Raduis', '.jet-menu__sub-menu', '0,0,0,0', 'border-radius')

        );
        $this->add_control(
            'child_wrapper_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__sub-menu', '', 'background-color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'child_wrapper_border',
                'selector' => '{{WRAPPER}} .jet-menu__sub-menu',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'wrapper_shadow',
                'selector' => '{{WRAPPER}} .jet-menu__sub-menu',
            )
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'child_section_style',
            $this->prepare_content_control('style', 'Child Items', '', '')
        );
        $this->add_control(
            'child_padding',
            $this->prepare_style_control('dimension', 'Padding', '.jet-menu__link', '10,10,10,10', 'padding')
        );
        $this->add_control(
            'child_gap',
            $this->prepare_style_control('slider', 'Row Gap', '.jet-menu__sub-menu', ['unit' => 'px', 'size' => 10], 'row-gap')
        );
        $this->add_control(
            'child_radius',
            $this->prepare_style_control('dimension', 'Border Raduis', '.jet-menu__link', '8,8,8,8', 'border-radius')
        );
        $this->add_control(
            'child_icon_width',
            $this->prepare_style_control('slider', 'Child Icon Size', '.jet-menu__icon--child', ['unit' => 'px', 'size' => 15], 'height:{{SIZE}}{{UNIT}};width')
        );
        $this->add_control(
            'child_icon__gap',
            $this->prepare_style_control('slider', 'Child Icon Gap', '.jet-menu__icon--child', ['unit' => 'px', 'size' => 10], 'margin-right')
        );
        $this->start_controls_tabs(
            'child_tabs'
        );
        $this->start_controls_tab(
            'child_normal_tab',
            ['label' => esc_html__('Normal', 'textdomain'),]
        );
        $this->add_control(
            'child_normal_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__link', '', 'background-color')
        );
        $this->add_control(
            'child_icon_color_normal',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__icon--child svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'child_typography_normal',
                'selector' => '{{WRAPPER}} .jet-menu__title--child',
            ]
        );
        $this->add_control(
            'child_normal_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title--child', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'child_border_normal',
                'selector' => '{{WRAPPER}} .jet-menu__link',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'child_box_shadow_normal',
                'selector' => '{{WRAPPER}} .jet-menu__link',
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'child_hover_tab',
            ['label' => esc_html__('Hover', 'textdomain'),]
        );
        $this->add_control(
            'child_hover_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__link:hover', '', 'background-color')
        );
        $this->add_control(
            'child_icon_color_hover',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__link:hover .jet-menu__icon--child svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'child_typography_hover',
                'selector' => '{{WRAPPER}} .jet-menu__link:hover .jet-menu__title--child',
            ]
        );
        $this->add_control(
            'child_hover_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__link:hover.jet-menu__title--child', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'child_border_hover',
                'selector' => '{{WRAPPER}} .jet-menu__link:hover',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'child_box_shadow_hover',
                'selector' => '{{WRAPPER}} .jet-menu__link:hover',
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'child_active_tab',
            [
                'label' => esc_html__('Active', 'textdomain'),
                'condition' => [
                    'active_items' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'child_active_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__link--active', '', 'background-color')
        );
        $this->add_control(
            'child_icon_color_active',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__icon--child--active svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'child_typography_active',
                'selector' => '{{WRAPPER}} .jet-menu__title--child--active',
            ]
        );
        $this->add_control(
            'child_active_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title--child--active', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'child_border_active',
                'selector' => '{{WRAPPER}} .jet-menu__link--active',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'child_box_shadow_active',
                'selector' => '{{WRAPPER}} .jet-menu__link--active',
            )
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section(
            'logout_section_style',
            [
                'label' => esc_html__('Logout Style', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'logout_switch' => 'yes',
                    'logout_custom_style_switch' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'logout_padding',
            $this->prepare_style_control('dimension', 'Padding', '.jet-menu__parent--logout', '5,10,5,10', 'padding')
        );
        $this->add_control(
            'logout_radius',
            $this->prepare_style_control('dimension', 'Border Raduis', '.jet-menu__parent--logout', '10,10,10,10', 'border-radius')
        );
        $this->add_control(
            'logout_icon__gap',
            $this->prepare_style_control('slider', 'Icon Gap', '.jet-menu__icon--logout-icon', ['unit' => 'px', 'size' => 10], 'margin-right')
        );
        $this->add_control(
            'logout_icon_width',
            $this->prepare_style_control('slider', 'Icon Size', '.jet-menu__icon--logout-icon', ['unit' => 'px', 'size' => 15], 'height:{{SIZE}}{{UNIT}};width')
        );
        $this->start_controls_tabs(
            'logout_style_tabs'
        );
        $this->start_controls_tab(
            'logout_normal_tab',
            [
                'label' => esc_html__('Normal', 'textdomain'),
            ]
        );
        $this->add_control(
            'logout_normal_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent--logout', '', 'background-color')
        );
        $this->add_control(
            'logout_icon_color',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__icon--logout-icon svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'logout_typography_normal',
                'selector' => '{{WRAPPER}} .jet-menu__title--logout-title',
            ]
        );
        $this->add_control(
            'logout_normal_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__title--logout-title', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'logout_border_normal',
                'selector' => '{{WRAPPER}} .jet-menu__parent--logout',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'logout_box_shadow_normal',
                'selector' => '{{WRAPPER}} .jet-menu__parent--logout',
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'logout_hover_tab',
            [
                'label' => esc_html__('Hover', 'textdomain'),
            ]
        );
        $this->add_control(
            'logout_hover_bg_color',
            $this->prepare_style_control('color', 'Background Color', '.jet-menu__parent--logout:hover', '', 'background-color')
        );
        $this->add_control(
            'logout_icon_hover_color',
            $this->prepare_style_control('color', 'Icon Color', '.jet-menu__parent--logout:hover .jet-menu__icon--logout-icon svg', '', 'fill')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'logout_typography_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent--logout:hover .jet-menu__title--logout-title',
            ]
        );
        $this->add_control(
            'logout_hover_text_color',
            $this->prepare_style_control('color', 'Text Color', '.jet-menu__parent--logout:hover .jet-menu__title--logout-title', '', 'color')
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'logout_border_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent--logout:hover',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'logout_box_shadow_hover',
                'selector' => '{{WRAPPER}} .jet-menu__parent--logout:hover',
            )
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
    ?>
        <style>
            <?php if ($settings['is_rtl'] == 'yes') : ?>.jet-menu__parent,
            .jet-menu__link {
                flex-direction: row-reverse;
            }

            .jet-menu__icon.jet-menu__icon.jet-menu__icon.jet-menu__icon.jet-menu__icon {
                margin-right: 0;
                margin-left: <?php echo $settings['parent_icon__gap']['size'] . $settings['parent_icon__gap']['unit']; ?>;
            }

            .jet-menu__title {
                text-align: right;
            }

            <?php endif; ?>.jet-menu {
                display: flex;
                flex-direction: column;
            }

            .jet-menu__parent,
            .jet-menu__link {
                <?php if ($settings['is_rtl'] == 'yes') : ?>flex-direction: row-reverse;
                <? endif ?>display: flex;
                align-items: center;
            }

            .jet-menu__title {
                flex-grow: 1;
            }

            .jet-menu .jet-menu__item.jet-menu__item:last-child {
                margin-bottom: 0;
            }

            .jet-menu__sub-menu {
                display: none;
                flex-direction: column;
            }

            .jet-menu__toggle,
            .jet-menu__icon {
                display: flex;
                align-items: center;
            }
        </style>
        <?php if (!empty($settings['menu_items'])) : ?>
            <?php // get current user role
            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $current_user_role = $current_user->roles[0];
            }
            ?>
            <div class="jet-menu">
                <?php foreach ($settings['menu_items'] as $item) : ?>
                    <?php
                    if (is_user_logged_in()) {
                        if (in_array($current_user_role, $item['parent_select_role'])) {
                            continue;
                        }
                    }
                    ?>
                    <?php if ($item['is_link'] == 'yes') : ?>
                        <?php
                        $title = ($item['jet_url'] == '') ? $item['url_label'] : (($item['jet_label_switch'] == 'yes') ? $item['jet_label'] : (($item['menu_context'] == 'account_page') ? $this->get_subpage_detail_by_index($item['profile_page'], 'title', 'profile') : (($item['menu_context'] == 'user_page') ? $this->get_subpage_detail_by_index($item['user_page'], 'title', 'user') : '')));
                        $url = ($item['jet_url'] == '') ? $item['url_link']['url'] : (($item['menu_context'] == 'account_page') ? $this->get_account_page_url('profile') . $this->get_subpage_detail_by_index($item['profile_page'], 'slug', 'profile') : (($item['menu_context'] == 'user_page') ? $this->get_account_page_url('user') . $this->get_subpage_detail_by_index($item['user_page'], 'slug', 'user') : ''));
                        ?>
                        <div class="jet-menu__item jet-menu__item--no-children">
                            <a class="jet-menu__parent" href="<?php echo $url; ?>">
                                <?php if ($item['add_icon'] === 'yes' && !empty($item['icon']['value'])) : ?>
                                    <span class="jet-menu__icon">
                                        <?php \Elementor\Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="jet-menu__title">
                                    <?php echo $title; ?>
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($item['is_link'] == '') : ?>
                        <div class="jet-menu__item jet-menu__item--with-children">
                            <div class="jet-menu__parent" onclick="toggleSubMenu(this)">
                                <?php if ($item['add_icon'] === 'yes' && !empty($item['icon']['value'])) : ?>
                                    <span class="jet-menu__icon">
                                        <?php \Elementor\Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                <?php endif; ?>

                                <span class="jet-menu__title">
                                    <?php echo $item['link_label']; ?>
                                </span>

                                <span class="jet-menu__toggle">
                                    <?php \Elementor\Icons_Manager::render_icon($settings['mega_open_icon'], ['aria-hidden' => 'true']); ?>
                                </span>
                            </div>
                            <div class="jet-menu__sub-menu">
                                <?php foreach ($item['sub_menu'] as $sub_item) : ?>
                                    <?php
                                    if (is_user_logged_in()) {
                                        if (in_array($current_user_role, $sub_item['submenu_select_role'])) {
                                            continue;
                                        }
                                    }
                                    ?>
                                    <?php
                                    $title = $sub_item['jet_url'] == '' ? $sub_item['url_label'] : ($sub_item['jet_label_switch'] == 'yes' ? $sub_item['jet_label'] : (($sub_item['menu_context'] == 'account_page') ? $this->get_subpage_detail_by_index($sub_item['profile_page'], 'title', 'profile') : (($sub_item['menu_context'] == 'user_page') ? $this->get_subpage_detail_by_index($sub_item['user_page'], 'title', 'user') : '')));
                                    $url = $sub_item['jet_url'] == '' ? $sub_item['url_link']['url'] : (($sub_item['menu_context'] == 'account_page') ? $this->get_account_page_url('profile') . $this->get_subpage_detail_by_index($sub_item['profile_page'], 'slug', 'profile') : (($sub_item['menu_context'] == 'user_page') ? $this->get_account_page_url('user') . $this->get_subpage_detail_by_index($sub_item['user_page'], 'slug', 'user') : ''));
                                    ?>
                                    <a class="jet-menu__link" href="<?php echo $url; ?>">
                                        <?php if ($sub_item['add_icon'] === 'yes' && !empty($sub_item['icon']['value'])) : ?>
                                            <?php $sub_icon_style = "width:{$settings['child_icon_width']['size']}{$settings['child_icon_width']['unit']};height:{$settings['child_icon_width']['size']}{$settings['parent_icon_width']['unit']};"; ?>
                                            <span class="jet-menu__icon jet-menu__icon--child">
                                                <?php \Elementor\Icons_Manager::render_icon($sub_item['icon'], ['aria-hidden' => 'true', 'style' => $sub_icon_style]); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="jet-menu__title jet-menu__title--child">
                                            <?php echo $title; ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (isset($settings['logout_switch']) && $settings['logout_switch'] === 'yes') : ?>
                    <?php $style_logout = (isset($settings['logout_custom_style_switch']) && $settings['logout_custom_style_switch'] === 'yes') ? true : false; ?>
                    <div class="jet-menu__item">
                        <a class="jet-menu__parent <?php echo ($style_logout) ? 'jet-menu__parent--logout' : ''; ?> " href="<?php echo isset($settings['logout_redirect']['url']) ? wp_logout_url($settings['logout_redirect']['url']) : '#'; ?>">
                            <?php if (isset($settings['logout_icon_switch']) && $settings['logout_icon_switch'] === 'yes' && !empty($settings['logout_icon']['value'])) : ?>
                                <span class="jet-menu__icon <?php echo ($style_logout) ? 'jet-menu__icon--logout-icon' : ''; ?>">
                                    <?php \Elementor\Icons_Manager::render_icon($settings['logout_icon'], ['aria-hidden' => 'true']); ?>
                                </span>
                            <?php endif; ?>
                            <span class="jet-menu__title <?php echo ($style_logout) ? 'jet-menu__title--logout-title' : ''; ?>">
                                <?php echo isset($settings['logout_label']) ? $settings['logout_label'] : ''; ?>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <script>
            <?php if ($settings['active_items'] === 'yes') : ?>
                document.addEventListener("DOMContentLoaded", function() {
                    // Function to separate base URL and query parameters
                    function getBaseUrlAndQuery(url) {
                        const parsedUrl = new URL(url);
                        const baseUrl = parsedUrl.origin + parsedUrl.pathname.replace(/\/$/, ""); // Normalize base URL
                        const query = parsedUrl.search; // Get query parameters
                        return {
                            baseUrl,
                            query
                        };
                    }
                    const currentUrlDetails = getBaseUrlAndQuery(window.location.href); // Get current URL details
                    const jetMenu = document.querySelector(".jet-menu");
                    const menuItems = jetMenu.querySelectorAll(".jet-menu__item");
                    let = breakLoop = [];
                    for (let i = 0; i < menuItems.length; i++) {
                        if (breakLoop[0] == 'break') {
                            break;
                        }
                        const item = menuItems[i];
                        const noChild = item.classList.contains("jet-menu__item--no-children");
                        if (noChild) {
                            const anchor = item.querySelector("a");
                            if (anchor) {
                                const anchorUrlDetails = getBaseUrlAndQuery(anchor.href); // Get anchor URL details
                                if (anchorUrlDetails.baseUrl === currentUrlDetails.baseUrl) {
                                    const icon = anchor.querySelector(".jet-menu__icon");
                                    const title = anchor.querySelector(".jet-menu__title");
                                    <?php if ($settings['active_item_paramter_switch'] === 'yes') : ?>
                                        if (anchorUrlDetails.query === currentUrlDetails.query) {
                                            anchor.classList.add("jet-menu__parent--active");
                                            if (icon) {
                                                icon.classList.add("jet-menu__icon--active");
                                            }
                                            if (title) {
                                                title.classList.add("jet-menu__title--active");
                                            }
                                            breakLoop[0] = 'break';
                                            break;
                                        }
                                    <?php else : ?>
                                        anchor.classList.add("jet-menu__parent--active");
                                        if (icon) {
                                            icon.classList.add("jet-menu__icon--active");
                                        }
                                        if (title) {
                                            title.classList.add("jet-menu__title--active");
                                        }
                                        breakLoop[0] = 'break';
                                        break;
                                    <?php endif; ?>
                                }
                            }
                        }
                        const isParent = item.classList.contains("jet-menu__item--with-children");
                        if (isParent) {

                            const parent = item.querySelector(".jet-menu__parent");
                            const submenu = item.querySelector(".jet-menu__sub-menu");
                            const childItems = submenu.querySelectorAll(".jet-menu__link");

                            for (let j = 0; j < childItems.length; j++) {
                                const child = childItems[j];
                                const childUrlDetails = getBaseUrlAndQuery(child.href); // Get child URL details
                                if (childUrlDetails.baseUrl === currentUrlDetails.baseUrl) {
                                    const icon = child.querySelector(".jet-menu__icon--child");
                                    const title = child.querySelector(".jet-menu__title--child");
                                    <?php if ($settings['active_item_paramter_switch'] === 'yes') : ?>
                                        if (childUrlDetails.query === currentUrlDetails.query) {
                                            child.classList.add("jet-menu__link--active");
                                            submenu.style.display = "flex";
                                            toggleSubMenu(parent);
                                            if (icon) {
                                                icon.classList.add("jet-menu__icon--child--active");
                                            }
                                            if (title) {
                                                title.classList.add("jet-menu__title--child--active");
                                            }
                                            breakLoop[0] = 'break';
                                            break;
                                        }
                                    <?php elseif ($settings['active_item_paramter_switch'] === '') : ?>
                                        child.classList.add("jet-menu__link--active");
                                        toggleSubMenu(parent);
                                        submenu.style.display = "flex";
                                        if (icon) {
                                            icon.classList.add("jet-menu__icon--child--active");
                                        }
                                        if (title) {
                                            title.classList.add("jet-menu__title--child--active");
                                        }
                                        breakLoop[0] = 'break';
                                        break;
                                    <?php endif; ?>
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            function toggleSubMenu(item) {
                const parent = item.parentElement;
                const submenu = parent.querySelector(".jet-menu__sub-menu");
                const toggleIcon = item.querySelector(".jet-menu__toggle svg");
                const parentIcon = item.querySelector(".jet-menu__icon");
                const parentTitle = item.querySelector(".jet-menu__title");
                const parentToggle = item.querySelector(".jet-menu__toggle");
                // Check if toggleIcon exists before setting innerHTML
                if (toggleIcon) {
                    if (!parent.classList.contains("active")) {
                        // Close all open submenus within the same menu
                        const menu = parent.closest(".jet-menu");
                        menu.querySelectorAll(".jet-menu__sub-menu").forEach(function(submenu) {
                            submenu.style.display = "none";
                        });
                        // Remove active class from all items within the same menu
                        menu.querySelectorAll(".jet-menu__item").forEach(function(menuItem) {
                            item.classList.remove("jet-menu__parent--dropdown");
                            menuItem.style.marginBottom = "10px";
                            if (parentIcon) {
                                parentIcon.classList.remove("jet-menu__icon--dropdown");
                            }
                            if (parentTitle) {
                                parentTitle.classList.remove("jet-menu__title--dropdown");
                            }
                            if (parentToggle) {
                                parentToggle.classList.remove("jet-menu__toggle--dropdown");
                            }
                            menuItem.classList.remove("active");
                            const icon = menuItem.querySelector(".jet-menu__toggle svg");
                            if (icon) {
                                icon.innerHTML = `<?php \Elementor\Icons_Manager::render_icon($settings['mega_open_icon'], ['aria-hidden' => 'true']); ?>`;
                            }
                        });
                        // Open the clicked submenu
                        submenu.style.display = "flex";
                        // Add active class to the clicked items parent
                        parent.style.marginBottom = "0px";
                        item.classList.add("jet-menu__parent--dropdown");
                        if (parentIcon) {
                            parentIcon.classList.add("jet-menu__icon--dropdown");
                        }
                        if (parentTitle) {
                            parentTitle.classList.add("jet-menu__title--dropdown");
                        }
                        if (parentToggle) {
                            parentToggle.classList.add("jet-menu__toggle--dropdown");
                        }
                        parent.classList.add("active");

                        if (toggleIcon.parentNode) {
                            toggleIcon.innerHTML = `<?php \Elementor\Icons_Manager::render_icon($settings['mega_close_icon'], ['aria-hidden' => 'true']); ?>`;
                        }
                    } else {
                        // Toggle the current submenu
                        submenu.style.display = (submenu.style.display === "flex") ? "none" : "flex";
                        // Remove active class
                        parent.style.marginBottom = "<?php echo json_encode($settings["parent_gap"]["size"]); ?>px";
                        item.classList.remove("jet-menu__parent--dropdown");
                        if (parentIcon) {
                            parentIcon.classList.remove("jet-menu__icon--dropdown");
                        }
                        if (parentTitle) {
                            parentTitle.classList.remove("jet-menu__title--dropdown");
                        }
                        if (parentToggle) {
                            parentToggle.classList.remove("jet-menu__toggle--dropdown");
                        }
                        parent.classList.remove("active");
                        toggleIcon.innerHTML = `<?php \Elementor\Icons_Manager::render_icon($settings['mega_open_icon'], ['aria-hidden' => 'true']); ?>`;
                    }
                }
            }
        </script>
    <?php
    }

    // Render Ends Here
    function prepare_content_control($type, $label, $value, $condition)
    {
        if ($type == 'icon') {
            return [
                'label'       => __($label, 'textdomain'),
                'label_block' => false,
                'default' => [
                    'value' => $value,
                    'library' => 'fa-solid',
                ],
                'type'        => \Elementor\Controls_Manager::ICONS,
                'skin'        => 'inline',
                'condition' => $condition,
            ];
        }
        if ($type == 'switcher') {
            return [
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'textdomain'),
                'label_off' => __('No', 'textdomain'),
                'return_value' => 'yes',
                'default' => $value,
                'condition' => $condition,
            ];
        }
        if ($type == 'content') {
            return [
                'label' => __($label, 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => $condition,
            ];
        }
        if ($type == 'style') {
            return [
                'label' => __($label, 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => $condition,
            ];
        }
    }
    function prepare_repeater_control($name, $label, $type, $default, $condition)
    {
        if ($type == 'switcher') {
            return [
                'name' => $name,
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'textdomain'),
                'label_off' => __('No', 'textdomain'),
                'return_value' => 'yes',
                'default' => $default,
                'condition' => $condition,

            ];
        }
        if ($type == 'icon') {
            return [
                'name' => $name,
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'label_block' => false,
                'default' => [
                    'value' => '',
                    'library' => '',
                ],
                'skin' => 'inline',
                'condition' => $condition,
            ];
        }
        if ($type == 'text') {
            return [
                'name' => $name,
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__($default, 'textdomain'),
                'condition' => $condition,
            ];
        }
        if ($type == 'url') {
            return [
                'name' => $name,
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => ['url', 'is_external', 'nofollow'],
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
                'condition' => $condition,
            ];
        }
        if ($type == 'select') {
            if ($default == 'profile') {
                $options = $this->get_subpage_titles_with_index('profile');
                $default = '0';
            } elseif ($default == 'user') {
                $options = $this->get_subpage_titles_with_index('user');
                $default = '0';
            } else {
                $options = [
                    'account_page' => __('Account', 'text-domain'),
                    'user_page'    => __('Single User Page', 'text-domain'),
                ];
                $default = 'account_page';
            }
            return [

                'name' => $name,
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $options,
                'default' => '0',
                'condition' => $condition,
            ];
        }
    }
    function prepare_style_control($type, $label, $selector, $default, $property)
    {
        if ($type == 'color') {
            return [
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $default,
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => $property . ': {{VALUE}}',
                ],
            ];
        }
        if ($type == 'dimension') {
            $default = explode(',', $default);
            return [
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'top' => $default[0],
                    'right' => $default[1],
                    'bottom' => $default[2],
                    'left' => $default[3],
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => $property . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ];
        }
        if ($type == 'slider') {
            return [
                'label' => esc_html__($label, 'textdomain'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => $default,
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => $property . ': {{SIZE}}{{UNIT}};',
                ],
            ];
        }
    }
    function get_subpage_titles_with_index($type)
    {
        $structure = '';
        if ($type == 'profile') {
            $structure = 'account_page_structure';
        } elseif ($type == 'user') {
            $structure = 'user_page_structure';
        } else {
            return [];
        }
        // Get the serialized data from wp_options
        $profile_builder_data = get_option('profile-builder');

        if ($profile_builder_data) {
            // Unserialize the data to work with it as an array
            $profile_builder_array = maybe_unserialize($profile_builder_data);

            // Check if the structure key exists
            if (isset($profile_builder_array[$structure]) && is_array($profile_builder_array[$structure])) {
                $formatted_titles = [];

                // Loop through each subpage in the structure
                foreach ($profile_builder_array[$structure] as $index => $subpage) {
                    if (isset($subpage['title'])) {
                        // Use the index as the key and title as the value
                        $formatted_titles[$index] = __($subpage['title'], 'text-domain');
                    }
                }

                // Return the formatted array
                return $formatted_titles;
            }
        }

        return [];
    }
    function get_account_page_url($type)
    {
        if ($type == 'profile') {
            $structure = 'account_page';
        } else {
            $structure = 'users_page';
        }
        // Get the serialized data from wp_options
        $profile_builder_data = get_option('profile-builder');

        if ($profile_builder_data) {
            // Unserialize the data to convert it to an array
            $profile_builder_array = maybe_unserialize($profile_builder_data);

            // Check if the 'account_page' key exists and return its value
            if (isset($profile_builder_array[$structure])) {
                $account_page_id = $profile_builder_array[$structure];  // Get the account page ID

                // Get the post URL using the ID
                $account_page_url = get_permalink($account_page_id);

                // Return the URL
                return $account_page_url;
            }
        }

        // Return null if not found
        return null;
    }
    function get_subpage_detail_by_index($index, $detail = 'title', $type = 'profile')
    {
        if ($index == '' || ! $detail === 'slug' || ! $detail === 'title' || ! $type === 'profile' || ! $type === 'user') {
            return null;
        }

        // Get the serialized data from wp_options
        $profile_builder_data = get_option('profile-builder');
        $structure = '';
        if ($type == 'profile') {
            $structure = 'account_page_structure';
        } else {
            $structure = 'user_page_structure';
        }
        if ($profile_builder_data) {
            // Unserialize the data to work with it as an array
            $profile_builder_array = maybe_unserialize($profile_builder_data);

            // Check if the 'account_page_structure' key exists
            if (isset($profile_builder_array[$structure]) && is_array($profile_builder_array['account_page_structure'])) {

                // Check if the provided index exists in the structure
                if (isset($profile_builder_array[$structure][$index])) {
                    $subpage = $profile_builder_array[$structure][$index];

                    // Return the requested detail (slug or title)
                    if ($detail === 'slug' && isset($subpage['slug'])) {
                        return $subpage['slug'];
                    } elseif ($detail === 'title' && isset($subpage['title'])) {
                        return __($subpage['title'], 'text-domain');
                    }
                }

                // Register the widget with Elementor
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Jet_Engine_Mega_Menu());
            }
            add_action('elementor/widgets/widgets_registered', 'register_jet_engine_mega_menu');
        }

        // Return null if the index or detail is not found
        return null;
    }
    function get_roles()
    {
        $roles = wp_roles()->roles;
        $role_options = [];
        foreach ($roles as $role => $role_data) {
            if ($role !== 'administrator') { // Exclude administrator role
                $role_options[$role] = esc_html__($role_data['name'], 'textdomain');
            }
        }
        return $role_options;
        // echo print_r(wp_roles()->roles);
        // return '';
    }
    function is_jet_engine_active()
    {
        return function_exists('jet_engine') ? 'yes' : '';  // Return 'yes' if active, else ''
    }
    function is_jet_engine_profile_builder_active()
    {
        if (function_exists('jet_engine')) {
            return jet_engine()->modules->is_module_active('profile-builder') ? 'yes' : '';
        }
        return '';  // Return '' if JetEngine or Profile Builder is not active
    }
    }
}
