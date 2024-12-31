<?php
namespace CrocoblockAddons\Addons\SingleRestApi;

class Settings
{
    public $nonce_key = 'single_rest_api_nonce';

    public function __construct()
    {
        add_action('jet-engine/dashboard/tabs', [$this, 'settings_tabs']);
        add_action('jet-engine/dashboard/assets', [$this, 'settings_js']);
        //add_action('wp_ajax_single_rest_api', [$this, 'save_single_api']);
    }

    public function settings_js(){
        wp_enqueue_script(
            'crocoblock-addons-single-rest-api',
            Addon::instance()->module_url('assets/js/settings.js'),
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
            true
        );

        $listings = $this->get_listings();

        wp_localize_script(
            'crocoblock-addons-single-rest-api',
            'CrocoBlockSingleRestApiSettings',
            [
                'listings'=> $listings,
                '_nonce' => wp_create_nonce($this->nonce_key),
            ],
            ['cx-vue-ui'],
            crocoblock_addon()->get_version(),
            true
        );
    }

    public function get_listings(){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table_name = $prefix . 'jet_post_types';

        $listings = $wpdb->get_results(
            "SELECT * FROM {$table_name} WHERE status = 'rest-api-endpoint'"
        );

        foreach ($listings as $listing) {
            if (property_exists($listing, 'labels') && is_string($listing->labels)) {
                $listing->labels = unserialize($listing->labels);
            }

            if (property_exists($listing, 'args') && is_string($listing->args)) {
                $listing->args = unserialize($listing->args);
            }
        }

        return $listings;
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
