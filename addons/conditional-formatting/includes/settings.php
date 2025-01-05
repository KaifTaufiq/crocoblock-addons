<?php
namespace CrocoblockAddons\Addons\ConditionalFormatting;

class Settings
{
    public $nonce_key = 'conditional_formatting_nonce';

    public function __construct()
    {
        add_action('jet-engine/dashboard/tabs', [$this, 'settings_tabs'], 999);
        add_action('jet-engine/dashboard/assets', [$this, 'settings_js']);
        add_action('wp_ajax_crocoblock_addons_conditional_formatting_save', [$this, 'save_formatting']);
        add_action('wp_ajax_crocoblock_addons_conditional_formatting_delete', [$this, 'delete_formatting']);
    }

    public function save_formatting() {

        if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

        $item    = ! empty( $_REQUEST['item'] ) ? $_REQUEST['item'] : array();
		$item_id = ! empty( $_REQUEST['item']['id'] ) ? absint( $_REQUEST['item']['id']  ) : false;
        $settings = Addon::instance()->get_setting();

        if ( !$item_id ) {
            $max_id = 0;
            foreach( $settings as $key => $value ) {
                $max_id = max($max_id, $key);
            }
            $item_id = $max_id + 1;
        }
        $result = Addon::instance()->update_single_item( $item_id, $item  );
        wp_send_json_success(['item_id' => $item_id,'message'=> __('New Item Created', 'crocoblock-addons')]);
    }

    public function delete_formatting(){
        if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
		}

		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
		}

		$item_id = ! empty( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : false;

        if ( $item_id < 0 ) {
			wp_send_json_error( array( 'message' => __( 'Item ID not found in the request', 'jet-engine' ) ) );
		}

        $settings = Addon::instance()->get_setting();
        if ( isset( $settings[ $item_id ] ) ) {
            $settings[ $item_id ] = '';
        }
        Addon::instance()->update_setting($settings);
        wp_send_json_success(['message'=> __('Item deleted', 'crocoblock-addons')]);
    }

    public function settings_js(){
        wp_enqueue_script(
            'crocoblock-addons-conditional-formatting',
            Addon::instance()->addon_assets_url('settings.js'),
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
            true
        );

        $listings = $this->get_listings();

        wp_localize_script(
            'crocoblock-addons-conditional-formatting',
            'CrocoBlockConditionalFormattingSettings',
            [
                'listings'=> $listings,
                '_nonce' => wp_create_nonce($this->nonce_key),
                'save_label' => __('Save', 'crocoblock-addons'),
                'saving_label' => __('Saving...', 'crocoblock-addons'),
                'sample_item' => array(
                    'name' => 'Sample Formatting',
                    'conditions' => [
                        [
                            'from' => 'on-hold',
                            'to' => 'On Hold',
                        ],
                        [
                            'from' => 'processing',
                            'to' => 'Processing',
                        ],
                        [
                            'from' => 'completed',
                            'to' => 'Completed',
                        ],
                    ],
                ),
            ],
        );
        add_action('admin_footer', [$this, 'print_templates']);
    }

    public function get_listings($id = null) {

        $settings = Addon::instance()->get_setting();
        if ($id) {
            if( empty($settings[$id]) ) {
                return [];
            }
            return [
                'id' => $id,
                'name' => $settings[$id]['name'],
                'conditions' => $settings[$id]['conditions'],
            ];
        }
        $listings = [];
        foreach( $settings as $key => $value ) {
            if( !empty($value)) {
                $listings[] = [
                    'id'=> $key,
                    'name'=> $value['name'],
                    'conditions'=> $value['conditions'],
                ];
            }
        }
        return $listings;
    }

    public function print_templates() {
        ?>
        <script type="text/x-template" id="crocoblock-addons-conditional-formatting">
            <?php require_once Addon::instance()->addon_assets_path('setting.php'); ?>
        </script>
        <script type="text/x-template" id="crocoblock-addons-conditional-formatting-item">
            <?php require_once Addon::instance()->addon_assets_path('repeater-item.php'); ?>
        </script>
        <?php
    }

    public function settings_tabs()
    {
?>
        <cx-vui-tabs-panel
            name="conditional_formatting"
            label="<?php _e('Conditional Formatting', 'crocoblock-addons'); ?>"
            key="conditional_formatting">
            <keep-alive>
                <crocoblock-addons-conditional-formatting></crocoblock-addons-conditional-formatting>
            </keep-alive>
        </cx-vui-tabs-panel>
<?php
    }
}
