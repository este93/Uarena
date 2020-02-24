<?php
/**
 * Template Name: Test code Template
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package plda
 */

        // ini_set('memory_limit','512M');
        // ini_set('max_execution_time', 0);

get_header(); ?>

<div class="container">

	<div class="row">

		<div class="col">

			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php
                    $temporalite_offre = array('vip' => 'VIP','salon' => 'SALON','loge' => 'LES LOGES' );

                    $temporalite_offre = array_map( function( $cat ) {
                            if ( $cat != 'VIP' ) {
                                return $cat;
                            }
                         },
                         $temporalite_offre
                    );

                    var_export(array_filter($temporalite_offre));

//Usage
  

                    // $terms[] = array
                    // (
                    //     'term'      => 'Test term parent',   // the term 
                    //     'taxonomy'  => 'type_offre',     // the taxonomy
                    //     'args'      => array(
                    //         'description' => 'Test parent',
                    //         'slug'        => 'test-parent',
                    //         'parent'      => 0
                    //     ),
                    // );
                    // $terms[] = array
                    // (
                    //     'term'      => 'Test enfant',   // the term 
                    //     'taxonomy'  => 'type_offre',     // the taxonomy
                    //     'args'      => array(
                    //         'description' => 'Test enfant',
                    //         'slug'        => 'test-enfant',
                    //         'parent'      => 'test-parent'
                    //     ),
                    // );

                    // foreach ($terms as $term) {
                    //     if ( isset($term['args']['parent']) && ($term['args']['parent'] !== 0) ){
                    //         $parent_term = term_exists( $term['args']['parent'], $term['taxonomy'] );
                    //         if ( $term !== null )
                    //             $term['args']['parent'] = $parent_term['term_id'];
                    //         else
                    //             $term['args']['parent'] = 0;
                    //     }

                    //     wp_insert_term(
                    //         $term['term'],      // the term 
                    //         $term['taxonomy'],  // the taxonomy
                    //         $term['args']       // args
                    //     );
                    // }

					// $data = new PldaPlugin\Api\ImportXmlApi();
    	// 			$data->processXML();
                    
                    // delete_transient( 'PLDA_ALL_CURRENT_PRODUCTS' );
                    // delete_transient( 'PLDA_ALL_CURRENT_EVENTS' );
                    // delete_transient( 'PLDA_ALL_CURRENT_VIP' );
                   
                    // function number_format_drop_zero_decimals($n, $n_decimals)
                    //         {
                    //              $n = (float) str_replace([','], ['.'], $n); 
                    //             return ((floor($n) == round($n, $n_decimals)) ? number_format($n) : number_format($n, $n_decimals));
                    //         }
                    // echo number_format_drop_zero_decimals('54.33', 2);
                    // echo "<hr>";
                    // echo number_format_drop_zero_decimals('54,33', 2);
                    // echo "<hr>";
                    // echo number_format_drop_zero_decimals('54,00', 2);
                    // echo "<hr>";
                    // echo number_format_drop_zero_decimals('54.00', 2);

    				//echo Plda\Core\Tags::get_date_text(1);

    				 	// 	$plda_pages_services_query = array();
    						// $terms = get_terms( 'plda_service' );
    						 
    						// foreach( $terms as $term ) {
    						//     wp_reset_query();
    						//     $args = array( 
    						//         'post_type' => 'page',
    						//         'tax_query' => array( array(
    						//                 'taxonomy' => 'plda_service',
    						//                 'field' => 'slug',
    						//                 'terms' => $term->slug,
    						//                  ),
    						//         ),
    						 
    						//      );
    						 
    						//      // if the term has associated posts, get the term and first Post ID
    						//      $query = new WP_Query( $args );
    						//      if( $query->have_posts() ) {
    						//      	$plda_pages_services_query[$term->slug] = $query->posts[0]->ID;
    						//      }
    						//    }

					?>

				</main><!-- #main -->
			</div><!-- #primary -->

		</div><!-- .col- -->

	</div><!-- .row -->

</div><!-- .container -->

<?php
get_footer();
