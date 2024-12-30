<?php

if (!defined('WPINC')) {
    die;
}

if (!class_exists('CrocoBlockAddonsDashboard')) {
    class CrocoblockAddonsDashboard
    {
        public $nonce_action = 'crocoblock-addons-settings';

        public function __construct()
        {
            add_action('jet-engine/dashboard/tabs/after-skins', [$this, 'settings_tab']);
            add_action('jet-engine/dashboard/assets', [$this, 'settings_assets']);
        }

        public function get_nonce_action()
        {
            return $this->nonce_action;
        }

        public function settings_tab()
        {
        ?>
            <cx-vui-tabs-panel name="crocoblock_addons" label="<?php _e('Crocblock Addons', 'crocoblock-addons'); ?>"
                key="crocoblock_addons">
                <keep-alive>
                    <crocoblock-addons-settings></crocoblock-addons-settings>
                </keep-alive>
            </cx-vui-tabs-panel>
        <?php
        }

        public function settings_assets()
        {
            wp_enqueue_script(
                'crocoblock-addons-settings',
                CrocoBlockAddons()->plugin_url( 'dashboard/assets/js/dashboard.js' ),
                ['cx-vue-ui'],
                CrocoBlockAddons()->get_version(),
                true
            );
            $addons = []; TODO: // Get addons list

            wp_localize_script(
                'crocoblock-addons-settings',
                'CrocoClockAddonsSettings',
                [
                    '_nonce' => wp_create_nonce($this->nonce_action),
                    'messages'          => array(
                    'saved'            => __('Saved!', 'crocoblock-addons'),
                    'saved_and_reload' => __('Saved! One of activated/deactivated addon requires page reloading. Page will be reloaded automatically in few seconds.', 'crocoblock-addons'),
                    ),
                    'addons' => $addons,
                    'active_addons' => [],
                ]
            );
        }
    }
}
