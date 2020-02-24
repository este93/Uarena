<?php
/**
 * Adds new shortcode 
 *
 */

namespace Plda\Setup\Shortcodes;

use \Plda\Custom\Transients;


if ( ! class_exists( 'ListePagesService' ) ) {

	class ListePagesService {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() 
		{
			
			// Registers the shortcode in WordPress
			add_shortcode( 'liste_pages_services', array( $this, 'output' ) );

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes 
			extract(shortcode_atts(array(
	                'page'  				=> 'home',
			), $atts));

			$output = '';


			//services
				$terms = get_terms( array( 
				    'taxonomy' => 'plda_service',
				    'hide_empty' => false
				) );

				if ( !empty($terms) ) :
					$output .= '<div class="container">';
					$output .= '<ul class="service__liste row">';
				    foreach( $terms as $term ) {
				    	$output .= '<li class="service__item col-xs-4">';
						$output .= '	<a href="' . carbon_get_term_meta($term->term_id, 'service_page_url') . '" title="' . esc_attr($term->name). '" class="service__link">';
				    	$output .= '<span class="service__btn">';
				    	$output .= inlineSvg( 'btn-service' , true);
				    	$output .= '	<span class="service__icon fa-inverse fas fa-' . $term->slug . '"></span>';
				    	$output .= '</span>';
				    	$output .= '<span class="service__label">'. esc_html( $term->name ) .'</span>';
				    	$output .= '	</a>';
				    	$output .= '</li>';
				    }
					$output .= '</ul>';
					$output .= '</div>';
				endif;

			// Return output
			return $output;

		}

	}

}