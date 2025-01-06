<?php
namespace CrocoblockAddons\Addons\CustomRoles;

class Settings
{
    public $nonce_key = 'custom_roles_nonce';

    public function __construct()
    {
        add_action('jet-engine/dashboard/tabs', [$this, 'settings_tabs'], 999);
        add_action('jet-engine/dashboard/assets', [$this, 'settings_js']);
        add_action('wp_ajax_crocoblock_addons_custom_role_save', [$this, 'save_custom_role']);
        add_action('wp_ajax_crocoblock_addons_custom_role_delete', [$this, 'delete_custom_role']);
    }

    public function save_custom_role() {

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
        if ($result) {
            wp_send_json_success(['item_id' => $item_id, 'message' => __($result, 'crocoblock-addons')]);
        } else {
            wp_send_json_error(['message' => __($result, 'crocoblock-addons')]);
        }
    }

    public function delete_custom_role(){
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

        $result = Addon::instance()->delete_role($item_id);
        if ($result) {
            wp_send_json_success(['message' => __($result, 'crocoblock-addons')]);
        } else {
            wp_send_json_error(['message' => __($result, 'crocoblock-addons')]);
        }
    }

    

    public function settings_js(){
        wp_enqueue_script(
            'crocoblock-addons-custom-roles',
            Addon::instance()->addon_assets_url('settings.js'),
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
            true
        );
        wp_localize_script(
            'crocoblock-addons-custom-roles',
            'CrocoBlockCustomRolesSettings',
            [
                'items'=> Addon::instance()->get_listings(),
                'dropdown_options' => Addon::instance()->get_available_roles(),
                '_nonce' => wp_create_nonce($this->nonce_key),
                'save_label' => __('Save', 'crocoblock-addons'),
                'saving_label' => __('Saving...', 'crocoblock-addons'),
				
            ],
        );
        add_action('admin_footer', [$this, 'print_templates']);
    }


    public function print_templates() {
        ?>
        <script type="text/x-template" id="crocoblock-addons-custom-roles">
            <?php require_once Addon::instance()->addon_assets_path('setting.php'); ?>
        </script>
        <script type="text/x-template" id="crocoblock-addons-custom-roles-item">
            <?php require_once Addon::instance()->addon_assets_path('repeater-item.php'); ?>
        </script>
        <?php
    }

    public function settings_tabs()
    {
?>
        <cx-vui-tabs-panel
            name="custom_roles"
            label="<?php _e('Custom Roles', 'crocoblock-addons'); ?>"
            key="custom_roles">
            <keep-alive>
                <crocoblock-addons-custom-roles></crocoblock-addons-custom-roles>
            </keep-alive>
        </cx-vui-tabs-panel>
<?php
    }
}
