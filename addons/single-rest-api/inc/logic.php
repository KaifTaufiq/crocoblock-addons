<?php 
// add_filter('jet-engine/rest-api-listings/response/body', 'updated_body', 10, 4);

function updated_body($body, $request_instance, $query_args, $response) {
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
}

// add_filter('jet-engine/rest-api-listings/request/url',  'updated_url', 10, 2);

function updated_url($url, $instance) {
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
}