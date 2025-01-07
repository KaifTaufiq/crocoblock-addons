<?php
namespace CrocoblockAddons;
if (!defined('WPINC')) {
    die;
}

use CrocoblockAddons\Migration;

if (!class_exists('Dashboard')) {
    class Dashboard
    {
        public $nonce_action = 'crocoblock-addons-settings';

        public function __construct()
        {
            add_action('jet-engine/dashboard/tabs/after-skins', [$this, 'settings_tab']);
            $migration_check = get_option('cba-migration');
            if ( $migration_check == "1"){
                require crocoblock_addon()->plugin_path('dashboard/migration.php');
                new Migration();
            } else {
                add_action('jet-engine/dashboard/assets', [$this, 'settings_assets']);
            }
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
                <?php if (get_option('cba-migration') == "1") : ?>
                    <crocoblock-addons-migration></crocoblock-addons-migration>
                <?php else : ?>
                    <crocoblock-addons-settings></crocoblock-addons-settings>
                <?php endif; ?>
                </keep-alive>
            </cx-vui-tabs-panel>
        <?php
        }

        public function settings_assets()
        {
            wp_enqueue_script(
                'crocoblock-addons-settings',
                crocoblock_addon()->plugin_url( 'dashboard/assets/js/dashboard.js' ),
                ['cx-vue-ui'],
                crocoblock_addon()->get_version(),
                true
            );

            wp_localize_script(
                'crocoblock-addons-settings',
                'CrocoClockAddonsSettings',
                [
                    'addons' => crocoblock_addon()->addons->get_all_addons_for_js(true, 'addon'),
                    'modules' => crocoblock_addon()->addons->get_all_addons_for_js(true, 'module'),
                    'callbacks' => crocoblock_addon()->addons->get_all_addons_for_js(true, 'callback'),
                    'active_addons' => crocoblock_addon()->addons->get_active_addons(),
                    '_nonce' => wp_create_nonce($this->nonce_action),
                    'messages'          => array(
                    'saved'            => __('Saved!', 'crocoblock-addons'),
                    'saved_and_reload' => __('Saved! One of activated/deactivated addon requires page reloading. Page will be reloaded automatically in few seconds.', 'crocoblock-addons'),
                    ),
                ]
            );

            add_action('admin_footer', [$this, 'print_templates']);
        }

        public function print_templates(){
            ?>
            <script type="text/x-template" id="crocoblock-addons-settings">
                <?php include crocoblock_addon()->plugin_path('dashboard/assets/dashboard-template.php'); ?>
            </script>
            <?php
        }
    }
}
