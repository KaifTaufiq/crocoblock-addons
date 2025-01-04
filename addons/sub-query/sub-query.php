<?php

/**
 * Sub Query Addon
 */
namespace CrocoblockAddons\Addons;
use CrocoblockAddons\Base\Addon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (! class_exists('AdvancedRestAPI')) {

    /**
     * Define AdvancedRestAPI
     */
    class SubQuery extends Addon
    {

        public $instance = null;

        public function addon_id()
        {
            return 'sub-query';
        }
        public function addon_name()
        {
            return __('Sub Query', 'crocoblock-addons');
        }
        public function addon_init()
        {
            add_action('crocoblock-addons/init', array($this,'create_instance'));
        }

        public function create_instance($crocoblock_addon){
            require $crocoblock_addon->addons->addons_path( 'sub-query/inc/addon.php' );
            $this->instance = \CrocoblockAddons\Addons\SubQuery\Addon::instance();
        }

        public function get_addon_details()
        {
            return '<div class="jet-engine-links__title">From Crocblock Dev Tools</div>
            <p>After activation, a new custom query in WordPress Dashboard > JetEngine > Query Builder will be added as Sub Query</p>
            <p>The query is intended to work with nested listings. That means the Listing Template that will be later built based on this query will be placed inside the main Listing Template. The Sub Query retrieves data from the parent object property</p>';
        }

        public function get_addon_links()
        {
            return array(
				array(
					'label' => __('Documentation', 'crocoblock-addons'),
					'url'=> 'https://crocoblock.com/knowledge-base/jetengine/how-to-display-array-elements-from-a-rest-api-endpoint-using-the-sub-query-addon/#set-up-a-sub-query-to-retrieve-the-array-of-objects',
				),
                array(
                    'label' => __('Crocblock Dev Tools', 'crocoblock-addons'),
                    'url' => 'https://crocoblock.com/freemium/tools/',
                ),
                array(
                    'label' => __('Gtihub Repo', 'crocoblock-addons'),
                    'url' => 'https://github.com/Crocoblock/jet-engine-sub-query',
                ),
                array(
                    'label' => __('MjHead (Andrew Shevchenko) Contributor', 'crocoblock-addons'),
                    'url' => 'https://github.com/MjHead',
                ),
                array(
                    'label' => __('ihslimn (Mykhailo Kulinich) Contributor', 'crocoblock-addons'),
                    'url' => 'https://github.com/ihslimn',
                ),
            );
        }
    }
}
