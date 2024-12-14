<?php
/**
 * Plugin Name: Crocoblock Addons
 * Description: A plugin to Power up your Crocoblock plugins with additional features.
 * Version: 1.0.1
 * Author: Kaif Shaikh (White Hat Devs)
 * Author URI: https://whitehatdevs.com
 */
if (!defined('ABSPATH')) {
	exit;
}
// Plugin Settings Page for Crocoblock Addons
add_action('admin_menu', 'register_page');
function register_page()
{
	add_menu_page(
		'Crocoblock Addons',            // Page title
		'Crocoblock Addons',            // Menu title
		'manage_options',               // Capability
		'crocoblock-addons',            // Menu slug
		'crocoblock_addons_page_html',  // Callback function
		'dashicons-admin-generic',      // Icon
		59.1                              // Position
	);
}
function crocoblock_get_feature_flags()
{
	return [
		[
			'name' => 'Single Listing',
			'slug' => 'single-listing',
			'file' => 'single-listing/single-listing.php',
			'elementor' => [
				[
					'path' => 'includes/elementor-widget.php',
					'class' => 'Single_Listing_Elementor'
				]
			],
			'bricks' => [
				'includes/bricks-widget.php'
			]
		],
		[
			'name' => 'Single Rest API',
			'slug' => 'single-rest-api',
			'file' => 'single-rest-api/single-rest-api.php',
		],
	];
}
add_action('wp_ajax_modules_form', 'modules_form_handler');

