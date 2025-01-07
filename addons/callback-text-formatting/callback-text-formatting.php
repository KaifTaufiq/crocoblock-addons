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

if (! class_exists('CallBack_TextFormatting')) {

    /**
     * Define CallBack_TextFormatting
     */
    class CallBack_TextFormatting extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'callback-text-formatting';
        }
        public function addon_name()
        {
            return __('CallBack Text Formatting', 'crocoblock-addons');
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
            require $crocoblock_addon->addons->addons_path( 'callback-text-formatting/includes/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\CallBack_TextFormatting\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">CallBack Text Formatting</div>
            <p>After activation, the CallBack Text Formatting will be added to the Dynamic Feild Widget.</p>';
        }

        public function get_addon_links()
        {
            return array();
        }
    }
}