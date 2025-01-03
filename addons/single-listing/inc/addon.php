<?php

namespace CrocoblockAddons\Addons\SingleListing;
use CrocoblockAddons\Base\ActiveAddon;
use CrocoblockAddons\Addons\SingleListing\Manager;

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'single-listing';

    /**
     * Constructor for the class
     */
    public function __construct()
    {
        require_once $this->addon_path('manager.php');    
        $this->manager = new Manager();
        wp_register_script(
            'SingleListing',
            $this->addon_url('assets/script.js'),
            ['jquery'],
            crocoblock_addon()->get_version(),
            true
        );
        $this->elementor_widgets = [
            [
                'filename' => 'elementor-widget',
                'class' => 'Single_Listing_Elementor',
            ]
        ];
        add_action('elementor/widgets/register', [ $this,'register_elementor_widget']);
        $this->bricks_widgets = ['bricks-widget'];
        add_action('init', [$this,'register_bricks_widget']);
    }
    
    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
