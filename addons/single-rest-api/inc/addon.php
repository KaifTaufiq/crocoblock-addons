<?php

namespace CrocoblockAddons\Addons\SingleRestApi;

use CrocoblockAddons\Addons\SingleRestApi\Settings;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon
{
    private static $instance = null;

    public $slug = 'single-rest-api';

    public $elementor_integration = null;
    public $blocks_integration = null;
    public $bricks_integration = null;

    public $settings = null;

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
        require_once $this->addon_path('settings.php');
        $this->settings = new Settings();
        require_once $this->addon_path('logic.php');
    }

    public function get_setting(){
        $option = get_option('cba-' . $this->slug, []);
        $unserialized = maybe_unserialize($option);
        return is_array($unserialized) ? $unserialized : [];
    }
    
    public function update_single_item($item_id, $item) {
    
        $settings = $this->get_setting();

        $query_parameters = isset($item['query_parameters']) && is_array($item['query_parameters']) ? $item['query_parameters'] : [];
        $settings[$item_id] = [
            'isSingle' => isset($item['isSingle']) ? sanitize_text_field($item['isSingle']) : false,
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
    
    public function update_setting($new_setting) {
        $serialized = maybe_serialize($new_setting);
        return update_option('cba-' . $this->slug, $serialized, true);
    }

    /**
     * Return path inside addon
     *
     * @param  string $relative_path [description]
     * @return [type]                [description]
     */
    public function addon_path($relative_path = '')
    {
        return crocoblock_addon()->addons->addons_path($this->slug . '/inc/' . $relative_path);
    }

    /**
     * Return url inside addon
     *
     * @param  string $relative_path [description]
     * @return [type]                [description]
     */
    public function addon_url($relative_path = '')
    {
        return crocoblock_addon()->addons->addons_url($this->slug . '/inc/' . $relative_path);
    }


    
    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
