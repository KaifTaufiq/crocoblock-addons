<?php
namespace CrocoblockAddons;

class Migration{

    public $nonce_key = 'crocoblock-addons-migration';

    public function __construct(){
        add_action('jet-engine/dashboard/assets', [$this, 'settings_migration']);
        add_action('wp_ajax_crocoblock_addons_migration', [$this, 'start_migration']);
    }

    public function start_migration(){
        if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}
        $single_rest_api = get_option('cba-single-rest-api');
    	$addon_features = get_option('crocoblock_addon_features');

        $new_active_addons = [];
        $new_advanced_rest_api = [];

        if ( $single_rest_api ) {
            foreach ( $single_rest_api as $id => $value) {
                if( $value['status'] == "on" ) {
                    $query_parameter = [
                        [
                            'key' => 'replace',
                            'from' => 'query_var',
                            'query_var' => 'id',
                            'shortcode' => '',
                            'debugShortcode' => false,
                        ]
                    ];
                    if( $value['custom_key'] != "" ){
                        $query_parameter[0]['query_var'] = $value['custom_key'];
                    }
                    $new_advanced_rest_api[$id] = [
                        'isSingle' => "true",
                        'query_parameters' => $query_parameter
                    ];
                }
            }
            update_option('cba-advanced-rest-api', $new_advanced_rest_api);
        }
        if( $addon_features ) {
            foreach ( $addon_features as $name => $status) {
                if($status === "on") {
                    if($name === "single-rest-api") {
                        $new_active_addons[] = "advanced-rest-api";
                    } else {
                        $new_active_addons[] = $name;
                    }
                }
            }
            update_option("crocoblock_addons_active_addon", $new_active_addons);
        }
        delete_option('cba-migration');
        delete_option('cba-single-rest-api');
        delete_option('crocoblock_addon_features');
        wp_send_json_success('Migration Completed');
    }

    public function settings_migration() {
        wp_enqueue_script(
            'crocoblock-addons-migration',
            crocoblock_addon()->plugin_url( 'dashboard/assets/js/migration.js' ),
            ['cx-vue-ui'],
            '1.1212.1',
            true
        );

        wp_localize_script(
            'crocoblock-addons-migration',
            'CrocoblockAddonsMigration',
            [
                '_nonce' => wp_create_nonce($this->nonce_key),
                'title' => __('Migration Needed', 'crocoblock-addons'),
                'description' => __('We have detected that you have used Crocoblock Addons before. We need to migrate your data to the new version. Please click the button below to start the migration process.', 'crocoblock-addons'),
                'save_label' => __('Update Database', 'crocoblock-addons'),
                'saving_label' => __('Database Updated,', 'crocoblock-addons'),
            ]
        );

        add_action('admin_footer', [$this, 'print_migration_templates']);
    }

    public function print_migration_templates() {
        ?>
            <script type="text/x-template" id="crocoblock-addons-migration">
                <div class="cx-vui-inner-panel">
                    <div class="cx-vui-component-wrapper">
                        <div class="cx-vui-component">
                            <div class="cx-vui-subtitle">{{ title }}</div>
                        </div>
                        <div class="cx-vui-component">
                            <div class="jet-engine-skins__header-desc">{{ description }}</div>
                        </div>
                        <div class="cx-vui-component">
                        <cx-vui-button button-style="accent" size="mini" @click="migrateDatabase" :loading="saving" :disabled="isDisabled()">
                                <span slot="label">{{ buttonLabel() }}</span>
                            </cx-vui-button>
                        </div>
                    </div>
                </div>
            </script>
        <?php
    }
}