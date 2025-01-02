<?php
namespace CrocoblockAddons\Addons\SingleRestApi;

class Settings
{
    public $nonce_key = 'single_rest_api_nonce';

    public function __construct()
    {
        add_action('jet-engine/dashboard/tabs', [$this, 'settings_tabs'], 999);
        add_action('jet-engine/dashboard/assets', [$this, 'settings_js']);
        add_action('wp_ajax_crcoblock_save_single_rest_api', [$this, 'save_single_api']);
    }

    public function save_single_api() {
        if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}
        $item = !empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : [];
        $item_id = ! empty( $item['id'] ) ? $item['id'] : false;

        $prev_listing = $this->get_listings($item_id);
        if ( $prev_listing->args['url'] !== $item['url']) {
            $this->update_url($item_id, $prev_listing->args , $item['url']);
        }
        $result = Addon::instance()->update_single_item( $item_id, $item );
        wp_send_json_success($result);
    }

    public function update_url($id,$args, $new_url){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table_name = $prefix . 'jet_post_types';
        $args['url'] = sanitize_url($new_url);
        $query = $wpdb->prepare(
            "UPDATE {$table_name} SET args = %s WHERE id = %d",
            maybe_serialize($args),
            $id
        );
        $wpdb->query($query);
    }

    public function settings_js(){
        wp_enqueue_script(
            'crocoblock-addons-single-rest-api',
            Addon::instance()->addon_url('assets/settings.js'),
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
            true
        );

        $listings = $this->get_listings();
        do_action('qm/info',$listings);

        wp_localize_script(
            'crocoblock-addons-single-rest-api',
            'CrocoBlockSingleRestApiSettings',
            [
                'listings'=> $listings,
                '_nonce' => wp_create_nonce($this->nonce_key),
				'dropdown_options' => [
					[
						'value' => '',
						'label' => __( 'From...', 'crocoblock-addons' ),
					],
					[
						'value' => 'query_var',
						'label' => __( 'Query Variable', 'crocoblock-addons' ),
					],
					[
						'value' => 'shortcode',
						'label' => __( 'Do Shortcode', 'crocoblock-addons' ),
					],
				],
                'save_label' => __('Save', 'crocoblock-addons'),
                'saving_label' => __('Saving...', 'crocoblock-addons'),
            ],
        );
        add_action('admin_footer', [$this, 'print_templates']);
    }

    public function print_templates() {
		require_once crocoblock_addon()->addons->addons_path('single-rest-api/inc/assets/template.php');
    }
    public function get_listings($id = null) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table_name = $prefix . 'jet_post_types';

        if ($id) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $id
            );
            $result = $wpdb->get_row($query);
            
            if ($result) {
                if (property_exists($result, 'labels') && is_string($result->labels)) {
                    $result->labels = unserialize($result->labels);
                }
                if (property_exists($result, 'args') && is_string($result->args)) {
                    $result->args = unserialize($result->args);
                }
                return $result;
            }
            return null;
        }

        $listings = $wpdb->get_results(
            "SELECT * FROM {$table_name} WHERE status = 'rest-api-endpoint'"
        );
        $settings = Addon::instance()->get_setting();
        $formatted_listings = [];

        foreach ($listings as $listing) {
            if (property_exists($listing, 'labels') && is_string($listing->labels)) {
                $listing->labels = unserialize($listing->labels);
            }
            if (property_exists($listing, 'args') && is_string($listing->args)) {
                $listing->args = unserialize($listing->args);
            }
            $formatted_listings[] = [
                'id' => $listing->id,
                'name' => $listing->labels['name'],
                'url' => $listing->args['url'],
                'isSingle' => isset($settings[$listing->id]['isSingle']) ? $settings[$listing->id]['isSingle'] : false,
                'query_parameters' => isset($settings[$listing->id]['query_parameters']) ? $settings[$listing->id]['query_parameters'] : [],
            ];
        }

        return $formatted_listings;
    }

    public function settings_tabs()
    {
?>
        <cx-vui-tabs-panel
            name="single_rest_api"
            label="<?php _e('Single REST API', 'crocoblock-addons'); ?>"
            key="data_stores">
            <keep-alive>
                <crocoblock-addons-single-rest-api></crocoblock-addons-single-rest-api>
            </keep-alive>
        </cx-vui-tabs-panel>
<?php
    }
}
