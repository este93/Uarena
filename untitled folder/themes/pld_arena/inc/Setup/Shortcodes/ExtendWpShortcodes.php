<?php
/**
 * Customize Wordpress  shortcodes 
 *
 */

namespace Plda\Setup\Shortcodes;

use BrightNucleus\Config\ConfigInterface as Config;
use BrightNucleus\Config\ConfigTrait;


if ( ! class_exists( 'ExtendWpShortcodes' ) ) {

	class ExtendWpShortcodes {

		use ConfigTrait;

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct( Config $config ) 
		{
			
			$this->processConfig( $config );


			// Alter the Gallery shortcode for Hospitality page
			add_filter( 'render_block', array($this, 'galleryToSlider'),10,2);

		}

		/**
		 * Chnage Gallery Output
		 * @param  $attr (array|string) Shortcode attributes array or empty string.
		 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
		 * @return shortcode output
		 */
		public static function galleryToSlider( $block_content, $block ) {

			// use blockName to only affect the desired block
			if( "core/gallery" !== $block['blockName'] ) {
				return $block_content;
			}

			if ( empty( $block['attrs']['ids'] ) )
				return $block_content;

			if ( !isset( $block['attrs']['className'] ) || ($block['attrs']['className'] != 'hospitalite-gallery') )//Class name for hospitality slider
				return $block_content;

			$imageIds = $block['attrs']['ids'];
			$output = '';

			foreach ( $imageIds as $imageId ) {
				$imageArray = wp_get_attachment_image_src( $imageId, 'large');

				$output .= "
					<div class=\"swiper-slide\" style=\"background-image:url(" . $imageArray[0] . ")\">
					</div>";

			}

			$output  = '<div class="swiper-container" data-context="genre_evenement_offre"><div class="swiper-wrapper">' . $output . '</div>';
			$output .= inlineSvg('bkgn-slider-parallax', true);
			$output .= '<div class="swiper-dots-nav">';
			$output .= '</div>';
			$output .= '<div class="swiper-buttons-nav container">';
			$output .= '	<div class="swiper-button-prev"><i class="fas fa-angle-left stack-circle fz-24 plda-icon-context"></i></div>';
			$output .= '	<div class="swiper-button-next"><i class="fas fa-angle-right stack-circle fz-24 plda-icon-context"></i></div>';
			$output .= '</div>';
			$output .= '</div>';

			return $output;
		}

	}
}