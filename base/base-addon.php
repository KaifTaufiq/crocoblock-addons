<?php
namespace CrocoblockAddons\Base;
/**
 * Base class for Addon
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('Addon')) {

    /**
     * Define Addon class
     */
    abstract class Addon
    {
        /**
         * Addons ID
         *
         * @return string
         */
        abstract public function addon_id();

        /**
         * Addons name
         *
         * @return string
         */
        abstract public function addon_name();

        /**
         * Addons init
         *
         * @return void
         */
        abstract public function addon_init();

        
        /**
         * Addons Type
         *
         * @return string
         */
        abstract public function addon_type();

        /**
         * Returns detailed information about current addon for the dashboard page
         * @return [type] [description]
         */
        public function get_addon_details()
        {
            return '';
        }

        /**
         * Returns Credit for the addon
         * @return [type] [description]
         */
        public function addon_from()
        {
            return '';
        }

        /**
         * Return video embed to showcase addon in the admin area
         */
        public function get_video_embed()
        {
            return '';
        }

        /**
         * Returns array links to the addon-related resources
         *
         * item format: array(
         * 'label'    => 'Link label',
         * 'url'      => 'https://link-url',
         * 'is_video' => true,
         * )
         *
         * @return [type] [description]
         */
        public function get_addon_links()
        {
            return array();
        }

        /**
         * Is addon supports elementor view
         *
         * @return [type] [description]
         */
        public function support_elementor()
        {
            return true;
        }

        /**
         * Is addon supports blocks view
         *
         * @return [type] [description]
         */
        public function support_blocks()
        {
            return false;
        }
        /**
         * Is addon supports Bricks view
         *
         * @return [type] [description]
         */
        public function support_bricks()
        {
            return true;
        }

        /**
         * Returns slug of the addon to install it from the crocoblock.com
         * 
         * @return false or string
         */
        public function external_slug()
        {
            return false;
        }
    }
}
