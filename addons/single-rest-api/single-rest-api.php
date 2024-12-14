<?php
if (!defined('ABSPATH')) {
    exit;
}
add_action('crocoblock_addons_sidebar_items', 'sidebar');
function sidebar()
{
    ?>
    <div class="nav-item" data-target="single-rest-api">Single Rest API</div>
    <?php
}

add_action('wp_ajax_single_rest_api', 'single_rest_api_form_handler');

function single_rest_api_form_handler()
{
    if (!current_user_can('manage_options') || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'single_rest_api_nonce')) {
        wp_send_json_error('Unauthorized');
    }

    // Get the submitted data
    $saved_endpoints = get_option('cba-single-rest-api', []);
    $submitted_endpoints = $_POST['endpoints'];
    $submitted_custom_keys = $_POST['custom_keys'];

    // Initialize new_saved_endpoints array
    $new_saved_endpoints = [];

    // Loop through all existing endpoints and update their status and custom keys
    foreach ($submitted_endpoints as $id => $status) {
        $status = isset($submitted_endpoints[$id]) && $submitted_endpoints[$id] === 'on' ? 'on' : 'off';
        $custom_key = isset($submitted_custom_keys[$id]) ? sanitize_text_field($submitted_custom_keys[$id]) : '';

        $new_saved_endpoints[$id] = [
            'status' => $status,
            'custom_key' => $custom_key
        ];
    }

    // Save the updated array back to the database
    update_option('cba-single-rest-api', $new_saved_endpoints);

    wp_send_json_success('Settings updated successfully.');
}
add_action('crocoblock_addons_content_sections', 'content_section');
function content_section()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    if (!class_exists('Jet_Engine')) {
        ?>
        <div class="content" id="single-rest-api">
            <p>JetEngine plugin is required to use this feature.</p>
        </div>
        <?php
        return;
    }
    global $wpdb;
    $prefix = $wpdb->prefix;
    $table_name = $prefix . 'jet_post_types';

    $endpoints = $wpdb->get_results(
        "SELECT * FROM {$table_name} WHERE status = 'rest-api-endpoint'"
    );

    // Handle form submission


    if (!empty($endpoints)) {
        $nonce = wp_create_nonce('single_rest_api_nonce');
        ?>
        <div id="single-rest-api" class="content">
            <h1>Single Rest API Endpoints</h1>
            <p class="paragraph">Enable/disable Single Rest API endpoints and set custom URL parameters</p>

            <form method="post" action="">
                <div class="feature-flags-flex">
                    <?php
                    // Fetch the saved options from the database
                    $saved_endpoints = get_option('cba-single-rest-api', []);
                    foreach ($endpoints as $endpoint):
                        // Unserialize the stored arrays
                        $args = maybe_unserialize($endpoint->args);
                        $labels = maybe_unserialize($endpoint->labels);

                        // Extract name and URL (with fallbacks)
                        $name = isset($labels['name']) ? esc_html($labels['name']) : 'Unnamed Endpoint';
                        $url = isset($args['url']) ? esc_url($args['url']) : '#';

                        // Extract saved status and custom key
                        $status = isset($saved_endpoints[$endpoint->id]['status']) && $saved_endpoints[$endpoint->id]['status'] === 'on' ? 'on' : 'off';
                        $custom_key = isset($saved_endpoints[$endpoint->id]['custom_key']) ? esc_attr($saved_endpoints[$endpoint->id]['custom_key']) : '';

                        ?>
                        <div class="feature-card">
                            <div class="name_switch">
                                <label class="switch">
                                    <!-- Checkbox for each endpoint -->
                                    <input type="checkbox" name="endpoints[<?php echo esc_attr($endpoint->id); ?>]" value="on" <?php checked($status, 'on'); ?>>
                                    <span class="slider"></span>
                                </label>
                                <div class="feature-title">
                                    <?php echo $name; ?>
                                </div>
                                <div class="feature-subtitle">
                                    <?php echo $url; ?>
                                </div>
                            </div>
                            <div class="custom-key">
                                <label for="custom_key_<?php echo esc_attr($endpoint->id); ?>">Custom URL Parameter</label>
                                <input type="text" name="custom_keys[<?php echo esc_attr($endpoint->id); ?>]"
                                    id="custom_key_<?php echo esc_attr($endpoint->id); ?>" value="<?php echo $custom_key; ?>"
                                    placeholder="Enter custom key">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="submit"><button type="button" id="single_rest_api_submit" class="button button-primary">Save
                        Settings</button></p>
            </form>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                function toggleCustomKeyFields() {
                    $('input[name^="endpoints["]').each(function () {
                        const endpointId = $(this).attr('name').match(/\[(\d+)\]/)[1]; // Extract the ID from the name attribute
                        const customKeyField = $('#custom_key_' + endpointId).closest('.custom-key');

                        if ($(this).is(':checked')) {
                            customKeyField.show();
                        } else {
                            customKeyField.hide();
                        }
                    });
                }

                // Initial check to set visibility based on saved states
                toggleCustomKeyFields();

                // Toggle visibility on checkbox change
                $('input[name^="endpoints["]').on('change', function () {
                    toggleCustomKeyFields();
                });
                $('#single_rest_api_submit').on('click', function (e) {
                    e.preventDefault();

                    let endpoints = {};
                    let customKeys = {};

                    // Collect checkbox states for endpoints
                    $('input[name^="endpoints["]').each(function () {
                        const endpointId = $(this).attr('name').match(/\[(\d+)\]/)[1]; // Extract the ID from the name attribute
                        endpoints[endpointId] = $(this).is(':checked') ? 'on' : 'off';
                    });

                    // Collect custom key values for each endpoint
                    $('input[name^="custom_keys["]').each(function () {
                        const endpointId = $(this).attr('name').match(/\[(\d+)\]/)[1]; // Extract the ID from the name attribute
                        customKeys[endpointId] = $(this).val();
                    });

                    console.log("Endpoints Data:", endpoints); // Debug endpoint states
                    console.log("Custom Keys Data:", customKeys); // Debug custom keys

                    // Send AJAX request
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'single_rest_api',
                            nonce: '<?php echo esc_js($nonce); ?>',
                            endpoints: endpoints,
                            custom_keys: customKeys,
                        },
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to update settings: ' + response.data);
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
            .feature-flags-flex {
                display: flex;
                flex-direction: column;
                gap: 20px;
                margin-bottom: 10px;
                padding: 30px;
                background-color: #f5f5f5;
            }
        </style>
        <?php
    } else {
        ?>
        <div class="content" id="single-rest-api">
            <p>No Endpoint Found.</p>
        </div>
        <?php
    }
}

