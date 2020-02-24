<?php

namespace Plda\Custom;

/**
 * Extras.
 */
class Extras
{
	/**
     * register default hooks and actions for WordPress
     * @return
     */
	public function register()
	{
		add_filter( 'body_class', array( $this, 'body_class' ) );
		//search customization
		add_filter( 'pre_get_posts',array( $this, 'filter_plda_search_query' ) );
		//add_filter( 'posts_orderby', array( $this,'order_search_by_posttype' ), 10, 2 );
		add_filter( 'relevanssi_comparison_order', array( $this,'rlv_order_search_by_posttype') );
		add_filter( 'relevanssi_modify_wp_query', array( $this,'rlv_orderby_search_by_posttype') );

	}

	public function filter_plda_search_query($wp_query) {
	
	if ( ! is_admin() && $wp_query->is_main_query() ) {
		if ( $wp_query->is_search ) {
		    //$wp_query->set('post__in',array('id'));
		    $wp_query->set( 'posts_per_page', 18 );
		    //TODO
		    //  Post per page : 18
		    //  Not past events ? 
		    //  Page + posts
		    // -> transient
		}
	}

	return $wp_query;
	}

	public function order_search_by_posttype( $orderby, $wp_query ){
	    if( ! $wp_query->is_admin && $wp_query->is_search ) :
	        global $wpdb;
	        $orderby =
	            "
	            CASE WHEN {$wpdb->prefix}posts.post_type = 'produit' THEN '1' 
	                 WHEN {$wpdb->prefix}posts.post_type = 'post' THEN '2' 
	                 WHEN {$wpdb->prefix}posts.post_type = 'page' THEN '3' 
	            ELSE {$wpdb->prefix}posts.post_type END ASC, 
	            {$wpdb->prefix}posts.post_title ASC";
	    endif;
	    return $orderby;
	}

	/**
	 * Specific Relevanssi- search plugin filters.
	 */

	function rlv_order_search_by_posttype( $order_array ) {
	    $order_array = array(
	        'produit' => 3,
	        'post' => 2,
	        'page' => 1,
	    );
	    return $order_array;
	}


	function rlv_orderby_search_by_posttype( $query ) {
	    $query->set( 'orderby', 'post_type' );
	    return $query;
	}

	public function body_class( $classes )
	{

		global $post;
		
		// classe specifique billetterie

		if( is_singular( 'produit' ) )
		{
		  $classes = array_merge( $classes, array_values(\Plda\Core\Tags::get_class_of_tax( $post->ID )) );
		}

		if( is_singular( 'post' ) )
		{
		  $classes = array_merge( $classes, array_keys(\Plda\Core\Tags::get_post_tax_terms($post->ID, 'category', true)) );
		  $classes = array_merge( $classes, array_keys(\Plda\Core\Tags::get_post_tax_terms($post->ID, 'plda_thematique_couleur', true)) );
		}

		// classe specifique pour les pages

		if( is_page() ) { 
		    /* Get an array of Ancestors and Parents if they exist */
		    $parents = get_post_ancestors( $post->ID );
		    /* Get the top Level page->ID count base 1, array base 0 so -1 */
		    $id = ($parents) ? $parents[count($parents)-1]: $post->ID;
		    /* Get the parent and set the $class with the page slug (post_name) */
		    $parent = get_post( $id );
		    $class = $parent->post_name;
		}

		if( is_page() )
		{

		    /* Get an array of Ancestors and Parents if they exist */
		    $parents = get_post_ancestors( $post->ID );
		    /* Get the top Level page->ID */
		    $id = ($parents) ? $parents[count($parents)-1]: $post->ID;
		    /* Get the parent and set the $class with the plda_type_page tax slug */
		    $parent = get_post( $id );

		    $custom_terms = get_the_terms($parent , 'plda_type_page');

		  if ( $custom_terms && ! is_wp_error( $custom_terms ) ) {
		  	foreach ($custom_terms as $custom_term) {
		  		$classes[] = $custom_term->slug;
		  	}
		  }
		}

		return $classes;
	}

}
