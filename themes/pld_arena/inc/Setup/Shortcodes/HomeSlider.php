<?php
/**
 * Adds new shortcode 
 *
 */

namespace Plda\Setup\Shortcodes;

use Plda\Custom\Transients;
use BrightNucleus\Config\ConfigInterface as Config;
use BrightNucleus\Config\ConfigTrait;


if ( ! class_exists( 'HomeSlider' ) ) {

	class HomeSlider {

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
			add_shortcode( 'plda_home_slider', array( $this, 'output' ) );

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
	                'post_type'  			=> 'produit,page,post',
	                'tag_selection'  		=> 'homeSlider',
	                'default_button_label'  => 'RÉSERVER',
	                'nbre_item'      		=> 7
			), $atts));

			// Define output
			$output = '';
			$compteur = 0;
			$validIds = array();

			$post_type_arr 	 = explode(",", $post_type);
			$vignette_format = array(
				'post' 		=> 'vignette_news_@x3',
				'produit' 	=> 'vignette_affiche',
				'page'	 	=> 'vignette_news_@x3',
			);
			$tax_genre = array(
				'post' 		=> 'category',
				'produit' 	=> 'genre_evenement',
				'page'	 	=> 'category',
			);

			if ( $tag_selection == 'homeSlider' ) {

				$args = array(
					'post_type' 	 => $post_type_arr, 
					'orderby' 		 => 'post__in',
					'posts_per_page' => $nbre_item,
					'post__in'		 => $this->outputHomeSlider()
				);

			} else {

				$args = array(
					'post_type' 	 => $post_type_arr, 
					'orderby' 		 => 'date',
					'order' 		 => 'DESC',
					'tag' 			 => $tag_selection,
					'post_status' 	 => 'publish',
					'posts_per_page' => $nbre_item
				) ;
			}

			$loop = new \WP_Query( $args );

			if( $loop->have_posts() ):

				$output  = '<div class="wrapper-slider">';
				$output .= '<div class="container">';

				$output .= '<div class="slider--' . $tag_selection . ' swiper-container">';
				$output .= inlineSvg('bkgn-slider-parallax', true);
				$output .= '<div class="swiper-wrapper">';
				while( $loop->have_posts() && ( $compteur <= (int)$nbre_item ) ): $loop->the_post();
					
					$compteur  += 1;
					$produitGenre =  '';
					$classGenre = 'genre-concert';
					$contentType	= get_post_type();
					$titre 		= carbon_get_post_meta( get_the_ID(), 'titre_slider') ? : get_the_title();
					$date_event = ( $contentType == 'produit' ) ? \Plda\Core\Tags::event_display_date() : get_the_date( 'j F Y' );


					$genre_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), $tax_genre[$contentType] );
				    $produitGenre 	 =  reset($genre_evenement);
				    $classGenre   	 =  array_key_first($genre_evenement);

					$themecouleur_evenement = \Plda\Core\Tags::get_post_tax_terms( get_the_ID(), 'plda_thematique_couleur', true );
				    $dataCouleur   	 		=  array_key_first($themecouleur_evenement);

				    $dataCouleur = ($dataCouleur=='')? ( ($classGenre == 'genre_evenement_racing92')? 'racing92-theme' : 'green-theme'  ) : $dataCouleur;

					$output .= '<div class="swiper-slide slide__item" data-slidetype="slide--' . $contentType . '" data-type="' . $classGenre . '" data-theme="genre_evenement_' . $dataCouleur  . '">';
					$output .= '	<div class="slide__body">';
					$output .= '		<div class="slide-category titre-hero text-grad-context">' . $produitGenre . '</div>';
					$output .= '		<div class="slide-title">' . $titre . inlineSvg('ecaille-titre', true) . '</div>';
					$output .= '		<p class="slide-date">' . $date_event . '</p>';
					$output .= '		<a href="' . get_permalink() . '" class="btn btn-context" title="' . esc_attr( get_the_title() ) . '">' . (($contentType == 'produit')? $default_button_label : 'LIRE' ). '</a>';
			  		$output .= '	</div>';
					$output .= '	<a class="slide__link-full" href="' . get_permalink() . '" title="' . esc_attr( get_the_title() ) . '"></a>';
		            if( has_post_thumbnail() )
		                $output .= '<div class="slide_image">' . get_the_post_thumbnail( get_the_ID(), $vignette_format[$contentType], array('class' => 'card-img-right') ) . '</div>';
					$output .= '</div>';

				endwhile;
				$output .= '</div>';
				$output .= '<div class="swiper-buttons-nav container">';
				$output .= '	<div class="swiper-button-prev"><i class="fas fa-angle-left stack-circle fz-24 plda-icon-context"></i></div>';
				$output .= '	<div class="swiper-button-next"><i class="fas fa-angle-right stack-circle fz-24 plda-icon-context"></i></div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				wp_reset_postdata();
			else:
				$output = '';
			endif;


			// Return output
			return $output;

		}

		private function outputHomeSlider() {

			$arrSlides 			  = carbon_get_theme_option( 'slider_news_hp' );
			$arrAllValidEventsIds = Transients::get_transient_query_ids( 'ALL_CURRENT_EVENTS' );
			$arrValidIds 	 	  = array();


			foreach ($arrSlides as $slide) {
				if ( $slide['subtype'] == 'produit' && in_array($slide['id'],$arrAllValidEventsIds) )
					$arrValidIds[] = $slide['id'];
				elseif ( $slide['subtype'] != 'produit' )
					$arrValidIds[] = $slide['id'];
			}

			return ((!isset($arrValidIds) || empty($arrValidIds)) ? array(0) : $arrValidIds);
		}

	}

}