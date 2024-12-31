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
