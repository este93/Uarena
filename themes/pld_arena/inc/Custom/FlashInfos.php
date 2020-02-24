<?php

namespace Plda\Custom;

/**
 * Transients.
 */
class FlashInfos
{
	/**
     * register default hooks and actions for WordPress
     * @return
     */
	public function register()
	{
		add_action( 'content_after_header', array( $this, 'insert_flash_infos' ) );
		add_filter( 'save_post', array( $this, 'delete_transient_query_ids' ), 10, 2 );
	}


	/**
	 * Function to search for product ids and cache result
	 * it returns an array of product ID's
	 * 
	 * @param string 
	 * @return array of product ID's
	 */

	public static function insert_flash_infos()
	{
		$now = (new \DateTime("now", new \DateTimeZone('Europe/Paris')))->format('Y-m-d');

		$args = array(
				'post_type' 	 => 'flashinfo', 
		        'fields'		 => 'ids',
		        'meta_key' 		 => '_flashinfo_date_debut',
		        'orderby' 		 => 'meta_value',
		        'order' 		 => 'ASC',
				'post_status' 	 => 'publish',
		        'posts_per_page' => -1,
				'meta_query' => array(
				    'relation' => 'AND',
				    array(
				        'key'     => '_flashinfo_date_debut',
				        'value'   => $now,
				        'compare' => '<='
				    ),
				    array(
				        'key'     => '_flashinfo_date_fin',
				        'value'   => $now,
				        'compare' => '>'
				    )
				),
			);



		$cache_key = 'PLDA_FLASHINFOS';
		if ( ! $ids = get_transient( $cache_key ) ) {
		    $query = new \WP_Query( $args );

		    $ids = $query->posts;

			
		    set_transient( $cache_key, $ids, HOUR_IN_SECONDS );
		}

		echo $this->get_the_current_flash_info( $ids );

	}


	public function get_the_current_flash_info( $ids )
	{

		$output = $list_icones = '';

		if ( !$ids || !(array)$ids )
			return $output;

		$post_id = (int)$ids[0];

		$couleur 			   = carbon_get_post_meta($post_id, 'flashinfo_couleur');
		$flashinfo_description = ($flashinfo_description = carbon_get_post_meta($post_id, 'flashinfo_description')) ? '<div class="flashinfo__description">' . $flashinfo_description . '</div>' : '';
		$icones 			   = carbon_get_post_meta($post_id, 'flashinfo_icones');

		$oConfigPlda 	= \Plda\Api\ConfigPlda::getInstance();
        $iconesChoices  = is_array($oConfigPlda->get_key('Plda/icones/flashinfo_icones'))? $oConfigPlda->get_key('Plda/icones/flashinfo_icones') : array();
      
        if ( $icones ) {
			foreach ($icones as $icone) {
				$list_icones.= '<li><a href="' . $icone['flashinfo_icone_url'] . '">' . '<i class="' . $icone['flashinfo_icone_svg'] . '"></i>' . $iconesChoices[$icone['flashinfo_icone_svg']] . '</a></li>';
			}
        }

		$output.= '<div id="flashinfo_' . $post_id . '" data-cookiename="flashinfo_' . $post_id . '" class="flashinfo-popup flashinfo--' . $couleur . '" uk-offcanvas="overlay:false;esc-close:false;bg-close:false">';
		$output.= '    <div class="uk-offcanvas-bar">';
		$output.= '	       <button class="uk-offcanvas-close btn-link" type="button" uk-close></button>';
		$output.= '	       <div class="flashinfo__content container">';
		$output.= '        	<div class="flashinfo__content-left">';
		$output.= '	        	<div class="flashinfo__title">' . get_the_title( $post_id ) . '</div>';
		$output.= '	        	' . $flashinfo_description . '';
		$output.= '        	</div>';
		$output.= '        	<div class="flashinfo__content-right">';
		$output.= '	        	<ul class="flashinfo__icones">' . $list_icones . '</ul>';
		$output.= '        	</div>';
		$output.= '        </div>';

		$output.= '    </div>';
		$output.= '</div>';

		//var_export($output);

		return $output;


	}

	public function delete_transient_query_ids( $post_id, $post )
	{

		// If this is an auto save routine don't do anyting
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if ( $post->post_type == 'flashinfo' ) {
			delete_transient( 'PLDA_FLASHINFOS' );
		}
	}

}
