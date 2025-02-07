<?php

namespace CrocoblockAddons\Addons\AdvancedRestApi;

use CrocoblockAddons\Addons\AdvancedRestApi\Settings;
use CrocoblockAddons\Addons\AdvancedRestApi\Manager;
use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'advanced-rest-api';

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
        if ( !jet_engine()->modules->is_module_active('rest-api-listings') ) {
            return;
        }
        require_once $this->addon_includes_path('settings.php');
        require_once $this->addon_includes_path('manager.php');
        $this->settings = new Settings();
        $this->manager = new Manager();
    }
    
    public function update_single_item($item_id, $item) {
    
        $settings = $this->get_setting();

        $query_parameters = isset($item['query_parameters']) && is_array($item['query_parameters']) ? $item['query_parameters'] : [];
        $settings[$item_id] = [
            'isSingle' => isset($item['isSingle']) ? filter_var($item['isSingle'], FILTER_VALIDATE_BOOLEAN) : false,
            'isPOST' => isset($item['isPOST']) ? filter_var($item['isPOST'], FILTER_VALIDATE_BOOLEAN) : false,
            'query_parameters' => array_map(function($param) {
                return [
                    'key' => isset($param['key']) ? sanitize_text_field($param['key']) : '',
                    'from' => isset($param['from']) ? sanitize_text_field($param['from']) : '',
                    'query_var' => isset($param['query_var']) ? sanitize_text_field($param['query_var']) : '',
                    'shortcode' => isset($param['shortcode']) ? sanitize_text_field($param['shortcode']) : '',
                    'debugShortcode' => isset($param['debugShortcode']) ? sanitize_text_field($param['debugShortcode']) : false,
                ];
            }, $query_parameters),
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
