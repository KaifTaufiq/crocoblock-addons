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
			add_filter("plugin_action_links_{$this->plugin_name}", [$this, 'plugin_action_links']);

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
			// Not Decided Yet what to do on activation
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

		public function plugin_action_links($links)
		{
			$settings_link = '';
			if( function_exists('jet_engine') ) {
				$settings_link = '<a href="' . admin_url('admin.php?page=jet-engine#crocoblock_addons') . '">Settings</a>';
			} else {
				$settings_link = '<span style="color: red;">Jet Engine Not Active</span>';
			}
			array_unshift($links, $settings_link);
			return $links;
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
