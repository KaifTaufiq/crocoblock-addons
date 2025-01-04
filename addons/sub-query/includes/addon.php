<?php

namespace CrocoblockAddons\Addons\SubQuery;
use CrocoblockAddons\Base\ActiveAddon;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

class Addon extends ActiveAddon
{
    private static $instance = null;

    public $slug = 'sub-query';

    /**
     * Constructor for the class
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
    }

    public function init()
    {
        add_action( 'jet-engine/query-builder/query-editor/register', [ $this, 'register_editor_component' ] );
		add_action( 'jet-engine/query-builder/queries/register', [ $this, 'register_query' ] );
    }

    /**
	 * Register editor componenet for the query builder
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_editor_component( $manager ) {
        require_once $this->addon_includes_path('editor.php');
		$manager->register_type( new Editor() );
	}

    /**
	 * Regsiter query class
	 *
	 * @param  [type] $manager [description]
	 * @return [type]          [description]
	 */
	public function register_query( $manager ) {

		$class = __NAMESPACE__ . '\Query';

		if ( ! class_exists( $class ) ) {
			require_once $this->addon_includes_path('query.php');
		}

		$manager::register_query( $this->slug, $class );

	}

    public static function instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
