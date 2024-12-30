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


// If this file is called directly, abort.
if (! defined('WPINC')) {
	die();
}

// PLugin Update Checker Configuration
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/KaifTaufiq/crocoblock-addons/',
	__FILE__,
	'croocblock-addons'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');


// the main Crocoblock Addons class.
if (! class_exists('CrocoBlockAddons')) {
	class CrocoBlockAddons
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
		 * @var CrocoblockAddonsDashboard
		 */
		public $dashboard;

		/**
		 * Addon Manager instance
		 * @var CrocoblockAddonManager
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

			// Load the Required Files
			require $this->plugin_path('dashboard/dashboard.php');
			
			add_action('jet-engine/init', array($this, 'init'));
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init()
		{
			$this->dashboard = new CrocoblockAddonsDashboard();
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

		public static function get_instance(){
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

if ( ! function_exists( 'CrocoBlockAddons' ) ) {

	/**
	 * Returns instance of the plugin class.
	 *
	 * @since  1.1
	 * @return CrocoBlockAddons
	 */
	function CrocoBlockAddons() {
		return CrocoBlockAddons::get_instance();
	}
}

CrocoBlockAddons();
