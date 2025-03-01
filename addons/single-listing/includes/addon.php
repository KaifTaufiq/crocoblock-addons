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
        if ( !function_exists('jet_engine' )) {
            return;
        }
        require_once $this->addon_includes_path('manager.php');    
        $this->manager = new Manager();
        
        $this->elementor_widgets = [
            [
                'file' => 'elementor-widget.php',
                'class' => 'Single_Listing_Elementor',
            ]
        ];
        add_action('elementor/widgets/register', [ $this,'register_elementor_widget']);
        $this->bricks_widgets = ['bricks-widget.php'];
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
