<?php

namespace CrocoblockAddons\Addons\ConditionalFormatting;

use CrocoblockAddons\Addons\ConditionalFormatting\Settings;
// use CrocoblockAddons\Addons\ConditionalFormatting\Manager;
use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'conditional-formatting';

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
        require $this->addon_includes_path('settings.php');
        $this->settings = new Settings();
        require_once $this->addon_includes_path('callback_function.php');
        add_filter('jet-engine/listings/allowed-callbacks', [$this,'add_custom_callbacks']);
        add_filter('jet-engine/listing/dynamic-field/callback-args', [$this,'add_custom_callbacks_args'], 10, 3);
        add_filter('jet-engine/listings/allowed-callbacks-args', [$this,'add_custom_controls'], 10, 1);
    }

    public function add_custom_callbacks($callbacks) {
        $callbacks['callback_conditonal_formatting'] = __('Conditional Formatting', 'crocoblock-addons');
        return $callbacks;
    }
    public function add_custom_callbacks_args($result, $callback, $settings) {
        if ($callback === 'callback_conditonal_formatting') {
            return array(
                $result[0],
                isset($settings['callback_conditonal_option']) ? $settings['callback_conditonal_option'] : '',
            );
        }
        return $result;
    }

    public function add_custom_controls($controls) {
        $settings = $this->get_setting();
        $options = [
            '' => esc_html__('Select', 'crocoblock-addons'),
        ];

        foreach ($settings as $key => $value) {
            if( !empty($value)) {
                $options[$key] = esc_html($value['name'], 'crocoblock-addons');
            }
        }

        $controls['callback_conditonal_option'] = array(
			'label'       => esc_html__( 'Conditional Formatting', 'crocoblock-addons' ),
			'type'        => 'select',
            'label_block' => false,
			'description' => esc_html__( 'Select Conditional Formatting', 'crocoblock-addons' ),
			'default'     => '',
			'options'     => $options,
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array('callback_conditonal_formatting'),
			),
		);
        
        return $controls;
    }
    
    public function update_single_item($item_id, $item) {
    
        $settings = $this->get_setting();

        $conditions = isset($item['conditions']) && is_array($item['conditions']) ? $item['conditions'] : [];
        $settings[$item_id] = [
            'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
            'conditions' => array_map(function($param) {
                return [
                    'from' => isset($param['from']) ? sanitize_text_field($param['from']) : '',
                    'to' => isset($param['to']) ? sanitize_text_field($param['to']) : '',
                ];
            }, $conditions),
        ];
        return $this->update_setting($settings);
    }

    public static function instance()
        {
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }
}
