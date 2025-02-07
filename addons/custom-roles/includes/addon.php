<?php

namespace CrocoblockAddons\Addons\CustomRoles;

use CrocoblockAddons\Addons\CustomRoles\Settings;
use CrocoblockAddons\Addons\CustomRoles\Manager;
use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'custom-roles';

    public $settings = null;

    public $manager = null;

    /**
     * Constructor for the class
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
    }

    /**
     * Init module components
     *
     * @return [type] [description]
     */
    public function init()
    {
        if ( !function_exists('jet_engine' )) {
            return;
        }
        require_once $this->addon_includes_path('settings.php');
        $this->settings = new Settings();
    }
    
    public function update_single_item($item_id, $item) {
    
        $settings = $this->get_setting();
        $name = isset($item['name']) ? sanitize_text_field($item['name']) : false;
        $from = isset($item['from']) ? sanitize_text_field($item['from']) : false;
        $slug = isset($settings[$item_id]['slug']) ? $settings[$item_id]['slug'] : false;
        $message = '';
        if ( $name  && $from ) {
            if ( !$slug ) {
                $genSlug = strtolower(str_replace(' ', '_', $name));
                $this->create_role($genSlug, $name, $from);
                $message = 'Role created';
                $settings[$item_id] = [
                    'name' => $name,
                    'slug'=> $genSlug,
                    'from' => $from,
                ];
                $this->update_setting($settings);
            } else {
                $message = $this->update_role($slug, $name, $from, $item_id);
            }
        } else {
            $settings[$item_id] = [
                'name' => $name,
                'from' => '',
            ];
            if ($item['name'] === '') {
                $message = 'Item Created';
            } else {
                $message = 'Name Saved, Please select a role to copy Capabilities from';
            }
            $this->update_setting($settings);
        }
        return $message;
    }

    public function get_listings() {

        $settings = $this->get_setting();
        $listings = [];
        foreach( $settings as $key => $value ) {
            if( !empty($value)) {
                $listings[] = [
                    'id'=> $key,
                    'name' => isset($value['name']) ? $value['name'] : '',
                    'from' => isset($value['from']) ? $value['from'] : '',
                    'slug'=> isset($value['slug']) ? $value['slug'] : '',
                ];
            }
        }
        return $listings;
    }

    public function get_available_roles()
    {
        global $wp_roles;
        $roles = $wp_roles->roles;
        do_action('qm/info', $roles);
        $settings = $this->get_setting();
        $custom_role_slugs = [];
        foreach( $settings as $key => $value ) {
            if( !empty($value) && isset($value['slug']) ) {
                $custom_role_slugs[] = $value['slug'];
            }
        }
        $return_roles = [
            [
                'value' => '',
                'label' => __( 'Select Role', 'crocoblock-addons' ),
            ],
        ];
        foreach( $roles as $slug => $role_details ) {
            if( in_array($slug, $custom_role_slugs) ) {
                continue;
            }
            $return_roles[] = [
                'value' => $slug,
                'label' => $role_details['name'],
            ];
        }
        return $return_roles;
    }

    public function create_role($slug, $name, $from)
    {
        $from_role = get_role( $from );
        if ( $from_role) {
            $capabilities = $from_role->capabilities;
            add_role($slug,$name,$capabilities);
        }
    }

    public function update_role($slug, $name, $from_slug, $item_id)
    {
        $settings = $this->get_setting();
        $existing_role = get_role($slug);
        if (!$existing_role) {
            $message = "Role '{$slug}' does not exist. Cannot update.";
            return $message;
        }
        if ( $existing_role->name != $name ) {
            global $wpdb;
            $wp_user_roles = get_option('wp_user_roles');
            if (isset($wp_user_roles[$slug])) {
                $wp_user_roles[$slug]['name'] = $name;
                $wpdb->update(
                    $wpdb->prefix . 'options',
                    ['option_value' => serialize($wp_user_roles)],
                    ['option_name' => 'wp_user_roles']
                ); 
            }
            $settings[$item_id]['name'] = $name;
        }
        if ( $settings[$item_id]['from'] != $from_slug ) {
            $from_role = get_role($from_slug);

            if ( $from_role) {
                $from_capabilities = $from_role->capabilities;
                // Remove all existing capabilities
                foreach ($existing_role->capabilities as $cap => $granted) {
                    $existing_role->remove_cap($cap);
                }
                // Add all capabilities from the 'from' role
                foreach ($from_capabilities as $cap => $granted) {
                    $existing_role->add_cap($cap, $granted);
                }
                $settings[$item_id]['from'] = $from_slug;
            }
        }
        $this->update_setting($settings);
        return 'Role updated';
    }

    public function delete_role($item_id)
    {
        $message = '';
        $settings = $this->get_setting();
        $message = $settings[ $item_id ];
        if ( isset($settings[ $item_id ]) && isset($settings[ $item_id ]['slug']) ) {
            $slug = $settings[ $item_id ]['slug'];
            if( !empty( $slug ) ) {
                if( get_role( $slug ) ) {
                    remove_role( $slug );
                    $message = 'Role removed';
                } else {
                    $message = 'Role not found';
                }
            }
        }
        $settings[ $item_id ] = '';
        $this->update_setting(  $settings );
        return $message;
        
    }

    public static function instance()
        {
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }
}
