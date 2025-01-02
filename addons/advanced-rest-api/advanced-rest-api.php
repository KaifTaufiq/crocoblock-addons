<?php

/**
 * Advanced Rest API Addon
 */

use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('Addon_Advanced_Rest_API')) {

    /**
     * Define Addon_Advanced_Rest_API
     */
    class Addon_Advanced_Rest_API extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'advanced-rest-api';
        }
        public function addon_name()
        {
            return __('Advanced Rest API', 'crocoblock-addons');
        }
        public function addon_init()
        {
            add_action('crocoblock-addons/init', array($this,'create_instance'));
        }

        public function create_instance($crocoblock_addon){
            require $crocoblock_addon->addons->addons_path( 'advanced-rest-api/inc/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\AdvancedRestApi\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<p>Advanced Rest API Details</p>';
        }

        public function get_addon_links()
        {
            return array();
        }
    }
}