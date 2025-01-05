<?php
function callback_cct_data_by_id($id, $slug, $field){
    global $wpdb;
    $table_name = $wpdb->prefix ."jet_cct_" . $slug;
    $query = $wpdb->prepare(
        "SELECT `%1s` FROM {$table_name} WHERE _ID = %d",
        $field,
        $id
    );
    $result = $wpdb->get_var($query);
    return $result;
}