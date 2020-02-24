<?php
/**
 * Adds new shortcode 
 *
 */

namespace Plda\Setup\Shortcodes;

use \Plda\Core\Tags;


if ( ! class_exists( 'HomeArticleSlider' ) ) {

	class HomeArticleSlider {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() 
		{
			
			// Registers the shortcode in WordPress
			add_shortcode( 'plda_slider_articles', array( $this, 'output' ) );

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes 
			extract(shortcode_atts(array(
	                'page' => 'home',
	                'max_articles' => '10'
			), $atts));

			ob_start();
			Tags::the_related_posts( intval($max_articles), false );
			$output = ob_get_clean();

			// Return output
			return $output;

		}

	}

}