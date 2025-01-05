<?php

namespace CrocoblockAddons\Addons\CallBack_CCT_Data_with_ID;

use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'callback-cct-data-by-id';

    public $elementor_integration = null;
    public $blocks_integration = null;
    public $bricks_integration = null;

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
        if ( !jet_engine()->modules->is_module_active('custom-content-types') ) {
            return;
        }
        require_once $this->addon_includes_path('callback_function.php');
        add_filter('jet-engine/listings/allowed-callbacks', [$this,'add_custom_dynamic_field_callbacks']);
        add_filter('jet-engine/listing/dynamic-field/callback-args', [$this,'add_custom_dynamic_field_callbacks_args'], 10, 3);
        add_filter('jet-engine/listings/allowed-callbacks-args', [$this,'add_custom_controls'], 10, 1);
    }

    public function add_custom_dynamic_field_callbacks( $callbacks ) {

        $callbacks['callback_cct_data_by_id'] = __( 'Get CCT Data by ID', 'crocoblock-addons' );
        
        return $callbacks;
        
    }
    
    public function add_custom_dynamic_field_callbacks_args($result, $callback, $settings) 
    {
        if ($callback === 'callback_cct_data_by_id') {
            $cct_slug = $settings['cct_slug'] ?? '';
            $cct_field = $settings['field_name_' . $cct_slug] ?? '';
            return [$result[0], $cct_slug, $cct_field];
        }
        return $result;
    }
    
    public function add_custom_controls( $controls ) 
    {

        [$cct_names, $cct_fields] = $this->get_cct_data();

        $controls['cct_slug'] = array(
			'label'       => esc_html__( 'Select CCT', 'crocoblock-addons' ),
			'type'        => 'select',
			'default'     => '',
			'options'     => $cct_names,
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array('callback_cct_data_by_id'),
			),
		);

        foreach ($cct_fields as $cct_slug => $fields) {
            $control_name = 'field_name_' . $cct_slug;
            $controls[$control_name] = [
                'label' => esc_html__('Field Name', 'crocoblock-addons'),
                'type' => 'select',
                'default' => '',
                'options' => $fields,
                'condition' => [
                    'dynamic_field_filter' => 'yes',
                    'cct_slug' => $cct_slug,
                    'filter_callback' => ['callback_cct_data_by_id'],
                ],
            ];
        }
        return $controls;
            
    }

    public function get_cct_data() {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $post_type = $prefix . 'jet_post_types';
    
        // Get all CCTs
        $query = $wpdb->prepare(
            "SELECT * FROM {$post_type} WHERE status = %s",
            'content-type'
        );
        $ccts = $wpdb->get_results($query);
    
        // Initialize return arrays
        $cct_names = ['' => esc_html__('Select', 'crocoblock-addons')];
        $cct_fields = [];
    
        // Default field options
        $default_fields = [
            'cct_author_id' => esc_html__('CCT Author ID', 'crocoblock-addons'),
            'cct_created' => esc_html__('CCT Created Timestamp', 'crocoblock-addons'),
            'cct_modified' => esc_html__('CCT Modified Timestamp', 'crocoblock-addons'),
            'cct_status' => esc_html__('CCT Status', 'crocoblock-addons')
        ];
    
        foreach ($ccts as $cct) {
            $cct_args = unserialize($cct->args);
            $cct_names[$cct->slug] = esc_html__($cct_args['name'], 'crocoblock-addons');
    
            // Initialize fields array with default select
            $cct_fields[$cct->slug] = ['' => esc_html__('Select CCT Field', 'crocoblock-addons')];
    
            // Add dynamic fields
            $fields = unserialize($cct->meta_fields);
            foreach ($fields as $field) {
                $cct_fields[$cct->slug][$field['name']] = esc_html__($field['title'], 'crocoblock-addons');
            }
    
            // Add default fields
            $cct_fields[$cct->slug] = array_merge($cct_fields[$cct->slug], $default_fields);
        }
    
        return [$cct_names, $cct_fields];
    }

    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
