<?php

namespace Plda\Custom;

/**
 * Transients.
 */
class Transients
{
	/**
     * register default hooks and actions for WordPress
     * @return
     */
	public function register()
	{
		add_filter( 'save_post', array( $this, 'delete_transient_services_pages' ), 10, 2 );
		add_filter( 'save_post', array( $this, 'delete_transient_query_ids' ), 10, 2 );
	}


	/**
	 * Function to search for product ids and cache result
	 * it returns an array of product ID's
	 * 
	 * @param string 
	 * @return array of product ID's
	 */

	public static function get_transient_query_ids( $productList = 'ALL_CURRENT_PRODUCTS' )
	{
		$now = (new \DateTime("now", new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s');

		$insertPromo = true;

		$args = array(
				'post_type' 	 => 'produit', 
		        'meta_key' 		 => '_product_start',
		        'orderby' 		 => 'meta_value',
		        'order' 		 => 'ASC',
				'post_status' 	 => 'publish',
		        'posts_per_page' => -1,
		        'fields'		 => 'ids',
				'meta_query' => array(
				    'relation' => 'AND',
				    array(
				        'key'     => '_product_end',
				        'value'   => $now,
				        'compare' => '>='
				    )
				),
			);

		switch ($productList) {
			case 'ALL_CURRENT_PRODUCTS':

				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' 	=> 'famille_produit',
						'field' 	=> 'slug',
						'terms'    	=> array('event', 'offre')
					),
					array(
						'taxonomy' 			=> 'type_offre',
						'field' 			=> 'slug',                
						'terms' 			=> array( 'vip' ),
						'include_children' 	=> true,       
						'operator' 			=> 'NOT IN'
					)
				);

				break;

			case 'ALL_CURRENT_EVENTS':

				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' 	=> 'famille_produit',
						'field' 	=> 'slug',
						'terms'    	=> array('event')
					),
					array(
						'taxonomy' => 'genre_evenement',
						'operator' => 'EXISTS'
					)
				);

				break;

			case 'ALL_CURRENT_EVENTS_BY_PUB_DATE':

				$args['orderby'] = 'date';
				$args['order']   = 'DESC';

				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' 	=> 'famille_produit',
						'field' 	=> 'slug',
						'terms'    	=> array('event')
					),
					array(
						'taxonomy' => 'genre_evenement',
						'operator' => 'EXISTS'
					)
				);

				break;

			case 'ALL_CURRENT_VIP':

				$insertPromo = false;

				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' => 'famille_produit',
						'field' => 'slug',
						'terms'    => array('offre')
					),
					array(
						'taxonomy' => 'type_offre',
						'field' => 'slug',                
						'terms' => array( 'vip' ),
						'include_children' => true,       
						'operator' => 'IN'                
					)
				);

				break;
			
			default:
				# code...
				break;
		}



		$cache_key = 'PLDA_'.$productList;
		if ( ! $ids = get_transient( $cache_key ) ) {
		    $query = new \WP_Query( $args );

		    $ids = $query->posts;

		    // Search promo product if exists
		    if ( $insertPromo ){
			    $args = array( 
			        'post_type' => 'produit',
			        'orderby' 	=> 'date',
			        'order' 	=> 'DESC',
			        'tax_query' => array( array(
			                'taxonomy' => 'famille_produit',
			                'field' => 'slug',
			                'terms' => 'promo',
			                 ),
			        ),
			    
			     );
			    
			     // if the term has associated posts, get the term and first Post ID
			     $query = new \WP_Query( $args );
			     if( $query->have_posts() ) {
			     	array_splice($ids, 2, 0, $query->posts[0]->ID);
			     }
			}
			
		    set_transient( $cache_key, $ids, 10 * MINUTE_IN_SECONDS );
		}

		return $ids;
	}

	public static function get_pages_services()
	{
		/* Retrieves all the terms from the taxonomy plda_service
		 *  http://codex.wordpress.org/Function_Reference/get_categories
		 */
		
		    if ( !( $plda_pages_services_query = get_transient('plda_pages_services_query') ) ) {
		  		$plda_pages_services_query = array();
		 		$terms = get_terms( 'plda_service' );
		 		 
		 		foreach( $terms as $term ) {
		 		    //wp_reset_query();
		 		    $args = array( 
		 		        'post_type' => 'page',
		 		        'tax_query' => array( array(
		 		                'taxonomy' => 'plda_service',
		 		                'field' => 'slug',
		 		                'terms' => $term->slug,
		 		                 ),
		 		        ),
		 		 
		 		     );
		 		 
		 		     // if the term has associated posts, get the term and first Post ID
		 		     $query = new \WP_Query( $args );
		 		     if( $query->have_posts() ) {
		 		     	$plda_pages_services_query[(string)$term->term_id] = array( 'service_slug' => $term->slug, 'post_ID' => $query->posts[0]->ID );
		 		     }
		 		     // 4. On réinitialise à la requête principale (important)
					 wp_reset_postdata();
				}
			
				set_transient( 'plda_pages_services_query', $plda_pages_services_query, DAY_IN_SECONDS );
			}
			
			return $plda_pages_services_query;
	}

	public static function resetTransients()
	{

		// If this is an auto save routine don't do anyting
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
		
		delete_transient( 'PLDA_ALL_CURRENT_PRODUCTS' );
		delete_transient( 'PLDA_ALL_CURRENT_EVENTS' );
		delete_transient( 'PLDA_ALL_CURRENT_EVENTS_BY_PUB_DATE' );
		delete_transient( 'PLDA_ALL_CURRENT_VIP' );
	}

	public function delete_transient_services_pages( $post_id, $post )
	{

		// If this is an auto save routine don't do anyting
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if ( $post->post_type == 'page' ) {
			delete_transient( 'plda_pages_services_query' );
		}
	}

	public function delete_transient_query_ids( $post_id, $post )
	{

		// If this is an auto save routine don't do anyting
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if ( $post->post_type == 'produit' ) {
			delete_transient( 'PLDA_ALL_CURRENT_PRODUCTS' );
			delete_transient( 'PLDA_ALL_CURRENT_EVENTS' );
			delete_transient( 'PLDA_ALL_CURRENT_EVENTS_BY_PUB_DATE' );
			delete_transient( 'PLDA_ALL_CURRENT_VIP' );
		}
	}

}
