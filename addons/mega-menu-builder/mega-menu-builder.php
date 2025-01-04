<?php

/**
 * Mega Menu Builder Addon
 */
namespace CrocoblockAddons\Addons;
use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('MegaMenuBuilder')) {

    /**
     * Define MegaMenuBuilder
     */
    class MegaMenuBuilder extends Addon
    {
        public $instance = null;

        public function support_bricks()
        {
            return false;
        }

        public function addon_id()
        {
            return 'mega-menu-builder';
        }
        public function addon_name()
        {
            return __('Mega Menu Builder', 'crocoblock-addons');
        }
        public function addon_init()
        {
            add_action('crocoblock-addons/init', array($this,'create_instance'));
        }

        public function create_instance($crocoblock_addon)
        {
            require $crocoblock_addon->addons->addons_path( 'mega-menu-builder/includes/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\MegaMenuBuilder\Addon::instance();
        }  

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">Mega Menu Builder</div>
            <p>After activation, Mega Menu Builder Widget will be added to Elementor or Bricks Widget.</p>';
        }
    }
}    