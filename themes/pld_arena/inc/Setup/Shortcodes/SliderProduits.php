<?php
/**
 * Adds new shortcode 
 *
 */

namespace Plda\Setup\Shortcodes;

use BrightNucleus\Config\ConfigInterface as Config;
use BrightNucleus\Config\ConfigTrait;
use Plda\Custom\Transients;


if ( ! class_exists( 'SliderProduits' ) ) {

	class SliderProduits {

		use ConfigTrait;

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct( Config $config ) 
		{
			
			$this->processConfig( $config );

			// Registers the shortcode in WordPress
			add_shortcode( 'slider_produits', array( $this, 'output' ) );

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			// Extract shortcode attributes 
			extract(shortcode_atts(array(
	                'class_card' =>  'col-md-4'
			), $atts));

			// Define output
			$output = '';
			$compteur = 0;
	        $idsOfProducts = Transients::get_transient_query_ids( 'ALL_CURRENT_EVENTS_BY_PUB_DATE' );

			$loop = new \WP_Query( array(
				'post_type' 	 => 'produit',
		        'posts_per_page' => -1,
        		'orderby' 		 => 'post__in',
			    'post__in'  	 => ((!isset($idsOfProducts) || empty($idsOfProducts)) ? array(0) : $idsOfProducts),
			) );

			$output = '<div class="wrapper-slider">';
			$output.= '<div class="to-anchor display-block-up-md"><a href="#section--billetterie" uk-scroll>'.inlineSvg('icon-bulle-fleche', true).'</a></div>';

			if( $loop->have_posts() ):

				$counter = 0;
				$endSlideColumns = $filter_div = '';

				$output .= '<div class="slider--events swiper-container">';
				$output .= '<div class="swiper-wrapper">';
				while( $loop->have_posts() ): $loop->the_post();

					if ($counter === 0 || $counter % 6 === 0) {
					    $output .= $endSlideColumns . '<div class="swiper-slide card__desk">';
					    $endSlideColumns = '</div>';
					}

					$genre_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'genre_evenement', true );
					$filtres_slider[array_key_first($genre_evenement)] = reset($genre_evenement);

					$output .= \Plda\Core\Tags::get_product_card( get_the_ID() );

					$counter++;

				endwhile;
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="swiper-buttons-nav container">';
				$output .= '	<div class="swiper-button-prev"><i class="fas fa-angle-left stack-circle fz-24 plda-icon-context"></i></div>';
				$output .= '	<div class="swiper-button-next"><i class="fas fa-angle-right stack-circle fz-24 plda-icon-context"></i></div>';
				$output .= '</div>';
				$output .= '</div>';

				foreach ($filtres_slider as $key => $value) {
					$filter_div .= ( !empty($value) ) ? '<li data-filter="' . $key . '"><span>' . $value .'</span></li>' : '';
				}
				$output = '<div class="wrapper-filters"><ul class="nav-filter filter--events"><li class="selected" data-filter="all"><span>Tout</span></li>' . $filter_div . '<li class="nav_indicator"></li></ul></div>' . $output . '<div class="hidden-up-md text-center m-1"><a href="/billetterie/" class="btn btn-outline-white">VOIR TOUS LES ÉVÉNEMENTS</a></div>';

				wp_reset_postdata();

			else:
				$output .= '<div>Aucun événement !</div>';
			endif;
			$output .= '</div>';

			// Return output
			return $output;

		}

	}

}