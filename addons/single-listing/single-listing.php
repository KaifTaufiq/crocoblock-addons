<?php
// Silence is Great
if (!defined('ABSPATH')) {
    exit;
}
$dir = __FILE__;
// echo '<script>console.log("'.$dir.'");</script>';
add_action('jet-engine/query-builder/query/after-query-setup', function ($query) {
    if (isset($_POST['singleID']) && $query->query_type != 'rest-api') {
        // echo "<script>console.log('" . json_encode($query) . "');</script>"; // For Debugging
        $singleID = intval($_POST['singleID']);
        switch ($query->query_type) {
            case 'posts':
                $query->final_query['p'] = $singleID;
                break;
            case 'custom-content-type':
                if (isset($query->final_query['args'][0]['field'])) {
                    $query->final_query['args'][0]['value'] = $singleID;
                }
                break;
            case 'users':
                $query->final_query['include'] = $singleID;
                break;
            case 'terms':
                $query->final_query['include'] = $singleID;
                break;
            case 'sql':
                if (isset($query->query['advanced_mode']) && $query->query['advanced_mode'] == 'true') {
                    $manual_query = $query->final_query['manual_query'];
                    if (str_contains($manual_query, '{replace}')) {
                        $query->final_query['manual_query'] = str_replace('{replace}', $singleID, $manual_query);
                    }
                } else {
                    if (isset($query->final_query['where'][0]['column'])) {
                        $query->final_query['where'][0]['value'] = $singleID;
                    }
                }
                break;
            case 'comments':
                $query->final_query['comment__in'] = $singleID;
                break;
            case 'wc-product-query':
                $query->final_query['include'] = [$singleID];
                break;
            default:
                break;
        }
        return $query;
    } elseif (isset($_POST['singleID']) && $query->query_type == 'rest-api') {
        $modules = get_option('crocoblock_addon_features');
        $is_single = get_option('cba-single-rest-api');
        $endpoint = $query->final_query['endpoint'];
        if (empty($modules) || !isset($modules['single-rest-api']) || $modules['single-rest-api'] == 'off' || empty($is_single) || !isset($is_single[$endpoint]) || $is_single[$endpoint]['status'] != 'on') {
            return;
        }
        add_filter( 'jet-engine/rest-api-listings/request/url', function( $url, $instance ){
            $singleID = intval($_POST['singleID']);
            if(str_contains($url, '{replace}')){
                $new_url = str_replace('{replace}', $singleID, $url);
            }
            return $new_url;
        }, 10, 2 );
    }
});