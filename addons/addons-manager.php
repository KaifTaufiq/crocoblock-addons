<?php

namespace CrocoblockAddons;
/**
 * Addons manager
 */

if (! defined('WPINC')) {
    die;
}

if (! class_exists('AddonManager')) {

    /**
     * Define AddonManager class
     */
    class AddonManager
    {
        public  $option_name    = 'crocoblock_addons_active_addon';
        private $addons        = array();
        private $active_addons = array();

        /**
         * Constructor for the class
         */
        public function __construct()
        {
            $this->preload_addons();
            $this->init_active_addons();
            
            add_action('wp_ajax_crocoblock_addons_save_addons', array($this, 'save_addons'));
        }

        /**
         * Save active Addons
         *
         * @return [type] [description]
         */
        public function save_addons()
        {
            $nonce_action = crocoblock_addon()->dashboard->get_nonce_action();

            if (empty($_REQUEST['_nonce']) || ! wp_verify_nonce($_REQUEST['_nonce'], $nonce_action)) {
                wp_send_json_error(array(
                    'message' => __('Nonce validation failed', 'jet-engine'),
                ));
            }

            if (! current_user_can('manage_options')) {
                wp_send_json_error(array(
                    'message' => 'You don\'t have permissions to do this',
                ));
            }

            $reload = false;
            $current = get_option($this->option_name, array());
            $new = isset($_REQUEST['addons']) ? $_REQUEST['addons'] : array();
            $activated = array_diff($new, $current);
            $deactivated = array_diff($current, $new);
            $reload_addons = [
                'advanced-rest-api',
            ];

            foreach ($reload_addons as $addon) {
                if (in_array($addon, $activated)  || in_array($addon, $deactivated)) {
                    $reload = true;
                }
            }

            update_option($this->option_name, $new);
            wp_send_json_success(['reload' => $reload]);
        }

        /**
         * Preload Addons
         *
         * @return void
         */
        public function preload_addons()
        {
            $path  = crocoblock_addon()->plugin_path('addons/');

            $all_addons = apply_filters('crocoblock_addons/available-addons', array(
                'AdvancedRestAPI' => $path . 'advanced-rest-api/advanced-rest-api.php',
                'SingleListing' => $path .'single-listing/single-listing.php',
                'SubQuery' => $path . 'sub-query/sub-query.php',
                'MegaMenuBuilder' => $path . 'mega-menu-builder/mega-menu-builder.php',
                'CallBack_TextFormatting' => $path . 'callback-text-formatting/callback-text-formatting.php',
                'CallBack_CCT_Data_with_ID' => $path . 'callback-cct-data-by-id/callback-cct-data-by-id.php',
            ));

            // Load the Base Classes
            require_once crocoblock_addon()->plugin_path('base/base-addon.php');
            require_once crocoblock_addon()->plugin_path('base/active-addon.php');

            foreach ($all_addons as $addon => $file) {
                require $file;
                $format = "\CrocoblockAddons\Addons\\" . $addon; // namespace concatenation
                $instance = new $format();
                $this->addons[$instance->addon_id()] = $instance;
            }
        }

        /**
         * Get all addons list in format required for JS
         *
         * @return [type] [description]
         */
        public function get_all_addons_for_js($extra_data = false, $type = false)
        {

            FIXME: // Check if this is needed
            $result = array();

            foreach ($this->addons as $addon) {
                $addon_data = [
                    'value' => $addon->addon_id(),
                    'label' => $addon->addon_name(),
                    'embed' => $addon->get_video_embed(),
                    'isElementor' => $addon->support_elementor(),
                    'isBricks' => $addon->support_bricks(),
                    'isBlocks' => $addon->support_blocks(),
                ];

                if ($extra_data) {
                    $addon_data['details'] = $addon->get_addon_details();
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

        /**
         * 
         * Get addon links
         * 
         * @param  [type] $addon [description]
         * @return [type]        [description]
         */
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
                        'utm_medium'   => $addon->addon_id(),
                    ), $link['url']);
                }

                $result[] = $link;
            }

            return $result;
        }

        /**
         * Initialize active Addons
         *
         * @return void
         */
        public function init_active_addons()
        {
            $addons = $this->get_active_addons();
            if (empty($addons)) {
                return;
            }
            foreach ($addons as $addon) {
                $this->init_addon($addon);
            }
        }

        /**
         * Get active Addons list
         *
         * @return [type] [description]
         */
        public function get_active_addons()
        {
            $active_addons = get_option($this->option_name, array());
            return $active_addons;
        }

        /**
         * Initialize addon by slug
         *
         * @param  string $addon Addon slug to init.
         * @return void
         */
        public function init_addon($addon)
        {
            $addon_instance = $this->get_addon($addon);
            if ($addon_instance) {
                call_user_func(array($addon_instance, 'addon_init'));
                $this->active_addons[] = $addon;
            }
        }

        /**
         * Get addon instance by addon ID
         *
         * @param  [type] $addon_id [description]
         * @return [type]           [description]
         */
        public function get_addon($addon_id)
        {
            return isset($this->addons[$addon_id]) ? $this->addons[$addon_id] : false;
        }

        /**
         * Returns path to file inside addons dir
         *
         * @param  [type] $path [description]
         * @return [type]       [description]
         */
        public function addons_path($path)
        {
            return crocoblock_addon()->plugin_path('addons/' . $path);
        }

        /**
         * Returns url to file inside modules dir
         *
         * @param  [type] $path [description]
         * @return [type]       [description]
         */
        public function addons_url($path)
        {
            return crocoblock_addon()->plugin_url('addons/' . $path);
        }
    }
}
