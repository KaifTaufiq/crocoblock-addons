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

if (! class_exists('CallBack_CCT_Data_with_ID')) {

    /**
     * Define CallBack_CCT_Data_with_ID
     */
    class CallBack_CCT_Data_with_ID extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'callback-cct-data-by-id';
        }
        public function addon_name()
        {
            return __('CallBack CCT Data by ID', 'crocoblock-addons');
        }
        public function addon_init()
        {
            add_action('crocoblock-addons/init', array($this,'create_instance'));
        }

        public function addon_type()
        {
            return 'callback';
        }

        public function create_instance($crocoblock_addon){
            require $crocoblock_addon->addons->addons_path( 'callback-cct-data-by-id/includes/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\CallBack_CCT_Data_with_ID\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">CallBack CCT Data by ID</div>
            <p>After activation, the CallBack CCT Data by ID will be added to the Dynamic Feild Widget.</p>';
        }

        public function get_addon_links()
        {
            return array();
        }
    }
}