add_filter('jet-engine/rest-api-listings/response/body', function ($body, $request_instance, $query_args, $response) {
    $saved_endpoints = get_option('cba-single-rest-api', []);
    $endpoint_id = $request_instance->get_endpoint()['id'];
    if (isset($endpoint_id) && isset($saved_endpoints[$endpoint_id]['status']) && $saved_endpoints[$endpoint_id]['status'] === 'on') {

        // Encapsulate the body in an array if the ID is enabled
        $modified_body = array(
            $body
        );
        return $modified_body;
    }
    return $body;
}, 10, 4);

add_filter('jet-engine/rest-api-listings/request/url', function ($url, $instance) {
    $saved_endpoints = get_option('cba-single-rest-api', []);
    $endpoint_id = $instance->get_endpoint()['id'];

    // Check if the endpoint is enabled and fetch the custom key
    if (isset($saved_endpoints[$endpoint_id]) && $saved_endpoints[$endpoint_id]['status'] === 'on') {
        $custom_key = $saved_endpoints[$endpoint_id]['custom_key'];
        if (empty($custom_key)) {
            $custom_key = 'id';
        }
        // echo '<script>console.log("custom_key: ' . $saved_endpoints[$endpoint_id]['custom_key'] . '")</script>';
        // Use the custom key instead of hardcoded 'id'
        $singleID = isset($_GET[$custom_key]) ? $_GET[$custom_key] : '';
        // echo '<script>console.log("Single ID: ' . $singleID . '")</script>';

        if (!empty($singleID) && str_contains($url, '{replace}')) {
            // echo '<script>console.log("OLD URL: ' . $url . '")</script>';
            $new_url = str_replace('{replace}', $singleID, $url);
            // echo '<script>console.log("NEW URL: ' . $new_url . '")</script>';
            return $new_url;
        }
    }

    return $url;
}, 10, 2);



