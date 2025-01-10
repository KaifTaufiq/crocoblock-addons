<?php
namespace CrocoblockAddons;

class Migration{

    public $nonce_key = 'crocoblock-addons-migration';

    public function __construct(){
        $option_version = get_option('cba-version', '');
        if( (empty($option_version)) ) {
            $this->run_once_migration();
        } elseif( $option_version == '1.0.1') {
            add_action('jet-engine/dashboard/assets', [$this, 'settings_migration']);
            add_action('wp_ajax_crocoblock_addons_migration', [$this, 'start_migration']);
            add_action('admin_menu', [$this,'register_page']);
        }
    }

    public function run_once_migration() {
        $single_rest_api = get_option('cba-single-rest-api', []);
        $addon_features = get_option('crocoblock_addon_features', []);
        
        // Perform migrations
        $this->migrate_advanced_rest_api($single_rest_api);
        $this->migrate_addon_features($addon_features);

        // Update version and clean up options
        update_option('cba-version', '1.0.1');
        delete_option('cba-single-rest-api');
        delete_option('crocoblock_addon_features');
    }

    private function migrate_advanced_rest_api($single_rest_api) {
        if(!$single_rest_api) {
            return;
        }

        $new_advanced_rest_api = [];
        foreach ($single_rest_api as $id => $value) {
            if ($value['status'] === "on") {
                $query_parameter = [
                    [
                        'key' => 'replace',
                        'from' => 'query_var',
                        'query_var' => $value['custom_key'] ?: 'id',
                        'shortcode' => '',
                        'debugShortcode' => false,
                    ]
                ];
                $new_advanced_rest_api[$id] = [
                    'isSingle' => "true",
                    'query_parameters' => $query_parameter,
                ];
            }
        }

        update_option('cba-advanced-rest-api', $new_advanced_rest_api);
    }

    private function migrate_addon_features($addon_features) {
        if(!$addon_features) {
            return;
        }
        $new_active_addons = [];
        foreach ($addon_features as $name => $status) {
            if ($status === "on") {
                $new_active_addons[] = ($name === "single-rest-api") ? "advanced-rest-api" : $name;
            }
        }
        update_option("crocoblock_addons_active_addon", $new_active_addons);
    }

    public function start_migration(){
        if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

        update_option('cba-version', '1.1');
        wp_send_json_success('Migration Completed');
    }

    public function settings_migration() {
        wp_enqueue_script(
            'crocoblock-addons-migration',
            crocoblock_addon()->plugin_url( 'dashboard/assets/js/migration.js' ),
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
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

    public function register_page(){
        add_menu_page(
            'Crocoblock Addons',            // Page title
            'Crocoblock Addons',            // Menu title
            'manage_options',               // Capability
            'crocoblock-addons',            // Menu slug
            [$this,'crocoblock_addons_page_html'],  // Callback function
            'dashicons-admin-generic',      // Icon
            59.1                              // Position
        );
    }

    public function crocoblock_addons_page_html()
    {
        ?>
        <div class="dashboard">
            <h1 class="header-title">Crocoblock Addons Dashboard</h1>
            <div id="content-container">
                <div id="modules" class="content visible">
                    <h1>Migration Needed</h1>
                    <p class="paragraph">We have detected that you have used Crocoblock Addons before. We need to migrate your data to the new version. Please click the button below to start the migration process.</p>
                    <h4 class="paragraph">From Now on You can Access Crcoblock Addon Dashbaord within Jet Engine Dashbaord.</h4>
                    <p class="paragraph">Single Rest API is now Advannced Rest API.</p>
                    <p class="paragraph">Wordpress Dashaboard > Jet Engine > Jet Engine > Crocblock Addons <a href="<?php echo esc_url( admin_url( 'admin.php?page=jet-engine#crocoblock_addons' ) ); ?>">Link Here</a></p>
                    <form method="post" action="">
                        <p class="submit"><button type="button" id="modules_form_submit" class="button button-primary">Update Database</button></p>
                    </form>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                $('#modules_form_submit').on('click', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: window.ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'crocoblock_addons_migration',
                            nonce: '<?php echo esc_js(wp_create_nonce($this->nonce_key)); ?>',
                        },
                        success: function (response) {
                            if (response.success) {
                                var domain = window.location.origin;
                                var redirectUrl = domain + '/wp-admin/admin.php?page=jet-engine#crocoblock_addons';
                                window.location.href = redirectUrl;
                            } else {
                                alert('Failed to update features: ' + response.data);
                            }
                        },
                        error: function () {
                            alert('AJAX request failed.');
                        }
                    });
                });
            });
        </script>
        <style>
        .dashboard {
            margin: 10px 20px 0 2px;
            border-radius: 6px;
            box-shadow: 0px 2px 6px rgba(35, 40, 45, 0.07);
            /* display: inline-block; */
        }

        .header-title {
            font-size: 24px;
            font-weight: 500;
            line-height: 37px;
            padding: 0 0 20px;
            margin: 0;
            color: #232820;
        }
        p.submit {
            margin-top: 0px;
            padding: 0px;
            box-shadow: none;
            color: white;
            line-height: 19px;
            font-size: 13px;
            display: inline-block;
            background-color: #066EA2;
            font-weight: 500;
            box-shadow: 0px 4px 4px rgba(35, 40, 45, 0.24);
            border-radius: 4px;
        }

        .feature-title {
            color: #007CBA;
            margin: 0 10px 0 0;
            font-size: 15px;
            line-height: 17px;
            font-weight: 500;
        }

        .content {
            border-left: 1px solid #DCDCDD;
            background-color: white;
            padding: 30px;
            margin-left: -1px;
        }

        .paragraph {
            margin-bottom: 10px;
            margin-top: 0px;
        }
        </style>
        <?php
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