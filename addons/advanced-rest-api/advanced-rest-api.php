<?php

/**
 * Advanced Rest API Addon
 */
namespace CrocoblockAddons\Addons;
use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('AdvancedRestAPI')) {

    /**
     * Define AdvancedRestAPI
     */
    class AdvancedRestAPI extends Addon
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

        public function addon_type()
        {
            return 'module';
        }

        public function create_instance($crocoblock_addon){
            require $crocoblock_addon->addons->addons_path( 'advanced-rest-api/includes/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\AdvancedRestApi\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">Advanced Rest API</div>
            <p>After activation, the Advanced Rest API tab will be added to the JetEngine Settings dashboard.</p>
            <p>You can Add Dynamic Query Variable from to REST API URL, Enable Single Listing Item and POST Request Type</p>';
        }

        public function get_addon_links()
        {
            return array();
        }
    }
}