function modules_form_handler()
{
	if (!current_user_can('manage_options') || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'modules_form_nonce')) {
		wp_send_json_error('Unauthorized');
	}
	if (isset($_POST['feature_flags'])) {
		error_log('Received feature_flags: ' . print_r($_POST['feature_flags'], true));
	} else {
		error_log('No feature_flags received');
	}
	$submitted_flags = isset($_POST['feature_flags']) ? $_POST['feature_flags'] : [];
	$new_feature_flags = [];
	foreach (crocoblock_get_feature_flags() as $feature) {
		$new_feature_flags[$feature['slug']] = isset($submitted_flags[$feature['slug']]) ? 'on' : 'off';
	}
	update_option('crocoblock_addon_features', $new_feature_flags);

	wp_send_json_success('Options updated successfully');

}
function crocoblock_addons_page_html()
{
	if (!current_user_can('manage_options')) {
		return;
	}

	// Fetch existing feature flags from the database or set default values
	$feature_flags = get_option('crocoblock_addon_features', []);


	if (empty($feature_flags)) {
		$feature_flags = array_fill_keys(array_column(crocoblock_get_feature_flags(), 'slug'), 'off');
	}

	// Handle form submission when the 'Save Features' button is clicked

	$nonce = wp_create_nonce('modules_form_nonce');
	?>

	<div class="dashboard">
		<h1 class="header-title">Crocoblock Addons Dashboard</h1>
		<div class="main-content">
			<div class="sidebar">
				<div class="nav-item nav-item--active" data-target="modules">Modules</div>
				<?php do_action('crocoblock_addons_sidebar_items'); // Hook for adding more sidebar items ?>
			</div>
			<div id="content-container">
				<div id="modules" class="content visible">
					<h1>Crocoblock Addons</h1>
					<p class="paragraph">Enable/disable additional Addons features</p>

					<form method="post" action="">
						<div class="feature-flags-grid">
							<?php foreach (crocoblock_get_feature_flags() as $feature): ?>
								<?php $status = isset($feature_flags[$feature['slug']]) ? $feature_flags[$feature['slug']] : 'off'; ?>
								<div class="feature-card">
									<div class="name_switch">
										<label class="switch">
											<input type="checkbox"
												name="feature_flags[<?php echo esc_attr($feature['slug']); ?>]" value="on" <?php checked($status, 'on'); ?>>
											<span class="slider"></span>
										</label>
										<div class="feature-title">
											<?php echo esc_html($feature['name']); ?>
										</div>
									</div>
									<div class="module-info">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
											<rect x="0" fill="none" width="20" height="20"></rect>
											<g>
												<path
													d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1 4c0-.55-.45-1-1-1s-1 .45-1 1 .45 1 1 1 1-.45 1-1zm0 9V9H9v6h2z">
												</path>
											</g>
										</svg>
										<div class="tooltip">
											Click here to get more info
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<p class="submit"><button type="button" id="modules_form_submit" class="button button-primary">Save
								Features</button></p>
					</form>
				</div>
				<?php do_action('crocoblock_addons_content_sections'); // Hook for adding more content sections ?>
			</div>

		</div>
	</div>
	<script>
		jQuery(document).ready(function ($) {
			$('#modules_form_submit').on('click', function (e) {
				e.preventDefault();

				// Use serialize() to gather form data
				let formData = $('form').serializeArray();
				let featureFlags = {};

				// Loop through the serialized data to build the featureFlags object
				$.each(formData, function (i, field) {
					console.log(`Field: ${field.name}, Value: ${field.value}`); // Debug each field
					if (field.name.startsWith('feature_flags[')) {
						featureFlags[field.name.replace('feature_flags[', '').replace(']', '')] = field.value;
					}
				});

				console.log("Feature Flags Data:", featureFlags); // Log the populated featureFlags object

				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'modules_form',
						nonce: '<?php echo esc_js($nonce); ?>',
						feature_flags: featureFlags,
					},
					success: function (response) {
						if (response.success) {
							location.reload();
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
		document.querySelectorAll('.switch input').forEach(function (input) {
			input.addEventListener('change', function () {
				const slider = this.nextElementSibling;
				if (this.checked) {
					input.value = 'on';
					slider.style.background = '#CCE5F1';
				} else {
					input.value = 'off';
					slider.style.background = '';
				}
			});
		});
		document.querySelectorAll('.nav-item').forEach(function (navItem) {
			navItem.addEventListener('click', function () {
				// Remove active state from all nav items
				document.querySelectorAll('.nav-item').forEach(function (item) {
					item.classList.remove('nav-item--active');
				});

				// Add active state to the clicked nav item
				this.classList.add('nav-item--active');

				// Get the target content section ID from data-target attribute
				const target = this.getAttribute('data-target');

				// Hide all content sections
				document.querySelectorAll('.content').forEach(function (content) {
					content.classList.remove('visible');
				});

				// Show the corresponding content section
				document.getElementById(target).classList.add('visible');
			});
		});
	</script>
	<style>
		#content-container {
			width: 100%;
		}

		.nav-item {
			border-top: none;
			border-bottom: 1px solid #DCDCDD;
			color: #23282D;
			cursor: pointer;
			padding: 14px 20px;
			font-weight: 500;
			font-size: 15px;
		}

		.nav-item--active {
			color: #007CBA;
			position: relative;
			background: #ffF;
			z-index: 3;
			border-right: none;
		}

		.sidebar {
			width: 20%;
			flex: 0 0 20%;
			max-width: 220px;
			padding: 0 0 40px;
			background: #f5f5f5;
		}

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

		.main-content {
			display: flex;
		}

		.module-info .tooltip {
			left: 50%;
			/* margin-left: -80px; */
			width: 160px;
			/* bottom: calc(100% + 15px); */
			box-sizing: border-box;
			pointer-events: none;
			transition: all 150ms linear;
			opacity: 0;
			padding-left: 0;
			padding-right: 0;
			height: 25px;
		}

		.module-info {
			position: relative;
		}

		.tooltip {
			background: #23282D;
			box-shadow: 0px 1px 4px rgba(35, 40, 45, 0.24);
			border-radius: 3px;
			padding: 5px 15px;
			font-size: 12px;
			line-height: 15px;
			color: #fff;
			position: absolute;
			text-align: center;
			bottom: calc(100% + 10px);
			/* Moves tooltip above the icon */
			left: 50%;
			/* Centers tooltip relative to the icon */
			transform: translateX(-50%);
			/* Ensures tooltip is centered horizontally */
			z-index: 9999;
			opacity: 0;
			transition: opacity 150ms linear;
		}

		.tooltip:after {
			content: "";
			position: absolute;
			top: 100%;
			/* Positions arrow below the tooltip */
			left: 50%;
			margin-left: -4px;
			border-width: 4px;
			border-style: solid;
			border-color: #23282D transparent transparent transparent;
		}

		.module-info:hover .tooltip {
			opacity: 1;
			pointer-events: auto;
		}

		.module-info:hover svg {
			fill: #007CBA;
		}


		.module-info {
			display: block;
			width: 24px;
			height: 24px;
			cursor: pointer;
		}

		.module-info svg {
			fill: #dcdcdd;
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

		.submit.input {
			padding: 6px 14px 7px;
			cursor: pointer;
		}

		.feature-title {
			color: #007CBA;
			margin: 0 10px 0 0;
			font-size: 15px;
			line-height: 17px;
			font-weight: 500;
		}

		.name_switch {
			display: flex;
			align-items: center;
			gap: 16px;
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

		.feature-flags-grid {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 20px;
			margin-bottom: 10px;
			padding: 30px;
			background-color: #f5f5f5;
		}

		.feature-card {
			background-color: white;
			border-radius: 5px;
			box-shadow: 0px 2px 6px rgba(35, 40, 45, 0.07);
			padding: 18px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		/* Switch styles */
		.switch {
			position: relative;
			display: inline-block;
			width: 36px;
			height: 18px;
		}

		.switch input {
			display: none;
		}

		.slider {
			position: absolute;
			cursor: pointer;
			top: 3px;
			left: 0;
			right: 0;
			bottom: 3px;
			background: #ECECEC;
			transition: .4s;
			border-radius: 34px;
		}

		.slider:before {
			position: absolute;
			content: "";
			width: 18px;
			height: 18px;
			left: 0px;
			top: 50%;
			margin-top: -9px;
			box-shadow: 0px 1px 4px rgba(35, 40, 45, 0.24);
			background: #fff;
			border-radius: 100%;
			transition: all 150ms linear;
		}

		input:checked+.slider:before {
			transform: translateX(18px);
			background: #007CBA;
		}

		.content {
			display: none;
		}

		/* Show only the visible content */
		.content.visible {
			display: block;
		}
	</style>
	<?php
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


add_action('init', function () {
	$features = crocoblock_get_feature_flags();
	foreach ($features as $feature) {
		if (isset($feature['bricks'])) {
			foreach ($feature['bricks'] as $bricks_widget) {
				$widget_file_path = plugin_dir_path(__FILE__) . 'addons/' . $feature['slug'] . '/' . $bricks_widget;
				if (file_exists($widget_file_path)) {
					if (class_exists('\Bricks\Elements')) {
						\Bricks\Elements::register_element($widget_file_path);
					}
				}
			}
		}
	}
}, 11);

function crocoblock_register_bricks_widgets()
{
	// will upodate the code later
	return;
}
add_action('elementor/widgets/register', 'crocoblock_register_elementor_widgets');

function crocoblock_register_elementor_widgets($widgets_manager)
{
	$features = crocoblock_get_feature_flags();
	$feature_flags = get_option('crocoblock_addon_features', []);

	foreach ($features as $feature) {
		if (isset($feature_flags[$feature['slug']]) && $feature_flags[$feature['slug']] === 'on') {
			if (!empty($feature['elementor'])) {
				foreach ($feature['elementor'] as $elementor_widget) {
					$widget_file_path = plugin_dir_path(__FILE__) . 'addons/' . $feature['slug'] . '/' . $elementor_widget['path'];
					if (file_exists($widget_file_path)) {
						require_once $widget_file_path;
						$widget_class = $elementor_widget['class'];
						if (class_exists($widget_class)) {
							$widgets_manager->register_widget_type(new $widget_class());
						}
					}
				}
			}
		}
	}
}

function crocoblock_require_module()
{
	$features = crocoblock_get_feature_flags();
	$feature_flags = get_option('crocoblock_addon_features', []);

	foreach ($features as $feature) {
		if (isset($feature_flags[$feature['slug']]) && $feature_flags[$feature['slug']] === 'on') {
			$feature_file_path = plugin_dir_path(__FILE__) . 'addons/' . $feature['file'];
			if (file_exists($feature_file_path)) {
				require_once $feature_file_path;
			}
		}
	}
}
crocoblock_require_module();
