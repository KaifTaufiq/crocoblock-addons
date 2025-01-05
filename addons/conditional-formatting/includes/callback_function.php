<?php
function callback_conditonal_formatting($text, $id) {
    $settings = maybe_unserialize(get_option('cba-conditional-formatting'));
    if ( isset($settings[$id]) && !empty($settings[$id]) ) {
        $conditions = $settings[$id]['conditions'];
        foreach ($conditions as $condition) {
            if ($condition['from'] == $text) {
                return $condition['to'];
            }
        }
    }
    return $text;
}