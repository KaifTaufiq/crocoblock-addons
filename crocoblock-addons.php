<?php

/**
 * Plugin Name: Crocoblock Addons
 * Plugin URI:  https://whitehatdevs.com/community/crocoblock-addons/
 * Description: A plugin to Power up your Crocoblock plugins with additional features.
 * Version: 1.0.1
 * Author: Kaif Shaikh (White Hat Devs)
 * Author URI: https://whitehatdevs.com
 * Text Domain: crocoblock-addons
 */

use CrocoblockAddons\Dashboard;
use CrocoblockAddons\AddonManager;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die();
}

// the main Crocoblock Addons class.
if (! class_exists('CrocoblockAddons')) {
	class CrocoblockAddons
	{
		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.1
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.1
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '1.1';

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.1
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Plugin base name
		 *
		 * @var string
		 */
		public $plugin_name = null;

		/**
		 * Dashboard instance
		 *
		 * @var \CrocoblockAddons\Dashboard
		 */
		public $dashboard;

		/**
		 * Addon Manager instance
		 * @var \CrocoblockAddons\AddonManager
		 */
		public $addons;


		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.1
		 * @access public
		 * @return void
		 */
		public function __construct()
		{
			$this->plugin_name = plugin_basename(__FILE__);

			// PLugin Update Checker Configuration
			require 'plugin-update-checker/plugin-update-checker.php';

			$myUpdateChecker = PucFactory::buildUpdateChecker(
				'https://github.com/KaifTaufiq/crocoblock-addons/',
				__FILE__,
				'croocblock-addons'
			);

			//Set the branch that contains the stable release.
			$myUpdateChecker->setBranch('main');
			
			// Load the Required Files
			require $this->plugin_path('dashboard/dashboard.php');
			require $this->plugin_path('addons/addons-manager.php');

			add_action('jet-engine/init', array($this, 'init'));

			// Plugin activation and deactivation hook.
			register_activation_hook(__FILE__, [$this, 'activation']);
			register_deactivation_hook(__FILE__, [$this, 'deactivation']);
		}

		/**
		 * Plugin activation hook.
		 *
		 * @return void
		 */
		public function activation()
		{
			$single_rest_api = get_option('cba-single-rest-api');
    		$addon_features = get_option('crocoblock_addon_features');
			if (!empty($single_rest_api) || !empty($addon_features)) {
				update_option('cba-migration', '1');
			}
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
			delete_option('cba-single-rest-api');
			delete_option('crocoblock_addon_features');
		}

		/**
		 * Plugin deactivation hook.
		 *
		 * @return void
		 */
		public function deactivation()
		{
			// Not Decided Yet what to do on deactivation
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init()
		{
			$this->dashboard = new Dashboard();
			$this->addons = new AddonManager();
			do_action('crocoblock-addons/init', $this);
		}

		public function plugin_path($path = null)
		{
			if (! $this->plugin_path) {
				$this->plugin_path = trailingslashit(plugin_dir_path(__FILE__));
			}
			return $this->plugin_path . $path;
		}

		public function plugin_url($path = null)
		{
			if (! $this->plugin_url) {
				$this->plugin_url = trailingslashit(plugin_dir_url(__FILE__));
			}
			return $this->plugin_url . $path;
		}

		public function get_version()
		{
			return $this->version;
		}

		public static function get_instance()
		{
			// If the single instance hasn't been set, set it now.
			if (null == self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

if (! function_exists('CrocoblockAddons')) {

	/**
	 * Returns instance of the plugin class.
	 *
	 * @since  1.1
	 * @return CrocoblockAddons
	 */
	function crocoblock_addon()
	{
		return CrocoblockAddons::get_instance();
	}
}

crocoblock_addon();
