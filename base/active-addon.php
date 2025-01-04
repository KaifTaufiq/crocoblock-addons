<?php
namespace CrocoblockAddons\Base;
/**
 * Base class for Active Addon
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('ActiveAddon')) {

    /**
     * Define Addon class
     */
    class ActiveAddon
    {
        private static $instance;

        public $slug;

        public $elementor_integration = null;

        public $elementor_widgets = array();

        public $blocks_integration = null;
        
        public $bricks_integration = null;

        public $bricks_widgets = array();


        public $settings = null;

        public $manager = null;

        public function __construct()
        {
            
        }

        /**
         * Constructor for the class
         */
        public function init() {
            
        }

        public function get_setting(){
            $option = get_option('cba-' . $this->slug, []);
            $unserialized = maybe_unserialize($option);
            return is_array($unserialized) ? $unserialized : [];
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
        public function addon_includes_path($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_path($this->slug . '/includes/' . $relative_path);
        }

        /**
         * Return url inside addon
         *
         * @param  string $relative_path [description]
         * @return [type]                [description]
         */
        public function addon_includes_url($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_url($this->slug . '/includes/' . $relative_path);
        }

        /**
         * Return path of assets inside addon
         *
         * @param  string $relative_path [description]
         * @return [type]                [description]
         */
        public function addon_assets_path($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_path($this->slug . '/assets/' . $relative_path);
        }

        /**
         * Return url of assets inside addon
         *
         * @param  string $relative_path [description]
         * @return [type]                [description]
         */
        public function addon_assets_url($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_url($this->slug . '/assets/' . $relative_path);
        }

        /**
         * Return path of widgets inside addon
         *
         * @param  string $relative_path [description]
         * @return [type]                [description]
         */
        public function addon_widgets_path($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_path($this->slug . '/widgets/' . $relative_path);
        }

        /**
         * Return url of widgets inside addon
         *
         * @param  string $relative_path [description]
         * @return [type]                [description]
         */
        public function addon_widgets_url($relative_path = '')
        {
            return crocoblock_addon()->addons->addons_url($this->slug . '/widgets/' . $relative_path);
        }

        public function register_elementor_widget($widgets_manager) {
            foreach($this->elementor_widgets as $widget) {
                $path = $this->addon_widgets_path($widget['file']);
                $class = $widget['class'];
                if( file_exists($path) ) {
                    require_once $path;
                    if( class_exists($class) ) {
                        $widgets_manager->register_widget_type(new $class());
                    }
                }
            }
        }
        
        public function register_bricks_widget() {
            foreach($this->bricks_widgets as $filename) {
                $path = $this->addon_widgets_path($filename);
                if( file_exists($path) ) {
                    if (class_exists('\Bricks\Elements')) {
						\Bricks\Elements::register_element($path);
					}
                }
            }
        }
        
        public static function instance()
        {
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }
    }
}