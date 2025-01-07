<?php

namespace CrocoblockAddons\Addons\CallBack_TextFormatting;

use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'callback-text-formatting';

    public $elementor_integration = null;
    public $blocks_integration = null;
    public $bricks_integration = null;

    public $settings = null;

    public $manager = null;

    /**
     * Constructor for the class
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
    }

    /**
     * Init module components
     *
     * @return [type] [description]
     */
    public function init()
    {
        if ( !function_exists('jet_engine' )) {
            return;
        }
        require_once $this->addon_includes_path('callback_function.php');
        add_filter('jet-engine/listings/allowed-callbacks', [$this,'add_custom_dynamic_field_callbacks']);
        add_filter('jet-engine/listing/dynamic-field/callback-args', [$this,'add_custom_dynamic_field_callbacks_args'], 10, 3);
        add_filter('jet-engine/listings/allowed-callbacks-args', [$this,'add_custom_controls'], 10, 1);
    }

    public function add_custom_dynamic_field_callbacks( $callbacks ) {

        $callbacks['callback_text_formatting'] = __( 'Text (String) Formatting', 'crocoblock-addons' );
        
        return $callbacks;
        
    }
    
    public function add_custom_dynamic_field_callbacks_args($result, $callback, $settings) {
        if ($callback === 'callback_text_formatting') {
            return array(
                $result[0],
                isset($settings['callback_options']) ? $settings['callback_options'] : '',
            );
        }
        return $result;
    }
    
    public function add_custom_controls( $controls ) {
        
        $options = [
            '' => esc_html__('Select ...', 'crocoblock-addons'),
            'capitalize_each_word' => esc_html__('Capitalize Each Word', 'crocoblock-addons'),
            'capitalize_first_letter' => esc_html__('Capitalize first letter', 'crocoblock-addons'),
            'uppercase' => esc_html__('UPPERCASE', 'crocoblock-addons'),
            'lowercase' => esc_html__('lowercase', 'crocoblock-addons'),
            'snake_case' => esc_html__('Snake Case snake_case', 'crocoblock-addons'),
            'kebab_case' => esc_html__('Kebab Case kebab-case', 'crocoblock-addons'),
            'camel_case' => esc_html__('Camel Case camelCase', 'crocoblock-addons'),
            'pascal_case' => esc_html__('Pascal Case PascalCase', 'crocoblock-addons'),
            'title_case' => esc_html__('Title Case', 'crocoblock-addons'),
            'toggle_case' => esc_html__('Toggle Case', 'crocoblock-addons'),
            'sentence_case' => esc_html__('Sentence case', 'crocoblock-addons'),
            'reverse_case' => esc_html__('Reverse Case', 'crocoblock-addons'),
            'trim_whitespace' => esc_html__('Trim Whitespace', 'crocoblock-addons'),
            'remove_special_characters' => esc_html__('Remove Special Characters', 'crocoblock-addons'),
        ];

        $controls['callback_options'] = array(
			'label'       => esc_html__( 'Formatting Options', 'crocoblock-addons' ),
			'type'        => 'select',
			'label_block' => true,
			'description' => esc_html__( 'Select Formatting callback toapply', 'crocoblock-addons' ),
			'default'     => '',
			'options'     => $options,
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array('callback_text_formatting'),
			),
		);
        
        return $controls;
            
    }

    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
//Global callback
