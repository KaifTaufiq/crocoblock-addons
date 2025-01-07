<?php

/**
 * Custom Roles Addon
 */
namespace CrocoblockAddons\Addons;
use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('CustomRoles')) {

    /**
     * Define CustomRoles
     */
    class CustomRoles extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'custom-roles';
        }
        public function addon_name()
        {
            return __('Custom Roles', 'crocoblock-addons');
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
            require $crocoblock_addon->addons->addons_path( 'custom-roles/includes/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\CustomRoles\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">Custom Roles</div>';
        }
    }
}