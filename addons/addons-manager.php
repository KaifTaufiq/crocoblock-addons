<?php

/**
 * Addons manager
 */

if (! defined('WPINC')) {
    die;
}

if (! class_exists('CrocoblockAddonManager')) {

    /**
     * Define CrocoblockAddonManager class
     */
    class CrocoblockAddonManager
    {
        public  $option_name    = 'crocoblock_addons_settings';
        private $addons        = array();
        private $active_addons = array();

        public function __construct()
        {
            $this->preload_addons();
            $this->init_active_addons();
            add_action('wp_ajax_crocoblock_addons_save_addons', array($this, 'save_addons'));
        }

        public function save_addons()
        {
            TODO:
        }

        public function preload_addons()
        {
            $all_addons = apply_filters('crocoblock_addons/available-addons', array());

            // Load the Base Classes
            require CrocoBlockAddons()->plugin_path('base/base-addon.php');

            foreach ($all_addons as $addon => $file) {
                require $file;
                $instance = new $addon;
                $this->addons[$instance->addons_id()] = $instance;
            }
        }

        public function get_all_addons_for_js($extra_data = false, $type = false)
        {

            FIXME: // Check if this is needed
            $result = array();

            foreach ($this->addons as $addon) {
                $addon_data = [
                    'value' => $addon->addons_id(),
                    'label' => $addon->addons_name(),
                    'embed' => $addon->get_video_embed(),
                    'isElementor' => $addon->support_elementor(),
                    'isBricks' => $addon->support_bricks(),
                    'isBlocks' => $addon->support_blocks(),
                ];

                if ($extra_data) {
                    $addon_data['details'] = $addon->get_addons_details();
                    $addon_data['links']   = $this->get_addon_links($addon);
                }
                TODO: // External Addons Setiup
                // if ( $type && 'external' === $type ) {
                // 	$module_data['is_related_plugin_active'] = $module->is_related_plugin_active();
                // 	$module_data['plugin_data']              = $module->get_related_plugin_data();
                // }

                $result[] = $addon_data;
            }
            return $result;
        }

        public function get_addon_links($addon)
        {
            FIXME: // Check if this is needed
            $links  = $addon->get_addon_links();
            $result = array();

            if (empty($links)) {
                return $result;
            }

            foreach ($links as $link) {

                if (empty($link['is_video'])) {
                    $link['url'] = add_query_arg(array(
                        'utm_campaign' => 'need-help',
                        'utm_source'   => 'crocoblock-addons',
                        'utm_medium'   => $addon->addons_id(),
                    ), $link['url']);
                }

                $result[] = $link;
            }

            return $result;
        }

        public function init_active_addons()
        {
            $addons = $this->get_active_addons();

            if (empty($addons)) {
                return;
            }
            /**
             * Check if is new modules format or old
             */
            if (! isset($addons['gallery-grid'])) {

                $fixed = array();

                foreach ($addons as $addon) {
                    $fixed[$addon] = 'true';
                }

                $addons = $fixed;
            }
            foreach ($addons as $addon => $is_active) {
                if ('true' === $is_active) {
                    $this->init_addon($addon);
                }
            }
        }

        public function get_active_addons()
        {
            $active_addons = get_option($this->option_name, array());
            return array_values($active_addons);
        }

        public function init_addon($addon)
        {
            $addon_instance = $this->get_addon($addon);
            if ($addon_instance) {
                call_user_func(array($addon_instance, 'addons_init'));
                $this->active_addons[] = $addon;
            }
        }

        public function get_addon($addon_id)
        {
            return isset($this->addons[$addon_id]) ? $this->addons[$addon_id] : false;
        }
    }
}
