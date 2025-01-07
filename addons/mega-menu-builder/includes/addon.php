<?php

namespace CrocoblockAddons\Addons\MegaMenuBuilder;
use CrocoblockAddons\Base\ActiveAddon;

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'mega-menu-builder';

    /**
     * Constructor for the class
     */
    public function __construct()
    {
        if ( !function_exists('jet_engine' )) {
            return;
        }
        // require_once $this->addon_includes_path('manager.php');    
        // $this->manager = new Manager();
        // wp_register_script(
        //     'SingleListing',
        //     $this->addon_assets_url('script.js'),
        //     ['jquery'],
        //     crocoblock_addon()->get_version(),
        //     true
        // );
        $this->elementor_widgets = [
            [
                'file' => 'mega-menu-elementor.php',
                'class' => 'MegaMenuBuilder_Elementor',
            ]
        ];
        add_action('elementor/widgets/register', [ $this,'register_elementor_widget']);
        // $this->bricks_widgets = ['bricks-widget.php'];
        // add_action('init', [$this,'register_bricks_widget']);
    }
    
    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}