<?php

namespace Plda\Setup;

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Exception\RuntimeException;

/**
 * Shortcodes
 * Load all project shortcode
 */
class Shortcodes
{
	/**
	 * Store all the shortcode classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_shortcodes()
	{
		return [
			Shortcodes\HomeSlider::class,
			Shortcodes\SliderProduits::class,
			Shortcodes\ChildPagesMenu::class,
			Shortcodes\ExtendWpShortcodes::class,
			Shortcodes\ListePagesService::class,
			Shortcodes\HomeArticleSlider::class
		];

	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		foreach ( self::get_shortcodes() as $class ) {
			$shortcode = self::instantiate( $class );
			if ( method_exists( $shortcode, 'register') ) {
				$shortcode->register();
			}
		}
		
	}

	/**
	 * Initialize the class
	 * @param  class $class 		class from the shortcodes array
	 * @return class instance 		new instance of the class
	 */
	private static function instantiate( $class )
	{
		$config = ConfigFactory::create( ABSPATH . '../project_settings.php' );

		return new $class( $config );
	}
}