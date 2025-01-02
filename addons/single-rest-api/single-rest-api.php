<?php

/**
 * Single Rest API Addon
 */

use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('Addon_Single_Rest_API')) {

    /**
     * Define SingleRestApi class
     */
    class Addon_Single_Rest_API extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'single-rest-api';
        }
        public function addon_name()
        {
            return __('Single Rest API', 'crocoblock-addons');
        }
        public function addon_init()
        {
            add_action('crocoblock-addons/init', array($this,'create_instance'));
        }

        public function create_instance($crocoblock_addon){
            require $crocoblock_addon->addons->addons_path( 'single-rest-api/inc/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\SingleRestApi\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<p>Single Rest API Details</p>';
        }

        public function get_addon_links()
        {
            return array(
                array(
                    'label' => 'How to Display Custom Content Type Items Using REST API',
                    'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-display-custom-content-type-items-using-rest-api/',
                ),
                array(
                    'label' => 'How to Add and Edit CCT Items Remotely Using REST API',
                    'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-add-and-edit-cct-items-remotely-using-rest-api/',
                ),
            );
        }
    }
}
